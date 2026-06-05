@extends('layouts.app')

@section('title', 'Edit Post')

@section('content')
<h1 class="text-3xl font-bold mb-6">Edit Post</h1>

<form method="POST" action="{{ route('admin.posts.update', $post) }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow-md p-8">
    @csrf @method('PATCH')

    <div class="mb-4">
        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
        <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
    </div>

    <div class="mb-4">
        <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
        <select name="category_id" id="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ (old('category_id', $post->category_id) == $category->id) ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label for="body" class="block text-sm font-medium text-gray-700">Body</label>
        <textarea name="body" id="body" rows="15" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm tinymce">{{ old('body', $post->body) }}</textarea>
    </div>

    <div class="mb-4">
        <label for="excerpt" class="block text-sm font-medium text-gray-700">Excerpt</label>
        <textarea name="excerpt" id="excerpt" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('excerpt', $post->excerpt) }}</textarea>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Current Featured Image</label>
        @if($post->featured_image)
            <img src="{{ Storage::url($post->featured_image) }}" class="h-32 mt-2 mb-2 rounded">
        @endif
        <input type="file" name="featured_image" class="mt-1 block w-full">
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Tags</label>
        <div class="mt-2 grid grid-cols-3 gap-2">
            @foreach($tags as $tag)
                <label class="flex items-center">
                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="rounded border-gray-300 text-blue-600"
                        {{ in_array($tag->id, old('tags', $post->tags->pluck('id')->toArray())) ? 'checked' : '' }}>
                    <span class="ml-2 text-sm">{{ $tag->name }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <div class="mb-4 flex items-center">
        <input type="checkbox" name="is_published" id="is_published" value="1" class="rounded border-gray-300 text-blue-600" {{ old('is_published', $post->is_published) ? 'checked' : '' }}>
        <label for="is_published" class="ml-2 text-sm font-medium text-gray-700">Published</label>
    </div>

    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Update Post</button>
</form>

@push('scripts')
<script src="https://cdn.tiny.cloud/1/your-api-key/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '.tinymce',
    plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
    toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
    height: 500,
});
</script>
@endpush
@endsection
