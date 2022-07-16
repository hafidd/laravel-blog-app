<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\User;
use App\Models\Comment;
use App\Models\PostLike;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'subtitle', 'picture', 'published', 'content'];
    protected $columns = ["id", "user_id", "title", "subtitle", "picture", "content", "published", "created_at", "updated_at"];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return  $this->hasMany(Comment::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }

    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }

    public function views()
    {
        return $this->hasMany(PostView::class);
    }

    public function scopeExcludeCols($query, $value = [])
    {
        return $query->select(array_diff($this->columns, (array) $value));
    }
}
