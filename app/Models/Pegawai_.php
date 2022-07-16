<?php

namespace App\Models;

class Pegawai
{
    private static $pegawai = [
        ["id" => 1, "nama" => "Paijo", "alamat" => "kolong abc"],
        ["id" => 2, "nama" => "Marinah", "alamat" => "rsasfa safasfsa"],
        ["id" => 3, "nama" => "Sentolop", "alamat" => "sangsdgoa"],
        ["id" => 4, "nama" => "Kungkingkang", "alamat" => "saghd"],
    ];

    public static function all()
    {
        return self::$pegawai;
    }

    public static function getById($id)
    {
        foreach (self::$pegawai as $p) {
            if ($p['id'] === intval($id))
                return $p;
        }
        return null;
    }
}
