-- ============================================================
-- Nano Automóveis — Dados de demonstração (DEMO)
-- Use para popular o sistema com exemplos realistas
-- Roda DEPOIS do schema.sql, no phpMyAdmin → SQL
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- USUÁRIOS ADICIONAIS (vendedores e financeiro)
-- Todas as senhas são "nanoadmin123" (devem ser trocadas)
-- ============================================================
INSERT INTO users (nome, email, senha_hash, telefone, role) VALUES
  ('Carlos Mendes',   'carlos@nanoautomoveis.com.br',   '$2b$10$Z7V1fBGfRlrOGuWhokFdFeVcFw.PxUOSa8Jd3w..K8G4lmmDwFWqK', '(11) 98765-4321', 'vendedor'),
  ('Juliana Ribeiro', 'juliana@nanoautomoveis.com.br',  '$2b$10$Z7V1fBGfRlrOGuWhokFdFeVcFw.PxUOSa8Jd3w..K8G4lmmDwFWqK', '(11) 99876-5432', 'vendedor'),
  ('Pedro Almeida',   'pedro@nanoautomoveis.com.br',    '$2b$10$Z7V1fBGfRlrOGuWhokFdFeVcFw.PxUOSa8Jd3w..K8G4lmmDwFWqK', '(11) 97654-3210', 'vendedor'),
  ('Marina Santos',   'marina@nanoautomoveis.com.br',   '$2b$10$Z7V1fBGfRlrOGuWhokFdFeVcFw.PxUOSa8Jd3w..K8G4lmmDwFWqK', '(11) 96543-2109', 'financeiro');

-- ============================================================
-- CARROS — Estoque demo
-- Fotos: usando placeholders temáticos (substitua pelas fotos reais)
-- ============================================================
INSERT INTO cars (marca, modelo, versao, ano_fabricacao, ano_modelo, km, cor, combustivel, cambio, carroceria, portas, preco, preco_promocional, status, destaque, descricao, opcionais, foto_capa, fotos, visualizacoes, created_by) VALUES

('VOLKSWAGEN', 'GOL', '1.0 TL MBVI', 2022, 2023, 28500, 'Prata', 'Flex', 'Manual', 'Hatch', 4, 64900.00, 59900.00, 'disponivel', 1,
 'Volkswagen Gol 2023 em excelente estado, único dono, todas as revisões em concessionária autorizada. Carro de garagem, sem detalhes. Pneus novos e revisão completa feita.',
 'Ar-condicionado, Direção elétrica, Vidros elétricos, Travas elétricas, Airbag duplo, ABS, Computador de bordo, Multimídia com Android Auto',
 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?w=1200&q=80',
 '["https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=1200&q=80","https://images.unsplash.com/photo-1502877338535-766e1452684a?w=1200&q=80"]',
 247, 1),

('FIAT', 'MOBI', '1.0 LIKE', 2021, 2022, 42100, 'Branco', 'Flex', 'Manual', 'Hatch', 4, 48900.00, NULL, 'disponivel', 1,
 'Fiat Mobi Like, ótima opção para cidade. Econômico, ágil e com baixo custo de manutenção. Documentação em dia, IPVA 2026 pago.',
 'Ar-condicionado, Direção hidráulica, Vidros elétricos dianteiros, Travas elétricas, Airbag duplo, ABS, USB',
 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=1200&q=80',
 '[]',
 189, 2),

('HYUNDAI', 'HB20', '1.0 SENSE', 2023, 2024, 15200, 'Cinza', 'Flex', 'Manual', 'Hatch', 4, 78500.00, 74900.00, 'disponivel', 1,
 'Hyundai HB20 Sense seminovo, ainda na garantia de fábrica até 03/2027. Apenas 15 mil km rodados. Bancos em tecido, central multimídia original.',
 'Ar-condicionado, Direção elétrica, Vidros elétricos, Travas, Airbag duplo, ABS, Multimídia 8", Câmera de ré, Sensor de estacionamento',
 'https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?w=1200&q=80',
 '[]',
 412, 1),

('CHEVROLET', 'ONIX', '1.0 LT TURBO', 2022, 2023, 35800, 'Preto', 'Flex', 'Automático', 'Hatch', 4, 89900.00, NULL, 'disponivel', 1,
 'Chevrolet Onix LT Turbo 2023, motor 1.0 turbo com 116cv, câmbio automático de 6 marchas. Carro confortável e econômico, ideal para uso urbano e estrada.',
 'Ar-condicionado digital, Direção elétrica, Vidros elétricos, Travas, 6 Airbags, ABS, Multimídia MyLink, Wi-Fi, Câmera de ré, Controle de tração',
 'https://images.unsplash.com/photo-1502877338535-766e1452684a?w=1200&q=80',
 '[]',
 578, 2),

