<?php $c = config(); $contato = $c['contato']; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($c['site']['nome']) ?> — Seminovos selecionados</title>
<meta name="description" content="Os melhores carros seminovos com procedência. Financiamento facilitado, troca e garantia.">
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="navbar">
  <nav class="container navbar-inner">
    <a href="/" class="logo"><span class="logo-mark">NANO</span><span class="logo-sub">AUTOMÓVEIS</span></a>
    <ul class="nav-links">
      <li><a href="/">Início</a></li>
      <li><a href="/estoque">Estoque</a></li>
      <li><a href="/sobre">Sobre</a></li>
      <li><a href="/contato">Contato</a></li>
    </ul>
    <a href="/login" class="nav-restrita">🔒 Área restrita</a>
    <button class="nav-toggle" onclick="document.getElementById('navm').classList.toggle('open')">☰</button>
  </nav>
  <div class="nav-mobile" id="navm">
    <a href="/">Início</a><a href="/estoque">Estoque</a><a href="/sobre">Sobre</a><a href="/contato">Contato</a>
    <a href="/login" class="text-yellow">🔒 Área restrita</a>
  </div>
</header>
<?php if ($f = flash()): ?><div class="flash <?= e($f['type']) ?>"><?= e($f['msg']) ?></div><?php endif; ?>
<main>
