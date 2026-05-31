<?php
$user = requireLogin();

// KPIs
$totalCarros   = fetchOne("SELECT COUNT(*) c FROM cars")['c'];
$disponíveis   = fetchOne("SELECT COUNT(*) c FROM cars WHERE status='disponivel'")['c'];
$vendidos      = fetchOne("SELECT COUNT(*) c FROM cars WHERE status='vendido'")['c'];
$leadsNovos    = fetchOne("SELECT COUNT(*) c FROM leads WHERE status='novo'")['c'];
$leadsTotal    = fetchOne("SELECT COUNT(*) c FROM leads")['c'];
$faturamento   = fetchOne("SELECT COALESCE(SUM(valor_venda),0) v FROM sales WHERE MONTH(data_venda)=MONTH(CURDATE()) AND YEAR(data_venda)=YEAR(CURDATE())")['v'];

// Últimos leads
$ultimosLeads = fetchAll("SELECT l.*, c.marca, c.modelo FROM leads l LEFT JOIN cars c ON c.id=l.car_id ORDER BY l.created_at DESC LIMIT 6");

// Carros mais vistos
$maisVistos = fetchAll("SELECT id, marca, modelo, visualizacoes, foto_capa FROM cars WHERE status='disponivel' ORDER BY visualizacoes DESC LIMIT 5");
?>

<div class="flex items-center justify-between mb-8">
  <div>
    <h1>Dashboard</h1>
    <p class="subtitle">Bem-vindo, <?= e($user['nome']) ?>. Aqui está o resumo do dia.</p>
  </div>
  <a href="/admin/carros/novo" class="btn btn-primary">+ Novo Carro</a>
</div>

<!-- KPIs -->
<div class="kpi-grid">
  <div class="card kpi">
    <div class="ic">🚗</div>
    <div class="val"><?= $totalCarros ?></div>
    <div class="lbl">Carros no Estoque</div>
    <div class="sub"><?= $disponíveis ?> disponíveis · <?= $vendidos ?> vendidos</div>
  </div>
  <div class="card kpi">
    <div class="ic">📩</div>
    <div class="val"><?= $leadsNovos ?></div>
    <div class="lbl">Leads Novos</div>
    <div class="sub"><?= $leadsTotal ?> total no CRM</div>
  </div>
  <div class="card kpi">
    <div class="ic">💰</div>
    <div class="val"><?= brl($faturamento) ?></div>
    <div class="lbl">Faturamento do Mês</div>
    <div class="sub">Vendas fechadas</div>
  </div>
  <div class="card kpi">
    <div class="ic">📊</div>
    <div class="val"><?= $vendidos ?></div>
    <div class="lbl">Vendas Realizadas</div>
    <div class="sub">Total de carros vendidos</div>
  </div>
</div>

<!-- Últimos Leads -->
<div class="flex items-center justify-between mt-10 mb-4">
  <h2 style="font-size:1.25rem;font-weight:700">Últimos Leads</h2>
  <a href="/admin/crm" class="btn btn-ghost sm">Ver todos</a>
</div>
<div class="card">
  <div class="table-wrap" style="margin:0">
    <table class="tbl">
      <thead>
        <tr>
          <th>Nome</th>
          <th>Telefone</th>
          <th>Carro</th>
          <th>Status</th>
          <th>Recebido</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$ultimosLeads): ?>
          <tr><td colspan="6" style="text-align:center;color:var(--w30);padding:2rem">Nenhum lead ainda.</td></tr>
        <?php endif; ?>
        <?php foreach ($ultimosLeads as $l): ?>
        <tr>
          <td><?= e($l['nome']) ?></td>
          <td><a href="<?= e(whatsappLink($l['telefone'])) ?>" target="_blank" class="text-yellow"><?= e($l['telefone']) ?></a></td>
          <td><?= $l['marca'] ? e($l['marca'] . ' ' . $l['modelo']) : '<span style="color:var(--w30)">—</span>' ?></td>
          <td><?php
            $sc = ['novo'=>'bg-green-500/15 text-green-400','em_atendimento'=>'bg-orange-500/15 text-orange-400','proposta'=>'','fechado'=>'','perdido'=>'bg-red-500/15 text-red-400'];
            $sl = LEAD_STATUS[$l['status']] ?? $l['status'];
            $sc2 = $sc[$l['status']] ?? 'bg-white/10 text-white/60';
            echo "<span class='chip $sc2'>$sl</span>";
          ?></td>
          <td style="color:var(--w50);font-size:.8rem"><?= date('d/m H:i', strtotime($l['created_at'])) ?></td>
          <td><a href="/admin/crm/<?= $l['id'] ?>" class="btn btn-ghost sm">Ver</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Mais vistos -->
<?php if ($maisVistos): ?>
<div class="flex items-center justify-between mt-10 mb-4">
  <h2 style="font-size:1.25rem;font-weight:700">Mais Vistos</h2>
  <a href="/admin/carros" class="btn btn-ghost sm">Ver estoque</a>
</div>
<div class="card">
  <div class="table-wrap" style="margin:0">
    <table class="tbl">
      <thead><tr><th>Carro</th><th>Visualizações</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($maisVistos as $c): ?>
        <tr>
          <td>
            <div class="flex items-center gap-3">
              <?php if ($c['foto_capa']): ?><img src="<?= e($c['foto_capa']) ?>" class="thumb" alt=""><?php endif; ?>
              <strong><?= e($c['marca'] . ' ' . $c['modelo']) ?></strong>
            </div>
          </td>
          <td><?= number_format($c['visualizacoes'], 0, ',', '.') ?></td>
          <td><a href="/estoque/<?= $c['id'] ?>" class="btn btn-ghost sm" target="_blank">Ver</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>
