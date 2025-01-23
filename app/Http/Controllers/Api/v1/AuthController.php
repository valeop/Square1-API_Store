<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //REGISTER method: create a new user
    public function register(Request $request) : JsonResponse{
        $validatedData = $request -> validate([
            'name' => 'required | string',
            'email' => 'required | email | unique:users',
            'password' => 'required | string | min:8'
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password'])
        ]);

        return response()->json([
            'user' => $user,
            'message' => 'User created successfully'
        ], 201);
    }

    //LOGIN method: enter the current user by creating a token
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required | email',
            'password' => 'required'
        ]);

        $user = User::where('email', request('email'))->first();

        if (!$user || !Hash::check($request -> password, $user->password)) {
            return response()->json(['message'=>'The provided credentials are incorrect'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token
        ]);
    }

    //LOGOUT method: exit the current user by deleting the token
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out succesfully'
        ]);
    }

    //GET PROFILE method: obtain the user's information by a current active token
    public function getProfile(Request $request) {
        return $request->user();
    }
}