('RENAULT', 'KWID', '1.0 ZEN', 2022, 2022, 48600, 'Vermelho', 'Flex', 'Manual', 'Hatch', 4, 56900.00, 52900.00, 'disponivel', 0,
 'Renault Kwid Zen 2022, carro econômico e moderno. Visual de SUV compacto. Ótimo para primeira compra.',
 'Ar-condicionado, Vidros elétricos, Travas, Airbag duplo, ABS, Multimídia 8", Câmera de ré',
 'https://images.unsplash.com/photo-1612825173281-9a193378527e?w=1200&q=80',
 '[]',
 156, 3),

('HONDA', 'CIVIC', '2.0 EXL CVT', 2020, 2021, 67400, 'Prata', 'Flex', 'CVT', 'Sedan', 4, 124900.00, 119900.00, 'disponivel', 1,
 'Honda Civic EXL 2021 impecável. Sedan top de linha, câmbio CVT, bancos em couro, teto solar. Histórico completo em concessionária Honda.',
 'Ar-condicionado dual, Bancos de couro, Teto solar, Multimídia 7", Câmera de ré, Sensor estacionamento, Controle de cruzeiro, 6 Airbags, ABS, Faróis full LED, Rodas liga-leve 17"',
 'https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?w=1200&q=80',
 '[]',
 893, 1),

('JEEP', 'RENEGADE', '1.3 T270 LONGITUDE', 2022, 2023, 31200, 'Branco', 'Flex', 'Automático', 'SUV', 4, 142900.00, NULL, 'reservado', 0,
 'Jeep Renegade Longitude 2023, SUV compacto, motor turbo de 185cv, câmbio automático de 6 marchas. Ideal para família, conforto e versatilidade.',
 'Ar-condicionado digital dual, Multimídia 8.4" Uconnect, Câmera de ré, Sensores estacionamento, Bancos em couro, Controle de cruzeiro, Modos de condução, 6 Airbags',
 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=1200&q=80',
 '[]',
 734, 2),

('TOYOTA', 'COROLLA', '2.0 XEI CVT', 2019, 2020, 89500, 'Preto', 'Flex', 'CVT', 'Sedan', 4, 109900.00, NULL, 'disponivel', 0,
 'Toyota Corolla XEI 2020, robustez e qualidade Toyota. Manutenção em dia, todas as revisões realizadas. Carro de confiança para rodar tranquilo.',
 'Ar-condicionado dual, Multimídia, Câmera de ré, Sensores, Controle de cruzeiro adaptativo, Bancos em couro, 7 Airbags, ABS, EBD, Toyota Safety Sense',
 'https://images.unsplash.com/photo-1623006772851-a8bf2c47d3a8?w=1200&q=80',
 '[]',
 326, 1),

('NISSAN', 'KICKS', '1.6 SV CVT', 2021, 2022, 52300, 'Branco', 'Flex', 'CVT', 'SUV', 4, 99900.00, 94900.00, 'disponivel', 0,
 'Nissan Kicks SV 2022, SUV moderno e espaçoso. Motor 1.6 com câmbio CVT, ótima dirigibilidade urbana e econômico em viagens.',
 'Ar-condicionado, Multimídia 8" Nissan Connect, Câmera 360°, Sensores, Controle de cruzeiro, Bancos em tecido premium, 6 Airbags, ABS, Controle de tração',
 'https://images.unsplash.com/photo-1568844293986-8d0400bd4745?w=1200&q=80',
 '[]',
 467, 3),

('FIAT', 'STRADA', '1.4 ENDURANCE CS', 2022, 2023, 24800, 'Cinza', 'Flex', 'Manual', 'Picape', 2, 89900.00, NULL, 'disponivel', 1,
 'Fiat Strada Endurance Cabine Simples 2023, ideal para trabalho e lazer. Caçamba ampla, motor econômico, perfeita para autônomos e pequenos comércios.',
 'Ar-condicionado, Direção elétrica, Vidros elétricos, Travas, Airbag duplo, ABS, Capota marítima',
 'https://images.unsplash.com/photo-1601584115197-04ecc0da31d7?w=1200&q=80',
 '[]',
 283, 2),

