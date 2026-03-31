<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\User;

class UserRepository
{
    public function __construct(private readonly Database $db)
    {
    }

    public function findByEmail(string $email): ?array
    {
        return $this->db->first(
            'SELECT * FROM korisnici WHERE email = :email LIMIT 1',
            ['email' => mb_strtolower(trim($email))]
        );
    }

    public function findById(int $id): ?User
    {
        $row = $this->db->first('SELECT * FROM korisnici WHERE id = :id LIMIT 1', ['id' => $id]);
        return $row ? User::fromArray($row) : null;
    }

    public function create(array $data): int
    {
        $this->db->execute(
            'INSERT INTO korisnici (ime, prezime, spol, god_rodenja, email, pozivni, telefon, lozinka, org_jed, konf_br, tip_korisnika, aktivacija, datum_promijene, datum_kreiranja)
             VALUES (:ime, :prezime, :spol, :god_rodenja, :email, :pozivni, :telefon, :lozinka, :org_jed, :konf_br, :tip_korisnika, :aktivacija, NOW(), NOW())',
            [
                'ime' => $data['ime'],
                'prezime' => $data['prezime'],
                'spol' => $data['spol'],
                'god_rodenja' => $data['god_rodenja'],
                'email' => $data['email'],
                'pozivni' => $data['pozivni'],
                'telefon' => $data['telefon'],
                'lozinka' => $data['lozinka'],
                'org_jed' => $data['org_jed'],
                'konf_br' => $data['konf_br'],
                'tip_korisnika' => $data['tip_korisnika'] ?? 3,
                'aktivacija' => $data['aktivacija'],
            ]
        );

        return $this->db->lastInsertId();
    }

    public function updatePassword(int $userId, string $hash): void
    {
        $this->db->execute(
            'UPDATE korisnici SET lozinka = :lozinka, datum_promijene = NOW() WHERE id = :id',
            ['lozinka' => $hash, 'id' => $userId]
        );
    }
}
