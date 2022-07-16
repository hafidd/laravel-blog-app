<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;
    protected $table = 'pegawai';
    protected $fillable = ["nama", "alamat", "hobby", "gambar"];

    public function scopeFilter($query, array $filters)
    {
        if ($filters['search'] ?? false) {
            $query->where('nama', 'ilike', '%' . $filters['search'] . '%');
            $query->orWhere('alamat', 'ilike', '%' . $filters['search'] . '%');
            $query->orWhere('hobby', 'ilike', '%' . $filters['search'] . '%');
        }
    }
}
