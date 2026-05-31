<?php
$id = (int)($_GET['id'] ?? 0);
$car = fetchOne('SELECT * FROM cars WHERE id = ?', [$id]);
if (!$car) { http_response_code(404); echo '<div class="container" style="padding:4rem 0;text-align:center"><h1>Carro não encontrado</h1></div>'; return; }
q('UPDATE cars SET visualizacoes = visualizacoes + 1 WHERE id = ?', [$id]);
$wa = config('contato')['whatsapp'];
$preco = $car['preco_promocional'] ?: $car['preco'];
$msg = "Olá! Tenho interesse no {$car['marca']} {$car['modelo']} {$car['ano_modelo']} (" . brl($preco) . ').';
$fotos = [];
if ($car['foto_capa']) $fotos[] = $car['foto_capa'];
$extras = json_decode($car['fotos'] ?? '[]', true) ?: [];
$fotos = array_merge($fotos, $extras);
$opcionais = $car['opcionais'] ? array_map('trim', explode(',', $car['opcionais'])) : [];
?>
<div class="container car-detail">
  <div>
    <div class="card gallery-main">
      <?php if ($fotos): ?><img src="<?= e($fotos[0]) ?>" alt="" id="mainPhoto">
      <?php else: ?><div style="display:flex;align-items:center;justify-content:center;height:100%;color:var(--w30)">Sem foto</div><?php endif; ?>
    </div>
    <?php if (count($fotos) > 1): ?>
      <div class="gallery-thumbs">
        <?php foreach (array_slice($fotos, 0, 8) as $f): ?>
          <img src="<?= e($f) ?>" onclick="document.getElementById('mainPhoto').src=this.src" alt="">
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <aside>
    <div class="card pad">
      <h1 style="font-size:1.5rem;font-weight:900;text-transform:uppercase"><?= e("$car[marca] $car[modelo]") ?></h1>
      <p style="color:var(--w50);font-size:.9rem;margin-top:.25rem"><?= e($car['versao'] ?? '') ?></p>
      <div class="mt-4">
        <?php if ($car['preco_promocional']): ?><div class="text-sm" style="color:var(--w30);text-decoration:line-through"><?= brl($car['preco']) ?></div><?php endif; ?>
        <div style="font-size:2.25rem;font-weight:900;color:var(--yellow)"><?= brl($preco) ?></div>
      </div>
      <div class="grid gap-2 mt-6">
        <a href="<?= e(whatsappLink($wa, $msg)) ?>" target="_blank" class="btn btn-wa">💬 Falar pelo WhatsApp</a>
        <a href="tel:+<?= e($wa) ?>" class="btn btn-ghost">📞 Ligar agora</a>
      </div>
    </div>

    <div class="card pad mt-4">
      <h3 class="font-bold mb-4">Tenho interesse</h3>
      <form method="post" action="/leads">
        <input type="hidden" name="_csrf" value="<?= csrfToken() ?>">
        <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
        <div class="mt-2"><input name="nome" required placeholder="Seu nome" class="input"></div>
        <div class="mt-2"><input name="telefone" required placeholder="WhatsApp / Telefone" class="input"></div>
        <div class="mt-2"><input name="email" type="email" placeholder="E-mail (opcional)" class="input"></div>
        <div class="mt-2"><textarea name="mensagem" placeholder="Mensagem" class="input" rows="3"></textarea></div>
        <button class="btn btn-primary mt-4" style="width:100%">Quero saber mais</button>
      </form>
    </div>
  </aside>
</div>

<div class="container">
  <div class="especs">
    <div class="card item"><span class="ic">📅</span><div><small>Ano</small><strong><?= $car['ano_fabricacao'] ?>/<?= $car['ano_modelo'] ?></strong></div></div>
    <div class="card item"><span class="ic">⚙️</span><div><small>Quilometragem</small><strong><?= km($car['km']) ?></strong></div></div>
    <div class="card item"><span class="ic">⛽</span><div><small>Combustível</small><strong><?= e($car['combustivel'] ?? '—') ?></strong></div></div>
    <div class="card item"><span class="ic">🔧</span><div><small>Câmbio</small><strong><?= e($car['cambio'] ?? '—') ?></strong></div></div>
    <div class="card item"><span class="ic">🎨</span><div><small>Cor</small><strong><?= e($car['cor'] ?? '—') ?></strong></div></div>
    <div class="card item"><span class="ic">🚪</span><div><small>Portas</small><strong><?= e($car['portas'] ?? '—') ?></strong></div></div>
  </div>

  <?php if ($car['descricao']): ?>
    <div class="mt-10">
      <h2 class="font-black" style="font-size:1.5rem">Descrição</h2>
      <p style="color:var(--w70);white-space:pre-line;line-height:1.7;margin-top:1rem"><?= e($car['descricao']) ?></p>
    </div>
  <?php endif; ?>

  <?php if ($opcionais): ?>
    <div class="mt-10 mb-8">
      <h2 class="font-black mb-4" style="font-size:1.5rem">Opcionais</h2>
      <div class="opcionais-grid">
        <?php foreach ($opcionais as $o): ?><div>✓ <?= e($o) ?></div><?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
</div>
