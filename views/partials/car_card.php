<?php
$wa = config('contato')['whatsapp'];
$preco = $car['preco_promocional'] ?: $car['preco'];
$msg = "Olá! Tenho interesse no {$car['marca']} {$car['modelo']} {$car['ano_modelo']} anunciado no site.";
?>
<div class="card car-card">
  <a href="/estoque/<?= $car['id'] ?>" class="photo">
    <?php if (!empty($car['foto_capa'])): ?>
      <img src="<?= e($car['foto_capa']) ?>" alt="<?= e("$car[marca] $car[modelo]") ?>">
    <?php else: ?>
      <div style="display:flex;align-items:center;justify-content:center;height:100%;color:var(--w30)">Sem foto</div>
    <?php endif; ?>
    <?php if ($car['destaque']): ?><span class="badge-abs badge-destaque">★ Destaque</span><?php endif; ?>
    <?php if ($car['status'] === 'reservado'): ?><span class="badge-abs badge-reservado">Reservado</span><?php endif; ?>
    <?php if ($car['status'] === 'vendido'): ?><span class="badge-abs badge-vendido">Vendido</span><?php endif; ?>
  </a>
  <div class="body">
    <a href="/estoque/<?= $car['id'] ?>">
      <h3><?= e("$car[marca] $car[modelo]") ?></h3>
      <p class="versao"><?= e($car['versao'] ?? '—') ?></p>
    </a>
    <div class="specs">
      <span>📅 <?= $car['ano_fabricacao'] ?>/<?= $car['ano_modelo'] ?></span>
      <span>⚙️ <?= km($car['km']) ?></span>
      <?php if ($car['combustivel']): ?><span>⛽ <?= e($car['combustivel']) ?></span><?php endif; ?>
    </div>
    <div class="price-box">
      <?php if ($car['preco_promocional']): ?><div class="price-orig"><?= brl($car['preco']) ?></div><?php endif; ?>
      <div class="price"><?= brl($preco) ?></div>
    </div>
    <div class="actions">
      <a href="/estoque/<?= $car['id'] ?>" class="btn btn-ghost">Detalhes</a>
      <a href="<?= e(whatsappLink($wa, $msg)) ?>" target="_blank" class="btn btn-wa">💬 WhatsApp</a>
    </div>
  </div>
</div>
