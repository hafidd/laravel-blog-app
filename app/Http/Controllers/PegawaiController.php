<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function index()
    {
        return view(
            'pegawai.index',
            [
                "pegawai_r" => Pegawai::latest()->filter(request()->query())->paginate(6),
                "query" => request()->query()
            ]
        );
    }

    public function show(Pegawai $pegawai)
    {
        return view(
            'pegawai.index',
            ["pegawai" => $pegawai]
        );
    }

    public function create()
    {
        return view(
            'pegawai.create',
            []
        );
    }

    public function store()
    {
        $fields = request()->validate([
            "nama" => "required",
            "alamat" => "required",
            "hobby" => "nullable|string",
        ]);

        if (request()->hasFile('gambar')) {
            $fields["gambar"] = request()->file('gambar')->store('users', 'public');
        }

        Pegawai::create($fields);

        return redirect('/pegawai')->with('message', 'Sukses');
    }

    public function edit(Pegawai $pegawai)
    {
        return view(
            'pegawai.edit',
            ["pegawai" => $pegawai]
        );
    }

    public function update(Pegawai $pegawai)
    {
        $fields = request()->validate([
            "nama" => "required",
            "alamat" => "required",
            "hobby" => "nullable|string",
        ]);

        if (request()->hasFile('gambar')) {
            $fields["gambar"] = request()->file('gambar')->store('users', 'public');
        }

        $pegawai->update($fields);

        return redirect('/pegawai')->with('message', 'Sukses');
    }

    public function delete(Pegawai $pegawai)
    {
        return view(
            'pegawai.delete',
            ["pegawai" => $pegawai]
        );
    }

    public function destroy(Pegawai $pegawai)
    {
        $pegawai->delete();

        return redirect('/pegawai')->with('message', 'Sukses dihapus');
    }
}
