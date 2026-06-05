@extends('layouts.app')

@section('title', isset($searchQuery) ? 'Search: ' . $searchQuery : 'Posts')
@section('meta_description', 'Browse all blog posts')

@section('content')
<div class="flex flex-col lg:flex-row gap-8">
    <div class="lg:w-3/4">
        @if(isset($searchQuery))
            <h1 class="text-3xl font-bold mb-6">Search results for "{{ $searchQuery }}"</h1>
        @else
            <h1 class="text-3xl font-bold mb-6">Latest Posts</h1>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($posts as $post)
                <article class="bg-white rounded-lg shadow-md overflow-hidden">
                    @if($post->featured_image)
                        <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                    @endif
                    <div class="p-6">
                        <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                            @if($post->category)
                                <a href="{{ route('categories.show', $post->category) }}" class="text-blue-600 hover:text-blue-800">{{ $post->category->name }}</a>
                                <span>&middot;</span>
                            @endif
                            <time>{{ $post->published_at->format('M j, Y') }}</time>
                        </div>
                        <h2 class="text-xl font-semibold mb-2">
                            <a href="{{ route('posts.show', $post) }}" class="text-gray-900 hover:text-blue-600">{{ $post->title }}</a>
                        </h2>
                        <p class="text-gray-600 mb-4">{{ $post->excerpt }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">By {{ $post->author->name }}</span>
                            <div class="flex gap-1">
                                @foreach($post->tags as $tag)
                                    <a href="{{ route('tags.show', $tag) }}" class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded hover:bg-gray-200">#{{ $tag->name }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full text-center py-12 text-gray-500">
                    No posts found.
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    </div>

    <aside class="lg:w-1/4">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="font-semibold text-lg mb-4">Categories</h3>
            <ul class="space-y-2">
                @foreach($categories as $category)
                    <li>
                        <a href="{{ route('categories.show', $category) }}" class="text-gray-600 hover:text-blue-600 flex justify-between">
                            <span>{{ $category->name }}</span>
                            <span class="text-sm text-gray-400">({{ $category->publishedPosts->count() }})</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="font-semibold text-lg mb-4">Tags</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($tags as $tag)
                    <a href="{{ route('tags.show', $tag) }}" class="text-sm bg-gray-100 text-gray-600 px-3 py-1 rounded-full hover:bg-blue-100 hover:text-blue-600">{{ $tag->name }}</a>
                @endforeach
            </div>
        </div>
    </aside>
</div>
@endsection
