<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\AuthService;
use DomainException;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $auth,
        private readonly Session $session,
        private readonly Response $response,
    ) {
    }

    public function showLogin(): void
    {
        $this->view('auth/login', [
            'error' => $this->session->consumeFlash('error'),
        ]);
    }

    public function login(Request $request): never
	{
		try {
			$this->auth->login(
				(string) $request->input('email', ''),
				(string) $request->input('pass', '')
			);

			$this->response->redirect('/profile');
		} catch (DomainException $e) {
			$this->session->flash('error', $e->getMessage());
			$this->response->redirect('/');
		}
	}

    public function logout(): never
    {
        $this->auth->logout();
        $this->response->redirect('/');
    }
}
