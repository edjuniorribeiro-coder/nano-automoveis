<?php
$u = requireRole(['proprietario','financeiro','vendedor']);

$id   = isset($_GET['id']) ? (int)$_GET['id'] : null;
$car  = $id ? fetchOne('SELECT * FROM cars WHERE id = ?', [$id]) : null;

if ($id && !$car) { http_response_code(404); echo 'Carro não encontrado.'; exit; }

$fotos = [];
if ($car && $car['fotos']) {
    $decoded = json_decode($car['fotos'], true);
    if (is_array($decoded)) $fotos = $decoded;
}

$title = $car ? 'Editar Carro' : 'Novo Carro';
$v = fn($k, $d = '') => e($car[$k] ?? $d);

$marcasComuns = ['CHEVROLET','FIAT','FORD','HONDA','HYUNDAI','JAC','JEEP','KIA','MITSUBISHI','NISSAN','PEUGEOT','RENAULT','TOYOTA','VOLKSWAGEN','CAOA CHERY'];
$cores = ['Branco','Preto','Prata','Cinza','Azul','Vermelho','Verde','Bege','Dourado','Marrom','Laranja','Vinho'];
?>

<div class="flex items-center justify-between mb-6">
  <div>
    <h1><?= $title ?></h1>
    <p class="subtitle"><?= $car ? 'Atualize as informações do veículo.' : 'Preencha os dados para cadastrar um novo veículo.' ?></p>
  </div>
  <a href="/admin/carros" class="btn btn-ghost">← Voltar</a>
</div>

