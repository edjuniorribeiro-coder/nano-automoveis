<?php
requireRole(['proprietario']);

$usuarios = fetchAll("SELECT * FROM users ORDER BY ativo DESC, nome");
?>

<div class="flex items-center justify-between mb-6">
  <div>
    <h1>Usuários</h1>
    <p class="subtitle">Gerencie os acessos ao painel administrativo.</p>
  </div>
  <button class="btn btn-primary" onclick="document.getElementById('modal-novo').style.display='flex'">+ Novo Usuário</button>
</div>

<!-- Tabela -->
<div class="card">
  <div class="table-wrap" style="margin:0">
    <table class="tbl">
      <thead>
        <tr><th>Nome</th><th>E-mail</th><th>Perfil</th><th>Status</th><th>Desde</th><th>Ações</th></tr>
      </thead>
      <tbody>
        <?php foreach ($usuarios as $usr): ?>
        <tr>
          <td><strong><?= e($usr['nome']) ?></strong></td>
          <td style="color:var(--w70)"><?= e($usr['email']) ?></td>
          <td><span class="chip dark"><?= e(ROLE_LABELS[$usr['role']] ?? $usr['role']) ?></span></td>
          <td>
            <?php if ($usr['ativo']): ?>
              <span class="chip bg-green-500/15 text-green-400">Ativo</span>
            <?php else: ?>
              <span class="chip bg-red-500/15 text-red-400">Inativo</span>
            <?php endif; ?>
          </td>
          <td style="font-size:.8rem;color:var(--w50)"><?= date('d/m/Y', strtotime($usr['created_at'])) ?></td>
          <td>
            <button class="btn btn-ghost sm" onclick="abrirEditar(<?= htmlspecialchars(json_encode($usr), ENT_QUOTES) ?>)">Editar</button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Novo Usuário -->
<div id="modal-novo" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:200;align-items:center;justify-content:center;padding:1rem">
  <div class="card" style="width:100%;max-width:480px;padding:2rem">
    <div class="flex items-center justify-between mb-6">
      <h2 style="font-weight:700">Novo Usuário</h2>
      <button type="button" class="btn btn-ghost sm" onclick="document.getElementById('modal-novo').style.display='none'">✕</button>
    </div>
    <form method="POST" action="/admin/usuarios/novo">
      <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
      <div class="form-grid">
        <div>
          <label class="label">Nome *</label>
          <input class="input" name="nome" required placeholder="Nome completo">
        </div>
        <div>
          <label class="label">E-mail *</label>
          <input class="input" type="email" name="email" required placeholder="email@exemplo.com">
        </div>
        <div>
          <label class="label">Senha *</label>
          <input class="input" type="password" name="senha" required minlength="8" placeholder="Mínimo 8 caracteres">
        </div>
        <div>
          <label class="label">Perfil *</label>
          <select class="input" name="role" required>
            <?php foreach (ROLE_LABELS as $k => $v): ?>
              <option value="<?= e($k) ?>"><?= e($v) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="flex gap-3 justify-end mt-6">
        <button type="button" class="btn btn-ghost" onclick="document.getElementById('modal-novo').style.display='none'">Cancelar</button>
        <button type="submit" class="btn btn-primary">Criar Usuário</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Editar Usuário -->
<div id="modal-editar" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:200;align-items:center;justify-content:center;padding:1rem">
  <div class="card" style="width:100%;max-width:480px;padding:2rem">
    <div class="flex items-center justify-between mb-6">
      <h2 style="font-weight:700">Editar Usuário</h2>
      <button type="button" class="btn btn-ghost sm" onclick="document.getElementById('modal-editar').style.display='none'">✕</button>
    </div>
    <form method="POST" action="/admin/usuarios/atualizar" id="form-editar">
      <input type="hidden" name="_csrf" value="<?= e(csrfToken()) ?>">
      <input type="hidden" name="id" id="edit-id">
      <div class="form-grid">
        <div>
          <label class="label">Nome</label>
          <input class="input" id="edit-nome" value="" disabled style="opacity:.5">
        </div>
        <div>
          <label class="label">E-mail</label>
          <input class="input" id="edit-email" value="" disabled style="opacity:.5">
        </div>
        <div>
          <label class="label">Nova Senha (deixe em branco para manter)</label>
          <input class="input" type="password" name="senha" minlength="8" placeholder="Nova senha…">
        </div>
        <div>
          <label class="label">Perfil</label>
          <select class="input" name="role" id="edit-role">
            <?php foreach (ROLE_LABELS as $k => $v): ?>
              <option value="<?= e($k) ?>"><?= e($v) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer">
            <input type="checkbox" name="ativo" value="1" id="edit-ativo">
            <span>Usuário ativo</span>
          </label>
        </div>
      </div>
      <div class="flex gap-3 justify-end mt-6">
        <button type="button" class="btn btn-ghost" onclick="document.getElementById('modal-editar').style.display='none'">Cancelar</button>
        <button type="submit" class="btn btn-primary">Salvar</button>
      </div>
    </form>
  </div>
</div>

<div class="mt-8 card" style="padding:1.5rem">
  <h2 style="font-weight:700;margin-bottom:.75rem">Permissões por Perfil</h2>
  <div class="table-wrap" style="margin:0">
    <table class="tbl">
      <thead><tr><th>Perfil</th><th>Dashboard</th><th>Carros</th><th>CRM</th><th>Financeiro</th><th>Usuários</th></tr></thead>
      <tbody>
        <tr><td><span class="chip yellow">Proprietário</span></td><td>✅</td><td>✅ + excluir</td><td>✅</td><td>✅</td><td>✅</td></tr>
        <tr><td><span class="chip dark">Financeiro</span></td><td>✅</td><td>✅</td><td>✅</td><td>✅</td><td>—</td></tr>
        <tr><td><span class="chip dark">Vendedor</span></td><td>✅</td><td>✅</td><td>✅</td><td>—</td><td>—</td></tr>
        <tr><td><span class="chip dark">Leitor</span></td><td>✅</td><td>👁 só ver</td><td>👁 só ver</td><td>—</td><td>—</td></tr>
      </tbody>
    </table>
  </div>
</div>

<script>
function abrirEditar(usr) {
  document.getElementById('edit-id').value  = usr.id;
  document.getElementById('edit-nome').value  = usr.nome;
  document.getElementById('edit-email').value = usr.email;
  document.getElementById('edit-role').value  = usr.role;
  document.getElementById('edit-ativo').checked = usr.ativo == 1;
  document.getElementById('modal-editar').style.display = 'flex';
}
</script>
