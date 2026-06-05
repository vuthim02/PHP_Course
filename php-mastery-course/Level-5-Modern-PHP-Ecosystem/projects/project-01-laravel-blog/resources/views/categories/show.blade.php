@extends('layouts.app')

@section('title', 'Category: ' . $category->name)
@section('meta_description', $category->description)

@section('content')
<h1 class="text-3xl font-bold mb-2">Category: {{ $category->name }}</h1>
@if($category->description)
    <p class="text-gray-600 mb-8">{{ $category->description }}</p>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($posts as $post)
        <article class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-2">
                    <a href="{{ route('posts.show', $post) }}" class="text-gray-900 hover:text-blue-600">{{ $post->title }}</a>
                </h2>
                <p class="text-gray-600 text-sm mb-4">{{ $post->excerpt }}</p>
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <span>{{ $post->author->name }}</span>
                    <time>{{ $post->published_at->format('M j, Y') }}</time>
                </div>
            </div>
        </article>
    @empty
        <div class="col-span-full text-center py-12 text-gray-500">No posts in this category yet.</div>
    @endforelse
</div>

<div class="mt-8">{{ $posts->links() }}</div>
@endsection
