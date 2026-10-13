<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    // Login API
    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please fix the validation errors',
                'data' => [
                    'errors' => $validator->errors()
                ]
            ], 422);
        }

        // Find user by email
        $user = User::where('email', $request->email)->first();

        // Check email and password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Either email/password is incorrect.',
                'data' => []
            ], 404);
        }

        // Generate Sanctum token
        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'You have logged in successfully.',
            'data' => [
                'name' => $user->name,
                'token' => $token
            ]
        ], 200);
    }


    // Register API
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please fix the validation errors',
                'data' => [
                    'errors' => $validator->errors()
                ]
            ], 422);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = 'user';
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'You have registered successfully.',
            'data' => [
                'user' => $user
            ]
        ], 201);
    }
}