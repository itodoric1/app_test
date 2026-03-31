<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

class ProfileRepository
{
    public function __construct(private readonly Database $db)
    {
    }

    public function findProfile(int $userId): ?array
    {
        return $this->db->first(
            'SELECT id, ime, prezime, spol, god_rodenja, email, pozivni, telefon, org_jed, konf_br, tip_korisnika, aktivacija, datum_promijene, datum_kreiranja
             FROM korisnici
             WHERE id = :id
             LIMIT 1',
            ['id' => $userId]
        );
    }

    public function updateField(int $userId, string $field, mixed $value): void
    {
        $allowed = ['ime', 'prezime', 'god_rodenja', 'email', 'pozivni', 'telefon', 'org_jed', 'konf_br', 'lozinka'];
        if (!in_array($field, $allowed, true)) {
            throw new \InvalidArgumentException("Field [$field] is not allowed for update.");
        }

        $sql = sprintf('UPDATE korisnici SET %s = :value, datum_promijene = NOW() WHERE id = :id', $field);
        $this->db->execute($sql, ['value' => $value, 'id' => $userId]);
    }

    public function updatePhone(int $userId, ?string $prefix, ?string $phone): void
    {
        $this->db->execute(
            'UPDATE korisnici SET pozivni = :pozivni, telefon = :telefon, datum_promijene = NOW() WHERE id = :id',
            ['pozivni' => $prefix, 'telefon' => $phone, 'id' => $userId]
        );
    }

    public function deleteUser(int $userId): void
    {
        $pdo = $this->db->pdo();
        $pdo->beginTransaction();

        try {
            $this->db->execute('DELETE FROM prijave_zbirno WHERE korisnik_id = :id', ['id' => $userId]);
            $this->db->execute('DELETE FROM prijava WHERE korisnik_id = :id', ['id' => $userId]);
            $this->db->execute('DELETE FROM korisnici WHERE id = :id', ['id' => $userId]);
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
