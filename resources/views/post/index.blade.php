@extends('layout') @section('content')
    <div class="py-5 block xl:flex w-full">

        <!-- kiri -->
        <div class="px-1 md:px-4 lg:border-r-2 w-full overflow-hidden break-all">
            <!-- post -->
            <div class="px-4 pb-6 flex flex-col w-full border-b-2" x-data="likeData">

                {{-- meme --}}
                <div id="meme" class="hidden w-full h-full fixed left-0 top-0 justify-center items-center animate-ping">
                    <div class="border-2">
                        <img src="/gambar/medal-meme.png" alt="" class="w-[60vw] md:w-[40vw] xl:w-[20vw] object-cover">
                    </div>
                </div>

                <div class="text-xs md:text-sm font-semibold flex justify-between items-center mb-3">
                    <a href="/user/{{ $post->user->username }}" class="flex items-center"><img
                            src="{{ $post->user->picture ?? '/gambar/pp_default.png' }}" alt="profile pic"
                            class="h-7 w-7 border rounded-full mr-2">{{ $post->user->name }} </a>
                    <p>{{ date('d M Y - H:i', strtotime($post->published)) }}</p>
                </div>
                @auth
                    @if ($post->user_id === auth()->user()->id)
                        <div class="mb-3 flex flex-col md:flex-row justify-end" x-data="{ openDelete: false }">
                            <div class="">
                                <a href="/post/{{ $post->id }}/edit"><button
                                        class="px-2 py-1 bg-yellow-200 rounded-md mr-1">Edit</button></a>
                                <button x-on:click="openDelete=true"
                                    class="text-gray-100 px-1 py-1 bg-red-400 rounded-md mr-1">Delete</button>
                            </div>
                            <div class="ml-4 flex items-center" x-show="openDelete" x-transition.duration.500ms>
                                <form action="/post/{{ $post->id }}" method="post" class="">
                                    @csrf
                                    @method('DELETE')
                                    <span class="text-yellow-700 mr-1">Delete this post?</span>
                                    <button class="px-1 mr-1 bg-red-600 text-gray-50 rounded-md">Yes</button>
                                    <button type="button" x-on:click="openDelete=false"
                                        class="px-2 bg-blue-500 text-gray-50 rounded-md">x</button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endauth

                {{-- counter, like button --}}
                <div class="pl-10 self-end mb-2">
                    <span class="border px-2 rounded-lg mr-2">
                        {{ $post->views_count }} views
                    </span>
                    <button class="border px-2 rounded-lg" x-on:click="like" :disabled="disabled">
                        <span x-text="likeCount"></span>
                        <span x-text="liked ? '❤️' : '🤍'"></span>
                    </button>
                </div>

                <!-- title, tags -->
                <div class="text-center w-fit pb-2 self-center">
                    <h3 class="text-2xl md:text-3xl mb-3 font-bold">{{ $post->title }}</h3>
                    @foreach ($post->tags as $tag)
                        <span class="mr-1 px-1 border rounded-md mb-1">{{ $tag->name }}</span>
                    @endforeach
                </div>

                {{-- pic --}}
                <img src="{{ $post->picture ?? '/gambar/defaultL.JPG' }}" alt="image"
                    class="mb-4 object-cover self-center w-11/12 lg:w-9/12 2xl:w-8/12 min-h-[10rem] bg-gray-200">

                <!-- content -->
                <div class="mb-4 md:text-lg ck-content">
                    {!! $post->content !!}
                </div>

                {{-- like btn2 --}}
                <div class="pl-10 self-end">
                    <button class="border px-2 rounded-lg" x-on:click="like" :disabled="disabled">
                        <span x-text="likeCount"></span>
                        <span x-text="liked ? '❤️' : '🤍'"></span>
                    </button>
                </div>


            </div>
            <!-- comments -->
            <div class="p-4 flex flex-col w-full">
                <h3 class="text-xl mb-3">Comments ({{ $comments->total() }})</h3>

                @auth
                    {{-- comment forn --}}
                    <form action="/post/{{ $post->id }}/comment" method="post">
                        @csrf
                        <div class="mb-2">
                            <div>
                                <textarea name="content" rows="4" class="w-full p-4 text-lg border rounded-lg" maxlength="250"></textarea>
                                @error('content')
                                    <p class="text-sm text-yellow-500">{{ $message }}</p>
                                @enderror
                                <button class="px-2 py-1 text-lg bg1-green-400 font-bold border rounded-lg">Send</button>
                            </div>
                        </div>
                    </form>
                @else
                    <p class="text-blue-500 text-lg mb-2">
                        <a href="/user/login?prev={{ request()->getRequestUri() }}">Login</a> to post a comment
                    </p>
                @endauth

                @foreach ($comments as $comment)
                    <div class="p-2 border rounded-lg mb-2">
                        <div class="text-xs md:text-sm font-semibold flex justify-between items-center mb-3">
                            <a href="/user/{{ $comment->user->username }}" class="flex items-center"><img
                                    src="{{ $comment->user->picture ?? '/gambar/pp_default.png' }}" alt="profile pic"
                                    class="h-7 w-7 border rounded-full mr-2">
                                {{ $comment->user->name }}
                            </a>
                            <p>{{ date('d M Y - H:i', strtotime($comment->created_at)) }}</p>
                        </div>
                        <p class="pl-10 md:pr-32 break-all ck-content mb-2">
                            {!! nl2br($comment->content) !!}
                        </p>
                        <div class="pl-10 text-xs flex justify-between">

                            <div x-data="{
                                disabled: {{ auth()->user() ? 'false' : 'true' }},
                                liked: {{ $comment->liked ? 'true' : 'false' }},
                                likeCount: {{ $comment->likes_count }},
                                like: async function() {
                                    try {
                                        this.disabled = true;
                                        const response = await fetch('/comment/{{ $comment->id }}/like', {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-Token': '{{ csrf_token() }}'
                                            }
                                        })
                                        const {
                                            liked,
                                            likeCount
                                        } = await response.json();
                                        this.liked = liked;
                                        this.likeCount = likeCount;
                                        this.disabled = false;
                                    } catch (error) {
                                        this.disabled = false;
                                        console.log(error)
                                    }
                                }
                            }">
                                <button class="border px-2 rounded-lg" x-on:click="like" :disabled="disabled">
                                    <span x-text="likeCount"></span> <span x-text="liked? '❤️' : '🤍'"></span></button>
                            </div>
                            @auth
                                @if ($comment->user_id === auth()->user()->id)
                                    <div x-data="{ open: false }" class="flex">
                                        <button x-on:click="open=true" x-show="!open"
                                            class="px-2 rounded-lg bg-red-500 text-white">
                                            Delete</button>
                                        <div x-show="open" x-transition.duration.500ms>
                                            <form action="/comment/{{ $comment->id }}" method="post">
                                                @csrf
                                                @method('DELETE')
                                                <span class="text-yellow-500 ml-1">Delete this comment?</span>
                                                <button class="px-2 bg-red-500 rounded-sm text-white">Y</button>
                                                <button type="button" x-on:click="open=false"
                                                    class="px-2 border rounded-sm">N</button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @endauth
                        </div>
                    </div>
                @endforeach
                <!-- comment page -->
                <div class="w-full p-2">
                    {{ $comments->links() }}
                </div>
            </div>

        </div>

        <!-- Kanan -->
        <div class="mx-auto w-full xl:w-72 p-2 h-max shrink-0">
            <div class="flex flex-col justify-center items-center border-b-2 py-2 mb-4">
                <img src="{{ $post->user->picture ?? '/gambar/pp_default.png' }}" alt=""
                    class="w-10/12 max-h-[220px] object-scale-down">
                <p class="text-lg font-bold mt-2 w-fit">{{ $post->user->name }}</p>
                <a href="/user/{{ $post->user->username }}" class="">
                    {{ $post->user->username }}
                </a>
                {{-- <div class="flex justify-center flex-wrap mt-2">
                    <a href="/twitter.com" class="mx-1 p-1 border rounded-full">
                        🐦
                    </a>
                    <a href="/twitter.com" class="mx-1 p-1 border rounded-full">
                        📷
                    </a>
                    <a href="/twitter.com" class="mx-1 p-1 border rounded-full">
                        🎥
                    </a>
                    <a href="/twitter.com" class="mx-1 p-1 border rounded-full">
                        🐈
                    </a>
                    <a href="/twitter.com" class="mx-1 p-1 border rounded-full">
                        🧑‍💼
                    </a>
                </div> --}}
            </div>
            <div class="flex flex-col justify-center p-2 px-4">
                @foreach ($morePosts as $morePost)
                    <a href="/post/{{ $morePost->id }}" class="">
                        <div class="mb-4">
                            <img src="{{ $morePost->picture ?? '/gambar/defaultL.JPG' }}" alt="gambar"
                                class="w-full min-h-[60px] bg-red-50">
                            <p class="font-bold">{{ $morePost->title }}</p>
                        </div>
                    </a>
                @endforeach
                @if ($post->user->posts()->count() > 3)
                    <a href="/user/{{ $post->user->id }}" class="mx-auto">
                        <button class="border px-1 font-semibold">
                            More post from {{ $post->user->name }}
                        </button>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('likeData', () => ({
                disabled: {{ auth()->user() ? 'false' : 'true' }},
                liked: {{ $post->liked ? 'true' : 'false' }},
                likeCount: {{ $post->likes_count }},
                meme: false,

                async like() {
                    try {
                        this.disabled = true;
                        const response = await fetch("/post/{{ $post->id }}/like", {
                            method: 'POST',
                            headers: {
                                "X-CSRF-Token": '{{ csrf_token() }}'
                            }
                        })
                        const {
                            liked,
                            likeCount
                        } = await response.json();
                        this.liked = liked;
                        this.likeCount = likeCount;
                        this.disabled = false;

                        // meme
                        if ({{ (auth()->user()->id ?? '') === $post->user_id ? 'true' : 'false' }} &&
                            liked) {
                            //this.meme = true

                            document.getElementById('meme').classList.add('flex');
                            document.getElementById('meme').classList.remove('hidden');

                            setTimeout(() => {
                                document.getElementById('meme').classList.add('hidden');
                                document.getElementById('meme').classList.remove(
                                    'flex');
                                //this.meme = false
                            }, 1000);
                        }

                    } catch (error) {
                        this.disabled = false;
                        console.log(error)
                    }
                }

            }))
        })
    </script>
@endsection
