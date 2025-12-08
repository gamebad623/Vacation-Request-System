<?php

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\VacationTypeController;
use App\Http\Controllers\VacationBalanceController;
use App\Http\Controllers\VacationRequestController;
use App\Http\Controllers\Auth\RegisteredUserController;

//Auth routes
Route::post('/auth/register' , [RegisteredUserController::class , 'store']);
Route::post('/auth/login' ,  [LoginController::class , 'store']);


Route::middleware('auth:sanctum')->group(function(){

    //Logout 
    Route::post('/auth/logout' , [LoginController::class , 'destroy']);

    //Getting user info
    Route::get('/profile' , function (Request $request){
        return $request->user();
    });

    //Departments (Admin Only)
    Route::middleware('role:admin')->group(function(){
        Route::apiResource('/departments' , DepartmentController::class);
    });

    //Vacation Types (Admin and HR)
    Route::middleware('role:admin,hr')->group(function(){
        Route::apiResource('/vacation-types' , VacationTypeController::class);
    });

    Route::middleware('role:admin,hr')->group(function(){
        Route::apiResource('/vacation-balances' , VacationBalanceController::class);
    });

    Route::prefix('vacations')->group(function(){
        Route::post('/' , [VacationRequestController::class , 'store']);
        Route::get('/' , [VacationRequestController::class , 'myRequests']);
        Route::get('/{id}' , [VacationRequestController::class , 'show']);
        Route::put('/{vacationRequest}' , [VacationRequestController::class , 'update']);
        Route::delete('/{vacationRequest}' , [VacationRequestController::class , 'destroy']);
    });

    Route::prefix('approvals')->middleware('role:manager,hr,admin')->group(function(){
        Route::get('/pending' , [ApprovalController::class , 'pending']);
        Route::post('/{id}/approve' , [ApprovalController::class , 'approve']);
        Route::Post('/{id}/reject' , [ApprovalController::class , 'reject']);
    });




});




//Route::apiResource('/vacation_requests', VacationRequestController::class);


require __DIR__ .'/auth.php';