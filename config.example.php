<?php
/**
 * Nano Automóveis — Configuração
 * Copie este arquivo para `config.php` e preencha com seus dados do cPanel.
 * Os dados de MySQL estão em cPanel → Bancos de Dados MySQL.
 */

return [
    // === Banco de dados (Hostgator cPanel → MySQL Databases) ===
    'db' => [
        'host' => 'localhost',
        'name' => 'usuariocpanel_nano',     // ex.: nanoauto_db
        'user' => 'usuariocpanel_user',     // ex.: nanoauto_admin
        'pass' => 'SUA_SENHA_FORTE',
        'charset' => 'utf8mb4',
    ],

    // === Site ===
    'site' => [
        'nome' => 'Nano Automóveis',
        'url'  => 'https://nanoautomoveis.com.br',  // sem barra no final
        'timezone' => 'America/Sao_Paulo',
    ],

    // === Contato ===
    'contato' => [
        'whatsapp'  => '5511999999999',                       // DDI+DDD+número, só dígitos
        'telefone'  => '(11) 9 9999-9999',
        'email'     => 'contato@nanoautomoveis.com.br',
        'endereco'  => 'Rua Exemplo, 123 — Cidade/UF',
        'instagram' => 'https://www.instagram.com/nanoautomoveis/',
    ],

    // Segurança — gere uma string aleatória longa
    'app_key' => 'TROQUE-POR-UMA-STRING-ALEATORIA-LONGA',

    // === Deploy automático via webhook do GitHub ===
    // Veja DEPLOY.md para o setup completo.
    'deploy' => [
        // Mesmo segredo que você colar no GitHub → Webhooks → "Secret"
        'secret'      => 'TROQUE-POR-UMA-STRING-ALEATORIA-LONGA-DIFERENTE',
        // Caminho ABSOLUTO do repositório git clonado pelo cPanel
        // (cPanel → Git Version Control → "Caminho do repositório")
        'repo_path'   => '/home1/edjuni41/repositories/nano-automoveis',
        // Pasta onde o site está servido (DEPLOYPATH do .cpanel.yml)
        'deploy_path' => '/home1/edjuni41/nano.waveenterprise.com.br',
        'branch'      => 'main',
    ],
];
