<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Post;

class CommentController extends Controller
{
    public function store(Post $post)
    {
        $fields = request()->validate([
            'content' => "required|min:3|max:250"
        ]);
        
        $fields["user_id"] = auth()->user()->id;
        $fields["post_id"] = $post->id;
        $fields["comment_id"] = null;

        Comment::create($fields);
        return back();
    }

    // like/unlike comment
    public function like(Comment $comment, CommentLike $commentLike)
    {
        $userId = auth()->user()->id;
        $like = $comment->getLikeByUser($userId);

        if ($like) $like->delete();
        else $commentLike->create(['comment_id' => $comment->id, 'user_id' => auth()->user()->id]);

        return response()->json([
            'liked' => $like ? false : true,
            'likeCount' => $comment->likes->count()
        ]);
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== auth()->user()->id) abort(403, "Unauthorized");

        $comment->delete();
        return back();
    }
}