('VOLKSWAGEN', 'T-CROSS', '1.0 200 TSI COMFORTLINE', 2021, 2022, 58900, 'Azul', 'Flex', 'Automático', 'SUV', 4, 115900.00, 109900.00, 'vendido', 0,
 'Volkswagen T-Cross Comfortline, SUV compacto premium. Motor TSI turbo com excelente performance e economia.',
 'Ar-condicionado digital, Multimídia VW Play, Câmera de ré, Sensores, 6 Airbags, ABS, Controle eletrônico de estabilidade, Faróis LED, Rodas 16"',
 'https://images.unsplash.com/photo-1606664515882-c4cb6e57b51d?w=1200&q=80',
 '[]',
 1247, 1),

('CHEVROLET', 'TRACKER', '1.2 TURBO LTZ', 2023, 2024, 8500, 'Vermelho', 'Flex', 'Automático', 'SUV', 4, 132900.00, NULL, 'disponivel', 1,
 'Chevrolet Tracker LTZ 2024 zero km de garagem, apenas 8.500 km! Ainda na garantia de fábrica até 11/2027. Sai imediatamente, oportunidade única.',
 'Ar-condicionado digital dual, Multimídia MyLink 8", Wi-Fi 4G LTE, Wireless Apple CarPlay/Android Auto, Câmera de ré, Sensor estacionamento, Controle de cruzeiro, 6 Airbags, ABS, Controle de estabilidade, Faróis full LED, Rodas liga-leve 17"',
 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=1200&q=80',
 '[]',
 1834, 1);

-- ============================================================
-- LEADS — CRM demo
-- Distribuídos entre os status do funil
-- ============================================================
INSERT INTO leads (nome, telefone, email, car_id, origem, status, mensagem, responsavel_id, valor_proposta, created_at) VALUES
  -- NOVOS
  ('Ricardo Tavares',    '(11) 98123-4567', 'ricardo.tavares@gmail.com',   3,  'site',      'novo',            'Olá, tenho interesse no HB20. Vocês aceitam meu carro como parte do pagamento?',     NULL, NULL,    DATE_SUB(NOW(), INTERVAL 2 HOUR)),
  ('Camila Oliveira',    '(11) 99234-5678', 'camila.oliv@hotmail.com',     6,  'whatsapp',  'novo',            'Boa tarde! Vi o anúncio do Civic, ele está disponível ainda? Posso ver hoje?',       NULL, NULL,    DATE_SUB(NOW(), INTERVAL 5 HOUR)),
  ('Fernando Costa',     '(11) 97345-6789', NULL,                          1,  'instagram', 'novo',            'Quero saber sobre financiamento do Gol prata',                                       NULL, NULL,    DATE_SUB(NOW(), INTERVAL 1 DAY)),
  ('Patrícia Lima',      '(11) 96456-7890', 'patricia.lima@outlook.com',   12, 'site',      'novo',            'Tenho interesse no Tracker 2024. Aceitam Honda Fit 2018 na troca?',                  NULL, NULL,    DATE_SUB(NOW(), INTERVAL 3 HOUR)),

  -- EM ATENDIMENTO
  ('Roberto Silva',      '(11) 95567-8901', 'roberto.silva@gmail.com',     4,  'site',      'em_atendimento',  'Quero o Onix automático. Como funciona o financiamento?',                            2,    NULL,    DATE_SUB(NOW(), INTERVAL 2 DAY)),
  ('Aline Ferreira',     '(11) 94678-9012', 'aline.ferr@gmail.com',        9,  'whatsapp',  'em_atendimento',  'Gostaria de agendar test-drive do Kicks',                                            3,    NULL,    DATE_SUB(NOW(), INTERVAL 3 DAY)),
  ('Bruno Martins',      '(11) 93789-0123', 'bruno.martins@yahoo.com',     2,  'site',      'em_atendimento',  'Vocês entregam o Mobi em ABC paulista?',                                             2,    NULL,    DATE_SUB(NOW(), INTERVAL 1 DAY)),

  -- PROPOSTA
  ('Sandra Pereira',     '(11) 92890-1234', 'sandra.p@gmail.com',          3,  'site',      'proposta',        'Aceito pagar R$ 72.000 à vista no HB20',                                             3,    72000.00,DATE_SUB(NOW(), INTERVAL 4 DAY)),
  ('Diego Rocha',        '(11) 91901-2345', 'diego.rocha@gmail.com',       10, 'indicacao', 'proposta',        'Posso dar entrada de R$ 30k e financiar o restante da Strada',                       2,    85000.00,DATE_SUB(NOW(), INTERVAL 6 DAY)),
  ('Larissa Mendes',     '(11) 90012-3456', NULL,                          6,  'whatsapp',  'proposta',        'R$ 115k no Civic, financia o resto em 48x',                                          4,    115000.00,DATE_SUB(NOW(), INTERVAL 5 DAY)),

  -- FECHADO
  ('Marcos Vinicius',    '(11) 99123-4567', 'marcos.v@gmail.com',          11, 'site',      'fechado',         'Fechado! Honda Civic vendido',                                                       2,    119900.00,DATE_SUB(NOW(), INTERVAL 12 DAY)),
  ('Carla Souza',        '(11) 98234-5678', 'carla.souza@uol.com.br',      NULL,'whatsapp', 'fechado',         'Comprou um Gol que tínhamos antes',                                                  3,    58500.00, DATE_SUB(NOW(), INTERVAL 18 DAY)),

  -- PERDIDO
  ('Henrique Alves',     '(11) 97345-6789', 'henrique.a@gmail.com',        8,  'site',      'perdido',         'Achou mais barato em outra loja',                                                    2,    NULL,    DATE_SUB(NOW(), INTERVAL 10 DAY)),
  ('Beatriz Carneiro',   '(11) 96456-7890', NULL,                          5,  'instagram', 'perdido',         'Desistiu da compra',                                                                 4,    NULL,    DATE_SUB(NOW(), INTERVAL 15 DAY));

