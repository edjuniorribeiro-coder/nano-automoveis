<?php
$u = requireRole(['proprietario','financeiro']);

// Filtro período
$mes = (int)($_GET['mes'] ?? date('m'));
$ano = (int)($_GET['ano'] ?? date('Y'));

$primeiroDia = sprintf('%04d-%02d-01', $ano, $mes);
$ultimoDia   = date('Y-m-t', strtotime($primeiroDia));

// KPIs do mês
$totalVendas     = fetchOne("SELECT COUNT(*) c FROM sales WHERE data_venda BETWEEN ? AND ?", [$primeiroDia, $ultimoDia])['c'];
$faturamento     = fetchOne("SELECT COALESCE(SUM(valor_venda),0) v FROM sales WHERE data_venda BETWEEN ? AND ?", [$primeiroDia, $ultimoDia])['v'];
$totalComissoes  = fetchOne("SELECT COALESCE(SUM(comissao),0) v FROM sales WHERE data_venda BETWEEN ? AND ?", [$primeiroDia, $ultimoDia])['v'];
$ticketMedio     = $totalVendas > 0 ? ($faturamento / $totalVendas) : 0;

// Vendas do período
$vendas = fetchAll("
  SELECT s.*, c.marca, c.modelo, c.ano_fabricacao, u.nome as vendedor_nome
  FROM sales s
  JOIN cars c ON c.id = s.car_id
  LEFT JOIN users u ON u.id = s.vendedor_id
  WHERE s.data_venda BETWEEN ? AND ?
  ORDER BY s.data_venda DESC
", [$primeiroDia, $ultimoDia]);

// Formulário novo registro de venda
$carrosVendidos = fetchAll("SELECT id, marca, modelo, ano_fabricacao FROM cars WHERE status='vendido' ORDER BY marca, modelo");
$vendedores     = fetchAll("SELECT id, nome FROM users WHERE ativo=1 AND role IN ('proprietario','financeiro','vendedor') ORDER BY nome");

// Meses em português para o seletor
$meses = ['01'=>'Janeiro','02'=>'Fevereiro','03'=>'Março','04'=>'Abril','05'=>'Maio','06'=>'Junho','07'=>'Julho','08'=>'Agosto','09'=>'Setembro','10'=>'Outubro','11'=>'Novembro','12'=>'Dezembro'];
?>

<div class="flex items-center justify-between mb-6">
  <div>
    <h1>Financeiro</h1>
    <p class="subtitle">Registro e análise de vendas realizadas.</p>
  </div>
  <button class="btn btn-primary" onclick="document.getElementById('modal-venda').style.display='flex'">+ Registrar Venda</button>
</div>

<!-- Seletor de período -->
<form method="GET" action="/admin/financeiro" class="flex gap-3 mb-6 items-center">
  <select class="input" name="mes" style="max-width:10rem">
    <?php foreach ($meses as $num => $nome): ?>
      <option value="<?= (int)$num ?>" <?= $mes === (int)$num ? 'selected' : '' ?>><?= $nome ?></option>
    <?php endforeach; ?>
  </select>
  <select class="input" name="ano" style="max-width:7rem">
    <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
      <option value="<?= $y ?>" <?= $ano === $y ? 'selected' : '' ?>><?= $y ?></option>
    <?php endfor; ?>
  </select>
  <button type="submit" class="btn btn-ghost sm">Ver</button>
</form>

<!-- KPIs -->
<div class="kpi-grid">
  <div class="card kpi">
    <div class="ic">🏆</div>
    <div class="val"><?= $totalVendas ?></div>
    <div class="lbl">Vendas no Mês</div>
  </div>
  <div class="card kpi">
    <div class="ic">💰</div>
    <div class="val"><?= brl($faturamento) ?></div>
    <div class="lbl">Faturamento</div>
  </div>
  <div class="card kpi">
    <div class="ic">📊</div>
    <div class="val"><?= brl($ticketMedio) ?></div>
    <div class="lbl">Ticket Médio</div>
  </div>
  <div class="card kpi">
    <div class="ic">🤝</div>
    <div class="val"><?= brl($totalComissoes) ?></div>
    <div class="lbl">Comissões</div>
  </div>
</div>

<!-- Tabela de Vendas -->
<div class="mt-8">
  <h2 style="font-weight:700;margin-bottom:1rem">Vendas — <?= $meses[sprintf('%02d',$mes)] ?>/<?= $ano ?></h2>
  <div class="card">
    <div class="table-wrap" style="margin:0">
      <table class="tbl">
        <thead>
          <tr>
            <th>Data</th>
            <th>Veículo</th>
            <th>Cliente</th>
            <th>Vendedor</th>
            <th>Valor</th>
            <th>Pagamento</th>
            <th>Comissão</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$vendas): ?>
            <tr><td colspan="7" style="text-align:center;color:var(--w30);padding:2.5rem">Nenhuma venda registrada neste período.</td></tr>
          <?php endif; ?>
          <?php foreach ($vendas as $v): ?>
          <tr>
            <td style="font-size:.85rem"><?= date('d/m/Y', strtotime($v['data_venda'])) ?></td>
            <td>
              <strong><?= e($v['marca'] . ' ' . $v['modelo']) ?></strong>
              <div style="font-size:.75rem;color:var(--w50)"><?= $v['ano_fabricacao'] ?></div>
            </td>
            <td>
              <div><?= e($v['cliente_nome']) ?></div>
              <?php if ($v['cliente_telefone']): ?>
                <div style="font-size:.75rem;color:var(--w50)"><?= e($v['cliente_telefone']) ?></div>
              <?php endif; ?>
            </td>
            <td><?= e($v['vendedor_nome'] ?? '—') ?></td>
            <td class="text-yellow font-bold"><?= brl($v['valor_venda']) ?></td>
            <td style="font-size:.85rem"><?= e($v['forma_pagamento'] ?? '—') ?></td>
            <td><?= $v['comissao'] ? brl($v['comissao']) : '<span style="color:var(--w30)">—</span>' ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <?php if ($vendas): ?>
        <tfoot>
          <tr style="border-top:2px solid var(--w10)">
            <td colspan="4" style="font-weight:700;padding:1rem">TOTAL</td>
            <td class="text-yellow font-bold"><?= brl($faturamento) ?></td>
            <td></td>
            <td class="font-bold"><?= brl($totalComissoes) ?></td>
          </tr>
        </tfoot>
        <?php endif; ?>
      </table>
    </div>
  </div>
