-- ============================================================
-- Nano Automóveis — MySQL Schema (Hostgator)
-- Rode no cPanel → phpMyAdmin → SQL
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- USUÁRIOS (login + roles)
DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  email VARCHAR(180) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  telefone VARCHAR(40),
  role ENUM('proprietario','financeiro','vendedor','leitor') NOT NULL DEFAULT 'leitor',
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- CARROS
DROP TABLE IF EXISTS cars;
CREATE TABLE cars (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  marca VARCHAR(60) NOT NULL,
  modelo VARCHAR(80) NOT NULL,
  versao VARCHAR(120),
  ano_fabricacao SMALLINT NOT NULL,
  ano_modelo SMALLINT NOT NULL,
  km INT UNSIGNED NOT NULL DEFAULT 0,
  cor VARCHAR(40),
  combustivel VARCHAR(40),
  cambio VARCHAR(40),
  carroceria VARCHAR(40),
  portas TINYINT,
  placa_final VARCHAR(10),
  preco DECIMAL(12,2) NOT NULL,
  preco_promocional DECIMAL(12,2) NULL,
  status ENUM('disponivel','reservado','vendido','rascunho') NOT NULL DEFAULT 'disponivel',
  destaque TINYINT(1) NOT NULL DEFAULT 0,
  descricao TEXT,
  opcionais TEXT,            -- separados por vírgula
  foto_capa VARCHAR(255),
  fotos TEXT,                -- JSON com array de paths
  visualizacoes INT UNSIGNED DEFAULT 0,
  created_by INT UNSIGNED,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_status (status),
  INDEX idx_destaque (destaque),
  INDEX idx_marca (marca),
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- LEADS (CRM)
DROP TABLE IF EXISTS leads;
CREATE TABLE leads (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  telefone VARCHAR(40),
  email VARCHAR(180),
  car_id INT UNSIGNED NULL,
  origem ENUM('site','whatsapp','instagram','indicacao','outro') NOT NULL DEFAULT 'site',
  status ENUM('novo','em_atendimento','proposta','fechado','perdido') NOT NULL DEFAULT 'novo',
  mensagem TEXT,
  responsavel_id INT UNSIGNED NULL,
  valor_proposta DECIMAL(12,2) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_status (status),
  INDEX idx_resp (responsavel_id),
  FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE SET NULL,
  FOREIGN KEY (responsavel_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- INTERAÇÕES DO CRM (histórico)
DROP TABLE IF EXISTS lead_interactions;
CREATE TABLE lead_interactions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  lead_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NULL,
  tipo ENUM('nota','ligacao','whatsapp','visita','email') NOT NULL DEFAULT 'nota',
  conteudo TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_lead (lead_id),
  FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- VENDAS (financeiro)
DROP TABLE IF EXISTS sales;
CREATE TABLE sales (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  car_id INT UNSIGNED NOT NULL,
  lead_id INT UNSIGNED NULL,
  vendedor_id INT UNSIGNED NULL,
  cliente_nome VARCHAR(180) NOT NULL,
  cliente_documento VARCHAR(40),
  cliente_telefone VARCHAR(40),
  valor_venda DECIMAL(12,2) NOT NULL,
  forma_pagamento VARCHAR(60),
  comissao DECIMAL(12,2),
  observacoes TEXT,
  data_venda DATE NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (car_id) REFERENCES cars(id),
  FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE SET NULL,
  FOREIGN KEY (vendedor_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- USUÁRIO INICIAL — PROPRIETÁRIO
-- Senha: nanoadmin123 (TROQUE no primeiro acesso!)
-- Hash gerado com PHP password_hash() para 'nanoadmin123'
-- ============================================================
INSERT INTO users (nome, email, senha_hash, role)
VALUES (
  'Administrador',
  'admin@nanoautomoveis.com.br',
  '$2y$10$wH5y8j9Z1QcUe7G/8KlqHurAUI8nM5pHkqzVeUvLgZQYn5d4JuKQS',
  'proprietario'
);
-- ⚠️ Após o primeiro login, vá em /admin/usuarios e troque a senha!
