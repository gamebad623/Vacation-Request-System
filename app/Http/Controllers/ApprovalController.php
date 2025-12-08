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

    public function approve($id){
        $vacationRequest = VacationRequest::findOrFail($id);
        if($vacationRequest->status !== 'pending'){
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
            return response()->json(['message' => 'Not enough balanc'] , 422);
        }

        $balance->remaining -= $vacationRequest->total_days;
        $balance->used += $vacationRequest->total_days;
        $balance->save();

        

        $vacationRequest->status = 'approved';
        $vacationRequest->save();

        return new VacationRequestResource($vacationRequest);


        
    }
    public function reject($id){
        $vacationRequest = VacationRequest::findOrFail($id);

        if($vacationRequest->status !== 'pending'){
            return response()->json(['error' => 'Request already processed']);
        }

        $vacationRequest->status = 'rejected';
        $vacationRequest->save();

        return new VacationRequestResource($vacationRequest);
    }
    
}
