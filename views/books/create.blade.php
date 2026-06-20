@extends('layouts.app')

@section('title', 'Add New Book')
@section('page_title', 'Add New Book')

@section('content')
<div class="mb-6">
    <a href="/" class="inline-flex items-center gap-1.5 text-sm text-slate-400 hover:text-slate-200 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Cancel and Back
    </a>
</div>

<form action="/books" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Left Column: Image Upload & Preview -->
    <div class="space-y-6">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl flex flex-col items-center">
            <h3 class="text-sm font-semibold text-slate-300 mb-4 self-start">Book Cover Image</h3>
            
            <!-- Cover Preview Box -->
            <div id="cover-preview-container" class="w-full max-w-[200px] aspect-[3/4] bg-slate-950 rounded-xl border-2 border-dashed border-slate-800 flex flex-col items-center justify-center p-6 text-center text-slate-600 relative overflow-hidden group transition">
                <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <p class="text-xs">No image selected</p>
                <img id="cover-preview-img" src="" alt="Cover Preview" class="hidden absolute inset-0 w-full h-full object-cover">
            </div>

            <!-- Upload input -->
            <div class="mt-5 w-full">
                <input type="file" id="cover_image" name="cover_image" accept="image/jpeg,image/png,image/webp" class="hidden">
                <label for="cover_image" class="w-full py-2.5 px-4 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl text-xs transition duration-200 border border-slate-700/80 flex items-center justify-center gap-1.5 cursor-pointer shadow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Choose Image File
                </label>
                <p class="text-[10px] text-slate-500 mt-2 text-center">JPG, PNG, or WebP. Max 2MB.</p>
                @if(isset($errors['cover_image']))
                    <p class="mt-1 text-xs text-rose-500 text-center">{{ $errors['cover_image'][0] }}</p>
                @endif
            </div>

        </div>
    </div>

    <!-- Right Columns: Form Data Fields -->
    <div class="lg:col-span-2 space-y-6 bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h3 class="text-base font-bold text-slate-200 border-b border-slate-800 pb-3">Book Details</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Title -->
            <div class="md:col-span-2">
                <label for="title" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Book Title *</label>
                <input type="text" id="title" name="title" required value="{{ $old['title'] ?? '' }}"
                    class="block w-full px-4 py-2.5 bg-slate-950/60 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition duration-200 text-sm"
                    placeholder="e.g. The Lord of the Rings">
                @if(isset($errors['title']))
                    <p class="mt-1 text-xs text-rose-500">{{ $errors['title'][0] }}</p>
                @endif
            </div>

            <!-- Author -->
            <div>
                <label for="author" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Author *</label>
                <input type="text" id="author" name="author" required value="{{ $old['author'] ?? '' }}"
                    class="block w-full px-4 py-2.5 bg-slate-950/60 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition duration-200 text-sm"
                    placeholder="e.g. J.R.R. Tolkien">
                @if(isset($errors['author']))
                    <p class="mt-1 text-xs text-rose-500">{{ $errors['author'][0] }}</p>
                @endif
            </div>

            <!-- ISBN -->
            <div>
                <label for="isbn" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">ISBN (Optional)</label>
                <input type="text" id="isbn" name="isbn" value="{{ $old['isbn'] ?? '' }}"
                    class="block w-full px-4 py-2.5 bg-slate-950/60 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition duration-200 text-sm"
                    placeholder="e.g. 978-0-261-10325-2">
                @if(isset($errors['isbn']))
                    <p class="mt-1 text-xs text-rose-500">{{ $errors['isbn'][0] }}</p>
                @endif
            </div>

            <!-- Total Pages -->
            <div>
                <label for="total_pages" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Total Pages *</label>
                <input type="number" id="total_pages" name="total_pages" required min="0" value="{{ $old['total_pages'] ?? '0' }}"
                    class="block w-full px-4 py-2.5 bg-slate-950/60 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition duration-200 text-sm">
                @if(isset($errors['total_pages']))
                    <p class="mt-1 text-xs text-rose-500">{{ $errors['total_pages'][0] }}</p>
                @endif
            </div>

            <!-- Current Page -->
            <div>
                <label for="current_page" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Current Page (Start Progress)</label>
                <input type="number" id="current_page" name="current_page" min="0" value="{{ $old['current_page'] ?? '0' }}"
                    class="block w-full px-4 py-2.5 bg-slate-950/60 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition duration-200 text-sm">
                @if(isset($errors['current_page']))
                    <p class="mt-1 text-xs text-rose-500">{{ $errors['current_page'][0] }}</p>
                @endif
            </div>

            <!-- Condition -->
            <div>
                <label for="condition" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Physical Condition *</label>
                <select id="condition" name="condition" required
                    class="block w-full px-4 py-2.5 bg-slate-950/60 border border-slate-800 rounded-xl text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition duration-200 text-sm">
                    <option value="new" {{ ($old['condition'] ?? '') === 'new' ? 'selected' : '' }}>New</option>
                    <option value="good" {{ ($old['condition'] ?? '') === 'good' ? 'selected' : '' }}>Good</option>
                    <option value="damaged" {{ ($old['condition'] ?? '') === 'damaged' ? 'selected' : '' }}>Damaged</option>
                </select>
                @if(isset($errors['condition']))
                    <p class="mt-1 text-xs text-rose-500">{{ $errors['condition'][0] }}</p>
                @endif
            </div>

            <!-- Reading Status -->
            <div>
                <label for="reading_status" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Reading Status *</label>
                <select id="reading_status" name="reading_status" required
                    class="block w-full px-4 py-2.5 bg-slate-950/60 border border-slate-800 rounded-xl text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition duration-200 text-sm">
                    <option value="to_read" {{ ($old['reading_status'] ?? '') === 'to_read' ? 'selected' : '' }}>To Read</option>
                    <option value="reading" {{ ($old['reading_status'] ?? '') === 'reading' ? 'selected' : '' }}>Currently Reading</option>
                    <option value="completed" {{ ($old['reading_status'] ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
                @if(isset($errors['reading_status']))
                    <p class="mt-1 text-xs text-rose-500">{{ $errors['reading_status'][0] }}</p>
                @endif
            </div>

            <!-- Tags/Categories List -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Categories / Tags</label>
                @if(empty($tags))
                    <div class="p-4 bg-slate-950/40 border border-slate-800 rounded-xl text-center">
                        <p class="text-xs text-slate-500">No categories/tags available. <a href="/tags" class="text-indigo-400 hover:underline">Manage tags</a> to add some.</p>
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 max-h-36 overflow-y-auto p-4 bg-slate-950/40 border border-slate-800 rounded-xl">
                        @foreach($tags as $tag)
                            <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer select-none">
                                <input type="checkbox" name="tags[]" value="{{ $tag['id'] }}" 
                                    {{ in_array($tag['id'], $old['tags'] ?? []) ? 'checked' : '' }}
                                    class="rounded bg-slate-950 border-slate-850 text-indigo-600 focus:ring-indigo-500/40">
                                <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $tag['color'] }}"></span>
                                {{ $tag['name'] }}
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <label for="description" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Description / Notes</label>
                <textarea id="description" name="description" rows="4"
                    class="block w-full px-4 py-2.5 bg-slate-950/60 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition duration-200 text-sm"
                    placeholder="Enter summary or physical book details...">{{ $old['description'] ?? '' }}</textarea>
                @if(isset($errors['description']))
                    <p class="mt-1 text-xs text-rose-500">{{ $errors['description'][0] }}</p>
                @endif
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end border-t border-slate-800 pt-4 mt-6">
            <button type="submit"
                class="py-3 px-8 bg-indigo-600 hover:bg-indigo-500 font-bold text-white rounded-xl text-sm transition duration-200 shadow-md shadow-indigo-600/10 flex items-center gap-1.5">
                Save and Add Book
            </button>
        </div>
    </div>
</form>

<script>
    // Live Cover Image Preview
    const coverInput = document.getElementById('cover_image');
    const previewContainer = document.getElementById('cover-preview-container');
    const previewImg = document.getElementById('cover-preview-img');

    coverInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImg.src = e.target.result;
                previewImg.classList.remove('hidden');
                previewContainer.classList.add('border-solid', 'border-slate-800');
            }
            reader.readAsDataURL(file);
        } else {
            previewImg.src = '';
            previewImg.classList.add('hidden');
            previewContainer.classList.remove('border-solid', 'border-slate-800');
        }
    });
</script>
@endsection
