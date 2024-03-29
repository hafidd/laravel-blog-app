<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\User;
use App\Models\Comment;
use App\Models\PostLike;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'subtitle', 'picture', 'published', 'content'];
    protected $columns = ["id", "user_id", "title", "subtitle", "picture", "content", "published", "created_at", "updated_at"];

    // protected $appends = ['isLiked'];
    // public function getIsLikedAttribute()
    // {
    //     //return false;
    //     if (!auth()->user()) return false;
    //     return $this->likes->where('user_id', auth()->user()?->id ?? 0)->first() ? true : false;
    // }// n+1

    // relations
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
    public function liked()
    {
        return $this->hasOne(PostLike::class)->where('user_id', auth()->user()?->id ?? 0);
    }

    // scopes
    public function scopeExcludeCols($query, $value = []) // if needed call this first
    {
        return $query->select(array_diff($this->columns, (array) $value));
    }
    public function scopePublished($query)
    {
        $query->where('published', '<>', null);
    }
    public function scopeSearch($query, $search)
    {
        $query->where(DB::raw('lower(title)'), 'like', '%' . (request()->query()['search'] ?? '') . '%');
    }
    public function scopeTags($query, $tags = [])
    {
        $query->whereHas('tags', function ($q) use ($tags) {
            $q->whereIn(DB::raw('lower(name)'), $tags);
        });
    }
    public function scopeFollowing($query)
    {
        $query->select("posts.id", "posts.user_id", "title", "subtitle", "picture", "published", "created_at", "updated_at", 'follows.id as fid')
            ->join('follows', function ($join) {
                $join->on('posts.user_id', '=', 'follows.user_id')
                    ->select('follows.id as fid')
                    ->where('follows.follower_id', '=', auth()->user()->id);
            });
    }

    // latest published post by search, and tags
    public function getHomepagePosts($paginate = 6)
    {
        // 6 posts
        $posts = $this
            ->excludeCols(['content'])
            ->with(['tags', 'user', 'liked'])
            ->withCount(['likes'])
            ->published();

        $posts = request()->following == true ?  $posts->following() : $posts;
        $posts = !empty(request()->search) ? $posts->search(request()->search) : $posts;
        $posts = !empty(request()->tags) ?  $posts->tags(request()->tags) : $posts;

        $posts = $posts->orderBy('published', 'desc')
            ->orderBy('id', 'desc')
            ->simplePaginate($paginate);

        return $posts;
    }

    public function getLikeByUser($userId)
    {
        return $this->likes()->where('user_id', $userId)->first();
    }

    public function getDetailPost($id)
    {
        return $this->with(['tags', 'liked'])
            ->withCount(['likes', 'views'])
            ->published()
            ->findOrFail($id);
    }
}
