<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\AdminService;
use DomainException;

class AdminController extends Controller
{
    public function __construct(
        private readonly AdminService $admin,
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

        try {
            $data = $this->admin->getDashboardData($userId);
        } catch (DomainException $e) {
            $this->session->flash('error', $e->getMessage());
            $this->response->redirect('/home');
        }

        $this->view('admin/index', array_merge($data, [
            'flash' => [
                'success' => $this->session->consumeFlash('success'),
                'error' => $this->session->consumeFlash('error'),
            ],
        ]));
    }

    public function update(Request $request): never
    {
        $userId = (int) $this->session->get('fs_korisnik_id', 0);
        if ($userId <= 0) {
            $this->response->redirect('/');
        }

        try {
            $this->admin->getDashboardData($userId);
            $message = $this->admin->handleAction((string) $request->input('action', ''), $request->all());
            $this->session->flash('success', $message);
        } catch (DomainException $e) {
            $this->session->flash('error', $e->getMessage());
        }

        $this->response->redirect('/admin');
    }
}
