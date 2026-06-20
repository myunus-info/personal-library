@extends('layouts.app')

@section('title', $book['title'])
@section('page_title', 'Book Details')

@section('content')
<div class="mb-6">
    <a href="/" class="inline-flex items-center gap-1.5 text-sm text-slate-400 hover:text-slate-200 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Dashboard
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Left Column: Cover & Quick Info -->
    <div class="space-y-6">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl flex flex-col items-center">
            
            <!-- Book Cover -->
            <div class="w-full max-w-[240px] aspect-[3/4] bg-slate-950 rounded-xl overflow-hidden shadow-2xl relative border border-slate-800">
                @if($book['cover_image'])
                    <img src="{{ $book['cover_image'] }}" alt="Cover for {{ $book['title'] }}" class="w-full h-full object-cover">
                @else
                    <!-- Fallback -->
                    <div class="w-full h-full p-6 flex flex-col justify-between items-center bg-gradient-to-br from-indigo-900/40 to-violet-950/40 select-none">
                        <span class="text-[10px] uppercase font-bold tracking-widest text-slate-500">Book Cover</span>
                        <div class="text-center">
                            <h4 class="text-base font-extrabold text-slate-200 line-clamp-4 px-2">{{ $book['title'] }}</h4>
                            <p class="text-xs text-slate-400 mt-1 line-clamp-1 italic">{{ $book['author'] }}</p>
                        </div>
                        <svg class="w-10 h-10 text-slate-600/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                @endif
            </div>

            <!-- Badges -->
            <div class="flex flex-wrap gap-2.5 justify-center mt-6 w-full">
                <!-- Condition -->
                @if($book['condition'] === 'new')
                    <span class="px-3 py-1 text-xs font-bold uppercase tracking-wider bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 rounded-lg">New Condition</span>
                @elseif($book['condition'] === 'good')
                    <span class="px-3 py-1 text-xs font-bold uppercase tracking-wider bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-lg">Good Condition</span>
                @else
                    <span class="px-3 py-1 text-xs font-bold uppercase tracking-wider bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-lg">Damaged</span>
                @endif

                <!-- Reading Status -->
                @if($book['reading_status'] === 'completed')
                    <span class="px-3 py-1 text-xs font-bold uppercase tracking-wider bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 rounded-lg">Completed</span>
                @elseif($book['reading_status'] === 'reading')
                    <span class="px-3 py-1 text-xs font-bold uppercase tracking-wider bg-blue-500/15 border border-blue-500/30 text-blue-400 rounded-lg">Reading</span>
                @else
                    <span class="px-3 py-1 text-xs font-bold uppercase tracking-wider bg-slate-800 border border-slate-700 text-slate-400 rounded-lg">To Read</span>
                @endif
            </div>

            <div class="w-full border-t border-slate-800/80 my-5"></div>

            <!-- Actions buttons -->
            <div class="flex gap-3 w-full">
                <a href="/books/{{ $book['id'] }}/edit" class="flex-1 py-2.5 px-4 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-xl text-xs transition duration-200 border border-slate-700/80 flex items-center justify-center gap-1.5 shadow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Details
                </a>
                
                <form action="/books/{{ $book['id'] }}/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this book? This action cannot be undone.');" class="flex-1">
                    <button type="submit" class="w-full py-2.5 px-4 bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white font-bold rounded-xl text-xs transition duration-200 border border-rose-500/20 hover:border-transparent flex items-center justify-center gap-1.5 shadow">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete Book
                    </button>
                </form>
            </div>

        </div>
    </div>

    <!-- Right Columns: Detailed Info & Progress Tracker -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Book Details Block -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <h2 class="text-2xl font-extrabold text-slate-100 tracking-tight leading-tight">{{ $book['title'] }}</h2>
            <p class="text-sm font-medium text-indigo-400 mt-1">by <span class="font-semibold">{{ $book['author'] }}</span></p>
            
            <div class="flex flex-wrap gap-1.5 mt-4">
                @foreach($book['tags'] as $tag)
                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold" style="background-color: {{ $tag['color'] }}15; color: {{ $tag['color'] }}; border: 1px solid {{ $tag['color'] }}25;">
                        {{ $tag['name'] }}
                    </span>
                @endforeach
            </div>

            @if($book['isbn'])
                <div class="mt-5 text-xs text-slate-500 font-semibold uppercase tracking-wider flex items-center gap-1.5">
                    <span>ISBN:</span>
                    <span class="text-slate-400 font-mono select-all">{{ $book['isbn'] }}</span>
                </div>
            @endif

            <div class="border-t border-slate-800/80 my-6"></div>

            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Description / Notes</h3>
            <p class="text-sm text-slate-300 whitespace-pre-line leading-relaxed">
                {{ $book['description'] ?: 'No description or notes provided for this book.' }}
            </p>
        </div>

        <!-- Interactive Reading Progress Widget -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden">
            <!-- Background Glow -->
            <div class="absolute w-[200px] h-[200px] rounded-full bg-indigo-500/5 blur-[50px] -bottom-10 -right-10 pointer-events-none"></div>

            <h3 class="text-base font-bold text-slate-200 mb-4">Reading Progress Tracker</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                <!-- Circular/Percentage Stats dial -->
                <div class="flex flex-col items-center">
                    <div class="w-28 h-28 rounded-full border-4 border-slate-800 flex flex-col justify-center items-center relative bg-slate-950/40">
                        <!-- Progress Dial Ring -->
                        <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 36 36">
                            <path class="text-slate-800" stroke-width="2.5" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="{{ $book['reading_status'] === 'completed' ? 'text-emerald-500' : 'text-indigo-500' }} transition-all duration-500" stroke-dasharray="{{ $book['progress_percent'] }}, 100" stroke-width="2.5" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <span class="text-2xl font-black text-slate-100">{{ $book['progress_percent'] }}%</span>
                        <span class="text-[9px] uppercase font-bold tracking-widest text-slate-500 mt-0.5">Read</span>
                    </div>
                </div>

                <!-- Page progress indicators -->
                <div class="md:col-span-2 space-y-4">
                    <div>
                        <div class="flex justify-between items-center text-xs mb-1.5">
                            <span class="text-slate-400 font-semibold">Pages completed</span>
                            <span class="font-bold text-slate-300">{{ $book['current_page'] }} / {{ $book['total_pages'] }} pages</span>
                        </div>
                        <div class="w-full h-2 bg-slate-950 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-300 {{ $book['reading_status'] === 'completed' ? 'bg-emerald-500' : 'bg-indigo-500' }}" style="width: {{ $book['progress_percent'] }}%"></div>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center text-xs text-slate-500">
                        <span>{{ $book['total_pages'] - $book['current_page'] }} pages left to complete</span>
                        <span>Updated: {{ date('M d, Y', strtotime($book['updated_at'])) }}</span>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-800/80 my-6"></div>

            <!-- Progress Update Form -->
            <form action="/books/{{ $book['id'] }}/progress" method="POST" class="space-y-4">
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <div class="w-full sm:flex-1">
                        <label for="progress-range" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Adjust Current Page</label>
                        <!-- Dynamic Slider -->
                        <input type="range" id="progress-range" min="0" max="{{ $book['total_pages'] }}" value="{{ $book['current_page'] }}"
                            class="w-full h-2 bg-slate-950 rounded-lg appearance-none cursor-pointer accent-indigo-500">
                    </div>
                    
                    <div class="w-full sm:w-32">
                        <label for="current_page_input" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Page Number</label>
                        <input type="number" id="current_page_input" name="current_page" min="0" max="{{ $book['total_pages'] }}" value="{{ $book['current_page'] }}" required
                            class="block w-full px-4 py-2 bg-slate-950/60 border border-slate-800 rounded-xl text-slate-200 text-center font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 text-sm">
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit"
                        class="py-2.5 px-6 bg-indigo-600 hover:bg-indigo-500 font-bold text-white rounded-xl text-sm transition duration-200 shadow-md shadow-indigo-600/10 flex items-center gap-1.5">
                        Update Progress
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    // Link Slider & Number Input for Progress Tracker
    const slider = document.getElementById('progress-range');
    const numberInput = document.getElementById('current_page_input');

    slider.addEventListener('input', (e) => {
        numberInput.value = e.target.value;
    });

    numberInput.addEventListener('input', (e) => {
        let val = parseInt(e.target.value) || 0;
        const max = parseInt(slider.max);
        if (val < 0) val = 0;
        if (val > max) val = max;
        slider.value = val;
    });
</script>
@endsection
