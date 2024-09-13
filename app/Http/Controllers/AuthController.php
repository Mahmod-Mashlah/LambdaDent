<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;

class AuthController extends Controller
{
    use HttpResponses; // this is my trait


    public function login(LoginUserRequest $request)
    {
        $request->validated($request->all());

        if (!Auth::attempt($request->only(['email', 'password']))) {
            return $this->error('', "Credentials don't match (Unauthorized)", 401);
        }

        $user = User::where('email', $request->email)->first();

        return $this->success([
            'token' => $user->createToken('API Token of' . $user->name)->plainTextToken,
            'user' => $user,
        ], "logged in successfully");
    }

    public function register(StoreUserRequest $request)
    {
        $request->validated($request->all());

        $user = User::create([

            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'type' => 'client',
            'register_accepted' => false,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),

        ]);

        return $this->success([
            'token' => $user->createToken('API Token of ' . $user->name)->plainTextToken,
            'user' => $user,
        ], "registered successfully, waiting for admin approval");
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return $this->success(
            [
                /*'message' => 'You have successfuly logged out', */],
            'logged out successfully'
        );
    }
}
