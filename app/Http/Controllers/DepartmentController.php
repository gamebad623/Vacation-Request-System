<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\UserResource;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(){
        return DepartmentResource::collection(Department::all());
    }
    public function store(StoreDepartmentRequest $request){
        $validatedData = $request->validated();
        $department = Department::create([
            'name' => $request->name,
        ]);

        return (new DepartmentResource($department))->response()->setStatusCode(201);

    }

    public function show(Department $department){
        return new DepartmentResource($department);

    }
    public function update(StoreDepartmentRequest $request , Department $department){
        $validatedData = $request->validated();
        $department->update([
            'name'=> $request->name,
        ]);
        return new DepartmentResource($department);


    }

    public function destroy(Department $department){

        $department->delete();
        return response()->noContent();

    }
}
