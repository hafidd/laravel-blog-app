@extends('layout')

@section('content')

<div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
    <div class="p-2 text-white flex justify-between px-3">
        <h3>Delete Pegawai</h3>
        <span>
            <a href="/pegawai">
                << Kembali</a>
        </span>
    </div>
    <div class="p-2 px-4 md:px-12 lg:pr-64">
        <form method="post" action="/pegawai/{{$pegawai->id}}" enctype="multipart/form-data">
            @csrf
            @method('DELETE')
            <div class="">
                <div class="mb-1 flex justify-between">
                    <span class="w-28 text-slate-100">Nama </span>
                    <div class="w-full">
                        <input class="w-full" name="nama" type="text" value="{{$pegawai->nama}}" readonly>
                        @error('nama')
                        <p class="text-red-500 text-sm mt-1">{{$message}}</p>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="">
                <div class="mb-1 flex justify-between">
                    <span class="w-28 text-slate-100">Alamat </span>
                    <div class="w-full">
                        <input class="w-full" name="alamat" type="text" value="{{$pegawai->alamat}}" readonly>
                        @error('alamat')
                        <p class="text-red-500 text-sm mt-1">{{$message}}</p>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="">
                <div class="mb-1 flex justify-between">
                    <span class="w-28 text-slate-100">Hobby </span>
                    <div class="w-full">
                        <input class="w-full" name="hobby" type="text" value="{{$pegawai->hobby}}" readonly>
                    </div>
                </div>
            </div>
            <div class="">
                <div class="mb-1 flex justify-between">
                    <span class="w-28 text-slate-100">Gambar </span>
                    <div class="w-full">
                        @if($pegawai->gambar)
                        <div class="mb-1">
                            <img src="/storage/{{$pegawai->gambar}}" alt="">
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mb-1 flex justify-center mt-3">
                <button onclick="return confirm('Hapus?')" class="border px-1 text-slate-100 rounded-sm">Hapus</button>
            </div>
        </form>
    </div>
</div>

@endsection