<?php

namespace App\Http\Controllers;

use App\Models\Artikel;
use Illuminate\Http\Request;

class ArtikelController extends Controller
{
    public function index()
    {
        return view('artikel.index', ['artikels' => Artikel::latest()->paginate(6)]);
    }

    public function show(Artikel $artikel)
    {
        return view(
            'pegawai.index',
            ["pegawai" => $artikel]
        );
    }

    public function create()
    {
        return view(
            'artikel.create',
            []
        );
    }

    public function store()
    {
        $fields = request()->validate([
            "judul" => "required|min:3",
            "tags" => "nullable|string",
            "konten" => "required|min:20",
        ]);

        $fields["user_id"] = auth()->user()->id;

        if (request()->hasFile('gambar')) {
            $fields["gambar"] = request()->file('gambar')->store('artikel', 'public');
        }

        Artikel::create($fields);

        return redirect('/artikel')->with('message', 'Sukses');
    }

    public function edit(Artikel $artikel)
    {
        if ($artikel->user_id != auth()->user()->id) {
            abort(403, "Unauthorized");
        }

        return view(
            'artikel.edit',
            ["artikel" => $artikel]
        );
    }

    public function update(Artikel $artikel)
    {
        $fields = request()->validate([
            "judul" => "required|min:3",
            "tags" => "nullable|string",
            "konten" => "required|min:20",
        ]);

        if (request()->hasFile('gambar')) {
            $fields["gambar"] = request()->file('gambar')->store('users', 'public');
        }

        $artikel->update($fields);

        return redirect('/artikel')->with('message', 'Sukses');
    }

    public function destroy(Artikel $artikel)
    {
        $artikel->delete();

        return redirect('/artikel')->with('message', 'Sukses dihapus');
    }
}
