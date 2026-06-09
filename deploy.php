<?php
/**
 * deploy.php — Webhook do GitHub para deploy automático
 *
 * Como funciona:
 *  1. GitHub envia POST com X-Hub-Signature-256 a cada push
 *  2. Verificamos a assinatura HMAC com o segredo configurado em config.php
 *  3. Rodamos `git pull` no diretório do repositório e copiamos arquivos para o site
 *
 * Configuração: ver config.example.php, seção 'deploy'.
 * URL para colar no GitHub:  https://nano.waveenterprise.com.br/deploy.php
 */
declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');

require __DIR__ . '/lib/helpers.php';

$cfg = config('deploy');
if (!$cfg || empty($cfg['secret']) || empty($cfg['repo_path'])) {
    http_response_code(500);
    exit("Deploy não configurado. Edite config.php → seção 'deploy'.");
}

$secret      = (string)$cfg['secret'];
$repoPath    = rtrim((string)$cfg['repo_path'], '/');
$deployPath  = rtrim((string)($cfg['deploy_path'] ?? __DIR__), '/');
$branch      = (string)($cfg['branch'] ?? 'main');
$logFile     = __DIR__ . '/deploy.log';

// ---- 1. Verificar assinatura ----
$payload   = file_get_contents('php://input') ?: '';
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$expected  = 'sha256=' . hash_hmac('sha256', $payload, $secret);

if (!$signature || !hash_equals($expected, $signature)) {
    http_response_code(401);
    deployLog($logFile, "BLOQUEADO: assinatura inválida de " . ($_SERVER['REMOTE_ADDR'] ?? '?'));
    exit("Assinatura inválida.");
}

// ---- 2. Filtrar evento ----
$event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? '';
$data  = json_decode($payload, true) ?: [];
$ref   = $data['ref'] ?? '';

if ($event === 'ping') {
    deployLog($logFile, "PING recebido.");
    exit("pong");
}

if ($event !== 'push' || $ref !== "refs/heads/$branch") {
    http_response_code(204);
    deployLog($logFile, "Ignorado: event=$event ref=$ref");
    exit("Evento ignorado: $event $ref");
}

// ---- 3. Executar deploy ----
$out = [];
$out[] = "[" . date('Y-m-d H:i:s') . "] Push em $branch detectado.";

if (!function_exists('shell_exec')) {
    http_response_code(500);
    deployLog($logFile, "ERRO: shell_exec desabilitado no PHP.");
    exit("shell_exec desabilitado — peça para o suporte da Hostgator habilitar, ou troque para deploy via cron.");
}

// Git fetch + reset hard (sobrescreve qualquer mudança local)
runShell("cd " . escapeshellarg($repoPath) . " && git fetch --all 2>&1", $out);
runShell("cd " . escapeshellarg($repoPath) . " && git reset --hard origin/" . escapeshellarg($branch) . " 2>&1", $out);

// Espelha .cpanel.yml: copia arquivos para o deploy path
$files = ['index.php', 'actions.php', '.htaccess', '.user.ini'];
foreach ($files as $f) {
    $src = "$repoPath/$f";
    if (file_exists($src)) {
        copy($src, "$deployPath/$f");
        $out[] = "  cp $f";
    }
}
foreach (['lib','views','assets','sql'] as $d) {
    $src = "$repoPath/$d";
    if (is_dir($src)) {
        rcopy($src, "$deployPath/$d");
        $out[] = "  sync $d/";
    }
}

// Garantir uploads/ gravável
@mkdir("$deployPath/uploads/carros", 0755, true);
@chmod("$deployPath/uploads", 0755);

$out[] = "[" . date('Y-m-d H:i:s') . "] Deploy concluído.";
deployLog($logFile, implode("\n", $out));

echo "OK\n" . implode("\n", $out);

// ============================================================
function runShell(string $cmd, array &$out): void {
    $result = shell_exec($cmd);
    $out[] = "> $cmd";
    $out[] = trim($result ?? '(sem saída)');
}

function rcopy(string $src, string $dst): void {
    if (!is_dir($dst)) mkdir($dst, 0755, true);
    foreach (new DirectoryIterator($src) as $f) {
        if ($f->isDot()) continue;
        $s = $f->getPathname();
        $d = $dst . '/' . $f->getFilename();
        if ($f->isDir()) rcopy($s, $d);
        else copy($s, $d);
    }
}

function deployLog(string $file, string $msg): void {
    @file_put_contents($file, $msg . "\n\n", FILE_APPEND);
}
