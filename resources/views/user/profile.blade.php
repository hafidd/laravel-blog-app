@extends('layout')
@section('content')
    <div class="py-5 w-full">

        {{--  --}}
        <div class="">
            @error('name')
                <div x-data="{ show: true }">
                    <p x-show="show" class="text-red-500"> {{ $message }} <button x-on:click="show=false"
                            class="text-black px-1 border rounded-md">x</button> </p>
                </div>
            @enderror
            @error('picture')
                <div x-data="{ show: true }">
                    <p x-show="show" class="text-red-500"> {{ $message }} <button x-on:click="show=false"
                            class="text-black px-1 border rounded-md">x</button> </p>
                </div>
            @enderror
        </div>

        <div class="flex flex-wrap p-1 md:p-4 px-1 md:px-6 items-center justify-between mb-1">
            <div class="flex items-center">
                <div class="relative shrink-0" x-data="{ upload: false }">
                    <img src="{{ $user->picture ?? '/gambar/pp_default.png' }}" alt="user"
                        class="h-16 w-16 md:h-24 md:w-24 rounded-full">
                    @if (auth()->user() && auth()->user()->id == $user->id)
                        <button class="absolute bottom-0 rounded-md px-1 bg-black bg-opacity-30" x-on:click="upload=true">
                            ✏️
                        </button>
                        <div x-show="upload"
                            class="fixed w-screen h-screen top-0 left-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                            <form action="/user/{{ $user->id }}/picture"
                                class="w-11/12 md:w-1/2 2xl:w-1/3 border rounded-lg bg-white h-40 p-4"
                                enctype="multipart/form-data" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="flex flex-col w-full h-full justify-between">
                                    <input type="file" name="picture">
                                    <p class="text-blue-500">jpg, png max 200Kb</p>
                                    <div class="text-lg font-semibold self-end">
                                        <button type="button" x-on:click="upload=false"
                                            class=" px-1 border rounded-lg mr-1">x</button>
                                        <button class=" px-1 border rounded-lg">Upload</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
                <div class="ml-2">
                    <h1 class="text-xl md:text-2xl font-bold">
                        @if (!auth()->user() || auth()->user()->id != $user->id)
                            {{ $user->name }}
                        @else
                            <div x-data="{
                                edit: false,
                                saveBtn: false,
                                name: '{{ $user->name }}',
                                setForm() {
                                    this.edit = true;
                                    setTimeout(() => document.getElementById('editName').focus(), 10)
                                },
                                input() {
                                    if (this.name.trim() != '' && this.name.trim() != '{{ $user->name }}') {
                                        this.saveBtn = true;
                                        return
                                    }
                                    this.saveBtn = false;
                                },
                            }" class="flex">
                                <button class="px-1 border rounded-md text-sm mr-2" x-on:click="setForm"
                                    x-show="!edit">✏️</button>
                                <span x-show="!edit">{{ $user->name }}</span>
                                <form action="/user/{{ $user->id }}/name" x-show='edit' method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button class="px-1 border rounded-md text-sm mr-2" x-on:click="setForm"
                                        x-show="saveBtn">save</button>
                                    <input id="editName" type="text" name="name" x-model="name" x-on:input="input"
                                        class="focus:outline-none">
                                </form>
                            </div>
                        @endif
                    </h1>
                    <p class="md:text-lg">
                        <span class="font-semibold">{{ $user->username }}</span>
                        <br>
                    </p>
                    <div class="flex">

                        {{-- followers --}}
                        <div class="mr-2" x-data="{
                            followersModal: false,
                            followers: [],
                            async getData() {
                                if (this.followersModal) {
                                    try {
                                        const res = await fetch('/user/{{ $user->id }}/followers')
                                        const data = await res.json()
                                        //console.log(data)
                                        this.followers = data
                                    } catch (e) { console.log(e) }
                                }
                            }
                        }">
                            <button x-on:click="followersModal=!followersModal; getData()"
                                class="text-sm md:text-base font-light">{{ $user->followers_count }} Followers
                            </button>
                            <div class="fixed border p-4 z-40 bg-white" x-show="followersModal">
                                <p class="text-lg font-bold">Followers (<span x-text="followers.length"></span>)
                                    <button x-on:click="followersModal=!followersModal"
                                        class="rounded-md border px-1">x</button>
                                </p>
                                <template x-for="data in followers">
                                    <a :href="'/user/' + data.follower.username">
                                        <div class="flex items-center mt-1">
                                            <img :src="(data.follower.picture ? data.follower.picture :
                                                '/gambar/pp_default.png')"
                                                alt="" class="rounded-full w-8 h-8 object-cover mr-2">
                                            <span x-text="data.follower.name"></span>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </div>

                        {{-- following --}}
                        <div class="mr-2" x-data="{
                            followingModal: false,
                            follows: [],
                            async getData() {
                                if (this.followingModal) {
                                    try {
                                        const res = await fetch('/user/{{ $user->id }}/follows')
                                        const data = await res.json()
                                        //console.log(data)
                                        this.follows = data
                                    } catch (e) { console.log(e) }
                                }
                            }
                        }">
                            <button x-on:click="followingModal=!followingModal; getData()"
                                class="text-sm md:text-base font-light">{{ $user->follows_count }} Following
                            </button>
                            <div class="fixed border p-4 z-40 bg-white" x-show="followingModal">
                                <p class="text-lg font-bold">Following (<span x-text="follows.length"></span>)
                                    <button x-on:click="followingModal=!followingModal"
                                        class="rounded-md border px-1">x</button>
                                </p>
                                <template x-for="data in follows">
                                    <div class="flex items-center mt-1">
                                        <a :href="'/user/' + data.user.username" class="flex items-center">
                                            <img :src="(data.user.picture ? data.user.picture :
                                                '/gambar/pp_default.png')"
                                                alt="" class="rounded-full w-8 h-8 object-cover mr-2">
                                            <span x-text="data.user.name"></span>
                                        </a>
                                        @if (auth()->user() && auth()->user()->id == $user->id)
                                            <div class="ml-1">
                                                <form :action="'/user/' + data.user.id + '/follow'" method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="px-1 rounded-md text-sm border">Unfollow</button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </template>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
            @if (auth()->user() && auth()->user()->id != $user->id)
                <div class="self-start">
                    <form action="/user/{{ $user->id }}/follow" method="post">
                        @csrf
                        @if ($following)
                            @method('DELETE')
                            <button class="p-1 px-2 md:p-2 md:px-3 rounded-lg text-lg font-bold border">Following</button>
                        @else
                            <button class="p-1 px-2 md:p-2 md:px-3 rounded-lg text-lg font-bold border">Follow</button>
                        @endif
                    </form>
                </div>
            @endif
        </div>
        <div class="border-t"></div>
        <div class="p-4 flex flex-wrap">
            <div class="w-full text-center">
                <h3 class="text-xl font-bold mb-3">Posts ({{ $posts->total() }})</h3>
            </div>
            @foreach ($posts as $post)
                <div
                    class="flex flex-col md:flex-row mb-4 md:h-40 2xl:h-44 w-full md:w-1/2 2xl:w-1/3 transition ease-in-out delay-50 hover:translate-x-1 duration-200 shadow-sm">
                    <a href="/post/{{ $post->id }}{{ $post->published ? '' : '/settings' }}"
                        class="relative self-center shrink-0">
                        <img src="{{ $post->picture ?? '/gambar/defaultL.JPG' }}" alt="test"
                            class="object-cover min-h-[9rem] md:h-40 2xl:h-44 w-screen md:w-64 2xl:w-72 mb-2 md:mb-0">
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
                        <a href="/post/{{ $post->id }}{{ $post->published ? '' : '/settings' }}" class="">
                            <h4
                                class="{{ $post->published ? '' : 'text-yellow-500' }} group font-bold text-lg mb-1 w-full overflow-hidden overflow-ellipsis break-all md:whitespace-nowrap">
                                {{ (!$post->published ? '(Draft) ' : '') . $post->title }}
                                <span
                                    class="
                                    hidden group-hover:block bg-white p-1 rounded shadow absolute text-center -mt-16 ml-4 z-50
                                    ">
                                    {{ $post->title }}
                                </span>
                            </h4>

                            <p class="mx-auto w-full overflow-hidden md:h-18 2xl:h-24 break-all">
                                {{ substr($post->subtitle, 0, 80) }}
                            </p>
                        </a>
                        <div class="self-center md:self-end text-xs md:text-sm font-semibold flex items-center mt-auto">
                            @if ($post->published)
                                <span>{{ date('d M Y - H:i', strtotime($post->published)) }} </span>
                                <span class="ml-1 border rounded-lg px-1"> {{ $post->likes_count }} ❤️</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="p-4">
            {{ $posts->links() }}
        </div>
    </div>
@endsection
