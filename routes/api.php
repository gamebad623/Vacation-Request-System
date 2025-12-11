<?php

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\VacationTypeController;
use App\Http\Controllers\VacationBalanceController;
use App\Http\Controllers\VacationRequestController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Resources\UserResource;

//Auth routes

Route::post('/auth/login' ,  [LoginController::class , 'store']);



Route::middleware('auth:sanctum')->group(function(){

    //Logout 
    Route::post('/auth/logout' , [LoginController::class , 'destroy']);

    //Getting user info
    Route::get('/profile' , function (Request $request){
        return new UserResource($request->user());
    });

    //Email verification routes (All authenticated users)
    Route::post('/resend-verification' , [EmailVerificationNotificationController::class , 'resend']);

    //Departments (Admin Only)
    Route::middleware('role:admin')->group(function(){
        Route::apiResource('/departments' , DepartmentController::class);
        Route::apiResource('/auth/users' , UserController::class);
        Route::post('/auth/register' , [RegisteredUserController::class , 'store']);
    });

    //Vacation Types and balances (HR)
    Route::middleware(['role:hr' , 'verified'])->group(function(){
        Route::apiResource('/vacation-types' , VacationTypeController::class);
        Route::apiResource('/vacation-balances' , VacationBalanceController::class);
    });

    Route::middleware(['verified' , 'role:employee'])->group(function(){
        Route::prefix('vacations')->group(function(){
            Route::post('/' , [VacationRequestController::class , 'store']);
            Route::get('/' , [VacationRequestController::class , 'myRequests']);
            Route::get('/{id}' , [VacationRequestController::class , 'show']);
            Route::put('/{vacationRequest}' , [VacationRequestController::class , 'update']);
            Route::delete('/{vacationRequest}' , [VacationRequestController::class , 'destroy']);
        });
    });

    //Approvals (Manager, HR only - verified users)
    Route::middleware('verified')->group(function(){
        Route::prefix('approvals')->middleware('role:manager,hr')->group(function(){
            Route::get('/pending' , [ApprovalController::class , 'pending']);
            Route::get('/vacations' , [VacationRequestController::class , 'index']);
            Route::post('/{id}/approve' , [ApprovalController::class , 'approve']);
            Route::post('/{id}/reject' , [ApprovalController::class , 'reject']);
        });
    });
});




//Route::apiResource('/vacation_requests', VacationRequestController::class);


require __DIR__ .'/auth.php';