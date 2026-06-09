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
<script>
  (function(){
    try {
      var saved = localStorage.getItem('nano-theme');
      if (saved === 'light' || saved === 'dark') {
        document.documentElement.setAttribute('data-theme', saved);
      }
    } catch(e){}
  })();
  function toggleTheme() {
    var cur = document.documentElement.getAttribute('data-theme') === 'light' ? 'light' : 'dark';
    var next = cur === 'light' ? 'dark' : 'light';
    if (next === 'light') document.documentElement.setAttribute('data-theme', 'light');
    else document.documentElement.removeAttribute('data-theme');
    try { localStorage.setItem('nano-theme', next); } catch(e){}
  }
</script>
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
        <button type="button" class="theme-toggle" onclick="toggleTheme()" title="Alternar tema" aria-label="Alternar tema" style="width:2.25rem;height:2.25rem">
          <span class="icon-dark">☀️</span><span class="icon-light">🌙</span>
        </button>
        <a href="/logout" class="btn btn-ghost sm flex-1">Sair</a>
      </div>
    </div>
  </aside>
  <main class="admin-main">
