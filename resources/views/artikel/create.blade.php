@extends('layout')

@section('content')

<div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
    <div class="p-2 text-white flex justify-between px-3">
        <h3>Create Artikel</h3>
        <span>
            <a href="/pegawai">
                << Back</a>
        </span>
    </div>
    <div class="p-2 px-4 md:px-12 lg:pr-64">
        <form method="post" action="/artikel" enctype="multipart/form-data">
            @csrf
            <div class="">
                <div class="mb-1 flex justify-between">
                    <span class="w-28 text-slate-100">Judul </span>
                    <div class="w-full">
                        <input class="w-full" name="judul" type="text" value="{{old('judul')}}">
                        @error('judul')
                        <p class="text-red-500 text-sm mt-1">{{$message}}</p>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="">
                <div class="mb-1 flex justify-between">
                    <span class="w-28 text-slate-100">Tags </span>
                    <div class="w-full">
                        <input class="w-full" name="tags" type="text" value="{{old('tags')}}">
                        @error('tags')
                        <p class="text-red-500 text-sm mt-1">{{$message}}</p>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="">
                <div class="mb-1 flex justify-between">
                    <span class="w-28 text-slate-100">Gambar </span>
                    <div class="w-full">
                        <input class="w-full" name="gambar" type="file" value="{{old('gambar')}}">
                        @error('gambar')
                        <p class="text-red-500 text-sm mt-1">{{$message}}</p>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="">
                <div class="mb-1 flex justify-between">
                    <span class="w-28 text-slate-100">Konten </span>
                    <div class="w-full">
                        <textarea name="konten" rows="7" class="w-full p-1">{{old('konten')}}</textarea>
                        @error('konten')
                        <p class="text-red-500 text-sm mt-1">{{$message}}</p>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="mb-1 flex justify-center mt-3">
                <button class="border px-1 text-slate-100 rounded-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection