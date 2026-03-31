<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\SelectionRepository;
use App\Repositories\SettingsRepository;
use App\Repositories\UserRepository;
use DomainException;

class SelectionService
{
    public function __construct(
        private readonly SelectionRepository $selection,
        private readonly SettingsRepository $settings,
        private readonly UserRepository $users,
    ) {
    }

    public function publicDashboard(): array
    {
        $status = $this->selection->getSelectionStatus();
        $totals = $this->selection->statsTotals();

        return [
            'status' => $status,
            'totals' => [
                ...$totals,
                'registered_total' => $totals['registered_women'] + $totals['registered_men'],
                'selected_total' => $totals['selected_women'] + $totals['selected_men'],
            ],
            'byOrganization' => $this->selection->statsByOrganization(),
            'bySport' => $this->selection->statsBySport(),
            'participants' => $status === 'rezultat' ? $this->selection->finalParticipants() : [],
            'vipParticipants' => $status === 'rezultat' ? $this->selection->vipParticipants() : [],
        ];
    }

    public function managementDashboard(int $userId): array
    {
        $user = $this->users->findById($userId);
        if ($user === null || $user->tipKorisnika !== 1) {
            throw new DomainException('Nemate pristup selekcijskom modulu.');
        }

        return [
            'user' => $user,
            'status' => $this->selection->getSelectionStatus(),
            'totals' => $this->publicDashboard()['totals'],
            'candidates' => $this->selection->selectionCandidates(),
        ];
    }

    public function toggleCandidate(int $userId, int $entryId, bool $selected): string
    {
        $user = $this->users->findById($userId);
        if ($user === null || $user->tipKorisnika !== 1) {
            throw new DomainException('Nemate pristup selekcijskom modulu.');
        }

        if ($entryId <= 0) {
            throw new DomainException('Neispravan ID prijave.');
        }

        $entry = $this->selection->candidateById($entryId);
        if ($entry === null) {
            throw new DomainException('Prijava nije pronađena.');
        }

        $value = $selected ? 'da' : 'ne';
        $this->selection->setCandidateSelection($entryId, $value);

        return sprintf(
            '%s %s / %s (%d. sport) je označen kao %s.',
            $entry['ime'],
            $entry['prezime'],
            $entry['sport_naziv'],
            $entry['broj_sporta'],
            $selected ? 'odabran' : 'nije odabran'
        );
    }
}
