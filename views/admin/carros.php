<?php
$user = requireLogin();

// Filtros
$marca     = trim($_GET['marca'] ?? '');
$status    = trim($_GET['status'] ?? '');
$busca     = trim($_GET['busca'] ?? '');

$where  = ['1=1'];
$params = [];

if ($marca) { $where[] = 'marca = ?'; $params[] = $marca; }
if ($status) { $where[] = 'status = ?'; $params[] = $status; }
if ($busca) {
    $where[] = '(marca LIKE ? OR modelo LIKE ? OR versao LIKE ?)';
    $like = '%' . $busca . '%';
    array_push($params, $like, $like, $like);
}

$sql    = 'SELECT * FROM cars WHERE ' . implode(' AND ', $where) . ' ORDER BY created_at DESC';
$carros = fetchAll($sql, $params);
$marcas = fetchAll("SELECT DISTINCT marca FROM cars ORDER BY marca");
?>

<div class="flex items-center justify-between mb-6">
  <div>
    <h1>Estoque de Carros</h1>
    <p class="subtitle"><?= count($carros) ?> veículo(s) encontrado(s)</p>
  </div>
  <a href="/admin/carros/novo" class="btn btn-primary">+ Novo Carro</a>
</div>

<!-- Filtros -->
<form method="GET" action="/admin/carros" class="card" style="padding:1rem;margin-bottom:1.5rem">
  <div class="form-grid" style="grid-template-columns:2fr 1fr 1fr auto">
    <div>
      <label class="label">Buscar</label>
      <input class="input" name="busca" placeholder="Marca, modelo, versão…" value="<?= e($busca) ?>">
    </div>
    <div>
      <label class="label">Marca</label>
      <select class="input" name="marca">
        <option value="">Todas</option>
        <?php foreach ($marcas as $m): ?>
          <option value="<?= e($m['marca']) ?>" <?= $marca === $m['marca'] ? 'selected' : '' ?>><?= e($m['marca']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="label">Status</label>
      <select class="input" name="status">
        <option value="">Todos</option>
        <option value="disponivel" <?= $status === 'disponivel' ? 'selected' : '' ?>>Disponível</option>
        <option value="reservado"  <?= $status === 'reservado'  ? 'selected' : '' ?>>Reservado</option>
        <option value="vendido"    <?= $status === 'vendido'    ? 'selected' : '' ?>>Vendido</option>
        <option value="rascunho"   <?= $status === 'rascunho'   ? 'selected' : '' ?>>Rascunho</option>
      </select>
    </div>
    <div style="display:flex;align-items:flex-end;gap:.5rem">
      <button type="submit" class="btn btn-primary sm">Filtrar</button>
      <?php if ($marca || $status || $busca): ?>
        <a href="/admin/carros" class="btn btn-ghost sm">Limpar</a>
      <?php endif; ?>
    </div>
  </div>
</form>

<!-- Tabela -->
<div class="card">
  <div class="table-wrap" style="margin:0">
    <table class="tbl">
      <thead>
        <tr>
          <th>Foto</th>
          <th>Veículo</th>
          <th>Ano</th>
          <th>KM</th>
          <th>Preço</th>
          <th>Status</th>
          <th>Destaque</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$carros): ?>
          <tr><td colspan="8" style="text-align:center;color:var(--w30);padding:3rem">Nenhum carro cadastrado.</td></tr>
        <?php endif; ?>
        <?php foreach ($carros as $c): ?>
        <tr>
          <td>
            <?php if ($c['foto_capa']): ?>
              <img src="<?= e($c['foto_capa']) ?>" class="thumb" alt="">
            <?php else: ?>
              <div class="thumb" style="background:var(--gray-2);display:flex;align-items:center;justify-content:center;border-radius:.25rem;font-size:1.5rem">🚗</div>
            <?php endif; ?>
          </td>
          <td>
            <strong style="text-transform:uppercase"><?= e($c['marca'] . ' ' . $c['modelo']) ?></strong>
            <?php if ($c['versao']): ?><div style="font-size:.8rem;color:var(--w50)"><?= e($c['versao']) ?></div><?php endif; ?>
          </td>
          <td><?= $c['ano_fabricacao'] ?>/<?= $c['ano_modelo'] ?></td>
          <td><?= km($c['km']) ?></td>
          <td>
            <?php if ($c['preco_promocional']): ?>
              <div style="font-size:.75rem;color:var(--w30);text-decoration:line-through"><?= brl($c['preco']) ?></div>
              <strong class="text-yellow"><?= brl($c['preco_promocional']) ?></strong>
            <?php else: ?>
              <strong class="text-yellow"><?= brl($c['preco']) ?></strong>
            <?php endif; ?>
          </td>
          <td><?= statusBadge($c['status']) ?></td>
          <td><?= $c['destaque'] ? '<span class="chip yellow">⭐ Destaque</span>' : '<span style="color:var(--w30)">—</span>' ?></td>
          <td>
            <div class="flex gap-2">
              <a href="/admin/carros/<?= $c['id'] ?>" class="btn btn-ghost sm">Editar</a>
              <a href="/estoque/<?= $c['id'] ?>" class="btn btn-ghost sm" target="_blank">Ver</a>
              <?php if (in_array($user['role'], ['proprietario'], true)): ?>
              <form method="POST" action="/admin/carros/excluir" onsubmit="return confirm('Excluir este carro?')">
                <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                <button type="submit" class="btn btn-danger sm">✕</button>
              </form>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
