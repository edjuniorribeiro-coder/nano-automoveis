<?php
requireLogin();

$status  = trim($_GET['status'] ?? '');
$busca   = trim($_GET['busca'] ?? '');

// Leads agrupados por status para o kanban
$statuses = array_keys(LEAD_STATUS);

// Se filtro ativo, lista flat
if ($status || $busca) {
    $where  = ['1=1'];
    $params = [];
    if ($status) { $where[] = 'l.status = ?'; $params[] = $status; }
    if ($busca) {
        $where[] = '(l.nome LIKE ? OR l.telefone LIKE ? OR l.email LIKE ?)';
        $like = '%' . $busca . '%';
        array_push($params, $like, $like, $like);
    }
    $leads = fetchAll("SELECT l.*, c.marca, c.modelo FROM leads l LEFT JOIN cars c ON c.id=l.car_id WHERE " . implode(' AND ', $where) . " ORDER BY l.created_at DESC", $params);
    $viewMode = 'list';
} else {
    // Kanban agrupado
    $allLeads = fetchAll("SELECT l.*, c.marca, c.modelo FROM leads l LEFT JOIN cars c ON c.id=l.car_id ORDER BY l.created_at DESC");
    $byStatus = array_fill_keys($statuses, []);
    foreach ($allLeads as $l) {
        $byStatus[$l['status']][] = $l;
    }
    $viewMode = 'kanban';
}
?>

<div class="flex items-center justify-between mb-6">
  <div>
    <h1>CRM / Leads</h1>
    <p class="subtitle">Gerencie os contatos e oportunidades de venda.</p>
  </div>
</div>

<!-- Filtros -->
<form method="GET" action="/admin/crm" class="flex gap-3 mb-6" style="flex-wrap:wrap">
  <input class="input" name="busca" style="max-width:18rem" placeholder="Buscar por nome, tel, e-mail…" value="<?= e($busca) ?>">
  <select class="input" name="status" style="max-width:12rem">
    <option value="">Todos os status</option>
    <?php foreach (LEAD_STATUS as $k => $v): ?>
      <option value="<?= e($k) ?>" <?= $status === $k ? 'selected' : '' ?>><?= e($v) ?></option>
    <?php endforeach; ?>
  </select>
  <button type="submit" class="btn btn-primary sm">Filtrar</button>
  <?php if ($status || $busca): ?>
    <a href="/admin/crm" class="btn btn-ghost sm">Limpar</a>
  <?php endif; ?>
</form>

<?php if ($viewMode === 'list'): ?>
<!-- Modo lista (com filtro) -->
<div class="card">
  <div class="table-wrap" style="margin:0">
    <table class="tbl">
      <thead>
        <tr><th>Nome</th><th>Telefone</th><th>Carro</th><th>Origem</th><th>Status</th><th>Data</th><th></th></tr>
      </thead>
      <tbody>
        <?php if (!$leads): ?>
          <tr><td colspan="7" style="text-align:center;color:var(--w30);padding:2rem">Nenhum lead encontrado.</td></tr>
        <?php endif; ?>
        <?php foreach ($leads as $l): ?>
        <tr>
          <td><strong><?= e($l['nome']) ?></strong></td>
          <td>
            <a href="<?= e(whatsappLink($l['telefone'])) ?>" target="_blank" class="text-yellow"><?= e($l['telefone']) ?></a>
          </td>
          <td><?= $l['marca'] ? e($l['marca'] . ' ' . $l['modelo']) : '<span style="color:var(--w30)">—</span>' ?></td>
          <td style="text-transform:capitalize"><?= e($l['origem']) ?></td>
          <td><span class="chip dark"><?= e(LEAD_STATUS[$l['status']] ?? $l['status']) ?></span></td>
          <td style="color:var(--w50);font-size:.8rem"><?= date('d/m/y', strtotime($l['created_at'])) ?></td>
          <td><a href="/admin/crm/<?= $l['id'] ?>" class="btn btn-ghost sm">Ver</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php else: ?>
<!-- Modo kanban -->
<div class="kanban">
  <?php foreach ($statuses as $st):
    $colLeads = $byStatus[$st];
    $colors = ['novo'=>'#F2E205','em_atendimento'=>'#60a5fa','proposta'=>'#c084fc','fechado'=>'#4ade80','perdido'=>'#f87171'];
  ?>
  <div class="card kanban-col" data-status="<?= $st ?>">
    <div class="kanban-head">
      <h3><?= e(LEAD_STATUS[$st]) ?></h3>
      <span class="chip dark"><?= count($colLeads) ?></span>
    </div>
    <div class="kanban-body">
      <?php if (!$colLeads): ?>
        <p style="color:var(--w30);font-size:.8rem;text-align:center;padding:.75rem 0">Nenhum lead</p>
      <?php endif; ?>
      <?php foreach ($colLeads as $l): ?>
      <a href="/admin/crm/<?= $l['id'] ?>" class="lead-card">
        <div class="nome"><?= e($l['nome']) ?></div>
        <?php if ($l['marca']): ?><div class="carro">🚗 <?= e($l['marca'] . ' ' . $l['modelo']) ?></div><?php endif; ?>
        <div class="fone">📱 <?= e($l['telefone']) ?></div>
        <div class="date"><?= date('d/m H:i', strtotime($l['created_at'])) ?></div>
        <div class="qa">
          <a href="<?= e(whatsappLink($l['telefone'], 'Olá ' . $l['nome'] . ', tudo bem?')) ?>" target="_blank" class="btn btn-wa sm" onclick="event.stopPropagation()" style="font-size:.7rem;padding:.2rem .5rem">WhatsApp</a>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>
