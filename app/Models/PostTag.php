<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostTag extends Model
{
    use HasFactory;
    protected $table = "post_tag";
    protected $fillable = ['post_id', 'tag_id'];
    public $timestamps = false;

    // get n most used tags
    public function getPopularTags($limit = 10)
    {
        return $this->select('tags.name', DB::raw('count(tags.id) as tag_count'))
            ->join('post_views', 'post_tag.post_id', 'post_views.post_id')
            ->join('tags', 'tags.id', 'post_tag.tag_id')
            ->groupBy('tags.id')
            ->orderBy('tag_count', 'desc')
            ->take($limit)->get();
    }
}
