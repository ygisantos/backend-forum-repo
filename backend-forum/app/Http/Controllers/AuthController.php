<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request) {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if(Auth::attempt($validated)) {
            $user = User::where('email', $request->email)->first();
            $authToken = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'access_token' => $authToken,
            ], 200);
        }

        return response()->json([
            'message' => 'Invalid Credentials'
        ], 401);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function register(Request $request) {

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:256'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed']
        ]);


        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password'])
        ]);

        Auth::login($user);

        return response()->json([
            'message' => 'Registration successful'
        ], 200);
    }

    public function updateEmail(Request $request, $id) {
        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:users,email']
        ]);

        $user = User::find($id);
        $user->email = $validated['email'];
        $user->save();

        return response()->json([
            'message' => 'Email updated successfully'
        ], 200);
    }

    public function changePassword(Request $request) {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], 400);
        }

        // Update the user's password
        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return response()->json([
            'message' => 'Password updated successfully'
        ], 200);
    }


    public function getUser(Request $request) {
        $user = $request->user();
        return response()->json([
            'user' => $user
        ], 200);
    }

    public function getAllUser() {
        $users = User::all();
        return response()->json([
            'users' => $users
        ], 200);
    }
}
