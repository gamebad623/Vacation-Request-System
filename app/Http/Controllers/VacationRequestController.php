<?php

namespace App\Http\Controllers;

use App\Http\Requests\VacationRequestRequest;
use App\Http\Resources\VacationRequestResource;
use App\Models\VacationBalance;
use App\Models\VacationRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VacationRequestController extends Controller
{
    public function index(){
        $vacationRequest =  VacationRequest::with(['user', 'vacationType'])->get();
        return VacationRequestResource::collection($vacationRequest);
    }
    public function myRequests(Request $request){
        $vacations =  $request->user()->vacationRequest()->with('vacationType')->get();
        return VacationRequestResource::collection($vacations);
    }
    public function store(VacationRequestRequest $request){
        $validatedData = $request->validated();
        $user = $request->user();

        $days = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) + 1;
        $year = Carbon::parse($request->start_date)->year;

        $balance = VacationBalance::where([
            'user_id'=> $user->id,
            'vacation_type_id' => $request->vacation_type_id,
            'year' => $year
        ])->first();

        if(!$balance)
            return response()->json(['error' => 'No balance for this year'] , 422);

        if($balance->remaining < $days)
            return response()->json(['error' => 'Not enough balance'] , 422);

        
        $validatedData['user_id'] = $user->id;
        $validatedData['total_days'] = $days;

        $validatedData['status'] = 'pending';

        $vacationRequest = VacationRequest::create($validatedData);
        return (new VacationRequestResource($vacationRequest))->response()->setStatusCode(201);

    }
    public function show($id){
        $vacationRequest = VacationRequest::with(['user', 'vacationType'])->findOrFail($id);
        return new VacationRequestResource($vacationRequest);
        

    }
    public function update(VacationRequest $vacationRequest, VacationRequestRequest $request){
        $validatedData = $request->validated();
        $user = $request->user();

        $days = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) + 1;
        $year = Carbon::parse($request->start_date)->year;

        $balance = VacationBalance::where([
            'user_id'=> $user->id,
            'vacation_type_id' => $request->vacation_type_id,
            'year' => $year
        ])->first();

        if(!$balance)
            return response()->json(['error' => 'No balance for this year'] , 422);

        if($balance->remaining < $days)
            return response()->json(['error' => 'Not enough balance'] , 422);

        $validatedData['user_id'] = $user->id;
        $validatedData['total_days'] = $days;

        $validatedData['status'] = 'pending';

        $vacationRequest->update($validatedData);
        
        return new VacationRequestResource($vacationRequest);

    
        
    }

    public function destroy(VacationRequest $vacationRequest){
        $vacationRequest->delete();
        return response()->json(["message" => "Deleted Successfully"]);
    }
}
