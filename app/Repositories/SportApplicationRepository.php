<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

class SportApplicationRepository
{
    public function __construct(private readonly Database $db)
    {
    }

    public function findByUserId(int $userId): ?array
    {
        return $this->db->first(
            'SELECT korisnik_id, id_sport_1, opis_1, id_sport_2, opis_2, id_sport_3, opis_3, status_upisa, datum_promijene, datum_upisa
             FROM prijava
             WHERE korisnik_id = :korisnik_id
             LIMIT 1',
            ['korisnik_id' => $userId]
        );
    }

    public function ensureRegistrationRow(int $userId): void
    {
        $exists = $this->findByUserId($userId);
        if ($exists !== null) {
            return;
        }

        $this->db->execute(
            'INSERT INTO prijava (korisnik_id, datum_promijene, datum_upisa, status_upisa) VALUES (:korisnik_id, NOW(), NOW(), 0)',
            ['korisnik_id' => $userId]
        );
    }

    public function updateSportSlot(int $userId, int $slot, ?int $disciplineId, ?string $description): void
    {
        if ($slot < 1 || $slot > 3) {
            throw new \InvalidArgumentException('Sport slot must be between 1 and 3.');
        }

        $sportField = 'id_sport_' . $slot;
        $opisField = 'opis_' . $slot;

        $this->db->execute(
            sprintf('UPDATE prijava SET %s = :sport_id, %s = :opis, datum_promijene = NOW() WHERE korisnik_id = :korisnik_id', $sportField, $opisField),
            [
                'sport_id' => $disciplineId,
                'opis' => $description,
                'korisnik_id' => $userId,
            ]
        );

        $this->syncSummarySlot($userId, $slot, $disciplineId, $description);
    }

    public function removeSportSlot(int $userId, int $slot): void
    {
        $this->updateSportSlot($userId, $slot, null, null);
    }

    private function syncSummarySlot(int $userId, int $slot, ?int $disciplineId, ?string $description): void
    {
        $existing = $this->db->first(
            'SELECT id FROM prijave_zbirno WHERE korisnik_id = :korisnik_id AND broj_sporta = :broj_sporta LIMIT 1',
            ['korisnik_id' => $userId, 'broj_sporta' => $slot]
        );

        if ($existing === null) {
            $this->db->execute(
                'INSERT INTO prijave_zbirno (broj_sporta, korisnik_id, sport_id, opis) VALUES (:broj_sporta, :korisnik_id, :sport_id, :opis)',
                [
                    'broj_sporta' => $slot,
                    'korisnik_id' => $userId,
                    'sport_id' => $disciplineId,
                    'opis' => $description,
                ]
            );
            return;
        }

        $this->db->execute(
            'UPDATE prijave_zbirno SET sport_id = :sport_id, opis = :opis WHERE korisnik_id = :korisnik_id AND broj_sporta = :broj_sporta',
            [
                'sport_id' => $disciplineId,
                'opis' => $description,
                'korisnik_id' => $userId,
                'broj_sporta' => $slot,
            ]
        );
    }
}
