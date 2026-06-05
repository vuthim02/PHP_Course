<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Click
{
    public ?int $id = null;
    public int $link_id;
    public string $ip_address;
    public string $referer;
    public string $user_agent;
    public ?string $clicked_at = null;

    public static function findByLink(int $linkId, int $limit = 50): array
    {
        return array_map(fn($r) => self::hydrate($r),
            Database::getInstance()->fetchAll(
                'SELECT * FROM clicks WHERE link_id = ? ORDER BY clicked_at DESC LIMIT ?',
                [$linkId, $limit]
            )
        );
    }

    public static function countByLink(int $linkId): int
    {
        $r = Database::getInstance()->fetch('SELECT COUNT(*) as c FROM clicks WHERE link_id = ?', [$linkId]);
        return (int) ($r->c ?? 0);
    }

    public static function refererStats(int $linkId): array
    {
        return Database::getInstance()->fetchAll(
            'SELECT referer, COUNT(*) as count FROM clicks WHERE link_id = ? GROUP BY referer ORDER BY count DESC LIMIT 10',
            [$linkId]
        );
    }

    private static function hydrate(object $d): self
    {
        $c = new self();
        $c->id = (int) $d->id;
        $c->link_id = (int) $d->link_id;
        $c->ip_address = $d->ip_address;
        $c->referer = $d->referer;
        $c->user_agent = $d->user_agent;
        $c->clicked_at = $d->clicked_at ?? null;
        return $c;
    }
}
