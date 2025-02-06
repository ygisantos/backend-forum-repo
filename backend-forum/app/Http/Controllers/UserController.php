<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Liked;

class UserController extends Controller
{
    public function changeEmail(Request $request) {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            $validated = $request->validate(['email' => ['required', 'email', 'unique:users,email']]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $user->email = $validated['email'];
        $user->save();

        return response()->json(['message' => 'Email updated successfully'], 200);
    }

    public function changePassword(Request $request) {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            $validated = $request->validate([
                'current_password' => ['required', 'string'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 400);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return response()->json(['message' => 'Password updated successfully'], 200);
    }

    public function changeName(Request $request) {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            $validated = $request->validate(['name' => ['required', 'string']]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 400);
        }

        $user->name = $validated['name'];
        $user->save();
        return response()->json(['message' => 'Name updated successfully'], 200);

    }

    public function uploadProfilePicture(Request $request) {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            $imagePath = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = "/storage/$imagePath";
            $user->save();
        } catch(Exception $e) {
            return response()->json(["message"=> $e->getMessage()], 400);
        }

        return response()->json(['message' => 'Profile picture updated successfully'], 200);
    }

    public function getLikedData(Request $request) {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $likedData = Liked::where('user_id', $user->id)->with('forum')->get();
        return response()->json(['liked_data' => $likedData], 200);
    }

    public function getAllUser() {
        return response()->json(User::all(), 200);
    }

    public function getUser(Request $request) {
        return response()->json(['user' => $request->user()], 200);
    }

    public function register(Request $request) {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Liked::create(['user_id' => $user->id]);

        return response()->json(['message' => 'User registered successfully'], 201);
    }

}
