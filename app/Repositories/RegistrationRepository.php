<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

class RegistrationRepository
{
    public function __construct(private readonly Database $db)
    {
    }

    public function ensureRegistrationRow(int $userId): void
    {
        $exists = $this->db->first(
            'SELECT id FROM prijava WHERE korisnik_id = :korisnik_id LIMIT 1',
            ['korisnik_id' => $userId]
        );

        if ($exists !== null) {
            return;
        }

        $this->db->execute(
            'INSERT INTO prijava (korisnik_id, datum_promijene, datum_upisa) VALUES (:korisnik_id, NOW(), NOW())',
            ['korisnik_id' => $userId]
        );
    }
}
