<?php

namespace App\Models;

use App\Models\Post;
use App\Models\User;
use App\Models\CommentLike;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'post_id', 'comment_id', 'content'];

    // relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function post()
    {
        $this->belongsTo(Post::class);
    }
    public function likes()
    {
        return $this->hasMany(CommentLike::class);
    }

    public function getLikeByUser($userId)
    {
        return $this->likes()->where('user_id', $userId)->first();
    }
}