<form method="POST" action="/admin/carros/salvar" enctype="multipart/form-data">
  <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
  <?php if ($id): ?><input type="hidden" name="id" value="<?= $id ?>"><?php endif; ?>

  <!-- Identificação -->
  <div class="card form-section mb-4">
    <h2>Identificação</h2>
    <div class="form-grid cols-3">
      <div>
        <label class="label">Marca *</label>
        <input class="input" name="marca" list="marcas-list" required value="<?= $v('marca') ?>" placeholder="Ex.: VOLKSWAGEN">
        <datalist id="marcas-list">
          <?php foreach ($marcasComuns as $m): ?><option value="<?= e($m) ?>"><?php endforeach; ?>
        </datalist>
      </div>
      <div>
        <label class="label">Modelo *</label>
        <input class="input" name="modelo" required value="<?= $v('modelo') ?>" placeholder="Ex.: GOL">
      </div>
      <div>
        <label class="label">Versão</label>
        <input class="input" name="versao" value="<?= $v('versao') ?>" placeholder="Ex.: 1.0 TL Plus">
      </div>
    </div>
    <div class="form-grid cols-3 mt-4">
      <div>
        <label class="label">Ano Fab. *</label>
        <input class="input" type="number" name="ano_fabricacao" required value="<?= $v('ano_fabricacao', date('Y')) ?>" min="1950" max="<?= date('Y')+1 ?>">
      </div>
      <div>
        <label class="label">Ano Modelo *</label>
        <input class="input" type="number" name="ano_modelo" required value="<?= $v('ano_modelo', date('Y')) ?>" min="1950" max="<?= date('Y')+2 ?>">
      </div>
      <div>
        <label class="label">KM *</label>
        <input class="input" type="number" name="km" required value="<?= $v('km', 0) ?>" min="0">
      </div>
    </div>
  </div>

  <!-- Características -->
  <div class="card form-section mb-4">
    <h2>Características</h2>
    <div class="form-grid cols-3">
      <div>
        <label class="label">Cor</label>
        <input class="input" name="cor" list="cores-list" value="<?= $v('cor') ?>">
        <datalist id="cores-list">
          <?php foreach ($cores as $c2): ?><option value="<?= e($c2) ?>"><?php endforeach; ?>
        </datalist>
      </div>
      <div>
        <label class="label">Combustível</label>
        <select class="input" name="combustivel">
          <?php foreach ([''=>'—','Gasolina'=>'Gasolina','Álcool'=>'Álcool','Flex'=>'Flex','Diesel'=>'Diesel','Elétrico'=>'Elétrico','Híbrido'=>'Híbrido'] as $val => $lbl): ?>
            <option value="<?= e($val) ?>" <?= ($car['combustivel'] ?? '') === $val ? 'selected' : '' ?>><?= e($lbl) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="label">Câmbio</label>
        <select class="input" name="cambio">
          <?php foreach ([''=>'—','Manual'=>'Manual','Automático'=>'Automático','Automatizado'=>'Automatizado','CVT'=>'CVT'] as $val => $lbl): ?>
            <option value="<?= e($val) ?>" <?= ($car['cambio'] ?? '') === $val ? 'selected' : '' ?>><?= e($lbl) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="label">Carroceria</label>
        <select class="input" name="carroceria">
          <?php foreach ([''=>'—','Hatch'=>'Hatch','Sedan'=>'Sedan','SUV'=>'SUV','Picape'=>'Picape','Van'=>'Van','Minivan'=>'Minivan','Conversível'=>'Conversível','Coupé'=>'Coupé'] as $val => $lbl): ?>
            <option value="<?= e($val) ?>" <?= ($car['carroceria'] ?? '') === $val ? 'selected' : '' ?>><?= e($lbl) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="label">Portas</label>
        <select class="input" name="portas">
          <?php foreach ([''=>'—','2'=>'2','3'=>'3','4'=>'4','5'=>'5'] as $val => $lbl): ?>
            <option value="<?= e($val) ?>" <?= (string)($car['portas'] ?? '') === $val ? 'selected' : '' ?>><?= e($lbl) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
  </div>

  <!-- Preço & Status -->
  <div class="card form-section mb-4">
    <h2>Preço e Status</h2>
    <div class="form-grid cols-3">
      <div>
        <label class="label">Preço *</label>
        <input class="input" type="number" name="preco" step="0.01" required value="<?= $v('preco') ?>" placeholder="0.00">
      </div>
      <div>
        <label class="label">Preço Promocional</label>
        <input class="input" type="number" name="preco_promocional" step="0.01" value="<?= $v('preco_promocional') ?>" placeholder="0.00">
      </div>
      <div>
        <label class="label">Status *</label>
        <select class="input" name="status" required>
          <?php foreach (['disponivel'=>'Disponível','reservado'=>'Reservado','vendido'=>'Vendido','rascunho'=>'Rascunho'] as $val => $lbl): ?>
            <option value="<?= $val ?>" <?= ($car['status'] ?? 'disponivel') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="mt-4">
      <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer">
        <input type="checkbox" name="destaque" value="1" <?= !empty($car['destaque']) ? 'checked' : '' ?>>
        <span>Carro em destaque (aparece na home e no topo do estoque)</span>
      </label>
    </div>
  </div>

  <!-- Descrição & Opcionais -->
  <div class="card form-section mb-4">
    <h2>Descrição e Opcionais</h2>
    <div>
      <label class="label">Descrição</label>
      <textarea class="input" name="descricao" rows="4" placeholder="Descreva o veículo, histórico, estado de conservação…"><?= $v('descricao') ?></textarea>
    </div>
    <div class="mt-4">
      <label class="label">Opcionais (separados por vírgula)</label>
      <textarea class="input" name="opcionais" rows="3" placeholder="Ar-condicionado, Direção hidráulica, Vidros elétricos, Travas elétricas, Airbag…"><?= $v('opcionais') ?></textarea>
    </div>
  </div>

  <!-- Fotos -->
  <div class="card form-section mb-6">
    <h2>Fotos</h2>
    <p class="text-sm" style="color:var(--w50);margin-bottom:1rem">A primeira foto será a capa. Você pode enviar múltiplas fotos de uma vez (JPG, PNG, WebP).</p>

    <!-- Fotos existentes -->
    <?php
    $todasFotos = [];
    if ($car && $car['foto_capa']) $todasFotos[] = $car['foto_capa'];
    foreach ($fotos as $f) { if ($f !== ($car['foto_capa'] ?? null)) $todasFotos[] = $f; }
    ?>
    <div class="photo-grid" id="photos-existing">
      <?php foreach ($todasFotos as $foto): ?>
      <div class="photo-tile">
        <img src="<?= e($foto) ?>" alt="">
        <input type="hidden" name="fotos_existentes[]" value="<?= e($foto) ?>">
        <button type="button" class="x" onclick="this.closest('.photo-tile').remove()" title="Remover">✕</button>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Upload novos -->
    <div class="mt-4">
      <label class="photo-upload">
        <input type="file" name="fotos[]" multiple accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewFotos(this)">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
        <span class="mt-2 text-sm">Clique para adicionar fotos</span>
      </label>
    </div>
    <div class="photo-grid mt-3" id="photos-preview"></div>
  </div>

  <!-- Ações -->
  <div class="flex gap-3 justify-end">
    <a href="/admin/carros" class="btn btn-ghost">Cancelar</a>
    <button type="submit" class="btn btn-primary"><?= $car ? 'Salvar Alterações' : 'Cadastrar Carro' ?></button>
  </div>
</form>

<script>
function previewFotos(input) {
  const grid = document.getElementById('photos-preview');
  grid.innerHTML = '';
  Array.from(input.files).forEach(file => {
    const reader = new FileReader();
    reader.onload = e => {
      const tile = document.createElement('div');
      tile.className = 'photo-tile';
      tile.innerHTML = `<img src="${e.target.result}" alt="">`;
      grid.appendChild(tile);
    };
    reader.readAsDataURL(file);
  });
}
</script>
