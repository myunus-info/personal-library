@extends('layouts.app')

@section('title', 'Manage Categories & Tags')
@section('page_title', 'Categories & Tags')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Tag Creation / Editing Card -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl h-fit">
        <h2 class="text-base font-bold text-slate-200 mb-4" id="form-title">Create New Category/Tag</h2>
        
        <form id="tag-form" action="/tags" method="POST" class="space-y-5">
            <!-- Name Input -->
            <div>
                <label for="tag_name" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Category Name</label>
                <input type="text" id="tag_name" name="name" required value="{{ $old['name'] ?? '' }}"
                    class="block w-full px-4 py-2.5 bg-slate-950/60 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition duration-200 text-sm"
                    placeholder="e.g. Fiction, History, To Read">
                @if(isset($errors['name']))
                    <p class="mt-1 text-xs text-rose-500">{{ $errors['name'][0] }}</p>
                @endif
            </div>

            <!-- Color Input -->
            <div>
                <label for="tag_color" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Tag Color</label>
                <div class="flex items-center gap-3">
                    <input type="color" id="tag_color" name="color" value="{{ $old['color'] ?? '#3b82f6' }}"
                        class="w-10 h-10 bg-transparent border-0 cursor-pointer focus:outline-none rounded">
                    <input type="text" id="tag_color_text" readonly value="{{ $old['color'] ?? '#3b82f6' }}"
                        class="block w-full px-4 py-2 bg-slate-950/60 border border-slate-800 rounded-xl text-slate-400 text-sm select-all">
                </div>
                @if(isset($errors['color']))
                    <p class="mt-1 text-xs text-rose-500">{{ $errors['color'][0] }}</p>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-2">
                <button type="submit" id="submit-btn"
                    class="flex-1 py-2.5 px-4 bg-indigo-600 hover:bg-indigo-500 font-bold text-white rounded-xl text-sm transition duration-200 shadow-md shadow-indigo-600/10">
                    Create Category
                </button>
                <button type="button" id="cancel-btn" class="hidden py-2.5 px-4 bg-slate-800 hover:bg-slate-700 font-bold text-slate-300 rounded-xl text-sm transition duration-200">
                    Cancel
                </button>
            </div>
        </form>
    </div>

    <!-- Tags List Column -->
    <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h2 class="text-base font-bold text-slate-200 mb-4">Existing Categories & Tags</h2>
        
        @if(empty($tags))
            <div class="text-center py-12 border-2 border-dashed border-slate-800 rounded-2xl">
                <svg class="w-12 h-12 text-slate-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <p class="text-slate-500 text-sm">No categories or tags created yet.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($tags as $tag)
                    <div class="flex items-center justify-between p-4 bg-slate-950/40 border border-slate-800/80 rounded-2xl hover:border-slate-700 transition duration-200 group">
                        <div class="flex items-center gap-3">
                            <span class="w-3.5 h-3.5 rounded-full border border-white/10" style="background-color: {{ $tag['color'] }}"></span>
                            <div>
                                <h4 class="text-sm font-semibold text-slate-200">{{ $tag['name'] }}</h4>
                                <p class="text-xs text-slate-500">{{ $tag['color'] }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition duration-150">
                            <!-- Edit Trigger Button -->
                            <button type="button" 
                                onclick="editTag({{ $tag['id'] }}, '{{ addslashes($tag['name']) }}', '{{ $tag['color'] }}')"
                                class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg hover:text-slate-100 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            
                            <!-- Delete Button -->
                            <form action="/tags/{{ $tag['id'] }}/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this category/tag? Books using it will lose this tag association.');">
                                <button type="submit" class="p-1.5 bg-slate-800 hover:bg-rose-500/10 text-slate-400 hover:text-rose-400 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

<script>
    // Link Color picker to Text Input
    const colorPicker = document.getElementById('tag_color');
    const colorText = document.getElementById('tag_color_text');
    
    colorPicker.addEventListener('input', (e) => {
        colorText.value = e.target.value;
    });

    // Handle Tag Editing Setup
    function editTag(id, name, color) {
        const form = document.getElementById('tag-form');
        const formTitle = document.getElementById('form-title');
        const submitBtn = document.getElementById('submit-btn');
        const cancelBtn = document.getElementById('cancel-btn');
        const tagInput = document.getElementById('tag_name');
        
        form.action = '/tags/' + id;
        formTitle.textContent = 'Edit Category: ' + name;
        submitBtn.textContent = 'Save Changes';
        cancelBtn.classList.remove('hidden');
        
        tagInput.value = name;
        colorPicker.value = color;
        colorText.value = color;
        
        tagInput.focus();
    }

    // Handle Edit Cancel
    document.getElementById('cancel-btn').addEventListener('click', () => {
        const form = document.getElementById('tag-form');
        const formTitle = document.getElementById('form-title');
        const submitBtn = document.getElementById('submit-btn');
        const cancelBtn = document.getElementById('cancel-btn');
        const tagInput = document.getElementById('tag_name');
        
        form.action = '/tags';
        formTitle.textContent = 'Create New Category/Tag';
        submitBtn.textContent = 'Create Category';
        cancelBtn.classList.add('hidden');
        
        tagInput.value = '';
        colorPicker.value = '#3b82f6';
        colorText.value = '#3b82f6';
    });
</script>
@endsection
