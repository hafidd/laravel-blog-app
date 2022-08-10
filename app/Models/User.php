<?php

namespace App\Models;

use App\Models\Post;
use App\Models\PostLike;
use Laravel\Sanctum\HasApiTokens;
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
    public function getPosts($pagination = 10)
    {
        return $this->posts()
            ->excludeCols(['content'])
            ->withCount('likes')
            ->with(['tags'])
            ->orderByRaw("CASE WHEN published IS NULL THEN 1 ELSE 0 END DESC")
            ->orderBy('published', 'desc')
            ->paginate($pagination);
    }
}
