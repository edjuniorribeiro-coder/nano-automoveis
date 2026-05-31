<?php
/** Autenticação e controle de acesso */

function startSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params(['lifetime' => 86400 * 7, 'httponly' => true, 'samesite' => 'Lax']);
        session_start();
    }
}

function currentUser(): ?array {
    startSession();
    if (empty($_SESSION['uid'])) return null;
    return fetchOne('SELECT * FROM users WHERE id = ? AND ativo = 1', [$_SESSION['uid']]);
}

function requireLogin(): array {
    $u = currentUser();
    if (!$u) { header('Location: /login?next=' . urlencode($_SERVER['REQUEST_URI'])); exit; }
    return $u;
}

function requireRole(array $roles): array {
    $u = requireLogin();
    if (!in_array($u['role'], $roles, true)) {
        http_response_code(403);
        echo '<h1 style="font-family:sans-serif;padding:2rem">403 — Acesso negado</h1>';
        echo '<a href="/admin">Voltar</a>';
        exit;
    }
    return $u;
}

function login(string $email, string $pass): bool {
    startSession();
    $u = fetchOne('SELECT * FROM users WHERE email = ? AND ativo = 1', [$email]);
    if (!$u || !password_verify($pass, $u['senha_hash'])) return false;
    session_regenerate_id(true);
    $_SESSION['uid'] = (int)$u['id'];
    return true;
}

function logout(): void {
    startSession();
    $_SESSION = [];
    session_destroy();
}

function csrfToken(): string {
    startSession();
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}

function csrfCheck(): void {
    startSession();
    $t = $_POST['_csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $t)) {
        http_response_code(419); exit('CSRF inválido');
    }
}

const ROLE_LABELS = [
    'proprietario' => 'Proprietário',
    'financeiro'   => 'Financeiro',
    'vendedor'     => 'Vendedor',
    'leitor'       => 'Leitor',
];
