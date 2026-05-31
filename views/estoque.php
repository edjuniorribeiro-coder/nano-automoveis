<?php
$where = ["status IN ('disponivel','reservado')"];
$params = [];
if (!empty($_GET['marca'])) { $where[] = 'marca = ?'; $params[] = $_GET['marca']; }
if (!empty($_GET['min']))   { $where[] = 'preco >= ?'; $params[] = (float)$_GET['min']; }
if (!empty($_GET['max']))   { $where[] = 'preco <= ?'; $params[] = (float)$_GET['max']; }
if (!empty($_GET['q'])) {
  $where[] = '(modelo LIKE ? OR marca LIKE ?)';
  $params[] = '%' . $_GET['q'] . '%'; $params[] = '%' . $_GET['q'] . '%';
}
$sql = 'SELECT * FROM cars WHERE ' . implode(' AND ', $where) . ' ORDER BY destaque DESC, created_at DESC';
$cars = fetchAll($sql, $params);
$marcas = array_column(fetchAll("SELECT DISTINCT marca FROM cars WHERE status IN ('disponivel','reservado') ORDER BY marca"), 'marca');
?>
<div class="container" style="padding:3rem 0">
  <h1 style="font-size:3rem;font-weight:900">Estoque</h1>
  <p style="color:var(--w70);margin-top:.5rem"><?= count($cars) ?> veículo(s) disponíveis</p>

  <form class="filters" method="get" action="/estoque">
    <input name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="Buscar marca/modelo" class="input">
    <select name="marca" class="input">
      <option value="">Todas as marcas</option>
      <?php foreach ($marcas as $m): ?>
        <option value="<?= e($m) ?>" <?= ($_GET['marca'] ?? '') === $m ? 'selected' : '' ?>><?= e($m) ?></option>
      <?php endforeach; ?>
    </select>
    <input name="min" type="number" value="<?= e($_GET['min'] ?? '') ?>" placeholder="Preço mín." class="input">
    <input name="max" type="number" value="<?= e($_GET['max'] ?? '') ?>" placeholder="Preço máx." class="input">
    <button class="btn btn-primary">Filtrar</button>
  </form>

  <?php if (empty($cars)): ?>
    <div class="card text-center" style="padding:3rem"><p style="color:var(--w50)">Nenhum veículo encontrado.</p></div>
  <?php else: ?>
    <div class="car-grid"><?php foreach ($cars as $car) include __DIR__ . '/partials/car_card.php'; ?></div>
  <?php endif; ?>
</div>
