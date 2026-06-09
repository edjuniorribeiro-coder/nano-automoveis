<?php
$destaques = fetchAll(
  "SELECT * FROM cars WHERE status = 'disponivel' AND destaque = 1 ORDER BY created_at DESC LIMIT 6"
);
if (count($destaques) < 6) {
    $faltam = 6 - count($destaques);
    $ids = array_column($destaques, 'id') ?: [0];
    $place = implode(',', array_fill(0, count($ids), '?'));
    $extras = fetchAll("SELECT * FROM cars WHERE status='disponivel' AND id NOT IN ($place) ORDER BY created_at DESC LIMIT $faltam", $ids);
    $destaques = array_merge($destaques, $extras);
}

// Banner: SÓ carros marcados como destaque (até 5)
$bannerCars = fetchAll(
  "SELECT * FROM cars WHERE status = 'disponivel' AND destaque = 1 AND foto_capa IS NOT NULL AND foto_capa != '' ORDER BY created_at DESC LIMIT 5"
);

$totalEstoque = (int) fetchOne("SELECT COUNT(*) c FROM cars WHERE status='disponivel'")['c'];
$totalVendidos = (int) fetchOne("SELECT COUNT(*) c FROM cars WHERE status='vendido'")['c'];
$wa = config('contato')['whatsapp'];
?>

<?php if (!empty($bannerCars)): ?>
<section class="banner-carousel" id="bannerCarousel" data-count="<?= count($bannerCars) ?>">
  <div class="banner-track">
    <?php foreach ($bannerCars as $i => $car):
      $preco = $car['preco_promocional'] ?: $car['preco'];
      $msg = "Olá! Tenho interesse no {$car['marca']} {$car['modelo']} {$car['ano_modelo']} (" . brl($preco) . ').';
    ?>
    <article class="banner-slide <?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i ?>">
      <img src="<?= e($car['foto_capa']) ?>" alt="<?= e("$car[marca] $car[modelo]") ?>" loading="<?= $i === 0 ? 'eager' : 'lazy' ?>">
      <div class="banner-overlay"></div>
      <div class="container banner-content">
        <span class="chip yellow">★ Em destaque</span>
        <h2><?= e("$car[marca] $car[modelo]") ?> <span class="banner-year"><?= $car['ano_modelo'] ?></span></h2>
        <p class="banner-versao"><?= e($car['versao'] ?? '') ?></p>
        <div class="banner-meta">
          <span>📅 <?= $car['ano_fabricacao'] ?>/<?= $car['ano_modelo'] ?></span>
          <span>⚙️ <?= km($car['km']) ?></span>
          <?php if ($car['combustivel']): ?><span>⛽ <?= e($car['combustivel']) ?></span><?php endif; ?>
          <?php if ($car['cambio']): ?><span>🔧 <?= e($car['cambio']) ?></span><?php endif; ?>
        </div>
        <div class="banner-price">
          <?php if ($car['preco_promocional']): ?><span class="banner-price-old"><?= brl($car['preco']) ?></span><?php endif; ?>
          <strong><?= brl($preco) ?></strong>
        </div>
        <div class="banner-actions">
          <a href="/estoque/<?= $car['id'] ?>" class="btn btn-primary">Ver detalhes</a>
          <a href="<?= e(whatsappLink($wa, $msg)) ?>" target="_blank" class="btn btn-wa">💬 Falar no WhatsApp</a>
        </div>
      </div>
    </article>
    <?php endforeach; ?>
  </div>

  <?php if (count($bannerCars) > 1): ?>
    <button class="banner-nav prev" type="button" aria-label="Anterior" onclick="bannerGo(-1)">‹</button>
    <button class="banner-nav next" type="button" aria-label="Próximo" onclick="bannerGo(1)">›</button>
    <div class="banner-dots">
      <?php foreach ($bannerCars as $i => $_): ?>
        <button class="banner-dot <?= $i === 0 ? 'active' : '' ?>" type="button" data-go="<?= $i ?>" onclick="bannerSet(<?= $i ?>)" aria-label="Slide <?= $i+1 ?>"></button>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<script>
(function(){
  var root = document.getElementById('bannerCarousel'); if (!root) return;
  var n = parseInt(root.dataset.count, 10); if (n < 2) return;
  var idx = 0, timer;
  function show(i){
    idx = (i + n) % n;
    root.querySelectorAll('.banner-slide').forEach(function(s, k){ s.classList.toggle('active', k === idx); });
    root.querySelectorAll('.banner-dot').forEach(function(d, k){ d.classList.toggle('active', k === idx); });
  }
  window.bannerGo = function(d){ show(idx + d); reset(); };
  window.bannerSet = function(i){ show(i); reset(); };
  function reset(){ clearInterval(timer); timer = setInterval(function(){ show(idx + 1); }, 6000); }
  root.addEventListener('mouseenter', function(){ clearInterval(timer); });
  root.addEventListener('mouseleave', reset);
  reset();
})();
</script>
<?php endif; ?>

