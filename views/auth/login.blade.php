@extends('layouts.app')

@section('title', 'Admin Login')

@section('content')
<div class="min-h-[80vh] flex flex-col justify-center items-center relative overflow-hidden">
    <!-- Decorative Glowing Orbs -->
    <div class="absolute w-[300px] h-[300px] rounded-full bg-indigo-500/10 blur-[100px] top-1/4 left-1/4 -translate-x-1/2 -translate-y-1/2 pointer-events-none"></div>
    <div class="absolute w-[250px] h-[250px] rounded-full bg-violet-500/10 blur-[80px] bottom-1/4 right-1/4 translate-x-1/2 translate-y-1/2 pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        <!-- Logo / Brand Title -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold tracking-tight bg-gradient-to-r from-indigo-300 via-indigo-200 to-violet-300 bg-clip-text text-transparent">
                Personal Library
            </h2>
            <p class="mt-2 text-sm text-slate-400">
                Log in to manage and track your library progress
            </p>
        </div>

        <!-- Glassmorphism Form Card -->
        <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 shadow-2xl shadow-slate-950/50">
            <form action="/login" method="POST" class="space-y-6">
                <!-- Username Input -->
                <div>
                    <label for="username" class="block text-sm font-semibold text-slate-300 mb-2">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <input id="username" name="username" type="text" value="{{ $old['username'] ?? '' }}" required
                            class="block w-full pl-11 pr-4 py-3 bg-slate-950/60 border border-slate-800 rounded-2xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition duration-200 text-sm"
                            placeholder="Enter admin username">
                    </div>
                    @if(isset($errors['username']))
                        <p class="mt-1.5 text-xs text-rose-500">{{ $errors['username'][0] }}</p>
                    @endif
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-300 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <input id="password" name="password" type="password" required
                            class="block w-full pl-11 pr-4 py-3 bg-slate-950/60 border border-slate-800 rounded-2xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition duration-200 text-sm"
                            placeholder="Enter password">
                    </div>
                    @if(isset($errors['password']))
                        <p class="mt-1.5 text-xs text-rose-500">{{ $errors['password'][0] }}</p>
                    @endif
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-2xl shadow-lg shadow-indigo-600/20 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition duration-200">
                        Access Dashboard
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
