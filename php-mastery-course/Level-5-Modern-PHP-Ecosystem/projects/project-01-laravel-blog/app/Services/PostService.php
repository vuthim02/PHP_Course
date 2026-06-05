<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostService
{
    public function createPost(array $data): Post
    {
        if (isset($data['featured_image']) && $data['featured_image'] instanceof UploadedFile) {
            $data['featured_image'] = $this->uploadImage($data['featured_image']);
        }

        $data['author_id'] = auth()->id();
        $data['is_published'] = $data['is_published'] ?? false;
        $data['published_at'] = $data['is_published']
            ? ($data['published_at'] ?? now())
            : null;

        $post = Post::create($data);

        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }

        return $post;
    }

    public function updatePost(Post $post, array $data): Post
    {
        if (isset($data['featured_image']) && $data['featured_image'] instanceof UploadedFile) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $data['featured_image'] = $this->uploadImage($data['featured_image']);
        }

        $data['is_published'] = $data['is_published'] ?? false;
        $data['published_at'] = $data['is_published']
            ? ($data['published_at'] ?? $post->published_at ?? now())
            : null;

        $post->update($data);

        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }

        return $post->fresh();
    }

    public function deletePost(Post $post): void
    {
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }
        $post->comments()->delete();
        $post->tags()->detach();
        $post->delete();
    }

    private function uploadImage(UploadedFile $image): string
    {
        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
        return $image->storeAs('posts/images', $filename, 'public');
    }
}
