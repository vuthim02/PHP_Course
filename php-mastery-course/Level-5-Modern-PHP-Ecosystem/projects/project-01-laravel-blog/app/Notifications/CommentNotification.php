<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Comment $comment
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Comment on "' . $this->comment->post->title . '"')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new comment has been posted on your article:')
            ->line('"' . $this->comment->post->title . '"')
            ->line('---')
            ->line($this->comment->user->name . ' wrote:')
            ->line(strip_tags($this->comment->body))
            ->line('---')
            ->action('View Comment', route('posts.show', $this->comment->post))
            ->line('This comment requires approval before being visible.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'comment_id' => $this->comment->id,
            'comment_body' => $this->comment->body,
            'post_id' => $this->comment->post_id,
            'post_title' => $this->comment->post->title,
            'user_name' => $this->comment->user->name,
        ];
    }
}
