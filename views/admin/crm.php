<?php
$u = requireLogin();

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

// Para os modais
$carrosAtivos = fetchAll("SELECT id, marca, modelo, ano_fabricacao FROM cars WHERE status IN ('disponivel','reservado') ORDER BY marca, modelo");
$vendedores   = fetchAll("SELECT id, nome FROM users WHERE ativo=1 AND role IN ('proprietario','financeiro','vendedor') ORDER BY nome");

$totalLeads = $viewMode === 'list' ? count($leads) : count($allLeads);
?>

<div class="flex items-center justify-between mb-6" style="flex-wrap:wrap;gap:1rem">
  <div>
    <h1>CRM / Leads</h1>
    <p class="subtitle"><?= $totalLeads ?> contato(s) no funil de vendas</p>
  </div>
  <div class="flex gap-2">
    <button type="button" class="btn btn-ghost" onclick="document.getElementById('modal-importar').style.display='flex'">
      ⬆️ Importar CSV
    </button>
    <button type="button" class="btn btn-primary" onclick="document.getElementById('modal-novo-lead').style.display='flex'">
      + Novo Lead
    </button>
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
        <tr><th>Nome</th><th>Telefone</th><th>Carro</th><th>Origem</th><th>Status</th><th>Data</th><th>Ações</th></tr>
      </thead>
      <tbody>
        <?php if (!$leads): ?>
          <tr><td colspan="7" style="text-align:center;color:var(--w30);padding:2rem">Nenhum lead encontrado.</td></tr>
        <?php endif; ?>
        <?php foreach ($leads as $l):
          $waLink = whatsappLink($l['telefone'], 'Olá ' . explode(' ', $l['nome'])[0] . ', tudo bem?');
        ?>
        <tr>
          <td><strong><?= e($l['nome']) ?></strong></td>
          <td>
            <div class="flex items-center gap-2">
              <span style="color:var(--w70)"><?= e($l['telefone']) ?></span>
              <a href="<?= e($waLink) ?>" target="_blank" class="btn-wa-icon" title="Abrir no WhatsApp">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
              </a>
            </div>
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
      <?php foreach ($colLeads as $l):
        $waLink = whatsappLink($l['telefone'], 'Olá ' . explode(' ', $l['nome'])[0] . ', tudo bem? Sou da Nano Automóveis.');
      ?>
      <div class="lead-card">
        <a href="/admin/crm/<?= $l['id'] ?>" style="display:block">
          <div class="nome"><?= e($l['nome']) ?></div>
          <?php if ($l['marca']): ?><div class="carro">🚗 <?= e($l['marca'] . ' ' . $l['modelo']) ?></div><?php endif; ?>
          <div class="fone">📱 <?= e($l['telefone']) ?></div>
          <div class="date"><?= date('d/m H:i', strtotime($l['created_at'])) ?></div>
        </a>
        <div class="qa">
          <a href="<?= e($waLink) ?>" target="_blank" class="btn-wa-mini" title="Abrir WhatsApp">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
            WhatsApp
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>


