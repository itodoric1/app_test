<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

class OrganizationRepository
{
    public function __construct(private readonly Database $db)
    {
    }

    public function all(): array
    {
        return $this->db->all('SELECT id, name FROM organizacija ORDER BY name ASC');
    }

    public function exists(int $id): bool
    {
        return $this->db->first('SELECT id FROM organizacija WHERE id = :id LIMIT 1', ['id' => $id]) !== null;
    }
}
