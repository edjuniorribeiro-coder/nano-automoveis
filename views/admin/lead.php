<?php
$u = requireLogin();

$id   = (int)($_GET['id'] ?? 0);
$lead = fetchOne("SELECT l.*, c.marca, c.modelo, c.ano_fabricacao, c.preco FROM leads l LEFT JOIN cars c ON c.id=l.car_id WHERE l.id=?", [$id]);
if (!$lead) { http_response_code(404); echo 'Lead não encontrado.'; exit; }

$interacoes   = fetchAll("SELECT i.*, u.nome as autor FROM lead_interactions i LEFT JOIN users u ON u.id=i.user_id WHERE i.lead_id=? ORDER BY i.created_at ASC", [$id]);
$vendedores   = fetchAll("SELECT id, nome FROM users WHERE ativo=1 AND role IN ('proprietario','financeiro','vendedor') ORDER BY nome");

$tipoIcons = ['nota'=>'📝','ligacao'=>'📞','whatsapp'=>'💬','visita'=>'🤝','email'=>'✉️'];
?>

<div class="flex items-center justify-between mb-6">
  <div>
    <h1><?= e($lead['nome']) ?></h1>
    <p class="subtitle">Lead #<?= $id ?> · <?= e(LEAD_STATUS[$lead['status']] ?? $lead['status']) ?></p>
  </div>
  <a href="/admin/crm" class="btn btn-ghost">← CRM</a>
</div>

