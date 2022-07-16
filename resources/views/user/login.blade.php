@extends('layout')
@section('content')
    <div class="mt-8 shadow md:w-1/2 xl:w-1/3 2xl:w-1/4 mx-auto p-10">
        <h2 class="text-3xl font-semibold mb-4 text-center">Login</h2>
        <form method="post" action="/user/auth?prev={{ request()->prev ?? '' }}">
            @csrf
            <div class="mb-2">
                <p class="mb-1 font-semibold">Email</p>
                <input type="email" name="email" class="px-2 py-1 border w-full" value="{{ old('email') }}">
                @error('email')
                    <p class="text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-2">
                <p class="mb-1 font-semibold">Password</p>
                <input type="password" name="password" class="px-2 py-1 border w-full" value="{{ old('password') }}">
                @error('password')
                    <p class="text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div class="mt-4">
                <button class="px-2 py-1 border rounded-md">Login</button>
                <a href="/user/create" class="ml-4">Register</a>
            </div>
        </form>
    </div>
@endsection
