<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function authenticate(Request $request)
    {
        // Validate request
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

        // Check user and password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid email or password.',
                'data' => []
            ], 401);
        }

        // Check admin role
        if ($user->role !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Access denied. Admin only.',
                'data' => []
            ], 403);
        }

        // Generate Sanctum token
        $token = $user->createToken('admin-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Admin logged in successfully.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'token' => $token
            ]
        ], 200);
    }
}