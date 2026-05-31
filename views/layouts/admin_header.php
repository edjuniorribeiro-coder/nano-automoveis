<?php
$user = requireLogin();
$contato = config('contato');
$current = $_SERVER['REQUEST_URI'];
$nav = [
  ['/admin',            'Dashboard',   ['proprietario','financeiro','vendedor','leitor']],
  ['/admin/carros',     'Carros',      ['proprietario','financeiro','vendedor','leitor']],
  ['/admin/crm',        'CRM / Leads', ['proprietario','financeiro','vendedor','leitor']],
  ['/admin/financeiro', 'Financeiro',  ['proprietario','financeiro']],
  ['/admin/usuarios',   'Usuários',    ['proprietario']],
];
?>
<!DOCTYPE html><html lang="pt-BR"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin — Nano Automóveis</title>
<link rel="stylesheet" href="/assets/css/style.css">
</head><body>
<?php if ($f = flash()): ?><div class="flash <?= e($f['type']) ?>"><?= e($f['msg']) ?></div><?php endif; ?>
<div class="admin-wrap">
  <aside class="sidebar">
    <div class="sidebar-head">
      <a href="/" class="logo"><span class="logo-mark">NANO</span><span class="logo-sub">AUTOMÓVEIS</span></a>
    </div>
    <nav class="sidebar-nav">
      <?php foreach ($nav as [$href, $label, $roles]): if (!in_array($user['role'], $roles, true)) continue; ?>
        <a href="<?= $href ?>" class="<?= $current === $href ? 'active' : '' ?>"><?= e($label) ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="sidebar-foot">
      <div class="userinfo">
        <strong><?= e($user['nome']) ?></strong>
        <span class="role"><?= e(ROLE_LABELS[$user['role']]) ?></span>
      </div>
      <div class="flex gap-2 mt-2">
        <a href="/" class="btn btn-ghost sm flex-1">🏠 Site</a>
        <a href="/logout" class="btn btn-ghost sm flex-1">Sair</a>
      </div>
    </div>
  </aside>
  <main class="admin-main">
