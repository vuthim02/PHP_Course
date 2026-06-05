<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', config('app.name'))">
    <title>@yield('title', config('app.name'))</title>
    <link rel="alternate" type="application/rss+xml" title="{{ config('app.name') }}" href="{{ route('feed') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-xl font-bold text-gray-900">{{ config('app.name') }}</a>
                    <div class="ml-10 flex space-x-4">
                        <a href="{{ route('posts.index') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2">Posts</a>
                        @foreach(\App\Models\Category::all() as $category)
                            <a href="{{ route('categories.show', $category) }}" class="text-gray-600 hover:text-gray-900 px-2 py-2 text-sm">{{ $category->name }}</a>
                        @endforeach
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <form action="{{ route('search') }}" method="GET" class="hidden md:flex">
                        <input type="text" name="q" placeholder="Search..." class="rounded-lg border-gray-300 text-sm px-3 py-1" value="{{ request('q') }}">
                    </form>
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-gray-900">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-gray-900">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">Login</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto py-6 px-4 text-center text-gray-500 text-sm">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            <a href="{{ route('feed') }}" class="ml-4 text-orange-600 hover:text-orange-800">RSS Feed</a>
        </div>
    </footer>
</body>
</html>
