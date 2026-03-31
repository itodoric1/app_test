<?php

declare(strict_types=1);

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\RegistrationController;
use App\Controllers\ProfileController;
use App\Controllers\SelectionController;
use App\Core\Container;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Core\Session;
use App\Repositories\AdminRepository;
use App\Repositories\RegistrationRepository;
use App\Repositories\SettingsRepository;
use App\Repositories\UserRepository;
use App\Repositories\ProfileRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\DisciplineRepository;
use App\Repositories\SportApplicationRepository;
use App\Repositories\SelectionRepository;
use App\Services\AdminService;
use App\Services\AuthService;
use App\Services\RegistrationService;
use App\Services\UserContextService;
use App\Services\ProfileService;
use App\Services\SelectionService;

require dirname(__DIR__) . '/bootstrap/app.php';

$container = new Container();

$container->singleton(Database::class, fn () => new Database(config('db')));
$container->singleton(Session::class, fn () => new Session());
$container->singleton(Response::class, fn () => new Response());
$container->singleton(Request::class, fn () => new Request());
$container->singleton(UserRepository::class, fn ($c) => new UserRepository($c->get(Database::class)));
$container->singleton(RegistrationRepository::class, fn ($c) => new RegistrationRepository($c->get(Database::class)));
$container->singleton(SettingsRepository::class, fn ($c) => new SettingsRepository($c->get(Database::class)));
$container->singleton(ProfileRepository::class, fn ($c) => new ProfileRepository($c->get(Database::class)));
$container->singleton(OrganizationRepository::class, fn ($c) => new OrganizationRepository($c->get(Database::class)));
$container->singleton(DisciplineRepository::class, fn ($c) => new DisciplineRepository($c->get(Database::class)));
$container->singleton(SportApplicationRepository::class, fn ($c) => new SportApplicationRepository($c->get(Database::class)));
$container->singleton(AdminRepository::class, fn ($c) => new AdminRepository($c->get(Database::class)));
$container->singleton(SelectionRepository::class, fn ($c) => new SelectionRepository($c->get(Database::class)));

$container->singleton(AuthService::class, fn ($c) => new AuthService(
    $c->get(UserRepository::class),
    $c->get(RegistrationRepository::class),
    $c->get(Session::class),
));
$container->singleton(RegistrationService::class, fn ($c) => new RegistrationService(
    $c->get(UserRepository::class),
    $c->get(RegistrationRepository::class),
));
$container->singleton(UserContextService::class, fn ($c) => new UserContextService(
    $c->get(UserRepository::class),
    $c->get(SettingsRepository::class),
));
$container->singleton(ProfileService::class, fn ($c) => new ProfileService(
    $c->get(ProfileRepository::class),
    $c->get(OrganizationRepository::class),
    $c->get(DisciplineRepository::class),
    $c->get(SportApplicationRepository::class),
));
$container->singleton(AdminService::class, fn ($c) => new AdminService(
    $c->get(AdminRepository::class),
    $c->get(SettingsRepository::class),
    $c->get(UserRepository::class),
));
$container->singleton(SelectionService::class, fn ($c) => new SelectionService(
    $c->get(SelectionRepository::class),
    $c->get(SettingsRepository::class),
    $c->get(UserRepository::class),
));

$container->singleton(AuthController::class, fn ($c) => new AuthController(
    $c->get(AuthService::class),
    $c->get(Session::class),
    $c->get(Response::class),
));
$container->singleton(RegistrationController::class, fn ($c) => new RegistrationController(
    $c->get(RegistrationService::class),
    $c->get(Session::class),
    $c->get(Response::class),
));
$container->singleton(HomeController::class, fn ($c) => new HomeController(
    $c->get(AuthService::class),
    $c->get(UserContextService::class),
));
$container->singleton(ProfileController::class, fn ($c) => new ProfileController(
    $c->get(ProfileService::class),
    $c->get(Session::class),
    $c->get(Response::class),
));
$container->singleton(AdminController::class, fn ($c) => new AdminController(
    $c->get(AdminService::class),
    $c->get(Session::class),
    $c->get(Response::class),
));
$container->singleton(SelectionController::class, fn ($c) => new SelectionController(
    $c->get(SelectionService::class),
    $c->get(Session::class),
    $c->get(Response::class),
));
$container->singleton(Router::class, fn () => new Router());

$router = $container->get(Router::class);
$router->get('/', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/register', [RegistrationController::class, 'showForm']);
$router->post('/register', [RegistrationController::class, 'register']);
$router->get('/home', [HomeController::class, 'index']);
$router->get('/profile', [ProfileController::class, 'show']);
$router->post('/profile', [ProfileController::class, 'update']);
$router->get('/admin', [AdminController::class, 'show']);
$router->post('/admin', [AdminController::class, 'update']);
$router->get('/selection', [SelectionController::class, 'show']);
$router->get('/selection/manage', [SelectionController::class, 'manage']);
$router->post('/selection/manage', [SelectionController::class, 'update']);

$router->dispatch($container->get(Request::class), $container);
