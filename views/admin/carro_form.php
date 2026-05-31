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

<!-- Consulta FIPE — preenche marca/modelo/ano automaticamente -->
<?php if (!$car): ?>
<div class="card form-section mb-4" style="border-color:rgba(242,226,5,.3);background:linear-gradient(135deg, rgba(242,226,5,.05), transparent)">
  <div class="flex items-center justify-between mb-4" style="flex-wrap:wrap;gap:.75rem">
    <div>
      <h2 style="margin-bottom:.25rem">🔍 Consulta FIPE</h2>
      <p style="color:var(--w70);font-size:.85rem;margin:0">Selecione marca → modelo → ano e o sistema preenche os campos automaticamente.</p>
    </div>
    <span class="chip yellow" style="font-size:.7rem">🚀 Economize tempo</span>
  </div>
  <div class="form-grid cols-3">
    <div>
      <label class="label">Marca FIPE</label>
      <select class="input" id="fipe-marca">
        <option value="">Carregando…</option>
      </select>
    </div>
    <div>
      <label class="label">Modelo FIPE</label>
      <select class="input" id="fipe-modelo" disabled>
        <option value="">Selecione a marca primeiro</option>
      </select>
    </div>
    <div>
      <label class="label">Ano FIPE</label>
      <select class="input" id="fipe-ano" disabled>
        <option value="">Selecione o modelo primeiro</option>
      </select>
    </div>
  </div>
  <div id="fipe-resultado" style="display:none;margin-top:1rem;padding:1rem;background:rgba(34,197,94,.1);border-radius:.5rem;border:1px solid rgba(34,197,94,.3)">
    <div class="flex items-center gap-2">
      <span style="font-size:1.25rem">✅</span>
      <strong style="color:#4ade80">Dados preenchidos automaticamente!</strong>
    </div>
    <p id="fipe-preco" style="margin-top:.5rem;font-size:.9rem;color:var(--w70)"></p>
  </div>
</div>
<?php endif; ?>

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

// =====================================================
// INTEGRAÇÃO API FIPE (parallelum) — preenche campos automaticamente
// =====================================================
(function() {
  const API = 'https://parallelum.com.br/fipe/api/v1/carros';
  const $marca   = document.getElementById('fipe-marca');
  const $modelo  = document.getElementById('fipe-modelo');
  const $ano     = document.getElementById('fipe-ano');
  const $result  = document.getElementById('fipe-resultado');
  const $preco   = document.getElementById('fipe-preco');

  if (!$marca) return; // somente no cadastro novo

  // 1) Carrega marcas ao abrir a página
  fetch(`${API}/marcas`)
    .then(r => r.json())
    .then(marcas => {
      $marca.innerHTML = '<option value="">Selecione…</option>' +
        marcas.map(m => `<option value="${m.codigo}" data-nome="${m.nome}">${m.nome}</option>`).join('');
    })
    .catch(() => {
      $marca.innerHTML = '<option value="">Erro ao carregar marcas FIPE</option>';
    });

  // 2) Ao mudar marca → carrega modelos
  $marca.addEventListener('change', () => {
    $modelo.innerHTML = '<option value="">Carregando…</option>';
    $modelo.disabled = true;
    $ano.innerHTML = '<option value="">Selecione o modelo primeiro</option>';
    $ano.disabled = true;
    $result.style.display = 'none';
    if (!$marca.value) return;
    fetch(`${API}/marcas/${$marca.value}/modelos`)
      .then(r => r.json())
      .then(data => {
        $modelo.innerHTML = '<option value="">Selecione…</option>' +
          data.modelos.map(m => `<option value="${m.codigo}" data-nome="${m.nome}">${m.nome}</option>`).join('');
        $modelo.disabled = false;
      });
  });

  // 3) Ao mudar modelo → carrega anos
  $modelo.addEventListener('change', () => {
    $ano.innerHTML = '<option value="">Carregando…</option>';
    $ano.disabled = true;
    $result.style.display = 'none';
    if (!$modelo.value) return;
    fetch(`${API}/marcas/${$marca.value}/modelos/${$modelo.value}/anos`)
      .then(r => r.json())
      .then(anos => {
        $ano.innerHTML = '<option value="">Selecione…</option>' +
          anos.map(a => `<option value="${a.codigo}">${a.nome}</option>`).join('');
        $ano.disabled = false;
      });
  });

  // 4) Ao mudar ano → busca dados completos e preenche os campos do form
  $ano.addEventListener('change', () => {
    if (!$ano.value) return;
    fetch(`${API}/marcas/${$marca.value}/modelos/${$modelo.value}/anos/${$ano.value}`)
      .then(r => r.json())
      .then(data => {
        // data: { Valor, Marca, Modelo, AnoModelo, Combustivel, CodigoFipe, MesReferencia, ... }
        const marca = data.Marca.toUpperCase();
        const modeloCompleto = data.Modelo;
        const anoModelo = parseInt(data.AnoModelo);
        const combustivel = mapearCombustivel(data.Combustivel);

        // Preenche os campos do formulário
        const f = document.querySelector('form[action="/admin/carros/salvar"]');
        f.marca.value = marca;
        // Tenta extrair só o modelo principal (primeira palavra) e a versão (resto)
        const partes = modeloCompleto.split(' ');
        f.modelo.value = partes[0].toUpperCase();
        if (partes.length > 1) {
          f.versao.value = partes.slice(1).join(' ');
        }
        f.ano_fabricacao.value = anoModelo === 32000 ? new Date().getFullYear() : anoModelo;
        f.ano_modelo.value = anoModelo === 32000 ? new Date().getFullYear() : anoModelo;
        if (combustivel) f.combustivel.value = combustivel;

        // Sugere preço FIPE como preço de partida (sem sobrescrever se já tiver)
        if (!f.preco.value) {
          const valor = parseFloat(data.Valor.replace(/[R$.\s]/g, '').replace(',', '.'));
          if (!isNaN(valor)) f.preco.value = valor.toFixed(2);
        }

        // Feedback visual
        $preco.innerHTML = `📊 <strong>Preço FIPE de referência:</strong> ${data.Valor} <span style="color:var(--w50)">(${data.MesReferencia})</span>`;
        $result.style.display = 'block';

        // Scroll suave até o campo de KM (próximo a preencher)
        f.km.focus();
      });
  });

  function mapearCombustivel(c) {
    if (!c) return null;
    const map = { 'Gasolina':'Gasolina', 'Álcool':'Álcool', 'Diesel':'Diesel', 'Flex':'Flex' };
    return map[c] || c;
  }
})();
</script>
