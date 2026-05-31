<?php $contato = config('contato'); $wa = $contato['whatsapp']; ?>
</main>
<footer class="footer">
  <div class="container footer-grid">
    <div>
      <a class="logo"><span class="logo-mark">NANO</span><span class="logo-sub">AUTOMÓVEIS</span></a>
      <p class="text-sm" style="color:var(--w50);margin-top:1rem">Seminovos selecionados com procedência. Sua próxima conquista começa aqui.</p>
    </div>
    <div>
      <h4>Navegação</h4>
      <ul>
        <li><a href="/estoque">Estoque</a></li>
        <li><a href="/sobre">Sobre</a></li>
        <li><a href="/contato">Contato</a></li>
        <li><a href="/login">Área restrita</a></li>
      </ul>
    </div>
    <div>
      <h4>Contato</h4>
      <ul>
        <li>📍 <?= e($contato['endereco']) ?></li>
        <li>✉️ <?= e($contato['email']) ?></li>
        <li>📱 <?= e($contato['telefone']) ?></li>
      </ul>
    </div>
    <div>
      <h4>Redes</h4>
      <ul>
        <li><a href="<?= e($contato['instagram']) ?>" target="_blank">📷 @nanoautomoveis</a></li>
        <li><a href="<?= e(whatsappLink($wa, 'Olá!')) ?>" target="_blank">💬 WhatsApp</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-copy">© <?= date('Y') ?> Nano Automóveis. Todos os direitos reservados.</div>
</footer>
<a class="fab-wa" target="_blank" href="<?= e(whatsappLink($wa, 'Olá! Vi o site da Nano Automóveis e gostaria de mais informações.')) ?>" title="Fale conosco no WhatsApp">
  <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.52 3.45A11.93 11.93 0 0 0 12 0C5.37 0 0 5.37 0 12c0 2.11.55 4.16 1.6 5.97L0 24l6.18-1.62A11.93 11.93 0 0 0 12 24c6.63 0 12-5.37 12-12 0-3.2-1.25-6.21-3.48-8.55ZM12 22a9.93 9.93 0 0 1-5.06-1.38l-.36-.21-3.67.96.98-3.58-.24-.37A9.94 9.94 0 1 1 22 12c0 5.51-4.49 10-10 10Zm5.47-7.5c-.3-.15-1.77-.87-2.04-.97-.27-.1-.47-.15-.67.15s-.77.97-.94 1.17c-.17.2-.35.22-.65.07a8.18 8.18 0 0 1-2.4-1.48 9.04 9.04 0 0 1-1.67-2.08c-.17-.3-.02-.46.13-.6.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51l-.57-.01a1.1 1.1 0 0 0-.8.37 3.36 3.36 0 0 0-1.04 2.5c0 1.47 1.07 2.88 1.22 3.08.15.2 2.1 3.2 5.08 4.5.71.31 1.26.5 1.7.64.71.23 1.36.2 1.87.12.57-.08 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.42-.07-.13-.27-.2-.57-.35Z"/></svg>
</a>
</body></html>
