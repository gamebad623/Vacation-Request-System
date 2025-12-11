<?php

namespace App\Http\Controllers;

use App\Http\Resources\VacationRequestResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\VacationBalance;
use App\Models\VacationRequest;

class ApprovalController extends Controller
{

    public function pending(){
        $vacationRequests = VacationRequest::with(['user' , 'vacationType'])->where('status' , 'pending')->get();
        return VacationRequestResource::collection($vacationRequests);
    }

    public function approve($id , Request $request){
        $vacationRequest = VacationRequest::findOrFail($id);
        if($vacationRequest->status === 'approved' || $vacationRequest->status === 'rejected'){
            return response()->json(['error' => 'Request already processed']);
        }

        $balance = VacationBalance::where([
            'user_id' => $vacationRequest->user_id,
            'vacation_type_id' => $vacationRequest->vacation_type_id,
            'year' => Carbon::parse($vacationRequest->start_date)->year
        ])->first();

        if(!$balance){
            return response()->json(['error' => 'No balance for this year'] , 422);
        }
        if($balance->remaining < $vacationRequest->total_days){
            return response()->json(['message' => 'Not enough balance'] , 422);
        }

        $balance->remaining -= $vacationRequest->total_days;
        $balance->used += $vacationRequest->total_days;
        $balance->save();

        

        
        if($request->user()->role === 'manager' ){
            $vacationRequest->manager_id = $request->user()->id;
            $vacationRequest->status = 'approved_manager';

        }elseif($request->user()->role === 'hr' && $vacationRequest->manager_id != null){
            $vacationRequest->hr_id = $request->user()->id;
            $vacationRequest->status = 'approved';
        }else{
            return response()->json(['message' => 'Manager should approve first']);
        }
        
        $vacationRequest->save();

        return new VacationRequestResource($vacationRequest);


        
    }
    public function reject($id , Request $request){
        $vacationRequest = VacationRequest::findOrFail($id);

        if($vacationRequest->status === 'approved' || $vacationRequest->status === 'rejected'){
            return response()->json(['error' => 'Request already processed']);
        }
        if($request->user()->role === 'manager' ){
            $vacationRequest->manager_id = $request->user()->id;
            $vacationRequest->status = 'rejected_manager';

        }elseif($request->user()->role === 'hr' && $vacationRequest->manager_id != null){
            $vacationRequest->hr_id = $request->user()->id;
            $vacationRequest->status = 'rejected';
        }else{
            return response()->json(['message' => 'Manager should reject first']);
        }

        $vacationRequest->status = 'rejected';
        $vacationRequest->hr_id = $request->user()->id;
        $vacationRequest->save();

        return new VacationRequestResource($vacationRequest);
    }
    
}