<div style="display:grid;grid-template-columns:1fr 340px;gap:1.5rem;align-items:start">

  <!-- Coluna principal: histórico de interações -->
  <div>
    <!-- Timeline -->
    <div class="card" style="padding:1.5rem;margin-bottom:1rem">
      <h2 style="font-weight:700;margin-bottom:1.25rem">Histórico de Interações</h2>

      <?php if (!$interacoes): ?>
        <p style="color:var(--w30)">Nenhuma interação registrada ainda.</p>
      <?php endif; ?>

      <?php foreach ($interacoes as $i): ?>
      <div class="flex gap-3" style="margin-bottom:1.5rem">
        <div style="font-size:1.4rem;flex-shrink:0"><?= $tipoIcons[$i['tipo']] ?? '💬' ?></div>
        <div style="flex:1">
          <div class="flex items-center gap-2" style="margin-bottom:.35rem">
            <strong style="font-size:.9rem"><?= e($i['autor'] ?? 'Sistema') ?></strong>
            <span style="font-size:.75rem;color:var(--w30)"><?= date('d/m/Y H:i', strtotime($i['created_at'])) ?></span>
            <span class="chip dark" style="font-size:.65rem"><?= e(ucfirst($i['tipo'])) ?></span>
          </div>
          <p style="font-size:.9rem;color:var(--w70);white-space:pre-wrap"><?= e($i['conteudo']) ?></p>
        </div>
      </div>
      <?php endforeach; ?>

      <!-- Formulário nova interação -->
      <?php if (in_array($u['role'], ['proprietario','financeiro','vendedor'], true)): ?>
      <div style="border-top:1px solid var(--w5);margin-top:1.5rem;padding-top:1.5rem">
        <h3 style="font-weight:700;margin-bottom:1rem">Nova Interação</h3>
        <form method="POST" action="/admin/crm/interacao">
          <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
          <input type="hidden" name="lead_id" value="<?= $id ?>">
          <div class="form-grid cols-2" style="margin-bottom:.75rem">
            <div>
              <label class="label">Tipo</label>
              <select class="input" name="tipo">
                <?php foreach (['nota'=>'📝 Nota','ligacao'=>'📞 Ligação','whatsapp'=>'💬 WhatsApp','visita'=>'🤝 Visita','email'=>'✉️ E-mail'] as $val => $lbl): ?>
                  <option value="<?= $val ?>"><?= $lbl ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div style="margin-bottom:.75rem">
            <label class="label">Conteúdo</label>
            <textarea class="input" name="conteudo" rows="3" required placeholder="Descreva a interação…"></textarea>
          </div>
          <button type="submit" class="btn btn-primary sm">Registrar Interação</button>
        </form>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Coluna lateral: dados do lead -->
  <div>
    <!-- Dados de contato -->
    <div class="card" style="padding:1.5rem;margin-bottom:1rem">
      <h2 style="font-weight:700;margin-bottom:1rem">Contato</h2>
      <dl style="display:grid;gap:.75rem">
        <div>
          <dt style="font-size:.75rem;color:var(--w50)">Telefone</dt>
          <dd>
            <a href="<?= e(whatsappLink($lead['telefone'])) ?>" target="_blank" class="text-yellow btn btn-wa sm mt-2" style="display:inline-flex">
              💬 <?= e($lead['telefone']) ?>
            </a>
          </dd>
        </div>
        <?php if ($lead['email']): ?>
        <div>
          <dt style="font-size:.75rem;color:var(--w50)">E-mail</dt>
          <dd><a href="mailto:<?= e($lead['email']) ?>" class="text-yellow"><?= e($lead['email']) ?></a></dd>
        </div>
        <?php endif; ?>
        <div>
          <dt style="font-size:.75rem;color:var(--w50)">Origem</dt>
          <dd style="text-transform:capitalize"><?= e($lead['origem']) ?></dd>
        </div>
        <div>
          <dt style="font-size:.75rem;color:var(--w50)">Recebido em</dt>
          <dd style="font-size:.85rem"><?= date('d/m/Y H:i', strtotime($lead['created_at'])) ?></dd>
        </div>
        <?php if ($lead['mensagem']): ?>
        <div>
          <dt style="font-size:.75rem;color:var(--w50)">Mensagem</dt>
          <dd style="font-size:.85rem;color:var(--w70);white-space:pre-wrap"><?= e($lead['mensagem']) ?></dd>
        </div>
        <?php endif; ?>
      </dl>
    </div>

    <!-- Carro de interesse -->
    <?php if ($lead['marca']): ?>
    <div class="card" style="padding:1.5rem;margin-bottom:1rem">
      <h2 style="font-weight:700;margin-bottom:.75rem">Carro de Interesse</h2>
      <strong><?= e($lead['marca'] . ' ' . $lead['modelo']) ?></strong>
      <?php if ($lead['ano_fabricacao']): ?><div style="font-size:.85rem;color:var(--w50)"><?= $lead['ano_fabricacao'] ?></div><?php endif; ?>
      <?php if ($lead['preco']): ?><div class="text-yellow font-bold mt-2"><?= brl($lead['preco']) ?></div><?php endif; ?>
      <a href="/estoque/<?= $lead['car_id'] ?>" target="_blank" class="btn btn-ghost sm mt-4">Ver anúncio</a>
    </div>
    <?php endif; ?>

    <!-- Atualizar status -->
    <?php if (in_array($u['role'], ['proprietario','financeiro','vendedor'], true)): ?>
    <div class="card" style="padding:1.5rem">
      <h2 style="font-weight:700;margin-bottom:1rem">Atualizar</h2>
      <form method="POST" action="/admin/crm/atualizar">
        <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="id" value="<?= $id ?>">
        <div style="margin-bottom:.75rem">
          <label class="label">Status</label>
          <select class="input" name="status">
            <?php foreach (LEAD_STATUS as $k => $v): ?>
              <option value="<?= e($k) ?>" <?= $lead['status'] === $k ? 'selected' : '' ?>><?= e($v) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div style="margin-bottom:.75rem">
          <label class="label">Responsável</label>
          <select class="input" name="responsavel_id">
            <option value="">— Não atribuído —</option>
            <?php foreach ($vendedores as $v): ?>
              <option value="<?= $v['id'] ?>" <?= $lead['responsavel_id'] == $v['id'] ? 'selected' : '' ?>><?= e($v['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <button type="submit" class="btn btn-primary sm" style="width:100%">Salvar</button>
      </form>
    </div>
    <?php endif; ?>
  </div>

</div>