</div>

<!-- Modal Registrar Venda -->
<div id="modal-venda" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:200;align-items:center;justify-content:center;padding:1rem">
  <div class="card" style="width:100%;max-width:600px;padding:2rem;max-height:90vh;overflow-y:auto">
    <div class="flex items-center justify-between mb-6">
      <h2 style="font-weight:700;font-size:1.25rem">Registrar Venda</h2>
      <button type="button" class="btn btn-ghost sm" onclick="document.getElementById('modal-venda').style.display='none'">✕</button>
    </div>
    <form method="POST" action="/admin/financeiro/salvar">
      <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
      <div class="form-grid cols-2">
        <div style="grid-column:1/-1">
          <label class="label">Veículo *</label>
          <select class="input" name="car_id" required>
            <option value="">Selecione o veículo…</option>
            <?php foreach ($carrosVendidos as $c): ?>
              <option value="<?= $c['id'] ?>"><?= e($c['marca'] . ' ' . $c['modelo'] . ' ' . $c['ano_fabricacao']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="label">Cliente *</label>
          <input class="input" name="cliente_nome" required placeholder="Nome do comprador">
        </div>
        <div>
          <label class="label">CPF/CNPJ do Cliente</label>
          <input class="input" name="cliente_documento" placeholder="000.000.000-00">
        </div>
        <div>
          <label class="label">Telefone do Cliente</label>
          <input class="input" name="cliente_telefone" placeholder="(11) 9 9999-9999">
        </div>
        <div>
          <label class="label">Data da Venda *</label>
          <input class="input" type="date" name="data_venda" required value="<?= date('Y-m-d') ?>">
        </div>
        <div>
          <label class="label">Valor de Venda *</label>
          <input class="input" type="number" step="0.01" name="valor_venda" required placeholder="0.00">
        </div>
        <div>
          <label class="label">Forma de Pagamento</label>
          <select class="input" name="forma_pagamento">
            <option value="">—</option>
            <?php foreach (['À Vista','Financiamento','Consórcio','Troca + Dinheiro','Cartão de Crédito','PIX'] as $fp): ?>
              <option value="<?= e($fp) ?>"><?= e($fp) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="label">Vendedor</label>
          <select class="input" name="vendedor_id">
            <option value="">—</option>
            <?php foreach ($vendedores as $v): ?>
              <option value="<?= $v['id'] ?>"><?= e($v['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="label">Comissão (R$)</label>
          <input class="input" type="number" step="0.01" name="comissao" placeholder="0.00">
        </div>
        <div style="grid-column:1/-1">
          <label class="label">Observações</label>
          <textarea class="input" name="observacoes" rows="2" placeholder="Observações sobre a venda…"></textarea>
        </div>
      </div>
      <div class="flex gap-3 justify-end mt-6">
        <button type="button" class="btn btn-ghost" onclick="document.getElementById('modal-venda').style.display='none'">Cancelar</button>
        <button type="submit" class="btn btn-primary">Registrar</button>
      </div>
    </form>
  </div>
</div>
