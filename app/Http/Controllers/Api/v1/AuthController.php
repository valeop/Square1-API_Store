<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\ShoppingCart;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //REGISTER method: create a new user
    public function register(Request $request) : JsonResponse{

        DB::beginTransaction();

        try {
            //validate if data is fulfilling requirements
            $validatedData = $request -> validate([
                'name' => 'required | string',
                'email' => 'required | email | unique:users',
                'password' => 'required | string | min:8'
            ]);

            //create a new user
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password'])
            ]);

            //create a unique shopping cart for the current registered user
            ShoppingCart::create([
                'user_id' => $user->id,
                'created_date' => now()->toDateString('Y-m-d'),
                'status' => 'inactive'
            ]);

            DB::commit();
            return response()->json([
                'user' => $user,
                'message' => 'User created successfully',
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Error creating user: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);

            return response()->json([
                'error' => 'Failed to create user',
            ], 500);
        }
    }

    //LOGIN method: enter the current user by creating a token
    public function login(Request $request): JsonResponse
    {
        //validate if data is correctly filled
        $request->validate([
            'email' => 'required | email',
            'password' => 'required'
        ]);


        $user = User::where('email', request('email'))->first();
        //check if user credentials match
        if (!$user || !Hash::check($request -> password, $user->password)) {
            return response()->json(['message'=>'The provided credentials are incorrect'], 401);
        }

        //create a token related to the user logged
        $token = $user->createToken('auth_token')->plainTextToken;

        //update shopping cart status to active
        DB::table('shopping_carts')->where('user_id', $user->id)->update(['status' => 'active']);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
            'shopping_cart_status' => $user->shoppingCart->status
        ]);
    }

    //LOGOUT method: exit the current user by deleting the token
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        $request->user()->currentAccessToken()->delete();

        //update shopping cart status to inactive
        DB::table('shopping_carts')->where('user_id', $request->user()->id)->update(['status' => 'inactive']);

        return response()->json([
            'message' => 'Logged out succesfully',
            'shopping_cart_status' => $request->user()->shoppingCart->status,
        ]);
    }

    //GET PROFILE method: obtain the user's information by a current active token
    public function getProfile(Request $request) {
        return $request->user()->load('shoppingCart');
    }
}
