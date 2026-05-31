<?php
/**
 * Nano Automóveis — Front Controller (router)
 */
declare(strict_types=1);

require __DIR__ . '/lib/helpers.php';
require __DIR__ . '/lib/db.php';
require __DIR__ . '/lib/auth.php';

date_default_timezone_set(config('site')['timezone'] ?? 'America/Sao_Paulo');
startSession();

// Parse URL
$uri = strtok($_SERVER['REQUEST_URI'], '?');
$path = '/' . trim(parse_url($uri, PHP_URL_PATH) ?? '/', '/');

// --- POST actions (mutations) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__ . '/actions.php';
    handleAction($path);
    exit;
}

// --- GET routes ---
$routes = [
    '/'              => ['view' => 'home',           'layout' => 'site'],
    '/estoque'       => ['view' => 'estoque',        'layout' => 'site'],
    '/sobre'         => ['view' => 'sobre',          'layout' => 'site'],
    '/contato'       => ['view' => 'contato',        'layout' => 'site'],
    '/login'         => ['view' => 'login',          'layout' => 'auth'],
    '/logout'        => ['view' => 'logout',         'layout' => 'none'],
    '/admin'         => ['view' => 'admin/dashboard','layout' => 'admin'],
    '/admin/carros'  => ['view' => 'admin/carros',   'layout' => 'admin'],
    '/admin/carros/novo' => ['view' => 'admin/carro_form', 'layout' => 'admin'],
    '/admin/crm'     => ['view' => 'admin/crm',      'layout' => 'admin'],
    '/admin/financeiro' => ['view' => 'admin/financeiro','layout' => 'admin'],
    '/admin/usuarios' => ['view' => 'admin/usuarios','layout' => 'admin'],
];

// dinâmicas
if (preg_match('#^/estoque/(\d+)$#', $path, $m)) {
    $_GET['id'] = $m[1];
    $route = ['view' => 'carro', 'layout' => 'site'];
} elseif (preg_match('#^/admin/carros/(\d+)$#', $path, $m)) {
    $_GET['id'] = $m[1];
    $route = ['view' => 'admin/carro_form', 'layout' => 'admin'];
} elseif (preg_match('#^/admin/crm/(\d+)$#', $path, $m)) {
    $_GET['id'] = $m[1];
    $route = ['view' => 'admin/lead', 'layout' => 'admin'];
} else {
    $route = $routes[$path] ?? null;
}

if (!$route) {
    http_response_code(404);
    $route = ['view' => '404', 'layout' => 'site'];
}

renderPage($route);

// =====================================================
function renderPage(array $route): void {
    $viewFile = __DIR__ . '/views/' . $route['view'] . '.php';
    if (!file_exists($viewFile)) { http_response_code(404); echo 'Página não encontrada.'; return; }

    if ($route['layout'] === 'admin') {
        // Proteção
        require __DIR__ . '/views/layouts/admin_header.php';
        require $viewFile;
        require __DIR__ . '/views/layouts/admin_footer.php';
    } elseif ($route['layout'] === 'site') {
        require __DIR__ . '/views/layouts/site_header.php';
        require $viewFile;
        require __DIR__ . '/views/layouts/site_footer.php';
    } elseif ($route['layout'] === 'auth') {
        require __DIR__ . '/views/layouts/site_header.php';
        require $viewFile;
        require __DIR__ . '/views/layouts/site_footer.php';
    } else {
        require $viewFile;
    }
}
