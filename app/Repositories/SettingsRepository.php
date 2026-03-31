<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

class SettingsRepository
{
    public function __construct(private readonly Database $db)
    {
    }

    public function get(string $naziv, ?string $default = null): ?string
    {
        $row = $this->db->first('SELECT vrijednost FROM postavke WHERE naziv = :naziv LIMIT 1', ['naziv' => $naziv]);
        return $row['vrijednost'] ?? $default;
    }

    public function all(): array
    {
        $rows = $this->db->all('SELECT naziv, vrijednost FROM postavke ORDER BY naziv');
        $settings = [];

        foreach ($rows as $row) {
            $settings[(string) $row['naziv']] = (string) $row['vrijednost'];
        }

        return $settings;
    }

    public function set(string $naziv, string $vrijednost): void
    {
        $this->db->execute(
            'UPDATE postavke SET vrijednost = :vrijednost WHERE naziv = :naziv',
            ['naziv' => $naziv, 'vrijednost' => $vrijednost]
        );
    }
}
