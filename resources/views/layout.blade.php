<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    {{-- <script src="{{ asset('js/alpine.min.js') }}"></script> --}}
    <script src="{{ asset('js/alpine.min.js') }}" defer></script>
    {{-- <script src="//unpkg.com/alpinejs" defer></script> --}}

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        .ck-content>blockquote,
        .ck-content>dl,
        .ck-content>dd,
        .ck-content>h1,
        .ck-content>h2,
        .ck-content>h3,
        .ck-content>h4,
        .ck-content>h5,
        .ck-content>h6,
        .ck-content>hr,
        .ck-content>figure,
        .ck-content>p,
        .ck-content>pre {
            margin: revert;
        }

        .ck-content>ol,
        .ck-content>ul {
            list-style: revert;
            margin: revert;
            padding: revert;
        }

        .ck-content>table {
            border-collapse: revert;
        }

        .ck-content>h1,
        .ck-content>h2,
        .ck-content>h3,
        .ck-content>h4,
        .ck-content>h5,
        .ck-content>h6 {
            font-size: revert;
            font-weight: revert;
        }

        .cke_chrome {
            border: none !important;
        }
    </style>
</head>

<body class="antialiased">
    <div class="relative min-h-screen">
        <div class="max-w-[1800px] mx-auto px-2 md:px-4 lg:px-20">
            <!-- top -->
            <div class="py-2 md:py-4 px-2 md:px-5 flex justify-between">
                <div class="justify-center items-center md:flex">
                    <a href="/" class="shrink-0">
                        <img src="/storage/gambar/risu.jpg" alt="logo"
                            class="h-10 w-10 rounded-full mr-2 bg-green-200">
                    </a>

                    <!-- search -->
                    <div class="hidden md:block">
                        @if (request()->route()->uri == '/')
                            <x-search :search="$search" :tagsSearch="$tagsSearch" />
                        @endif
                    </div>


                </div>
                <!-- user -->
                <div class="flex justify-center shrink-0">
                    @auth
                        <a href="/post/create" class="mr-2">
                            <div class="flex justify-center items-center h-10 w-10 border rounded-full">➕</div>
                        </a>

                        <div class="relative bg-white z-50" x-data={profile:false}>
                            <button x-on:click="profile=true">
                                <img src="{{ auth()->user()->picture ?? '/storage/gambar/pp_default.png' }}"
                                    alt="" class="h-10 w-10 object-cover rounded-full">
                            </button>
                            <div class="absolute w-40 -bottom-28 -left-28 md:-left-20" x-show="profile">
                                <a class="hover:bg-gray-200 px-2 block w-full border rounded-lg mb-1 bg-white text-lg font-semibold text-center"
                                    href="/user/{{ auth()->user()->username }}">Profile</a>
                                <form action="/user/logout" method="post">
                                    @csrf
                                    <button
                                        class="hover:bg-gray-200 px-2 block w-full border rounded-lg mb-1 bg-white text-lg font-semibold"
                                        onclick="return confirm('Logout')">
                                        Logout
                                    </button>
                                </form>
                                <button
                                    class="hover:bg-gray-200 px-2 block w-full border rounded-lg mb-1 bg-white text-lg font-semibold text-center"
                                    x-on:click="profile=false">Close</button>
                            </div>
                            {{--  --}}
                        </div>
                    @else
                        <a href="/user/login?prev={{ request()->getRequestUri() }}" class="mr-2">
                            <div class="flex justify-center items-center h-10 border rounded-md px-2">Login</div>
                        </a>
                        <a href="/user/create?prev={{ request()->getRequestUri() }}" class="mr-2">
                            <div class="flex justify-center items-center h-10 border rounded-md px-2">Register</div>
                        </a>
                    @endauth
                </div>

            </div>
            <!-- garis -->
            <div class="border-b-2 w-full"></div>
            <!-- search sm -->
            <div class="md:hidden w-full">
                @if (request()->route()->uri == '/')
                    <x-search :search="$search" :tagsSearch="$tagsSearch" />
                @endif
            </div>
            @yield('content')
        </div>
    </div>
</body>

</html>
