<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\SettingsRepository;
use App\Repositories\UserRepository;

class UserContextService
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly SettingsRepository $settings,
    ) {
    }

    public function build(int $userId): ?array
    {
        $user = $this->users->findById($userId);

        if ($user === null) {
            return null;
        }

        return [
            'user' => $user,
            'selekcija' => $this->settings->get('selekcija', 'otvoreno'),
            'godina' => $this->settings->get('godina'),
            'datum_prijave' => $this->settings->get('datum_prijave'),
        ];
    }
}
