<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Artikel extends Model
{
    use HasFactory;
    protected $table = "artikel";
    protected $fillable = ['judul', 'tags', 'gambar', 'user_id', 'konten'];

    protected function user()
    {
        return $this->belongsTo(User::class);
    }
}