<!-- ============================================ -->
<!-- MODAL: NOVO LEAD -->
<!-- ============================================ -->
<div id="modal-novo-lead" class="modal-overlay" style="display:none">
  <div class="card" style="width:100%;max-width:560px;padding:2rem;max-height:90vh;overflow-y:auto">
    <div class="flex items-center justify-between mb-6">
      <h2 style="font-weight:700;font-size:1.25rem">Novo Lead</h2>
      <button type="button" class="btn btn-ghost sm" onclick="document.getElementById('modal-novo-lead').style.display='none'">✕</button>
    </div>
    <form method="POST" action="/admin/crm/novo">
      <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
      <div class="form-grid cols-2">
        <div>
          <label class="label">Nome *</label>
          <input class="input" name="nome" required placeholder="Nome do contato">
        </div>
        <div>
          <label class="label">Telefone (WhatsApp) *</label>
          <input class="input" name="telefone" required placeholder="(11) 99999-9999">
        </div>
        <div>
          <label class="label">E-mail</label>
          <input class="input" type="email" name="email" placeholder="opcional">
        </div>
        <div>
          <label class="label">Origem</label>
          <select class="input" name="origem">
            <option value="site">Site</option>
            <option value="whatsapp">WhatsApp</option>
            <option value="instagram">Instagram</option>
            <option value="indicacao">Indicação</option>
            <option value="outro" selected>Outro</option>
          </select>
        </div>
        <div>
          <label class="label">Carro de interesse</label>
          <select class="input" name="car_id">
            <option value="">— Sem carro específico —</option>
            <?php foreach ($carrosAtivos as $c): ?>
              <option value="<?= $c['id'] ?>"><?= e($c['marca'] . ' ' . $c['modelo'] . ' ' . $c['ano_fabricacao']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="label">Responsável</label>
          <select class="input" name="responsavel_id">
            <option value="">— Eu mesmo —</option>
            <?php foreach ($vendedores as $v): ?>
              <option value="<?= $v['id'] ?>"><?= e($v['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div style="grid-column:1/-1">
          <label class="label">Mensagem / Observação</label>
          <textarea class="input" name="mensagem" rows="3" placeholder="Anote o contexto do contato…"></textarea>
        </div>
        <div style="grid-column:1/-1">
          <label class="label">Status inicial</label>
          <select class="input" name="status">
            <?php foreach (LEAD_STATUS as $k => $v): ?>
              <option value="<?= e($k) ?>" <?= $k === 'novo' ? 'selected' : '' ?>><?= e($v) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="flex gap-3 justify-end mt-6">
        <button type="button" class="btn btn-ghost" onclick="document.getElementById('modal-novo-lead').style.display='none'">Cancelar</button>
        <button type="submit" class="btn btn-primary">Criar Lead</button>
      </div>
    </form>
  </div>
</div>

<!-- ============================================ -->
<!-- MODAL: IMPORTAR CSV -->
<!-- ============================================ -->
<div id="modal-importar" class="modal-overlay" style="display:none">
  <div class="card" style="width:100%;max-width:560px;padding:2rem">
    <div class="flex items-center justify-between mb-6">
      <h2 style="font-weight:700;font-size:1.25rem">Importar Leads (CSV)</h2>
      <button type="button" class="btn btn-ghost sm" onclick="document.getElementById('modal-importar').style.display='none'">✕</button>
    </div>
    <form method="POST" action="/admin/crm/importar" enctype="multipart/form-data">
      <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">

      <div style="background:rgba(242,226,5,.08);border:1px solid rgba(242,226,5,.2);border-radius:.5rem;padding:1rem;margin-bottom:1rem">
        <strong style="color:var(--yellow);font-size:.85rem">📋 Formato esperado do CSV</strong>
        <p style="font-size:.8rem;color:var(--w70);margin-top:.5rem">
          Primeira linha deve ser o cabeçalho. Colunas aceitas:<br>
          <code style="background:var(--black);padding:.15rem .4rem;border-radius:.25rem;font-size:.75rem">nome</code>,
          <code style="background:var(--black);padding:.15rem .4rem;border-radius:.25rem;font-size:.75rem">telefone</code> <em>(obrigatórias)</em>,
          <code style="background:var(--black);padding:.15rem .4rem;border-radius:.25rem;font-size:.75rem">email</code>,
          <code style="background:var(--black);padding:.15rem .4rem;border-radius:.25rem;font-size:.75rem">mensagem</code>,
          <code style="background:var(--black);padding:.15rem .4rem;border-radius:.25rem;font-size:.75rem">origem</code>
        </p>
        <p style="font-size:.75rem;color:var(--w50);margin-top:.5rem">
          Separadores aceitos: vírgula, ponto-e-vírgula ou tabulação. Encoding UTF-8 ou ISO-8859-1.
        </p>
      </div>

      <div>
        <label class="label">Arquivo CSV</label>
        <input class="input" type="file" name="csv" accept=".csv,.txt" required>
      </div>

      <details style="margin-top:1rem">
        <summary style="cursor:pointer;color:var(--yellow);font-size:.85rem;font-weight:500">Ver exemplo de CSV</summary>
        <pre style="margin-top:.5rem;background:var(--black);padding:.75rem;border-radius:.5rem;font-size:.75rem;color:var(--w70);overflow:auto">nome,telefone,email,mensagem,origem
João da Silva,(11) 98765-4321,joao@email.com,Quero ver o Onix,site
Maria Souza,(11) 99876-5432,,Interesse em SUV,whatsapp
Pedro Lima,(11) 97654-3210,pedro@email.com,Indicação do Carlos,indicacao</pre>
      </details>

      <div class="flex gap-3 justify-end mt-6">
        <button type="button" class="btn btn-ghost" onclick="document.getElementById('modal-importar').style.display='none'">Cancelar</button>
        <button type="submit" class="btn btn-primary">Importar</button>
      </div>
    </form>
  </div>
</div>
