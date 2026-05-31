<?php
/** Helpers gerais */

function config(?string $key = null) {
    static $cfg = null;
    if ($cfg === null) {
        $path = __DIR__ . '/../config.php';
        if (!file_exists($path)) {
            die('Arquivo config.php não encontrado. Copie config.example.php para config.php e configure.');
        }
        $cfg = require $path;
    }
    return $key ? ($cfg[$key] ?? null) : $cfg;
}

function e(?string $s): string { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

function brl($v): string {
    if ($v === null || $v === '') return '—';
    return 'R$ ' . number_format((float)$v, 0, ',', '.');
}

function km($v): string { return number_format((int)$v, 0, ',', '.') . ' km'; }

function whatsappLink(string $num, string $msg = ''): string {
    $n = preg_replace('/\D+/', '', $num);
    return 'https://wa.me/' . $n . '?text=' . urlencode($msg);
}

function asset(string $path): string {
    return rtrim(config('site')['url'], '/') . '/assets/' . ltrim($path, '/');
}

function url(string $path = '/'): string {
    return rtrim(config('site')['url'], '/') . '/' . ltrim($path, '/');
}

function redirect(string $to): void { header("Location: $to"); exit; }

function flash(?string $msg = null, string $type = 'success'): ?array {
    startSession();
    if ($msg !== null) { $_SESSION['_flash'] = ['msg' => $msg, 'type' => $type]; return null; }
    $f = $_SESSION['_flash'] ?? null;
    unset($_SESSION['_flash']);
    return $f;
}

function statusBadge(string $status): string {
    $m = [
        'disponivel' => ['Disponível', 'bg-green-500/15 text-green-400'],
        'reservado'  => ['Reservado',  'bg-orange-500/15 text-orange-400'],
        'vendido'    => ['Vendido',    'bg-red-500/15 text-red-400'],
        'rascunho'   => ['Rascunho',   'bg-white/10 text-white/60'],
    ];
    [$label, $cls] = $m[$status] ?? [$status, 'bg-white/10'];
    return "<span class='chip $cls'>" . e($label) . "</span>";
}

const LEAD_STATUS = [
    'novo' => 'Novo',
    'em_atendimento' => 'Em atendimento',
    'proposta' => 'Proposta enviada',
    'fechado' => 'Fechado',
    'perdido' => 'Perdido',
];

function uploadFotos(array $files): array {
    $dir = __DIR__ . '/../uploads/carros';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $urls = [];
    $count = is_array($files['name']) ? count($files['name']) : 0;
    for ($i = 0; $i < $count; $i++) {
        if (($files['error'][$i] ?? 999) !== UPLOAD_ERR_OK) continue;
        $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp','gif'])) continue;
        $name = uniqid('car_', true) . '.' . $ext;
        $dest = $dir . '/' . $name;
        if (move_uploaded_file($files['tmp_name'][$i], $dest)) {
            $urls[] = '/uploads/carros/' . $name;
        }
    }
    return $urls;
}
