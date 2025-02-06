<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Forum;
use App\Models\User;
use App\Models\Liked;

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

    public function addLike(Request $request) {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $forum = Forum::findOrFail($request->id);
        $liked = Liked::firstOrCreate(['user_id' => $user->id, 'forum_id' => $forum->id]);

        if ($liked->wasRecentlyCreated) {
            $forum->likes += 1;
            $forum->save();
            return response()->json(['message' => 'Like added successfully'], 200);
        } else {
            return response()->json(['message' => 'You have already liked this forum'], 400);
        }
    }

    public function removeLike(Request $request) {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $forum = Forum::findOrFail($request->id);
        $liked = Liked::where(['user_id' => $user->id, 'forum_id' => $forum->id])->first();

        if ($liked) {
            $liked->delete();
            $forum->likes -= 1;
            $forum->save();
            return response()->json(['message' => 'Like removed successfully'], 200);
        } else {
            return response()->json(['message' => 'You have not liked this forum'], 400);
        }
    }
}
