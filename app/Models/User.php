<?php

declare(strict_types=1);

namespace App\Models;

class User
{
    public function __construct(
        public readonly int $id,
        public readonly string $ime,
        public readonly string $prezime,
        public readonly string $email,
        public readonly ?string $aktivacija,
        public readonly int $tipKorisnika,
    ) {}

    public static function fromArray(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            ime: (string) $row['ime'],
            prezime: (string) $row['prezime'],
            email: (string) $row['email'],
            aktivacija: $row['aktivacija'] ?? null,
            tipKorisnika: (int) ($row['tip_korisnika'] ?? 3),
        );
    }
}
