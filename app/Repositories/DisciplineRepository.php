<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

class DisciplineRepository
{
    public function __construct(private readonly Database $db)
    {
    }

    public function all(): array
    {
        return $this->db->all('SELECT id, naziv FROM discipline ORDER BY naziv ASC');
    }

    public function exists(int $id): bool
    {
        return $this->db->first('SELECT id FROM discipline WHERE id = :id LIMIT 1', ['id' => $id]) !== null;
    }
}
