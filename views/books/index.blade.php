@extends('layouts.app')

@section('title', 'Library Dashboard')
@section('page_title', 'My Library')

@section('content')
<!-- Statistics Dashboard Overview -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    <!-- Total Books -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-md">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Books</p>
        <h3 class="text-2xl font-bold text-slate-200 mt-1">{{ $stats['total'] }}</h3>
    </div>
    <!-- Reading -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-md">
        <div class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Reading</p>
        </div>
        <h3 class="text-2xl font-bold text-blue-400 mt-1">{{ $stats['reading'] }}</h3>
    </div>
    <!-- Completed -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-md">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Completed</p>
        <h3 class="text-2xl font-bold text-emerald-400 mt-1">{{ $stats['completed'] }}</h3>
    </div>
    <!-- To Read -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-md">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">To Read</p>
        <h3 class="text-2xl font-bold text-slate-400 mt-1">{{ $stats['to_read'] }}</h3>
    </div>
    <!-- New physical condition -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-md">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">New Condition</p>
        <h3 class="text-2xl font-bold text-indigo-400 mt-1">{{ $stats['new'] }}</h3>
    </div>
    <!-- Damaged books -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-md">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Damaged</p>
        <h3 class="text-2xl font-bold text-rose-400 mt-1">{{ $stats['damaged'] }}</h3>
    </div>
</div>

