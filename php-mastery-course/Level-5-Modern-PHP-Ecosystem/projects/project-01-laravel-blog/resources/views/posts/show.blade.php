@extends('layouts.app')

@section('title', $post->title)
@section('meta_description', $post->getMetaDescription())

@section('content')
<article class="bg-white rounded-lg shadow-md overflow-hidden">
    @if($post->featured_image)
        <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-96 object-cover">
    @endif

    <div class="p-8">
        <div class="flex items-center gap-3 text-sm text-gray-500 mb-4">
            @if($post->category)
                <a href="{{ route('categories.show', $post->category) }}" class="text-blue-600 hover:text-blue-800">{{ $post->category->name }}</a>
                <span>&middot;</span>
            @endif
            <time>{{ $post->published_at->format('F j, Y') }}</time>
            <span>&middot;</span>
            <span>{{ $post->author->name }}</span>
            <span>&middot;</span>
            <span>{{ $post->comments->count() }} comments</span>
        </div>

        <h1 class="text-4xl font-bold mb-6">{{ $post->title }}</h1>

        <div class="prose max-w-none mb-8">
            {!! $post->body !!}
        </div>

        <div class="flex flex-wrap gap-2 mb-8">
            @foreach($post->tags as $tag)
                <a href="{{ route('tags.show', $tag) }}" class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-sm hover:bg-blue-100 hover:text-blue-600">#{{ $tag->name }}</a>
            @endforeach
        </div>
    </div>
</article>

@if($relatedPosts->isNotEmpty())
    <section class="mt-12">
        <h2 class="text-2xl font-bold mb-6">Related Posts</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($relatedPosts as $related)
                <article class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-4">
                        <h3 class="font-semibold">
                            <a href="{{ route('posts.show', $related) }}" class="text-gray-900 hover:text-blue-600">{{ $related->title }}</a>
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $related->excerpt }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endif

<section class="mt-12">
    <h2 class="text-2xl font-bold mb-6">Comments ({{ $post->approvedComments->count() }})</h2>

    @foreach($post->approvedComments as $comment)
        <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
            <div class="flex items-center justify-between mb-2">
                <span class="font-semibold">{{ $comment->user->name }}</span>
                <span class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
            </div>
            <div class="text-gray-700">{!! $comment->body !!}</div>
        </div>
    @endforeach

    @auth
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h3 class="text-lg font-semibold mb-4">Leave a Comment</h3>
            <form method="POST" action="{{ route('comments.store', $post) }}">
                @csrf
                <div class="mb-4">
                    <textarea name="body" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Write your comment..." required></textarea>
                    @error('body')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Submit Comment</button>
            </form>
        </div>
    @else
        <div class="bg-gray-100 rounded-lg p-6 mt-6 text-center">
            <p class="text-gray-600"><a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800">Login</a> to leave a comment.</p>
        </div>
    @endauth
</section>
@endsection
