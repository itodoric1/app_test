<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

class SelectionRepository
{
    public function __construct(private readonly Database $db)
    {
    }

    public function getSelectionStatus(): string
    {
        return (string) (($this->db->first(
            'SELECT vrijednost FROM postavke WHERE naziv = :naziv LIMIT 1',
            ['naziv' => 'selekcija']
        )['vrijednost'] ?? 'otvoreno'));
    }

    public function statsTotals(): array
    {
        return [
            'registered_women' => (int) (($this->db->first(
                "SELECT COUNT(DISTINCT k.id) AS total
                 FROM korisnici k
                 INNER JOIN prijava p ON p.korisnik_id = k.id
                 WHERE k.tip_korisnika <> 1 AND p.status_upisa = 1 AND k.spol = 'f'"
            )['total'] ?? 0)),
            'registered_men' => (int) (($this->db->first(
                "SELECT COUNT(DISTINCT k.id) AS total
                 FROM korisnici k
                 INNER JOIN prijava p ON p.korisnik_id = k.id
                 WHERE k.tip_korisnika <> 1 AND p.status_upisa = 1 AND k.spol = 'm'"
            )['total'] ?? 0)),
            'selected_women' => (int) (($this->db->first(
                "SELECT COUNT(DISTINCT k.id) AS total
                 FROM prijave_zbirno pz
                 INNER JOIN korisnici k ON k.id = pz.korisnik_id
                 WHERE pz.odabran = 'da' AND k.spol = 'f'"
            )['total'] ?? 0)),
            'selected_men' => (int) (($this->db->first(
                "SELECT COUNT(DISTINCT k.id) AS total
                 FROM prijave_zbirno pz
                 INNER JOIN korisnici k ON k.id = pz.korisnik_id
                 WHERE pz.odabran = 'da' AND k.spol = 'm'"
            )['total'] ?? 0)),
        ];
    }

    public function statsByOrganization(): array
    {
        return $this->db->all(
            "SELECT
                o.id,
                o.name,
                o.sub_id,
                COALESCE(rw.total, 0) AS registered_women,
                COALESCE(rm.total, 0) AS registered_men,
                COALESCE(sw.total, 0) AS selected_women,
                COALESCE(sm.total, 0) AS selected_men
             FROM organizacija o
             LEFT JOIN (
                SELECT k.org_jed, COUNT(DISTINCT k.id) AS total
                FROM korisnici k
                INNER JOIN prijava p ON p.korisnik_id = k.id
                WHERE p.status_upisa = 1 AND k.tip_korisnika <> 1 AND k.spol = 'f'
                GROUP BY k.org_jed
             ) rw ON rw.org_jed = o.id
             LEFT JOIN (
                SELECT k.org_jed, COUNT(DISTINCT k.id) AS total
                FROM korisnici k
                INNER JOIN prijava p ON p.korisnik_id = k.id
                WHERE p.status_upisa = 1 AND k.tip_korisnika <> 1 AND k.spol = 'm'
                GROUP BY k.org_jed
             ) rm ON rm.org_jed = o.id
             LEFT JOIN (
                SELECT k.org_jed, COUNT(DISTINCT k.id) AS total
                FROM prijave_zbirno pz
                INNER JOIN korisnici k ON k.id = pz.korisnik_id
                WHERE pz.odabran = 'da' AND k.spol = 'f'
                GROUP BY k.org_jed
             ) sw ON sw.org_jed = o.id
             LEFT JOIN (
                SELECT k.org_jed, COUNT(DISTINCT k.id) AS total
                FROM prijave_zbirno pz
                INNER JOIN korisnici k ON k.id = pz.korisnik_id
                WHERE pz.odabran = 'da' AND k.spol = 'm'
                GROUP BY k.org_jed
             ) sm ON sm.org_jed = o.id
             ORDER BY o.sub_id ASC, o.name ASC"
        );
    }

