<?php

namespace App\Http\Controllers;

use App\Http\Requests\VacationBalanceRequest;
use App\Http\Resources\VacationBalanceResource;
use App\Models\VacationBalance;
use Illuminate\Http\Request;

class VacationBalanceController extends Controller
{
    public function index(){
        // return VacationBalanceResource::collection(
        //     VacationBalance::with(['user' , 'vacationType'])->get()
        // );
        $vacationBalance = VacationBalance::with(['user' , 'vacationType'])->get();
     
        return VacationBalanceResource::collection($vacationBalance);
    }
    public function store(VacationBalanceRequest $request){
        $validatedData = $request->validated();
        $existing = VacationBalance::where([
            'user_id' => $request->user_id,
            'vacation_type_id' => $request->vacation_type_id,
            'year' => $request->year
        ]) -> first();

        if($existing)
            return response()->json([
                'error' => 'Balance already exists' , 
            ] ,  409);
        
        $validatedData['used'] = 0;
        $validatedData['remaining'] = $request->balance;
        $vacationBalance = VacationBalance::create($validatedData);
        // $vacationBalance = VacationBalance::create([
        //     'user_id' => $request->user_id,
        //     'vacation_type_id' => $request->vacation_type_id,
        //     'year' => $request->year,
        //     'balance' => $request->balance,
        //     'used' => 0,
        //     'remaining' => $request->balance
            
        // ]);
        return (new VacationBalanceResource($vacationBalance))->response()->setStatusCode(201);
    }

    public function show($id){
        return new VacationBalanceResource(
            VacationBalance::with(['user', 'vacationType'])->findOrFail($id)
        );

    }
    public function update(VacationBalanceRequest $request , VacationBalance $vacationBalance){
        $validatedData = $request->validated();
        $existing = VacationBalance::where([
            'user_id' => $request->user_id,
            'vacation_type_id' => $request->vacation_type_id,
            'year' => $request->year
        ]) -> first();

        if($existing)
            return response()->json([
                'error' => 'Balance already exists' , 
            ] ,  409);
        
        $validatedData['used'] = 0;
        $validatedData['remaining'] = $request->balance;
        $vacationBalance->update($validatedData);
        return new VacationBalanceResource($vacationBalance);

    }
    public function destroy(VacationBalance $vacationBalance){
        $vacationBalance->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
   
}
