<?php

namespace App\Http\Controllers;

use App\Http\Requests\VacationRequestRequest;
use App\Http\Resources\VacationRequestResource;
use App\Models\VacationBalance;
use App\Models\VacationRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

use function Pest\Laravel\json;

class VacationRequestController extends Controller
{
    public function index(Request $request){
        $user = $request->user();
        if(in_array($user->role , ['hr' , 'manager'])){
            $vacationRequest =  VacationRequest::with(['user', 'vacationType'])->get();
        }else{
            $vacationRequest = VacationRequest::where('user_id' ,$user->id)
                ->with(['user' , 'vacationType'])
                ->get();
        }
        return VacationRequestResource::collection($vacationRequest);
    }
    
    public function myRequests(Request $request){
        $vacations =  $request->user()->vacationRequest()->with([ 'vacationType'])->get();
        return VacationRequestResource::collection($vacations);
    }
    public function store(VacationRequestRequest $request){
        $validatedData = $request->validated();
        $user = $request->user();

        $start_date = $request->start_date;
        $end_date = $request->end_date;

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

        $exists = VacationRequest::where('user_id' , $user->id)
            ->whereIn('status' , ['pending' , 'approved'])
            ->where('start_date' , '<=' , $end_date)
            ->where('end_date' , '>=' , $start_date)
            ->exists();

        if($exists){
            return response()->json(['dates' => 'You already have a vacation request in this date range.'] , 409);
        }


            

        
        $validatedData['user_id'] = $user->id;
        $validatedData['total_days'] = $days;

        $validatedData['status'] = 'pending';

        $vacationRequest = VacationRequest::create($validatedData);
        return (new VacationRequestResource($vacationRequest))->response()->setStatusCode(201);

    }
    public function show(Request $request, $id){
        $vacationRequest = VacationRequest::with(['user', 'vacationType'])->findOrFail($id);
        $user = $request->user();

        $canView = $vacationRequest->user_id === $user->id || 
                   in_array($user->role, ['admin', 'hr', 'manager']);

        if (!$canView) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return new VacationRequestResource($vacationRequest);
    }
    public function update(VacationRequest $vacationRequest, VacationRequestRequest $request){
        $validatedData = $request->validated();
        $user = $request->user();
        $start_date = $request->start_date;
        $end_date = $request->end_date;

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

        $exists = VacationRequest::where('user_id' , $user->id)
            ->whereIn('status' , ['pending' , 'approved'])
            ->where('start_date' , '<=' , $start_date)
            ->where('end_date' , '>=' , $end_date)
            ->exists();

        if($exists){
            return response()->json(['dates' => 'You already have a vacation request in this date range.'] , 409);
        }
        if($vacationRequest->status !== 'pending'){
            return response()->json(['error' => 'Request cannot be edited|This request is already proccessed'] , 403);
        }
        if($request->user()->id !== $vacationRequest->user_id){
            return response()->json(['error'=> 'Request vacation not found'], 404);
        }


        $validatedData['user_id'] = $user->id;
        $validatedData['total_days'] = $days;

        $validatedData['status'] = 'pending';

        

        $vacationRequest->update($validatedData);
        
        return new VacationRequestResource($vacationRequest);

    
        
    }

    public function destroy(VacationRequest $vacationRequest , Request $request){
        // if($vacationRequest->status === 'pending' && $request->user()->id == $vacationRequest->user_id){
        //     $vacationRequest->delete();
        //     return response()->json(["message" => "Deleted Successfully"]);
        // }else{
        //     return response()->json(['error' => 'Request cannot be deleted|This request might be for another user or its already proccessed'] , 403);
        // }
        if($vacationRequest->status !== 'pending'){
            return response()->json(['error' => 'Request cannot be deleted|This request is already proccessed'] , 403);
        }
        if($request->user()->id !== $vacationRequest->user_id){
            return response()->json(['error'=> 'Request vacation not found'], 404);
        }else{
            $vacationRequest->delete();
            return response()->json(["message" => "Deleted Successfully"]);
        }
        
        
    }
}