<!-- Search and Filter Bar -->
<div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-md mb-8">
    <form action="/" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Search Query -->
        <div class="relative lg:col-span-2">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                class="block w-full pl-9 pr-4 py-2.5 bg-slate-950/60 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition duration-200 text-sm"
                placeholder="Search by Title, Author, or ISBN...">
        </div>

        <!-- Reading Status Filter -->
        <div>
            <select name="reading_status"
                class="block w-full px-4 py-2.5 bg-slate-950/60 border border-slate-800 rounded-xl text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition duration-200 text-sm">
                <option value="">All Reading Statuses</option>
                <option value="to_read" {{ ($filters['reading_status'] ?? '') === 'to_read' ? 'selected' : '' }}>To Read</option>
                <option value="reading" {{ ($filters['reading_status'] ?? '') === 'reading' ? 'selected' : '' }}>Currently Reading</option>
                <option value="completed" {{ ($filters['reading_status'] ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>

        <!-- Condition Filter -->
        <div>
            <select name="condition"
                class="block w-full px-4 py-2.5 bg-slate-950/60 border border-slate-800 rounded-xl text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition duration-200 text-sm">
                <option value="">All Conditions</option>
                <option value="new" {{ ($filters['condition'] ?? '') === 'new' ? 'selected' : '' }}>New</option>
                <option value="good" {{ ($filters['condition'] ?? '') === 'good' ? 'selected' : '' }}>Good</option>
                <option value="damaged" {{ ($filters['condition'] ?? '') === 'damaged' ? 'selected' : '' }}>Damaged</option>
            </select>
        </div>

        <!-- Category/Tag Filter & Submit -->
        <div class="flex gap-2">
            <select name="tag_id"
                class="block flex-1 px-4 py-2.5 bg-slate-950/60 border border-slate-800 rounded-xl text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition duration-200 text-sm">
                <option value="">All Categories</option>
                @foreach($tags as $tag)
                    <option value="{{ $tag['id'] }}" {{ (int)($filters['tag_id'] ?? 0) === $tag['id'] ? 'selected' : '' }}>{{ $tag['name'] }}</option>
                @endforeach
            </select>
            
            <button type="submit"
                class="py-2.5 px-4 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold rounded-xl text-sm transition duration-200 border border-slate-700 flex items-center gap-1.5 shadow-md">
                Filter
            </button>
            @if(!empty($filters['search']) || !empty($filters['reading_status']) || !empty($filters['condition']) || !empty($filters['tag_id']))
                <a href="/" class="py-2.5 px-3 bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-xl border border-red-500/20 flex items-center transition" title="Clear Filters">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Books Catalog Grid -->
@if(empty($books))
    <div class="text-center py-20 bg-slate-900 border border-slate-800 rounded-2xl shadow-md">
        <svg class="w-16 h-16 text-slate-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        <h3 class="text-lg font-bold text-slate-300">No books found</h3>
        <p class="text-slate-500 text-sm mt-1 max-w-md mx-auto">Try adjusting your filters or search query, or add a new book to start building your library catalog.</p>
        <a href="/books/create" class="mt-5 inline-flex items-center gap-2 py-2.5 px-5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-sm transition duration-200 shadow-lg shadow-indigo-600/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add your first book
        </a>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
        @foreach($books as $book)
            <!-- Book Card -->
            <div class="bg-slate-900 border border-slate-800/80 rounded-2xl overflow-hidden hover:border-slate-700/80 hover:shadow-xl hover:shadow-slate-950/40 hover:-translate-y-1 transition duration-300 flex flex-col group relative">
                
                <!-- Book Cover Container -->
                <div class="aspect-[3/4] relative bg-slate-950 flex items-center justify-center overflow-hidden border-b border-slate-800/50">
                    @if($book['cover_image'])
                        <img src="{{ $book['cover_image'] }}" alt="Cover for {{ $book['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    @else
                        <!-- Elegant CSS Cover Fallback -->
                        <div class="w-full h-full p-6 flex flex-col justify-between items-center bg-gradient-to-br from-indigo-900/40 to-violet-950/40 select-none">
                            <span class="text-[10px] uppercase font-bold tracking-widest text-slate-500">Book Cover</span>
                            <div class="text-center">
                                <h4 class="text-sm font-extrabold text-slate-200 line-clamp-3 px-2">{{ $book['title'] }}</h4>
                                <p class="text-xs text-slate-400 mt-1 line-clamp-1 italic">{{ $book['author'] }}</p>
                            </div>
                            <svg class="w-8 h-8 text-slate-600/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                    @endif

                    <!-- Condition Badge -->
                    <div class="absolute top-3 left-3 z-10">
                        @if($book['condition'] === 'new')
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 rounded-md backdrop-blur">New</span>
                        @elseif($book['condition'] === 'good')
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-md backdrop-blur">Good</span>
                        @else
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-md backdrop-blur">Damaged</span>
                        @endif
                    </div>

                    <!-- Reading Status Badge -->
                    <div class="absolute top-3 right-3 z-10">
                        @if($book['reading_status'] === 'completed')
                            <span class="w-6 h-6 bg-emerald-500 border border-emerald-400 text-white rounded-full flex items-center justify-center shadow-lg" title="Completed">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </span>
                        @elseif($book['reading_status'] === 'reading')
                            <span class="w-6 h-6 bg-blue-500 border border-blue-400 text-white rounded-full flex items-center justify-center shadow-lg animate-pulse" title="Reading">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Book Body Details -->
                <div class="p-4 flex-1 flex flex-col justify-between">
                    <div>
                        <!-- Title & Author -->
                        <h4 class="text-sm font-bold text-slate-200 line-clamp-1 group-hover:text-indigo-400 transition">{{ $book['title'] }}</h4>
                        <p class="text-xs text-slate-500 mt-0.5 line-clamp-1">by {{ $book['author'] }}</p>

                        <!-- Tags/Categories List -->
                        <div class="flex flex-wrap gap-1 mt-2.5">
                            @foreach(array_slice($book['tags'], 0, 3) as $tag)
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-semibold" style="background-color: {{ $tag['color'] }}15; color: {{ $tag['color'] }}; border: 1px solid {{ $tag['color'] }}25;">
                                    {{ $tag['name'] }}
                                </span>
                            @endforeach
                            @if(count($book['tags']) > 3)
                                <span class="px-1 py-0.5 rounded text-[8px] font-bold bg-slate-800 text-slate-400">
                                    +{{ count($book['tags']) - 3 }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Reading Progress Bar -->
                    <div class="mt-4">
                        <div class="flex justify-between items-center text-[10px] text-slate-500 mb-1">
                            <span>{{ $book['current_page'] }}/{{ $book['total_pages'] }} pages</span>
                            <span class="font-bold text-slate-400">{{ $book['progress_percent'] }}%</span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-950 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-300 {{ $book['reading_status'] === 'completed' ? 'bg-emerald-500' : 'bg-indigo-500' }}" style="width: {{ $book['progress_percent'] }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Hover Details overlay -->
                <a href="/books/{{ $book['id'] }}" class="absolute inset-0 z-0"></a>
            </div>
        @endforeach
    </div>
@endif
@endsection
