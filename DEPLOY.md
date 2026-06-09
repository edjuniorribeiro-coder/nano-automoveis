# Deploy — Nano Automóveis (Hostgator Plano M via Git)

## Setup inicial (faz uma vez só)

### 1. No GitHub (repositório `nano-automoveis`)
Como o repo é privado, precisamos dar acesso ao servidor da Hostgator via **Deploy Key**:

1. cPanel → **Git Version Control** → **Gerar chave SSH** (se ainda não tiver)
2. Copie a chave pública gerada
3. GitHub → seu repo → **Settings → Deploy keys → Add deploy key**
4. Cole a chave. **Não** precisa marcar "Allow write access".

### 2. No cPanel da Hostgator

1. **Git Version Control → Criar**
   - URL de clonagem: `git@github.com:edjuniorribeiro-coder/nano-automoveis.git`
   - Caminho: `/home/SEU_USUARIO/repositories/nano-automoveis`
   - Nome: `nano-automoveis`
   - ✅ Clonar um repositório
2. Aguarde o clone terminar.

### 3. Editar caminhos no `.cpanel.yml`

Antes do primeiro deploy, abra `.cpanel.yml` e ajuste a linha:

```yaml
- export DEPLOYPATH=/home/$USER/public_html/nano/
```

Substitua `nano/` pela pasta correta do subdomínio na Hostgator. Confira no cPanel → **Subdomínios** qual é a *Raiz do documento* de `nano.waveenterprise.com.br` (normalmente algo como `/home/USUARIO/public_html/nano` ou `/home/USUARIO/nano.waveenterprise.com.br`).

Commit e push:
```bash
git add .cpanel.yml
git commit -m "fix: ajusta DEPLOYPATH"
git push
```

### 4. Configurar `config.php` no servidor (uma vez só)
O arquivo `config.php` **não** é versionado (está no `.gitignore`) — você precisa criá-lo direto no servidor:

1. cPanel → **Gerenciador de Arquivos** → vá até a raiz do site
2. Crie `config.php` baseado em `config.example.php`
3. Preencha os dados reais (MySQL do cPanel + WhatsApp + endereço)

### 5. Importar o banco
cPanel → **phpMyAdmin** → seu banco → **Importar** → suba `sql/schema.sql`

---

## Deploy a cada mudança (fluxo do dia-a-dia)

Toda vez que eu/você fizer mudanças aqui no projeto:

```bash
git add .
git commit -m "descrição da mudança"
git push
```

No cPanel da Hostgator:
1. **Git Version Control** → repositório `nano-automoveis` → **Gerenciar**
2. Aba **"Atualizar do Remoto"** (Update from Remote) → clica
3. Aba **"Implementação"** (Deployment) → **"Implementar Confirmação HEAD"** (Deploy HEAD Commit)

Pronto, o site atualiza em segundos.

---

## Deploy automático via Webhook (já implementado em `deploy.php`)

Com isso configurado, **cada `git push` para `main` atualiza o site sozinho em ~5 segundos**, sem precisar abrir o cPanel.

### Setup (uma vez só)

1. **Gere um segredo aleatório** (use https://www.random.org/strings/ ou execute `openssl rand -hex 32` no terminal):
   ```
   ex: 7f3a9c8e1b5d2f4a6c8e0b3d5f7a9c1e3b5d7f9a1c3e5b7d9f1a3c5e7b9d1f3a
   ```

2. **No servidor (cPanel → Gerenciador de Arquivos)**, edite o `config.php` e adicione:
   ```php
   'deploy' => [
       'secret'      => 'COLE-SEU-SEGREDO-AQUI',
       'repo_path'   => '/home1/edjuni41/repositories/nano-automoveis',
       'deploy_path' => '/home1/edjuni41/nano.waveenterprise.com.br',
       'branch'      => 'main',
   ],
   ```
   ⚠️ Confira `repo_path` no cPanel → Git Version Control → o "Caminho do repositório" do `nano-automoveis`.

3. **No GitHub** → repositório `nano-automoveis` → **Settings → Webhooks → Add webhook**:
   - **Payload URL**: `https://nano.waveenterprise.com.br/deploy.php`
   - **Content type**: `application/json`
   - **Secret**: cole o mesmo segredo do passo 1
   - **SSL verification**: Enable
   - **Which events?**: Just the push event
   - ✅ Active
   - Clique **Add webhook**

4. **Teste**: o GitHub envia automaticamente um evento `ping`. Aba "Recent Deliveries" do webhook deve mostrar resposta `200 OK` com corpo `pong`.

### Como funciona

- `deploy.php` recebe POST do GitHub a cada push
- Verifica assinatura HMAC-SHA256 contra o segredo (rejeita 401 se inválido)
- Aceita apenas eventos `push` na branch `main`
- Roda `git fetch && git reset --hard origin/main` no repo do servidor
- Copia `index.php`, `actions.php`, `.htaccess`, `.user.ini`, `lib/`, `views/`, `assets/`, `sql/` para o `deploy_path`
- Preserva `uploads/` (fotos dos carros) e `config.php`
- Log de cada deploy fica em `deploy.log` na pasta do `deploy.php`

### Troubleshooting

- **401 Unauthorized**: segredo no GitHub ≠ segredo no `config.php`
- **500 + "shell_exec desabilitado"**: peça ao suporte da Hostgator pra habilitar, ou use cron como fallback
- **404 no `/deploy.php`**: o `.htaccess` está reescrevendo a URL — confira que o arquivo está na raiz do `deploy_path`
- **Funciona mas site não atualiza**: confira o `repo_path` no `config.php` e veja `deploy.log`
