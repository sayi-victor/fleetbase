<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Fleetbase\Support\Auth;
use Illuminate\Support\Facades\Hash;
use Fleetbase\Models\User;

class AuthController extends Controller
{
    public function signIn(Request $request)
{
    $request->validate([
        'email' => 'required|email|max:60',
        'password' => 'required',
    ]);

    // Find user by email
    $user = User::where('email', $request->email)->first();

    // If user not found or password doesn't match
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'errors' => [
                'email' => ['Invalid email or password']
            ]
        ], 422);
    }

    // Create token for the user
    $token = $user->createToken($user->uuid)->plainTextToken;

    return response()->json([
        'message' => 'Login successful',
        'token' => $token,
        'user' => $user,
    ]);
    }

}
