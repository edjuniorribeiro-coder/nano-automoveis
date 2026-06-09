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

## Bônus: deploy automático via Webhook (opcional, avançado)

Se quiser que o deploy aconteça automaticamente a cada `git push` (sem ter que clicar no cPanel), dá pra configurar webhook do GitHub apontando para um script PHP no servidor que executa `git pull`. Posso te passar o script se interessar — mas o fluxo manual acima já é bem rápido.
