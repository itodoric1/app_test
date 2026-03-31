<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

class AdminRepository
{
    public function __construct(private readonly Database $db)
    {
    }

    public function stats(): array
    {
        return [
            'korisnici' => (int) ($this->db->first('SELECT COUNT(*) AS total FROM korisnici')['total'] ?? 0),
            'aktivni_upisi' => (int) ($this->db->first('SELECT COUNT(*) AS total FROM prijava WHERE status_upisa = 1')['total'] ?? 0),
            'prijave_zbirno' => (int) ($this->db->first('SELECT COUNT(*) AS total FROM prijave_zbirno')['total'] ?? 0),
            'odabrani' => (int) ($this->db->first('SELECT COUNT(*) AS total FROM odabrani')['total'] ?? 0),
        ];
    }

    public function findUserById(int $userId): ?array
    {
        return $this->db->first(
            'SELECT id, ime, prezime, email, tip_korisnika FROM korisnici WHERE id = :id LIMIT 1',
            ['id' => $userId]
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
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    public function deleteAllNonAdmins(): void
    {
        $pdo = $this->db->pdo();
        $pdo->beginTransaction();

        try {
            $this->db->execute('DELETE FROM prijave_zbirno');
            $this->db->execute('DELETE FROM odabrani');
            $this->db->execute('DELETE FROM prijava WHERE korisnik_id NOT IN (SELECT id FROM korisnici WHERE tip_korisnika = 1)');
            $this->db->execute('DELETE FROM korisnici WHERE tip_korisnika <> 1');
            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    public function resetApplications(): void
    {
        $pdo = $this->db->pdo();
        $pdo->beginTransaction();

        try {
            $this->db->execute(
                'UPDATE prijava
                 SET id_sport_1 = NULL,
                     opis_1 = NULL,
                     id_sport_2 = NULL,
                     opis_2 = NULL,
                     id_sport_3 = NULL,
                     opis_3 = NULL,
                     id_sport_4 = NULL,
                     opis_4 = NULL,
                     status_upisa = 0,
                     datum_promijene = NOW()
                 WHERE korisnik_id IN (SELECT id FROM korisnici WHERE tip_korisnika = 3)'
            );

            $this->db->execute('DELETE FROM prijava WHERE korisnik_id = 0');
            $this->db->execute('DELETE FROM prijave_zbirno WHERE broj_sporta > 1');
            $this->db->execute('DELETE FROM prijave_zbirno WHERE korisnik_id IN (SELECT id FROM korisnici WHERE tip_korisnika = 3)');
            $this->db->execute("UPDATE prijave_zbirno SET odabran = 'ne'");
            $this->db->execute('DELETE FROM odabrani');
            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    public function replaceFinalResults(array $vipRows, array $selectedRows): void
    {
        $pdo = $this->db->pdo();
        $pdo->beginTransaction();

        try {
            $this->db->execute('DELETE FROM odabrani');

            $vipSql = 'INSERT INTO odabrani (korisnik_id, ime, prezime, email, pozivni, telefon, spol, org_jed_sub_id, org_jed, konf_br, vip_id)
                       VALUES (:korisnik_id, :ime, :prezime, :email, :pozivni, :telefon, :spol, :org_jed_sub_id, :org_jed, :konf_br, 1)';

            foreach ($vipRows as $row) {
                $this->db->execute($vipSql, [
                    'korisnik_id' => $row['id'],
                    'ime' => $row['ime'],
                    'prezime' => $row['prezime'],
                    'email' => $row['email'],
                    'pozivni' => $row['pozivni'],
                    'telefon' => $row['telefon'],
                    'spol' => $row['spol'],
                    'org_jed_sub_id' => $row['sub_id'],
                    'org_jed' => $row['name'],
                    'konf_br' => $row['konf_br'],
                ]);
            }

            $selectedSql = 'INSERT INTO odabrani (korisnik_id, ime, prezime, email, pozivni, telefon, spol, konf_br, org_jed_sub_id, org_jed, sport_1, sport_2, sport_3, sport_4, vip_id)
                            VALUES (:korisnik_id, :ime, :prezime, :email, :pozivni, :telefon, :spol, :konf_br, :org_jed_sub_id, :org_jed, :sport_1, :sport_2, :sport_3, :sport_4, NULL)';

            foreach ($selectedRows as $row) {
                $this->db->execute($selectedSql, [
                    'korisnik_id' => $row['id'],
                    'ime' => $row['ime'],
                    'prezime' => $row['prezime'],
                    'email' => $row['email'],
                    'pozivni' => $row['pozivni'],
                    'telefon' => $row['telefon'],
                    'spol' => $row['spol'],
                    'konf_br' => $row['konf_br'],
                    'org_jed_sub_id' => $row['sub_id'],
                    'org_jed' => $row['name'],
                    'sport_1' => $row['sport_1'] ?: null,
                    'sport_2' => $row['sport_2'] ?: null,
                    'sport_3' => $row['sport_3'] ?: null,
                    'sport_4' => $row['sport_4'] ?: null,
                ]);
            }

            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    public function fetchVipRows(): array
    {
        return $this->db->all(
            'SELECT k.id, k.ime, k.prezime, k.email, k.pozivni, k.telefon, k.spol, k.konf_br, o.sub_id, o.name
             FROM korisnici k
             INNER JOIN organizacija o ON o.id = k.org_jed
             INNER JOIN prijava p ON p.korisnik_id = k.id
             WHERE p.status_upisa = 1 AND k.tip_korisnika = 1
             ORDER BY k.prezime, k.ime'
        );
    }

    public function fetchSelectedRows(): array
    {
        return $this->db->all(
            'SELECT
                k.id,
                k.ime,
                k.prezime,
                k.email,
                k.pozivni,
                k.telefon,
                k.spol,
                k.konf_br,
                o.sub_id,
                o.name,
                MAX(CASE WHEN pz.broj_sporta = 1 THEN d.naziv END) AS sport_1,
                MAX(CASE WHEN pz.broj_sporta = 2 THEN d.naziv END) AS sport_2,
                MAX(CASE WHEN pz.broj_sporta = 3 THEN d.naziv END) AS sport_3,
                MAX(CASE WHEN pz.broj_sporta = 4 THEN d.naziv END) AS sport_4
             FROM prijave_zbirno pz
             INNER JOIN korisnici k ON k.id = pz.korisnik_id
             INNER JOIN discipline d ON d.id = pz.sport_id
             INNER JOIN organizacija o ON o.id = k.org_jed
             INNER JOIN prijava p ON p.korisnik_id = k.id
             WHERE k.tip_korisnika <> 1
               AND pz.odabran = :odabran
               AND p.status_upisa = 1
             GROUP BY k.id, k.ime, k.prezime, k.email, k.pozivni, k.telefon, k.spol, k.konf_br, o.sub_id, o.name
             ORDER BY k.prezime, k.ime',
            ['odabran' => 'da']
        );
    }
}
