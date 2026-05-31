<?php $c = config('contato'); $wa = $c['whatsapp']; ?>
<div class="container" style="padding:4rem 0;display:grid;gap:3rem;grid-template-columns:1fr">
  <style>@media (min-width:768px){.contato-grid{grid-template-columns:1fr 1fr}}</style>
  <div class="contato-grid" style="display:grid;gap:3rem;grid-template-columns:1fr">
    <div>
      <h1 style="font-size:3rem;font-weight:900">Fale com a gente</h1>
      <p style="color:var(--w70);margin-top:1rem">Atendimento rápido pelo WhatsApp ou pelo formulário ao lado.</p>
      <div class="grid gap-4 mt-8">
        <a href="<?= e(whatsappLink($wa, 'Olá!')) ?>" target="_blank" class="card pad flex items-center gap-4"><span style="color:#25D366;font-size:1.5rem">💬</span><div><div class="font-bold">WhatsApp</div><div class="text-sm" style="color:var(--w50)"><?= e($c['telefone']) ?></div></div></a>
        <a href="mailto:<?= e($c['email']) ?>" class="card pad flex items-center gap-4"><span style="color:var(--yellow);font-size:1.5rem">✉️</span><div><div class="font-bold">E-mail</div><div class="text-sm" style="color:var(--w50)"><?= e($c['email']) ?></div></div></a>
        <a href="<?= e($c['instagram']) ?>" target="_blank" class="card pad flex items-center gap-4"><span style="color:#ec4899;font-size:1.5rem">📷</span><div><div class="font-bold">Instagram</div><div class="text-sm" style="color:var(--w50)">@nanoautomoveis</div></div></a>
        <div class="card pad flex items-center gap-4"><span style="color:var(--yellow);font-size:1.5rem">📍</span><div><div class="font-bold">Endereço</div><div class="text-sm" style="color:var(--w50)"><?= e($c['endereco']) ?></div></div></div>
      </div>
    </div>
    <div class="card pad">
      <h2 style="font-size:1.5rem;font-weight:900" class="mb-4">Envie sua mensagem</h2>
      <form method="post" action="/contato">
        <input type="hidden" name="_csrf" value="<?= csrfToken() ?>">
        <div><label class="label">Nome *</label><input name="nome" required class="input"></div>
        <div class="mt-4"><label class="label">WhatsApp / Telefone *</label><input name="telefone" required class="input"></div>
        <div class="mt-4"><label class="label">E-mail</label><input name="email" type="email" class="input"></div>
        <div class="mt-4"><label class="label">Mensagem</label><textarea name="mensagem" class="input" rows="4"></textarea></div>
        <button class="btn btn-primary mt-6" style="width:100%">Enviar mensagem</button>
      </form>
    </div>
  </div>
</div>
