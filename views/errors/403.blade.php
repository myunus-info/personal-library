@extends('layouts.app')

@section('title', 'Access Denied')

@section('content')
<div class="min-h-[70vh] flex flex-col justify-center items-center text-center">
    <div class="relative mb-6">
        <h1 class="text-9xl font-black text-slate-800 tracking-wider">403</h1>
        <span class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-lg font-bold text-rose-400 uppercase tracking-widest bg-slate-950 px-4">Restricted Section</span>
    </div>
    
    <h3 class="text-xl font-bold text-slate-300 mt-4">This section is restricted.</h3>
    <p class="text-sm text-slate-500 max-w-md mt-2">Only library administrators have the clearance to access these shelves.</p>
    
    <a href="/" class="mt-8 inline-flex items-center gap-2 py-3 px-6 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-xl text-sm transition duration-200 border border-slate-700">
        Return to Safety
    </a>
</div>
@endsection