    public function statsBySport(): array
    {
        return $this->db->all(
            "SELECT
                d.id,
                d.naziv,
                COALESCE(rw.total, 0) AS registered_women,
                COALESCE(rm.total, 0) AS registered_men,
                COALESCE(sw.total, 0) AS selected_women,
                COALESCE(sm.total, 0) AS selected_men
             FROM discipline d
             LEFT JOIN (
                SELECT pz.sport_id, COUNT(*) AS total
                FROM prijave_zbirno pz
                INNER JOIN korisnici k ON k.id = pz.korisnik_id
                WHERE pz.sport_id IS NOT NULL AND k.spol = 'f'
                GROUP BY pz.sport_id
             ) rw ON rw.sport_id = d.id
             LEFT JOIN (
                SELECT pz.sport_id, COUNT(*) AS total
                FROM prijave_zbirno pz
                INNER JOIN korisnici k ON k.id = pz.korisnik_id
                WHERE pz.sport_id IS NOT NULL AND k.spol = 'm'
                GROUP BY pz.sport_id
             ) rm ON rm.sport_id = d.id
             LEFT JOIN (
                SELECT pz.sport_id, COUNT(*) AS total
                FROM prijave_zbirno pz
                INNER JOIN korisnici k ON k.id = pz.korisnik_id
                WHERE pz.sport_id IS NOT NULL AND pz.odabran = 'da' AND k.spol = 'f'
                GROUP BY pz.sport_id
             ) sw ON sw.sport_id = d.id
             LEFT JOIN (
                SELECT pz.sport_id, COUNT(*) AS total
                FROM prijave_zbirno pz
                INNER JOIN korisnici k ON k.id = pz.korisnik_id
                WHERE pz.sport_id IS NOT NULL AND pz.odabran = 'da' AND k.spol = 'm'
                GROUP BY pz.sport_id
             ) sm ON sm.sport_id = d.id
             ORDER BY d.naziv ASC"
        );
    }

    public function finalParticipants(): array
    {
        return $this->db->all(
            'SELECT * FROM odabrani
             WHERE sport_1 IS NOT NULL OR sport_2 IS NOT NULL OR sport_3 IS NOT NULL OR sport_4 IS NOT NULL
             ORDER BY org_jed_sub_id ASC, prezime ASC, ime ASC'
        );
    }

    public function vipParticipants(): array
    {
        return $this->db->all(
            'SELECT * FROM odabrani WHERE vip_id = 1 ORDER BY prezime ASC, ime ASC'
        );
    }

    public function selectionCandidates(): array
    {
        return $this->db->all(
            "SELECT
                pz.id,
                pz.korisnik_id,
                pz.broj_sporta,
                pz.odabran,
                pz.opis,
                k.ime,
                k.prezime,
                k.spol,
                k.email,
                k.konf_br,
                o.name AS org_jed,
                o.sub_id AS org_jed_sub_id,
                d.naziv AS sport_naziv
             FROM prijave_zbirno pz
             INNER JOIN korisnici k ON k.id = pz.korisnik_id
             INNER JOIN discipline d ON d.id = pz.sport_id
             INNER JOIN organizacija o ON o.id = k.org_jed
             INNER JOIN prijava p ON p.korisnik_id = k.id
             WHERE p.status_upisa = 1 AND k.tip_korisnika <> 1
             ORDER BY o.sub_id ASC, k.prezime ASC, k.ime ASC, pz.broj_sporta ASC"
        );
    }

    public function setCandidateSelection(int $entryId, string $value): void
    {
        $this->db->execute(
            'UPDATE prijave_zbirno SET odabran = :value WHERE id = :id',
            ['value' => $value, 'id' => $entryId]
        );
    }

    public function candidateById(int $entryId): ?array
    {
        return $this->db->first(
            "SELECT pz.id, pz.korisnik_id, pz.broj_sporta, pz.odabran, k.ime, k.prezime, d.naziv AS sport_naziv
             FROM prijave_zbirno pz
             INNER JOIN korisnici k ON k.id = pz.korisnik_id
             INNER JOIN discipline d ON d.id = pz.sport_id
             WHERE pz.id = :id LIMIT 1",
            ['id' => $entryId]
        );
    }
}
