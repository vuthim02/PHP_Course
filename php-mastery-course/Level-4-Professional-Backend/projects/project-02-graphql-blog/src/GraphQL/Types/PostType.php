<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class PostType extends ObjectType
{
    private static ?PostType $instance = null;
    private static ?ObjectType $userType = null;
    private static ?ObjectType $commentType = null;
    private static ?ObjectType $tagType = null;

    public function __construct()
    {
        self::$userType = UserType::get();
        self::$commentType = CommentType::get();
        self::$tagType = TagType::get();

        parent::__construct([
            'name' => 'Post',
            'description' => 'A blog post',
            'fields' => [
                'id' => Type::nonNull(Type::int()),
                'title' => Type::nonNull(Type::string()),
                'content' => Type::nonNull(Type::string()),
                'excerpt' => [
                    'type' => Type::string(),
                    'resolve' => function (array $post) {
                        return strlen($post['content']) > 200
                            ? substr($post['content'], 0, 200) . '...'
                            : $post['content'];
                    },
                ],
                'author' => [
                    'type' => self::$userType,
                    'resolve' => function (array $post) {
                        $userModel = new User();
                        return $userModel->findById((int) $post['author_id']);
                    },
                ],
                'comments' => [
                    'type' => Type::listOf(self::$commentType),
                    'resolve' => function (array $post) {
                        $postModel = new Post();
                        return $postModel->getComments((int) $post['id'])['items'] ?? [];
                    },
                ],
                'tags' => [
                    'type' => Type::listOf(self::$tagType),
                    'resolve' => function (array $post) {
                        $postModel = new Post();
                        return $postModel->getTags((int) $post['id']);
                    },
                ],
                'created_at' => Type::nonNull(Type::int()),
                'updated_at' => Type::nonNull(Type::int()),
            ],
        ]);
    }

    public static function get(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
