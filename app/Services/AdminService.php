<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Repositories\AdminRepository;
use App\Repositories\SettingsRepository;
use App\Repositories\UserRepository;
use DomainException;

class AdminService
{
    private const SETTINGS_MAP = [
        'update_year' => 'godina',
        'update_version' => 'verzija',
        'update_bank_serial' => 'redni_broj_bankarskih',
        'update_term' => 'termin_odrzavanja',
        'update_signup_date' => 'datum_prijave',
    ];

    public function __construct(
        private readonly AdminRepository $admin,
        private readonly SettingsRepository $settings,
        private readonly UserRepository $users,
    ) {
    }

    public function getDashboardData(int $userId): array
    {
        $user = $this->users->findById($userId);
        if (!$user instanceof User) {
            throw new DomainException('Korisnik nije pronađen.');
        }

        if ($user->tipKorisnika !== 1) {
            throw new DomainException('Nemate administratorski pristup.');
        }

        return [
            'user' => $user,
            'settings' => $this->settings->all(),
            'stats' => $this->admin->stats(),
        ];
    }

    public function handleAction(string $action, array $payload): string
    {
        return match ($action) {
            'update_year', 'update_version', 'update_bank_serial', 'update_term', 'update_signup_date' => $this->updateSetting($action, $payload),
            'open_app' => $this->setSelection('otvoreno', 'Aplikacija je otključana za sve korisnike.'),
            'lock_app' => $this->setSelection('zatvoreno', 'Aplikacija je zaključana i ostaje dostupna samo za selekciju.'),
            'publish_results' => $this->publishResults(),
            'reset_data' => $this->resetData(),
            'delete_all' => $this->deleteAll(),
            'delete_user' => $this->deleteUser((int) ($payload['korisnik_id'] ?? 0)),
            default => throw new DomainException('Nepoznata administratorska akcija.'),
        };
    }

    private function updateSetting(string $action, array $payload): string
    {
        $settingName = self::SETTINGS_MAP[$action] ?? null;
        if ($settingName === null) {
            throw new DomainException('Nepoznata postavka.');
        }

        $value = trim((string) ($payload['value'] ?? ''));
        if ($value === '') {
            throw new DomainException('Vrijednost postavke ne smije biti prazna.');
        }

        if ($settingName === 'godina' && !preg_match('/^\d{4}\.?$/', $value)) {
            throw new DomainException('Godina mora biti u formatu 2026 ili 2026.');
        }

        if ($settingName === 'verzija' && !preg_match('/^[0-9A-Za-z._-]{1,30}$/', $value)) {
            throw new DomainException('Verzija sadrži nedozvoljene znakove.');
        }

        $this->settings->set($settingName, $value);

        return match ($settingName) {
            'godina' => 'Godina je uspješno spremljena.',
            'verzija' => 'Verzija je uspješno spremljena.',
            'redni_broj_bankarskih' => 'Redni broj bankarskih igara je spremljen.',
            'termin_odrzavanja' => 'Termin održavanja je spremljen.',
            'datum_prijave' => 'Datum prijave je spremljen.',
            default => 'Postavka je spremljena.',
        };
    }

    private function setSelection(string $status, string $message): string
    {
        $this->settings->set('selekcija', $status);
        return $message;
    }

    private function publishResults(): string
    {
        $this->settings->set('selekcija', 'rezultat');
        $vipRows = $this->admin->fetchVipRows();
        $selectedRows = $this->admin->fetchSelectedRows();
        $this->admin->replaceFinalResults($vipRows, $selectedRows);

        return 'Konačna lista je generirana i selekcija je prebačena u status rezultata.';
    }

    private function resetData(): string
    {
        $this->admin->resetApplications();
        return 'Baza prijava je resetirana i spremna za novi krug prijava.';
    }

    private function deleteAll(): string
    {
        $this->admin->deleteAllNonAdmins();
        return 'Svi neadministratorski korisnici i njihove prijave su obrisani.';
    }

    private function deleteUser(int $userId): string
    {
        if ($userId <= 0) {
            throw new DomainException('Neispravan ID korisnika.');
        }

        $user = $this->admin->findUserById($userId);
        if ($user === null) {
            throw new DomainException('Korisnik nije pronađen.');
        }

        if ((int) $user['tip_korisnika'] === 1) {
            throw new DomainException('Administratorski korisnik se ne može obrisati ovom akcijom.');
        }

        $this->admin->deleteUser($userId);

        return sprintf('Korisnik %s %s je obrisan.', $user['ime'], $user['prezime']);
    }
}
