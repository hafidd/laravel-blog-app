@extends('layout')
@section('content')
    <!-- content -->
    <div class="py-5 block xl:flex w-full">

        <!-- kiri -->
        <div class="px-1 md:px-4 lg:border-r-2 w-full">
            <div class="mb-1">
                <a href="/"> <button
                        class="{{ (!$following ? 'font-bold' : '') . ' text-lg mr-1 px-1' }}">Latest</button> </a>
                <a href="/?following=true">
                    <button class="{{ ($following ? 'font-bold' : '') . ' text-lg px-1' }}">Following</button>
                </a>
            </div>

            <div class="p-4 2xl:flex 2xl:flex-wrap">

                <!-- item -->
                @if (count($posts) > 0)
                    @foreach ($posts as $post)
                        <div
                            class="flex flex-col md:flex-row mb-4 md:h-40 2xl:h-44 w-full 2xl:w-1/2 transition ease-in-out delay-50 hover:translate-x-1 duration-200 shadow-sm">
                            <a href="/post/{{ $post->id }}" class="relative self-center shrink-0">
                                <img src="{{ $post->picture ?? '/gambar/defaultL.JPG' }}" alt="test"
                                    class="object-cover bg-gray-200 min-h-[9rem] md:h-40 2xl:h-44 w-screen md:w-64 2xl:w-72 mb-2 md:mb-0">
                                <div class="flex flex-wrap-reverse absolute bottom-0 w-full px-1 pb-3 md:pb-0">
                                    <!-- tags -->
                                    @foreach ($post->tags as $tag)
                                        <button
                                            class="bg-black bg-opacity-60 text-sm text-white shadow-md hover:bg-white hover:text-black font-semibold rounded-md px-1 mr-1 mb-1">
                                            {{ $tag->name }}
                                        </button>
                                    @endforeach
                                </div>
                            </a>
                            <div class="w-full px-1 md:px-4 py-1 flex flex-col-reverse md:flex-col h-full overflow-hidden">
                                <a href="/post/{{ $post->id }}" class="">
                                    <h4
                                        class="group font-bold text-lg mb-1 w-full overflow-hidden overflow-ellipsis break-all md:whitespace-nowrap">
                                        {{ $post->title }}
                                        <span
                                            class="
                                                hidden group-hover:block bg-white p-1 rounded shadow absolute text-center -mt-16 ml-4 z-50
                                                ">
                                            {{ $post->title }}
                                        </span>
                                    </h4>

                                    <p class="mx-auto w-full overflow-hidden md:h-18 2xl:h-24 break-all">
                                        {{ $post->subtitle }}
                                    </p>
                                </a>
                                <div
                                    class="self-center md:self-end text-xs md:text-sm font-semibold flex items-center mt-auto">
                                    <a href="/user/{{ $post->user->username }}" class="flex items-center">
                                        <img src="{{ $post->user->picture ?? '/gambar/pp_default.png' }}"
                                            alt="pp" class="h-7 w-7 object-cover border rounded-full mr-2">
                                        {{ $post->user->name }}
                                    </a>
                                    &nbsp;- {{ date('d M Y - H:i', strtotime($post->published)) }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="p-4 w-fit md:w-full mx-auto">
                        {{ $posts->withQueryString()->links() }}
                    </div>
                @endif

            </div>
        </div>

        <!-- Kanan -->
        <div class="mx-auto w-full xl:w-72 p-2 h-max shrink-0">
            <div class="tags flex flex-wrap-reverse items-center border-b-2 py-2 mb-4">
                @foreach ($tags as $tag)
                    <a href="/?tags[]={{ $tag->name }}" class="">
                        <button class="p-1 px-2 border-2 rounded-3xl mb-2 mr-1">{{ $tag->name }}</button>
                    </a>
                @endforeach
            </div>
            <div class="flex flex-col justify-center p-2 px-4">

                <!-- user -->
                @foreach ($users as $user)
                    <div class="flex h-20 mb-2">
                        <a href="/user/{{ $user->username }}" class="h-20 w-20 shrink-0">
                            <img src="{{ $user->picture ?? '/gambar/pp_default.png' }}"
                                class="object-cover rounded-full h-20 w-20">
                        </a>
                        <div class="p-1 flex flex-col justify-between w-full">
                            <a href="">
                                <p class="font-semibold">{{ $user->name }}</p>
                            </a>
                            @auth
                                <a href="" class="self-end">
                                    <form action="/user/{{ $user->id }}/follow" method="post">
                                        @csrf
                                        <button class="p-1 px-2 border text-sm rounded-2xl">Follow</button>
                                    </form>
                                </a>
                            @endauth
                        </div>
                    </div>
                @endforeach


            </div>
        </div>

    </div>


@endsection