<section class="hero">
  <div class="hero-bg"></div><div class="hero-glow"></div>
  <div class="container" style="position:relative">
    <span class="chip yellow" style="background:rgba(242,226,5,.1);color:var(--yellow);border:1px solid rgba(242,226,5,.3)">✦ Seminovos com procedência</span>
    <h1>Seu próximo carro,<br><span class="accent">sem dor de cabeça.</span></h1>
    <p>Vistoria completa, financiamento aprovado em minutos e atendimento humano pelo WhatsApp. A gente cuida da burocracia — você sai dirigindo.</p>
    <div class="btns">
      <a href="/estoque" class="btn btn-primary">🔍 Ver os <?= $totalEstoque ?> carros disponíveis</a>
      <a href="<?= e(whatsappLink(config('contato')['whatsapp'], 'Olá! Vi o site e quero falar com um vendedor.')) ?>" target="_blank" class="btn btn-wa">Falar no WhatsApp</a>
    </div>
    <?php if ($totalVendidos > 0): ?>
    <p style="margin-top:2rem;color:var(--w50);font-size:.9rem">
      🏆 Mais de <strong style="color:var(--yellow)"><?= $totalVendidos ?> famílias</strong> já levaram o carro dos sonhos com a Nano.
    </p>
    <?php endif; ?>
  </div>
</section>

<section class="container feats">
  <div class="card feat">
    <div class="ic">🛡️</div>
    <h3>Vistoria de verdade</h3>
    <p>Cada veículo passa por 80+ pontos de checagem mecânica, elétrica e de procedência. Você só leva pra casa o que a gente levaria.</p>
  </div>
  <div class="card feat">
    <div class="ic">💰</div>
    <h3>Financiamento na hora</h3>
    <p>Parceria direta com Santander, Bradesco, BV e Itaú. Pré-aprovação em 10 minutos via WhatsApp — sem precisar vir até a loja.</p>
  </div>
  <div class="card feat">
    <div class="ic">🔁</div>
    <h3>Aceitamos seu usado</h3>
    <p>Avaliação justa pela tabela FIPE no mesmo dia. Use seu carro como parte do pagamento e financie só a diferença.</p>
  </div>
</section>

<section class="container" style="padding:4rem 0">
  <div class="section-head">
    <div><div class="eyebrow">Em destaque</div><h2>Os queridinhos da semana</h2></div>
    <a href="/estoque" class="text-sm" style="color:var(--w70)">Ver estoque completo →</a>
  </div>

  <?php if (empty($destaques)): ?>
    <div class="card text-center" style="padding:3rem">
      <p style="color:var(--w50)">Estamos reabastecendo o estoque. Volte em breve — ou nos chame no WhatsApp que a gente avisa quando chegar.</p>
      <a href="<?= e(whatsappLink(config('contato')['whatsapp'])) ?>" target="_blank" class="btn btn-wa mt-4">Me avisa no WhatsApp</a>
    </div>
  <?php else: ?>
    <div class="car-grid"><?php foreach ($destaques as $car) include __DIR__ . '/partials/car_card.php'; ?></div>
  <?php endif; ?>
</section>

<!-- Como funciona -->
<section class="container" style="padding:4rem 0;border-top:1px solid var(--w5)">
  <div class="section-head">
    <div><div class="eyebrow">Sem mistério</div><h2>Como funciona</h2></div>
  </div>
  <div class="feats" style="grid-template-columns:repeat(auto-fit,minmax(220px,1fr))">
    <div class="card feat">
      <div class="ic" style="font-size:1.5rem;font-weight:900;color:var(--yellow)">01</div>
      <h3 style="margin-top:.5rem">Escolha o carro</h3>
      <p>Veja o estoque, compare versões e tire dúvidas com a gente sem compromisso.</p>
    </div>
    <div class="card feat">
      <div class="ic" style="font-size:1.5rem;font-weight:900;color:var(--yellow)">02</div>
      <h3 style="margin-top:.5rem">Faça o test-drive</h3>
      <p>Marque um horário e venha sentir o carro. Café, água e zero pressão.</p>
    </div>
    <div class="card feat">
      <div class="ic" style="font-size:1.5rem;font-weight:900;color:var(--yellow)">03</div>
      <h3 style="margin-top:.5rem">Combine o pagamento</h3>
      <p>À vista, financiado ou com seu carro na troca. A gente monta a melhor proposta.</p>
    </div>
    <div class="card feat">
      <div class="ic" style="font-size:1.5rem;font-weight:900;color:var(--yellow)">04</div>
      <h3 style="margin-top:.5rem">Saia dirigindo</h3>
      <p>Documentação, transferência e entrega. Tudo no mesmo dia, sempre que possível.</p>
    </div>
  </div>
</section>

<section class="container" style="padding:5rem 0">
  <div class="card text-center" style="padding:3rem 2rem;background:linear-gradient(135deg, var(--gray), var(--black));border:1px solid rgba(242,226,5,.3)">
    <h2 style="font-size:2.25rem;font-weight:900">Não encontrou o que procura?</h2>
    <p style="color:var(--w70);margin:1rem auto;max-width:34rem">A gente busca o carro certo pra você. Manda no WhatsApp o que está procurando — modelo, ano, faixa de preço — e te chamamos quando aparecer.</p>
    <div class="flex gap-3 justify-center mt-6" style="flex-wrap:wrap">
      <a href="<?= e(whatsappLink(config('contato')['whatsapp'], 'Olá! Estou procurando um carro específico.')) ?>" target="_blank" class="btn btn-wa">Pedir pelo WhatsApp</a>
      <a href="/contato" class="btn btn-ghost">Preencher formulário</a>
    </div>
  </div>
</section>
