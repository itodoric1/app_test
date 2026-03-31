<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;
use App\Models\User;
use App\Repositories\RegistrationRepository;
use App\Repositories\UserRepository;
use App\Support\LegacyPassword;
use DomainException;

class AuthService
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly RegistrationRepository $registrations,
        private readonly Session $session,
    ) {
    }

    public function login(string $email, string $password): User
    {
        $row = $this->users->findByEmail($email);

        if ($row === null) {
            throw new DomainException('Neispravan email ili lozinka.');
        }

        if (!empty($row['aktivacija'])) {
            throw new DomainException('Korisnički račun još nije aktiviran.');
        }

        if (!LegacyPassword::verify($password, (string) $row['lozinka'])) {
            throw new DomainException('Neispravan email ili lozinka.');
        }

        if (LegacyPassword::needsRehash((string) $row['lozinka'])) {
            $this->users->updatePassword((int) $row['id'], LegacyPassword::make($password));
        }

        $this->session->put('fs_korisnik_id', (int) $row['id']);
        $this->session->put('fs_korisnik', (string) $row['email']);
        $this->registrations->ensureRegistrationRow((int) $row['id']);

        return User::fromArray($row);
    }

    public function logout(): void
    {
        $this->session->forget(['fs_korisnik', 'fs_korisnik_id']);
        $this->session->invalidate();
    }

    public function userId(): ?int
    {
        $id = $this->session->get('fs_korisnik_id');
        return $id === null ? null : (int) $id;
    }
}
