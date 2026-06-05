@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<h1 class="text-3xl font-bold mb-8">Admin Dashboard</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-sm font-medium text-gray-500">Total Posts</h3>
        <p class="text-3xl font-bold text-gray-900">{{ $stats['posts_count'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-sm font-medium text-gray-500">Published Posts</h3>
        <p class="text-3xl font-bold text-green-600">{{ $stats['published_posts'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-sm font-medium text-gray-500">Pending Comments</h3>
        <p class="text-3xl font-bold text-orange-600">{{ $stats['pending_comments'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-sm font-medium text-gray-500">Users</h3>
        <p class="text-3xl font-bold text-blue-600">{{ $stats['users_count'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-sm font-medium text-gray-500">Categories</h3>
        <p class="text-3xl font-bold text-purple-600">{{ $stats['categories_count'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-sm font-medium text-gray-500">Tags</h3>
        <p class="text-3xl font-bold text-indigo-600">{{ $stats['tags_count'] }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Recent Posts</h2>
            <a href="{{ route('admin.posts.index') }}" class="text-blue-600 text-sm hover:underline">View All</a>
        </div>
        <ul class="space-y-3">
            @foreach($stats['recent_posts'] as $post)
                <li class="flex justify-between items-center">
                    <span class="text-sm text-gray-700 truncate">{{ $post->title }}</span>
                    <span class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Recent Comments</h2>
        </div>
        <ul class="space-y-3">
            @foreach($stats['recent_comments'] as $comment)
                <li class="text-sm">
                    <span class="font-medium">{{ $comment->user->name }}</span>
                    <span class="text-gray-500">on</span>
                    <span class="text-blue-600">{{ $comment->post->title }}</span>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                        @unless($comment->is_approved)
                            <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded">Pending</span>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
