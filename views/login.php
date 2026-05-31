<div class="login-wrap">
  <div class="card login-card">
    <div class="text-center mb-4"><a class="logo lg"><span class="logo-mark">NANO</span><span class="logo-sub">AUTOMÓVEIS</span></a></div>
    <h1 style="font-size:1.5rem;font-weight:900;text-align:center">Área restrita</h1>
    <p class="text-center text-sm" style="color:var(--w50);margin-top:.25rem">Acesso para a equipe Nano</p>

    <form method="post" action="/login" class="mt-6">
      <input type="hidden" name="_csrf" value="<?= csrfToken() ?>">
      <input type="hidden" name="next" value="<?= e($_GET['next'] ?? '/admin') ?>">
      <div><label class="label">E-mail</label><input name="email" type="email" required class="input"></div>
      <div class="mt-4"><label class="label">Senha</label><input name="senha" type="password" required class="input"></div>
      <button class="btn btn-primary mt-6" style="width:100%">Entrar</button>
    </form>
  </div>
</div>