-- ============================================================
-- INTERAÇÕES — histórico de alguns leads
-- ============================================================
INSERT INTO lead_interactions (lead_id, user_id, tipo, conteudo, created_at) VALUES
  (5, 2, 'whatsapp', 'Conversei pelo WhatsApp, cliente vai trazer documentação amanhã para análise de crédito.', DATE_SUB(NOW(), INTERVAL 2 DAY)),
  (5, 2, 'nota',     'Score Serasa: 680. Aprovado para financiamento via banco parceiro.',                       DATE_SUB(NOW(), INTERVAL 1 DAY)),
  (5, 2, 'ligacao',  'Liguei para confirmar visita de amanhã às 15h. Cliente confirmou.',                        DATE_SUB(NOW(), INTERVAL 6 HOUR)),

  (8, 3, 'visita',   'Cliente veio à loja, gostou muito do HB20. Voltará amanhã com a esposa para decidir.',     DATE_SUB(NOW(), INTERVAL 3 DAY)),
  (8, 3, 'nota',     'Proposta: R$ 72.000 à vista. Margem ok, podemos aceitar.',                                  DATE_SUB(NOW(), INTERVAL 2 DAY)),

  (11, 2, 'visita',   'Cliente fez test-drive, aprovou. Pagamento em 5 dias.',                                    DATE_SUB(NOW(), INTERVAL 14 DAY)),
  (11, 2, 'nota',     'VENDIDO! Documentação transferida em 12 dias. Cliente satisfeitíssimo.',                   DATE_SUB(NOW(), INTERVAL 12 DAY)),

  (1, NULL, 'nota',   'Lead recebido via formulário do site. Aguardando primeiro contato.',                       DATE_SUB(NOW(), INTERVAL 2 HOUR));

-- ============================================================
-- VENDAS — Financeiro demo
-- (car_id é NOT NULL — sempre vinculamos a um carro do estoque)
-- ============================================================
INSERT INTO sales (car_id, lead_id, vendedor_id, cliente_nome, cliente_documento, cliente_telefone, valor_venda, forma_pagamento, comissao, observacoes, data_venda) VALUES
  (11, 11, 2, 'Marcos Vinicius Carvalho',  '123.456.789-00', '(11) 99123-4567', 119900.00, 'Financiamento',     3597.00, 'Financiado em 48x via Santander', DATE_SUB(CURDATE(), INTERVAL 12 DAY)),
  (1,  12, 3, 'Carla Souza Pereira',       '987.654.321-00', '(11) 98234-5678',  58500.00, 'À Vista',           1755.00, 'Pagamento via TED, sem entrada',  DATE_SUB(CURDATE(), INTERVAL 18 DAY));

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- ✅ DADOS DE DEMONSTRAÇÃO INSERIDOS
-- - 4 usuários adicionais (senha: nanoadmin123)
-- - 12 carros variados
-- - 14 leads em vários estágios do funil
-- - 8 interações de exemplo
-- - 2 vendas registradas
--
-- ⚠️ PARA LIMPAR ANTES DE PRODUÇÃO REAL, execute:
-- DELETE FROM lead_interactions WHERE id > 0;
-- DELETE FROM leads WHERE id > 0;
-- DELETE FROM sales WHERE id > 0;
-- DELETE FROM cars WHERE id > 0;
-- DELETE FROM users WHERE email != 'admin@nanoautomoveis.com.br';
-- ALTER TABLE cars AUTO_INCREMENT = 1;
-- ALTER TABLE leads AUTO_INCREMENT = 1;
-- ============================================================
