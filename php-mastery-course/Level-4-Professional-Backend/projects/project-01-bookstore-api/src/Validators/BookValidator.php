<?php

declare(strict_types=1);

namespace App\Validators;

class BookValidator
{
    public static function validateCreate(array $data): array
    {
        $errors = [];

        if (empty($data['title'])) {
            $errors['title'] = 'Title is required';
        } elseif (strlen($data['title']) > 255) {
            $errors['title'] = 'Title must not exceed 255 characters';
        }

        if (empty($data['author_id'])) {
            $errors['author_id'] = 'Author ID is required';
        } elseif (!is_numeric($data['author_id'])) {
            $errors['author_id'] = 'Author ID must be a number';
        }

        if (empty($data['category_id'])) {
            $errors['category_id'] = 'Category ID is required';
        } elseif (!is_numeric($data['category_id'])) {
            $errors['category_id'] = 'Category ID must be a number';
        }

        if (empty($data['isbn'])) {
            $errors['isbn'] = 'ISBN is required';
        } elseif (!preg_match('/^(?:\d{10}|\d{13})$/', str_replace('-', '', $data['isbn']))) {
            $errors['isbn'] = 'Invalid ISBN format (10 or 13 digits)';
        }

        if (!isset($data['price'])) {
            $errors['price'] = 'Price is required';
        } elseif (!is_numeric($data['price']) || (float) $data['price'] <= 0) {
            $errors['price'] = 'Price must be a positive number';
        }

        if (!isset($data['stock'])) {
            $errors['stock'] = 'Stock is required';
        } elseif (!is_numeric($data['stock']) || (int) $data['stock'] < 0) {
            $errors['stock'] = 'Stock must be a non-negative integer';
        }

        if (empty($data['description'])) {
            $errors['description'] = 'Description is required';
        } elseif (strlen($data['description']) > 5000) {
            $errors['description'] = 'Description must not exceed 5000 characters';
        }

        return $errors;
    }

    public static function validateUpdate(array $data): array
    {
        $errors = [];

        if (isset($data['title']) && strlen($data['title']) > 255) {
            $errors['title'] = 'Title must not exceed 255 characters';
        }

        if (isset($data['isbn'])) {
            $cleaned = str_replace('-', '', $data['isbn']);
            if (!preg_match('/^(?:\d{10}|\d{13})$/', $cleaned)) {
                $errors['isbn'] = 'Invalid ISBN format (10 or 13 digits)';
            }
        }

        if (isset($data['price']) && (!is_numeric($data['price']) || (float) $data['price'] <= 0)) {
            $errors['price'] = 'Price must be a positive number';
        }

        if (isset($data['stock']) && (!is_numeric($data['stock']) || (int) $data['stock'] < 0)) {
            $errors['stock'] = 'Stock must be a non-negative integer';
        }

        if (isset($data['description']) && strlen($data['description']) > 5000) {
            $errors['description'] = 'Description must not exceed 5000 characters';
        }

        return $errors;
    }
}
