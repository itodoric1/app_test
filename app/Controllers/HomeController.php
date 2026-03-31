<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;
use App\Services\UserContextService;

class HomeController extends Controller
{
    public function __construct(
        private readonly AuthService $auth,
        private readonly UserContextService $context,
    ) {
    }

    public function index(): void
    {
        $userId = $this->auth->userId();

        if ($userId === null) {
            header('Location: /');
            exit;
        }

        $this->view('home/index', [
            'context' => $this->context->build($userId),
        ]);
    }
}
