<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use DI\Container;
use Slim\Factory\AppFactory;
use App\Config\Database;

$container = new Container();
AppFactory::setContainer($container);
$app = AppFactory::create();

// Error middleware (en dev true, en prod false)
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// CORS simple (si necesitas en desarrollo)
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

// Cargar .env si existe
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        putenv($line);
    }
}

$container->set('db', function () {
    $cfg = [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'dbname' => getenv('DB_NAME') ?: 'jardin',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: 'root',
        'charset' => getenv('DB_CHARSET') ?: 'utf8mb4'
    ];
    return (new Database($cfg))->getConnection();
});

// Controladores
$container->set(App\Controllers\PlantController::class, function($c) {
    return new App\Controllers\PlantController(
        new App\Repositories\PlantRepository($c->get('db')),
        new App\Services\RiegoService(),
        new App\Repositories\RiegoRepository($c->get('db'))
    );
});

$container->set(App\Controllers\RiegoController::class, function($c) {
    return new App\Controllers\RiegoController(
        new App\Repositories\PlantRepository($c->get('db')),
        new App\Repositories\RiegoRepository($c->get('db')),
        new App\Services\RiegoService()
    );
});

// Rutas
(require __DIR__ . '/../src/routes.php')($app);

$app->run();
