<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\RegistrationService;
use DomainException;

class RegistrationController extends Controller
{
    public function __construct(
        private readonly RegistrationService $registrations,
        private readonly Session $session,
        private readonly Response $response,
    ) {
    }

    public function showForm(): void
    {
        $this->view('auth/register', [
            'error' => $this->session->consumeFlash('error'),
            'success' => $this->session->consumeFlash('success'),
        ]);
    }

    public function register(Request $request): never
    {
        try {
            $this->registrations->register($request->only([
                'ime', 'prezime', 'spol', 'god_rodenja', 'email', 'pozivni', 'telefon',
                'lozinka1', 'lozinka2', 'org_jed', 'konf_br',
            ]));

            $this->session->flash('success', 'Registracija je spremljena. Aktivacija maila ide kao sljedeći korak.');
            $this->response->redirect('/register');
        } catch (DomainException $e) {
            $this->session->flash('error', $e->getMessage());
            $this->response->redirect('/register');
        }
    }
}
