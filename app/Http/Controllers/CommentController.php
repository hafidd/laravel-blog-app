<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Post;
use Illuminate\Http\Request;

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
    public function like(Comment $comment)
    {
        $like = CommentLike::where('user_id', auth()->user()->id)
            ->where('comment_id', $comment->id)->first();

        if ($like) {
            $like->delete();
            return response()->json([
                'liked' => false,
                'likeCount' => CommentLike::where('comment_id', $comment->id)->count()
            ]);
        }
        CommentLike::create(['comment_id' => $comment->id, 'user_id' => auth()->user()->id]);
        return response()->json([
            'liked' => true,
            'likeCount' => CommentLike::where('comment_id', $comment->id)->count()
        ]);
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== auth()->user()->id) {
            abort(403, "Unauthorized");
        }         
        $comment->delete();
        return back();
    }
}
