@extends('layout')
@section('content')
    <div class="mt-8 mx-auto p-10 w-full md:w-1/2 xl:w-1/3 2xl:w-1/4 2xl:mx-0 shadow sm:rounded-lg">
        <h2 class="text-3xl font-semibold mb-4 text-center">Register</h2>
        <form method="post" action="/user/store?prev={{ request()->prev ?? '' }}">
            @csrf
            <div class="mb-2">
                <p class="mb-1 font-semibold">Username</p>
                <input type="text" name="username" class="px-2 py-1 border w-full" value="{{ old('username') }}">
                @error('username')
                    <p class="text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-2">
                <p class="mb-1 font-semibold">Name</p>
                <input type="text" name="name" class="px-2 py-1 border w-full" value="{{ old('name') }}">
                @error('name')
                    <p class="text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div class=" mb-2">
                <p class="mb-1 font-semibold">Email</p>
                <input type="email" name="email" class="px-2 py-1 border w-full" value="{{ old('email') }}">
                @error('email')
                    <p class="text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div class=" mb-2">
                <p class="mb-1 font-semibold">Password</p>
                <input type="password" name="password" class="px-2 py-1 border w-full" value="{{ old('password') }}">
                @error('password')
                    <p class="text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div class=" mb-2">
                <p class="mb-1 font-semibold">Confirm Password</p>
                <input type="password" name="password_confirmation" class="px-2 py-1 border w-full">
            </div>
            <div class="mt-4">
                <button class="px-2 py-1 border rounded-md">Register</button>
                <a href="/user/login" class="ml-4">Login</a>
            </div>
        </form>
    </div>
@endsection
