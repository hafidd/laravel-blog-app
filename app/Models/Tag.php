<?php

namespace App\Models;

use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;
    protected $fillable = ['name'];
    public $timestamps = false;

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tag');
    }

    public function getTagNamesByKeyword($keyword)
    {
        return $this->where(DB::raw('lower(name)'), 'like', ('%' . strtolower($keyword) . '%'))
            ->where('name', '<>', $keyword)
            ->limit(3)->pluck("name");
    }
}
