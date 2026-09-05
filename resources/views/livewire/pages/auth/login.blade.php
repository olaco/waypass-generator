<?php

use Laravel\Fortify\Fortify;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new class extends Component {
    //
};

?>

<div class="flex min-h-screen flex-col justify-center bg-[#F8F9FA] py-12 sm:px-6 lg:px-8">

    <!-- Header section with Logo -->
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="flex justify-center">
            <!-- Official Evans Baroque Logo -->
            <a href="{{ route('home') }}" class="block">
                <img src="{{ asset('images/evans-baroque-logo.png') }}"
                     alt="Evans Baroque Ltd"
                     class="h-12 w-auto object-contain"
                     style="filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));">
            </a>
        </div>

        <p class="mt-6 text-center text-sm text-gray-600">
            Sign in to your internal portal
        </p>
    </div>

    <!-- Login Card -->
    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
        <div class="bg-white py-8 px-4 shadow-sm ring-1 ring-gray-900/5 sm:rounded-lg sm:px-10">

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Standard Laravel/Volt Login Form -->
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required autofocus
                            class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-[#E91E63] focus:outline-none focus:ring-[#E91E63] sm:text-sm @error('email') border-red-500 @enderror"
                            value="{{ old('email') }}">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mt-6">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-[#E91E63] focus:outline-none focus:ring-[#E91E63] sm:text-sm @error('password') border-red-500 @enderror">
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="mt-6 flex items-center justify-between">
                    <label for="remember_me" class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 text-[#E91E63] focus:ring-[#E91E63]">
                        <span class="ml-2 block text-sm text-gray-900">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <div class="text-sm">
                            <a href="{{ route('password.request') }}" class="font-medium text-gray-600 hover:text-[#E91E63]">
                                Forgot your password?
                            </a>
                        </div>
                    @endif
                </div>

                <div class="mt-6">
                    <button type="submit"
                        class="flex w-full justify-center rounded-md bg-[#E91E63] px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#D81B60] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#E91E63]">
                        Log in
                    </button>
                </div>
            </form>
        </div>

        <!-- Back to Home Link -->
        <p class="mt-10 text-center text-sm text-gray-500">
            <a href="{{ route('home') }}" class="font-medium text-gray-600 hover:text-[#E91E63]">
                &larr; Back to Corporate Home
            </a>
        </p>
    </div>
</div>
