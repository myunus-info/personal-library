@extends('layouts.app')

@section('title', 'Page Not Found')

@section('content')
<div class="min-h-[70vh] flex flex-col justify-center items-center text-center">
    <div class="relative mb-6">
        <h1 class="text-9xl font-black text-slate-800 tracking-wider">404</h1>
        <span class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-lg font-bold text-indigo-400 uppercase tracking-widest bg-slate-950 px-4">Lost in Space</span>
    </div>
    
    <h3 class="text-xl font-bold text-slate-300 mt-4">The book you are looking for has been misplaced.</h3>
    <p class="text-sm text-slate-500 max-w-md mt-2">The page you requested does not exist or has been relocated to another shelf.</p>
    
    <a href="/" class="mt-8 inline-flex items-center gap-2 py-3 px-6 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-sm transition duration-200 shadow-lg shadow-indigo-600/20">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        Return to Library
    </a>
</div>
@endsection
