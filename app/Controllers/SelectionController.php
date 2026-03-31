<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\SelectionService;
use DomainException;

class SelectionController extends Controller
{
    public function __construct(
        private readonly SelectionService $selection,
        private readonly Session $session,
        private readonly Response $response,
    ) {
    }

    public function show(Request $request): void
    {
        $data = $this->selection->publicDashboard();

        $this->view('selection/index', [
            ...$data,
            'flash' => [
                'success' => $this->session->consumeFlash('success'),
                'error' => $this->session->consumeFlash('error'),
            ],
        ]);
    }

    public function manage(Request $request): void
    {
        $userId = (int) $this->session->get('fs_korisnik_id', 0);
        if ($userId <= 0) {
            $this->response->redirect('/');
        }

        try {
            $data = $this->selection->managementDashboard($userId);
        } catch (DomainException $e) {
            $this->session->flash('error', $e->getMessage());
            $this->response->redirect('/home');
        }

        $this->view('selection/manage', [
            ...$data,
            'flash' => [
                'success' => $this->session->consumeFlash('success'),
                'error' => $this->session->consumeFlash('error'),
            ],
        ]);
    }

    public function update(Request $request): never
    {
        $userId = (int) $this->session->get('fs_korisnik_id', 0);
        if ($userId <= 0) {
            $this->response->redirect('/');
        }

        try {
            $message = $this->selection->toggleCandidate(
                $userId,
                (int) $request->input('entry_id', 0),
                (bool) ((int) $request->input('selected', 0))
            );
            $this->session->flash('success', $message);
        } catch (DomainException $e) {
            $this->session->flash('error', $e->getMessage());
        }

        $this->response->redirect('/selection/manage');
    }
}
