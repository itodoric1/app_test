<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\DisciplineRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\ProfileRepository;
use App\Repositories\SportApplicationRepository;
use App\Support\LegacyPassword;
use DomainException;

class ProfileService
{
    private const PHONE_PREFIXES = ['091', '092', '095', '097', '098', '099'];
    private const SHIRT_SIZES = ['xs', 's', 'm', 'l', 'xl', 'xxl', 'xxxl'];

    public function __construct(
        private readonly ProfileRepository $profiles,
        private readonly OrganizationRepository $organizations,
        private readonly DisciplineRepository $disciplines,
        private readonly SportApplicationRepository $applications,
    ) {
    }

    public function getPageData(int $userId): array
    {
        $profile = $this->profiles->findProfile($userId);
        if ($profile === null) {
            throw new DomainException('Korisnik nije pronađen.');
        }

        $application = $this->applications->findByUserId($userId);
        $this->applications->ensureRegistrationRow($userId);
        $application ??= $this->applications->findByUserId($userId);

        return [
            'profile' => $profile,
            'application' => $application,
            'organizations' => $this->organizations->all(),
            'disciplines' => $this->disciplines->all(),
            'allowedPhonePrefixes' => self::PHONE_PREFIXES,
            'allowedShirtSizes' => self::SHIRT_SIZES,
        ];
    }

    public function updateName(int $userId, string $value): string
    {
        $name = $this->normalizeHumanName($value, 'ime');
        $this->profiles->updateField($userId, 'ime', $name);
        return 'Ime je uspješno promijenjeno.';
    }

    public function updateSurname(int $userId, string $value): string
    {
        $surname = $this->normalizeHumanName($value, 'prezime');
        $this->profiles->updateField($userId, 'prezime', $surname);
        return 'Prezime je uspješno promijenjeno.';
    }

    public function updateBirthYear(int $userId, string $value): string
    {
        $year = (int) trim($value);
        $currentYear = (int) date('Y');

        if (!preg_match('/^\d{4}$/', trim($value)) || $year < 1940 || $year > $currentYear) {
            throw new DomainException('Godina rođenja mora biti ispravna 4-znamenkasta godina.');
        }

        $this->profiles->updateField($userId, 'god_rodenja', $year);
        return 'Godina rođenja je uspješno promijenjena.';
    }

    public function updateEmail(int $userId, string $email): string
    {
        $normalized = mb_strtolower(trim($email));
        if (!filter_var($normalized, FILTER_VALIDATE_EMAIL)) {
            throw new DomainException('Neispravan format e-mail adrese.');
        }

        $this->profiles->updateField($userId, 'email', $normalized);
        return 'E-mail je uspješno promijenjen. Potrebna je ponovna prijava.';
    }

    public function updatePhone(int $userId, ?string $prefix, ?string $phone): string
    {
        $normalizedPrefix = $prefix !== null ? trim($prefix) : null;
        $normalizedPhone = $phone !== null ? trim($phone) : null;

        if ($normalizedPrefix !== null && $normalizedPrefix !== '' && !in_array($normalizedPrefix, self::PHONE_PREFIXES, true)) {
            throw new DomainException('Pozivni broj nije dozvoljen.');
        }

        if ($normalizedPhone !== null && $normalizedPhone !== '' && !preg_match('/^\d{5,7}$/', $normalizedPhone)) {
            throw new DomainException('Telefon mora sadržavati 5 do 7 znamenki.');
        }

        $this->profiles->updatePhone(
            $userId,
            $normalizedPrefix !== '' ? $normalizedPrefix : null,
            $normalizedPhone !== '' ? $normalizedPhone : null,
        );

        return 'Telefon je uspješno promijenjen.';
    }

    public function updateOrganization(int $userId, int $organizationId): string
    {
        if (!$this->organizations->exists($organizationId)) {
            throw new DomainException('Organizacijska jedinica ne postoji.');
        }

        $this->profiles->updateField($userId, 'org_jed', $organizationId);
        return 'Organizacijska jedinica je uspješno promijenjena.';
    }

    public function updateShirtSize(int $userId, string $size): string
    {
        $normalized = mb_strtolower(trim($size));
        if (!in_array($normalized, self::SHIRT_SIZES, true)) {
            throw new DomainException('Konfekcijski broj nije dozvoljen.');
        }

        $this->profiles->updateField($userId, 'konf_br', $normalized);
        return 'Konfekcijski broj je uspješno promijenjen.';
    }

    public function updatePassword(int $userId, string $password, string $confirmation): string
    {
        if ($password !== $confirmation) {
            throw new DomainException('Lozinke se ne podudaraju.');
        }

        if (!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z]{6,30}$/', $password)) {
            throw new DomainException('Lozinka mora imati 6 do 30 znakova i barem jedno slovo i jedan broj.');
        }

        $this->profiles->updateField($userId, 'lozinka', LegacyPassword::hash($password));
        return 'Lozinka je uspješno promijenjena.';
    }

    public function updateSport(int $userId, int $slot, int $disciplineId, string $description): string
    {
        if ($slot < 1 || $slot > 3) {
            throw new DomainException('Neispravan redni broj sporta.');
        }

        if (!$this->disciplines->exists($disciplineId)) {
            throw new DomainException('Sportska disciplina ne postoji.');
        }

        $normalizedDescription = trim($description);
        if ($normalizedDescription === '') {
            throw new DomainException('Opis iskustva je obavezan.');
        }

        if (mb_strlen($normalizedDescription) > 300) {
            throw new DomainException('Opis iskustva može imati maksimalno 300 znakova.');
        }

        $this->applications->ensureRegistrationRow($userId);
        $this->applications->updateSportSlot($userId, $slot, $disciplineId, $normalizedDescription);

        return sprintf('%d. sportska disciplina je uspješno spremljena.', $slot);
    }

    public function deleteSport(int $userId, int $slot): string
    {
        if ($slot < 1 || $slot > 3) {
            throw new DomainException('Neispravan redni broj sporta.');
        }

        $this->applications->ensureRegistrationRow($userId);
        $this->applications->removeSportSlot($userId, $slot);

        return sprintf('Uspješno ste se odjavili iz %d. sportske discipline.', $slot);
    }

    public function deleteUser(int $userId): string
    {
        $this->profiles->deleteUser($userId);
        return 'Korisnički račun je obrisan.';
    }

    private function normalizeHumanName(string $value, string $fieldLabel): string
    {
        $normalized = trim($value);
        if ($normalized === '' || !preg_match('/^[a-zA-ZčšžđćČŠŽĐĆ ]+$/u', $normalized)) {
            throw new DomainException('Nedozvoljeni znakovi u polju za upis ' . $fieldLabel . '.');
        }

        return mb_convert_case($normalized, MB_CASE_TITLE, 'UTF-8');
    }
}
