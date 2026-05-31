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

$totalEstoque = (int) fetchOne("SELECT COUNT(*) c FROM cars WHERE status='disponivel'")['c'];
$totalVendidos = (int) fetchOne("SELECT COUNT(*) c FROM cars WHERE status='vendido'")['c'];
?>
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
