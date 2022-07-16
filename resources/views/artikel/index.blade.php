@extends('layout')
@section('content')
<div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg dark:text-white">
    <div class="p-2 mb-1 text-center">
        <h2 class="">Artikel</h2>
    </div>
    @foreach($artikels as $artikel)
    <div class="p-3 w-full lg:w-1/2 xl:w-1/3 flex flex-col justify-center">
        <div class="border rounded-md w-full p-2">
            <div class="flex mb-1 justify-between">
                <a href="/artikel/id">
                    <h3 class="mr-1">{{$artikel->judul}}</h3>
                </a>
                @auth
                @if($artikel->user_id == auth()->user()->id)
                <div class="flex">
                    <a href="/artikel/{{$artikel->id}}/edit"><button class="bg-yellow-700 text-xs px-1 border rounded-sm mr-1">Edit</button></a>
                    <form action="/artikel/{{$artikel->id}}" method="post">
                        @method('DELETE')
                        @csrf
                        <button onclick="return confirm('Hapus artikel ini?')" class="bg-red-600 text-xs px-1 border rounded-sm">Delete</button>
                    </form>
                </div>
                @endif
                @endauth
            </div>
            <div class="mb-1 px-2 text-xs">
                <div class="mb-1">
                    @foreach(explode(",", $artikel->tags) as $tag)
                    <a href="/artikel?tag={{$tag}}" class="">
                        <button class="px-1 mr-1 border rounded-lg">{{$tag}}</button>
                    </a>
                    @endforeach
                </div>
                <div class="flex p-1 border rounded-md">
                    <div class="w-24 h-24 shrink-0">
                        <img src="/storage/gambar/pp_default.png" alt="">
                    </div>
                    <div class=" ">
                        <p class="max-h-24 p-2 break-all overflow-hidden">{{$artikel->konten}}</p>
                    </div>
                </div>
                <div class="text-xs px-1 text-right">
                    <p>{{$artikel->created_at}} - <a href="/artikel/author/1">{{$artikel->user->name}}</a></p>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection