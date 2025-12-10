<?php

namespace App\Http\Controllers;

use Rules\Password;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class UserController extends Controller
{
    
    public function index(){
        // return new UserResource(User::all());
        return UserResource::collection(User::all());
    }
    // public function store(Request $request){
    //     $request->validate([
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
    //         'password' => ['required', 'confirmed', Rules\Password::defaults()],
    //         'role' => ['string' , 'in:employee,manager,hr']
            
    //     ]);

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->string('password')),
    //         'role' => $request->role
    //     ]);

    //     event(new Registered($user));

    //     Auth::login($user);

    //     return response()->noContent();

    // }
    public function show(){

    }
    public function update(User $user , Request $request){
        $validated = $request->validate([
            'name' => ['sometimes','required', 'string', 'max:255'],
            'email' => ['sometimes','required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['sometimes','string' , 'in:employee,manager,hr'],
            'department_id' => ['sometimes','required' , 'integer' , 'exists:departments,id'],
            
            
        ] , ['department_id.exits' => 'The selected department does not exist']);

        if(!empty($validated['password'])){
            $validated['password'] = Hash::make($validated['password']);

        }else{
            unset($validated['password']);
        }

        $user->update($validated);

        return new UserResource($user);



        // $request->update([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'password' => Hash::make($request->string('password')),
        //     'role' => $request->role,
        //     'department_id' => $request->department_id
        // ]);

    }
    public function destroy(User $user){
        
        if($user->role === 'admin'){
           return response()->json(['message' => 'Admin cannot delete admin'] , 403);
        }else{
            $user->delete();
            return response()->json(['message' => 'User deleted successfully']);
        }
        
    }
}
