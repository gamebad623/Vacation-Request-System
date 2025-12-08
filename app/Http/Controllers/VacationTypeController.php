<?php

namespace App\Http\Controllers;

use App\Http\Requests\VacationTypeRequest;
use App\Http\Resources\VacationTypeResource;
use App\Models\VacationType;
use Illuminate\Http\Request;

class VacationTypeController extends Controller
{
    public function index(){
        return VacationTypeResource::collection(VacationType::all());

    }

    public function store(VacationTypeRequest $request){
        $validatedData = $request->validated();
        $vacationType = VacationType::create($request->all());

        return (new VacationTypeResource($vacationType))->response()->setStatusCode(201);
    }

    public function show(VacationType $vacationType){
        return new VacationTypeResource($vacationType);
    }
    public function update(VacationType $vacationType , VacationTypeRequest $request){
        $validatedData = $request->validated();
        $vacationType->update([
            'name' => $request->name,
            'is_paid' => $request->is_paid,
            'max_days_per_year' => $request->max_days_per_year
        ]);
        return new VacationTypeResource($vacationType);
    }

    public function destroy(VacationType $vacationType){
        $vacationType->delete();
        return response()->noContent();
    }
}
