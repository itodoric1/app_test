<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\ProfileService;
use DomainException;

class ProfileController extends Controller
{
    public function __construct(
        private readonly ProfileService $profiles,
        private readonly Session $session,
        private readonly Response $response,
    ) {
    }

    public function show(Request $request): void
    {
        $userId = (int) $this->session->get('fs_korisnik_id', 0);
        if ($userId <= 0) {
            $this->response->redirect('/');
        }

        $flash = [
            'success' => $this->session->consumeFlash('success'),
            'error' => $this->session->consumeFlash('error'),
        ];

        $this->view('profile/index', array_merge(
            $this->profiles->getPageData($userId),
            ['flash' => $flash]
        ));
    }

    public function update(Request $request): never
    {
        $userId = (int) $this->session->get('fs_korisnik_id', 0);
        if ($userId <= 0) {
            $this->response->redirect('/');
        }

        $action = (string) ($request->input('action') ?? '');

        try {
            $message = match ($action) {
                'update_name' => $this->profiles->updateName($userId, (string) $request->input('ime', '')),
                'update_surname' => $this->profiles->updateSurname($userId, (string) $request->input('prezime', '')),
                'update_birth_year' => $this->profiles->updateBirthYear($userId, (string) $request->input('god_rodenja', '')),
                'update_email' => $this->profiles->updateEmail($userId, (string) $request->input('email', '')),
                'update_phone' => $this->profiles->updatePhone($userId, $request->input('pozivni'), $request->input('telefon')),
                'update_organization' => $this->profiles->updateOrganization($userId, (int) $request->input('org_jed', 0)),
                'update_shirt_size' => $this->profiles->updateShirtSize($userId, (string) $request->input('konf_br', '')),
                'update_password' => $this->profiles->updatePassword(
                    $userId,
                    (string) $request->input('lozinka_1', ''),
                    (string) $request->input('lozinka_2', '')
                ),
                'update_sport_1' => $this->profiles->updateSport($userId, 1, (int) $request->input('sport_1', 0), (string) $request->input('opis_1', '')),
                'update_sport_2' => $this->profiles->updateSport($userId, 2, (int) $request->input('sport_2', 0), (string) $request->input('opis_2', '')),
                'update_sport_3' => $this->profiles->updateSport($userId, 3, (int) $request->input('sport_3', 0), (string) $request->input('opis_3', '')),
                'delete_sport_1' => $this->profiles->deleteSport($userId, 1),
                'delete_sport_2' => $this->profiles->deleteSport($userId, 2),
                'delete_sport_3' => $this->profiles->deleteSport($userId, 3),
                'delete_user' => $this->profiles->deleteUser($userId),
                default => throw new DomainException('Nepoznata akcija.'),
            };

            if ($action === 'update_email' || $action === 'delete_user') {
                $this->session->flash('success', $message);
                $this->session->invalidate();
                $this->response->redirect('/');
            }

            $this->session->flash('success', $message);
        } catch (DomainException $e) {
            $this->session->flash('error', $e->getMessage());
        }

        $this->response->redirect('/profile');
    }
}
