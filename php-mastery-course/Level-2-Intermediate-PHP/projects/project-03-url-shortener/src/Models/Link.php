<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Link
{
    public ?int $id = null;
    public string $long_url;
    public string $short_code;
    public ?int $user_id = null;
    public ?string $expires_at = null;
    public int $clicks = 0;
    public ?string $created_at = null;

    public static function find(int $id): ?self
    {
        $d = Database::getInstance()->fetch('SELECT * FROM links WHERE id = ?', [$id]);
        return $d ? self::hydrate($d) : null;
    }

    public static function findByCode(string $code): ?self
    {
        $d = Database::getInstance()->fetch('SELECT * FROM links WHERE short_code = ?', [$code]);
        return $d ? self::hydrate($d) : null;
    }

    public static function findByUser(int $userId): array
    {
        return array_map(fn($r) => self::hydrate($r),
            Database::getInstance()->fetchAll('SELECT * FROM links WHERE user_id = ? ORDER BY created_at DESC', [$userId])
        );
    }

    public static function all(): array
    {
        return array_map(fn($r) => self::hydrate($r),
            Database::getInstance()->fetchAll('SELECT l.*, COALESCE(c.click_count, 0) as click_count FROM links l LEFT JOIN (SELECT link_id, COUNT(*) as click_count FROM clicks GROUP BY link_id) c ON l.id = c.link_id ORDER BY l.created_at DESC')
        );
    }

    public function save(): int
    {
        $db = Database::getInstance();
        $data = ['long_url' => $this->long_url, 'short_code' => $this->short_code, 'user_id' => $this->user_id, 'expires_at' => $this->expires_at];
        if ($this->id) {
            $data['clicks'] = $this->clicks;
            $db->update('links', $data, 'id = ?', [$this->id]);
            return $this->id;
        }
        return $db->insert('links', $data);
    }

    public function delete(): void
    {
        Database::getInstance()->delete('links', 'id = ?', [$this->id]);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && strtotime($this->expires_at) < time();
    }

    public function recordClick(): void
    {
        $db = Database::getInstance();
        $db->insert('clicks', [
            'link_id'    => $this->id,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            'referer'    => $_SERVER['HTTP_REFERER'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ]);
        $this->clicks++;
        $db->update('links', ['clicks' => $this->clicks], 'id = ?', [$this->id]);
    }

    public function clickStats(): array
    {
        return Click::findByLink($this->id);
    }

    public static function generateShortCode(int $length = 6): string
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        $existing = self::findByCode($code);
        if ($existing) return self::generateShortCode($length);
        return $code;
    }

    private static function hydrate(object $d): self
    {
        $l = new self();
        $l->id = (int) $d->id;
        $l->long_url = $d->long_url;
        $l->short_code = $d->short_code;
        $l->user_id = isset($d->user_id) ? (int) $d->user_id : null;
        $l->expires_at = $d->expires_at ?? null;
        $l->clicks = (int) ($d->clicks ?? ($d->click_count ?? 0));
        $l->created_at = $d->created_at ?? null;
        return $l;
    }
}
