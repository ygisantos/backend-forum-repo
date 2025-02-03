<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function changeEmail(Request $request) {
        try { $validated = $request->validate(['email' => ['required', 'email', 'unique:users,email']]); }
        catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        $user->email = $validated['email'];
        $user->save();

        return response()->json(['message' => 'Email updated successfully'], 200);
    }


    public function changePassword(Request $request) {
        try {
            $validated = $request->validate([
                'current_password' => ['required', 'string'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $user = Auth::user();

        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);
        if (!Hash::check($validated['current_password'], $user->password))
            return response()->json(['message' => 'Current password is incorrect'], 400);


        $user->password = Hash::make($validated['password']);
        $user->save();

        return response()->json(['message' => 'Password updated successfully'], 200);
    }

    public function changeName(Request $request) {
        try { $validated = $request->validate(['name'=> ['required', 'string']]); }
        catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors'=> $e->errors()], 400);
        }

        $user = Auth::user();
        if (!$user) return response()->json(['message'=> 'Unauthorized'], 400);

        $user->name = $validated['name'];
        $user->save();
        return response()->json(['message'=> 'Name updated successfully'], 200);

    }

    public function getAllUser() {
        return response()->json(User::all(), 200);
    }

    public function getUser(Request $request) {
        return response()->json(['user' => $request->user()], 200);
    }

}
