<?php

namespace App\Models;

use App\Models\Post;
use App\Models\PostLike;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'picture',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // relations
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    public function followers()
    {
        return $this->hasMany(Follow::class);
    }
    public function follows()
    {
        return $this->hasMany(Follow::class, 'follower_id');
    }
    public function post_likes()
    {
        return $this->hasMany(PostLike::class);
    }

    // check is user a following user b
    public function isFollowing($userId, $followerId)
    {
        return $this->follows()->where([
            'user_id' => $userId,
            'follower_id' => $followerId
        ])->first() ? true : false;
    }

    // get user by username with follows and followers count
    public function getUserWithFollowCount($username)
    {
        return $this->where('username', $username)
            ->withCount(['followers', 'follows'])
            ->firstOrFail();
    }

    // get user posts
    public function getPosts($pagination = 10, $includeUnpublished = false)
    {
        $posts = $this->posts();

        if (!$includeUnpublished) $posts = $posts->published();

        $posts = $posts
            ->excludeCols(['content'])
            ->withCount('likes')
            ->with(['tags'])
            ->orderByRaw("CASE WHEN published IS NULL THEN 1 ELSE 0 END DESC")
            ->orderBy('published', 'desc')
            ->paginate($pagination);

        return $posts;
    }

    // get n latest published user posts
    public function getPublishedPost($limit = 3, $excludeIds = [])
    {
        return $this->posts()
            ->published()
            ->whereNotIn('id', $excludeIds)
            ->latest()->take($limit)->get();
    }

    // get n popular users (by post views)
    public function getPopularUsers($limit = 5)
    {
        $users = $this->select('users.*', DB::raw('count(users.id) as view_count'))
            ->join('posts', 'users.id', 'posts.user_id')
            ->join('post_views', 'post_views.post_id', 'posts.id')
            ->groupBy('users.id')
            ->orderBy('view_count', 'desc')
            ->take($limit);

        if (auth()->user()) {
            // exclude followed users
            $users = $users
                ->leftJoin('follows', function ($join) {
                    $join->on('users.id', '=', 'follows.user_id')
                        ->where('follower_id', auth()->user()->id);
                })
                ->whereNull('follows.id')
                ->where('users.id', '<>', auth()->user()->id);
        }
        return $users = $users->get();
    }
}
