<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\User;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class UserType extends ObjectType
{
    private static ?UserType $instance = null;

    public function __construct()
    {
        parent::__construct([
            'name' => 'User',
            'description' => 'A blog user',
            'fields' => [
                'id' => Type::nonNull(Type::int()),
                'name' => Type::nonNull(Type::string()),
                'email' => Type::nonNull(Type::string()),
                'postCount' => [
                    'type' => Type::nonNull(Type::int()),
                    'resolve' => function (array $user) {
                        $userModel = new User();
                        return $userModel->getPostCount((int) $user['id']);
                    },
                ],
                'commentCount' => [
                    'type' => Type::nonNull(Type::int()),
                    'resolve' => function (array $user) {
                        $userModel = new User();
                        return $userModel->getCommentCount((int) $user['id']);
                    },
                ],
                'created_at' => Type::nonNull(Type::int()),
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
