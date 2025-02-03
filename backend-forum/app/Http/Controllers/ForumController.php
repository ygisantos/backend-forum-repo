<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Forum;
use App\Models\User;

class ForumController extends Controller
{
    public function getAllForum() {
        if(Forum::all()->count() == 0) {
            return response()->json(['message' => 'No forum found'], 404);
        }
        $forums = Forum::with('user')->get();
        return response()->json($forums, 200);
    }

    public function newForum(Request $request) {
        try {
            $validated = $request->validate([
                'title' => ['required'],
                'message' => ['required'],
                'userId' => ['required'],
                'image' => ['nullable']
            ]);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('forum_images', 'public');
        }

        $forum = Forum::create([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'userId' => $validated['userId'],
            'image' => $imagePath ? "/storage/$imagePath" : null
        ]);

        return response()->json(['message' => $forum], 200);
    }


    public function deleteForum(Request $request) {
        try {
            $forum = Forum::findOrFail($request->forum_id);
            $forum->delete();
            return response()->json(['message'=> 'successfully deleted'],200);
        } catch (\Exception $e) {
            return response()->json(['errors'=> $e->getMessage()], 204);
        }
    }
}
