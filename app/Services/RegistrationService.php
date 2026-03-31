<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\RegistrationRepository;
use App\Repositories\UserRepository;
use App\Support\LegacyPassword;
use DomainException;

class RegistrationService
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly RegistrationRepository $registrations,
    ) {
    }

    public function register(array $input): int
    {
        $ime = $this->normalizeName((string) ($input['ime'] ?? ''));
        $prezime = $this->normalizeName((string) ($input['prezime'] ?? ''));
        $email = mb_strtolower(trim((string) ($input['email'] ?? '')));
        $lozinka1 = (string) ($input['lozinka1'] ?? '');
        $lozinka2 = (string) ($input['lozinka2'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new DomainException('Email nije ispravan.');
        }

        if ($this->users->findByEmail($email) !== null) {
            throw new DomainException('Korisnik s tim emailom već postoji.');
        }

        if (!preg_match('/^[a-zA-ZčšžđćČŠŽĐĆ ]+$/u', $ime)) {
            throw new DomainException('Ime sadrži nedozvoljene znakove.');
        }

        if (!preg_match('/^[a-zA-ZčšžđćČŠŽĐĆ ]+$/u', $prezime)) {
            throw new DomainException('Prezime sadrži nedozvoljene znakove.');
        }

        if ($lozinka1 !== $lozinka2) {
            throw new DomainException('Lozinka nije ispravno potvrđena.');
        }

        if (!preg_match('/^(?=.*\d)(?=.*[A-Za-z]).{6,30}$/', $lozinka1)) {
            throw new DomainException('Lozinka mora imati 6-30 znakova i sadržavati slovo i broj.');
        }

        $userId = $this->users->create([
            'ime' => $ime,
            'prezime' => $prezime,
            'spol' => $input['spol'] ?? 'm',
            'god_rodenja' => $input['god_rodenja'] ?? null,
            'email' => $email,
            'pozivni' => $input['pozivni'] ?? null,
            'telefon' => $input['telefon'] ?? null,
            'lozinka' => LegacyPassword::make($lozinka1),
            'org_jed' => $input['org_jed'] ?? null,
            'konf_br' => $input['konf_br'] ?? null,
            'tip_korisnika' => 3,
            'aktivacija' => bin2hex(random_bytes(32)),
        ]);

        $this->registrations->ensureRegistrationRow($userId);

        return $userId;
    }

    private function normalizeName(string $value): string
    {
        return mb_convert_case(trim($value), MB_CASE_TITLE, 'UTF-8');
    }
}
