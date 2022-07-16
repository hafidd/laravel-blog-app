@extends('layout') @section('content')
    <div class="py-5 flex w-full">
        <!-- kiri -->
        <div class="px-1 md:px-4 lg:border-r-2 w-full">
            <div class="flex mb-2 p-2 border-b-2 justify-start">
                <a href="/post/{{ $post->id }}/edit">
                    <button class="px-2 py-1 border rounded-md font-bold mr-2">Edit</button>
                </a>
                @if ($post->published == null)
                    <div class="flex" x-data="{ show: false }">
                        <button x-on:click="show=!show"
                            class="px-2 py-1 border rounded-md bg-blue-400 text-white font-bold mr-2">Publish</button>
                        <div x-show="show" x-transition.duration.400ms>
                            <form action="/post/{{ $post->id }}/publish" method="post">
                                <span class="text-blue-400 mr-1">Publish this post? </span>
                                @csrf
                                @method('PATCH')
                                <button class="bg-blue-400 text-white px-2 py-1 rounded-md mr-1">Y</button>
                                <button type="button" x-on:click="show=false" class="border px-1 rounded-md">N</button>
                            </form>
                        </div>
                    </div>
                @endif
                <div class="ml-auto flex" x-data="{ show: false }">
                    <button x-on:click="show=!show"
                        class="px-2 py-1 border rounded-md bg-red-500 text-white font-bold mr-2">Delete</button>
                    <div x-show="show" x-transition.duration.400ms>
                        <form action="/post/{{ $post->id }}" method="post">
                            <span class="text-red-400 mr-1">Delete this post? </span>
                            @csrf
                            @method('DELETE')
                            <button class="bg-red-400 text-white px-2 py-1 rounded-md mr-1">Y</button>
                            <button type="button" x-on:click="show=false" class="border px-1 rounded-md">N</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="px-4 pb-6 flex flex-col w-full border-b-2" x-data="likeData">

                <!-- title, tags -->
                <div class="text-center border-b w-fit pb-2 self-center">
                    <h3 class="text-2xl md:text-3xl mb-3 font-bold">{{ $post->title }}</h3>
                    @foreach ($post->tags as $tag)
                        <span class="mr-1 px-1 border rounded-md">{{ $tag->name }}</span>
                    @endforeach
                </div>


                {{-- picture --}}
                <img src="{{ $post->picture ?? '/storage/gambar/defaultL.JPG' }}" alt="image"
                    class="mb-4 object-cover self-center w-11/12 lg:w-9/12 2xl:w-8/12 min-h-[10rem] bg-gray-200">

                <!-- content -->
                <div class="mb-4 md:text-lg ck-content">
                    {!! $post->content !!}
                </div>

            </div>
        </div>
    </div>
@endsection
