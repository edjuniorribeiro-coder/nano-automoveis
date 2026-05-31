<?php
$destaques = fetchAll(
  "SELECT * FROM cars WHERE status = 'disponivel' ORDER BY destaque DESC, created_at DESC LIMIT 6"
);
?>
<section class="hero">
  <div class="hero-bg"></div><div class="hero-glow"></div>
  <div class="container" style="position:relative">
    <span class="chip yellow" style="background:rgba(242,226,5,.1);color:var(--yellow);border:1px solid rgba(242,226,5,.3)">✦ Seminovos selecionados</span>
    <h1>Seu próximo carro<br><span class="accent">com procedência</span><br>e confiança.</h1>
    <p>Veículos vistoriados, financiamento facilitado e atendimento direto pelo WhatsApp. Encontre o carro ideal sem sair de casa.</p>
    <div class="btns">
      <a href="/estoque" class="btn btn-primary">🔍 Ver estoque</a>
      <a href="/contato" class="btn btn-ghost">Falar com vendedor →</a>
    </div>
  </div>
</section>

<section class="container feats">
  <div class="card feat"><div class="ic">🛡️</div><h3>Procedência garantida</h3><p>Todos os veículos passam por vistoria completa antes de entrar no estoque.</p></div>
  <div class="card feat"><div class="ic">💰</div><h3>Financiamento facilitado</h3><p>Trabalhamos com os melhores bancos para você sair dirigindo.</p></div>
  <div class="card feat"><div class="ic">✨</div><h3>Aceitamos seu usado</h3><p>Avaliamos seu carro na hora e abatemos no valor da compra.</p></div>
</section>

<section class="container" style="padding:4rem 0">
  <div class="section-head">
    <div><div class="eyebrow">Em destaque</div><h2>Veículos disponíveis</h2></div>
    <a href="/estoque" class="text-sm" style="color:var(--w70)">Ver tudo →</a>
  </div>

  <?php if (empty($destaques)): ?>
    <div class="card text-center" style="padding:3rem">
      <p style="color:var(--w50)">Estoque sendo atualizado. Em breve novos veículos!</p>
    </div>
  <?php else: ?>
    <div class="car-grid"><?php foreach ($destaques as $car) include __DIR__ . '/partials/car_card.php'; ?></div>
  <?php endif; ?>
</section>

<section class="container" style="padding:5rem 0">
  <div class="card text-center" style="padding:3rem 2rem">
    <h2 style="font-size:2.25rem;font-weight:900">Não encontrou o carro ideal?</h2>
    <p style="color:var(--w70);margin:1rem auto;max-width:32rem">Conte para nossa equipe o que está procurando. Buscamos o veículo certo para você.</p>
    <a href="/contato" class="btn btn-primary mt-4">Falar com a Nano</a>
  </div>
</section>
