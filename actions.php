<?php
/** Handlers de POST (mutations) */

function handleAction(string $path): void {
    switch (true) {
        case $path === '/login':
            csrfCheck();
            if (login($_POST['email'] ?? '', $_POST['senha'] ?? '')) {
                $next = $_POST['next'] ?? '/admin';
                redirect($next);
            }
            flash('E-mail ou senha incorretos.', 'error');
            redirect('/login');

        case $path === '/contato':
        case $path === '/leads':
            csrfCheck();
            $carId = !empty($_POST['car_id']) ? (int)$_POST['car_id'] : null;
            insertGetId('leads', [
                'nome' => trim($_POST['nome'] ?? ''),
                'telefone' => trim($_POST['telefone'] ?? ''),
                'email' => trim($_POST['email'] ?? '') ?: null,
                'mensagem' => trim($_POST['mensagem'] ?? '') ?: null,
                'car_id' => $carId,
                'origem' => 'site',
            ]);
            flash('Recebemos sua mensagem! Em instantes entraremos em contato.');
            // Redireciona abrindo WhatsApp como bônus
            $wa = config('contato')['whatsapp'];
            $nome = $_POST['nome'] ?? '';
            $msg = "Olá! Meu nome é $nome. " . ($_POST['mensagem'] ?? 'Tenho interesse no veículo anunciado.');
            redirect(whatsappLink($wa, $msg));

        case $path === '/admin/carros/salvar':
            $u = requireRole(['proprietario','financeiro','vendedor']);
            csrfCheck();
            saveCarro($u);
            redirect('/admin/carros');

        case $path === '/admin/carros/excluir':
            $u = requireRole(['proprietario']);
            csrfCheck();
            deleteRow('cars', (int)($_POST['id'] ?? 0));
            flash('Carro excluído.');
            redirect('/admin/carros');

        case $path === '/admin/crm/atualizar':
            $u = requireRole(['proprietario','financeiro','vendedor']);
            csrfCheck();
            $id = (int)$_POST['id'];
            updateRow('leads', [
                'status' => $_POST['status'],
                'responsavel_id' => $_POST['responsavel_id'] ?: null,
            ], $id);
            flash('Lead atualizado.');
            redirect('/admin/crm/' . $id);

        case $path === '/admin/crm/interacao':
            $u = requireRole(['proprietario','financeiro','vendedor']);
            csrfCheck();
            insertGetId('lead_interactions', [
                'lead_id' => (int)$_POST['lead_id'],
                'user_id' => (int)$u['id'],
                'tipo' => $_POST['tipo'],
                'conteudo' => $_POST['conteudo'],
            ]);
            redirect('/admin/crm/' . (int)$_POST['lead_id']);

        case $path === '/admin/crm/novo':
            $u = requireRole(['proprietario','financeiro','vendedor']);
            csrfCheck();
            $leadId = insertGetId('leads', [
                'nome'           => trim($_POST['nome']),
                'telefone'       => trim($_POST['telefone']),
                'email'          => trim($_POST['email'] ?? '') ?: null,
                'mensagem'       => trim($_POST['mensagem'] ?? '') ?: null,
                'car_id'         => !empty($_POST['car_id']) ? (int)$_POST['car_id'] : null,
                'origem'         => $_POST['origem'] ?: 'outro',
                'status'         => $_POST['status'] ?: 'novo',
                'responsavel_id' => !empty($_POST['responsavel_id']) ? (int)$_POST['responsavel_id'] : (int)$u['id'],
            ]);
            flash('Lead criado com sucesso!');
            redirect('/admin/crm/' . $leadId);

        case $path === '/admin/crm/importar':
            $u = requireRole(['proprietario','financeiro','vendedor']);
            csrfCheck();
            $result = importarLeadsCSV($_FILES['csv'] ?? null, (int)$u['id']);
            if ($result['ok']) {
                flash("Importação concluída: {$result['imported']} lead(s) importado(s), {$result['skipped']} ignorado(s).");
            } else {
                flash($result['error'], 'error');
            }
            redirect('/admin/crm');

        case $path === '/admin/usuarios/atualizar':
            requireRole(['proprietario']);
            csrfCheck();
            $id = (int)$_POST['id'];
            $data = ['role' => $_POST['role'], 'ativo' => isset($_POST['ativo']) ? 1 : 0];
            if (!empty($_POST['senha'])) $data['senha_hash'] = password_hash($_POST['senha'], PASSWORD_DEFAULT);
            updateRow('users', $data, $id);
            flash('Usuário atualizado.');
            redirect('/admin/usuarios');

        case $path === '/admin/financeiro/salvar':
            $u = requireRole(['proprietario','financeiro']);
            csrfCheck();
            insertGetId('sales', [
                'car_id'             => (int)$_POST['car_id'],
                'vendedor_id'        => !empty($_POST['vendedor_id']) ? (int)$_POST['vendedor_id'] : null,
                'cliente_nome'       => trim($_POST['cliente_nome']),
                'cliente_documento'  => trim($_POST['cliente_documento'] ?? '') ?: null,
                'cliente_telefone'   => trim($_POST['cliente_telefone'] ?? '') ?: null,
                'valor_venda'        => (float)$_POST['valor_venda'],
                'forma_pagamento'    => $_POST['forma_pagamento'] ?: null,
                'comissao'           => !empty($_POST['comissao']) ? (float)$_POST['comissao'] : null,
                'observacoes'        => trim($_POST['observacoes'] ?? '') ?: null,
                'data_venda'         => $_POST['data_venda'],
            ]);
            flash('Venda registrada com sucesso!');
            redirect('/admin/financeiro');

        case $path === '/admin/usuarios/novo':
            requireRole(['proprietario']);
            csrfCheck();
            insertGetId('users', [
                'nome' => $_POST['nome'],
                'email' => $_POST['email'],
                'senha_hash' => password_hash($_POST['senha'], PASSWORD_DEFAULT),
                'role' => $_POST['role'],
                'ativo' => 1,
            ]);
            flash('Usuário criado.');
            redirect('/admin/usuarios');
    }
    http_response_code(404); exit('Ação inválida');
}

function saveCarro(array $user): void {
    $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;

    // Upload de fotos novas
    $novasFotos = [];
    if (!empty($_FILES['fotos']['name'][0])) {
        $novasFotos = uploadFotos($_FILES['fotos']);
    }

    // Fotos existentes (mantidas)
    $fotosExistentes = $_POST['fotos_existentes'] ?? [];
    if (is_string($fotosExistentes)) $fotosExistentes = [];
    $todasFotos = array_merge($fotosExistentes, $novasFotos);

    $data = [
        'marca' => mb_strtoupper(trim($_POST['marca'])),
        'modelo' => mb_strtoupper(trim($_POST['modelo'])),
        'versao' => $_POST['versao'] ?: null,
        'ano_fabricacao' => (int)$_POST['ano_fabricacao'],
        'ano_modelo' => (int)$_POST['ano_modelo'],
        'km' => (int)$_POST['km'],
        'cor' => $_POST['cor'] ?: null,
        'combustivel' => $_POST['combustivel'] ?: null,
        'cambio' => $_POST['cambio'] ?: null,
        'carroceria' => $_POST['carroceria'] ?: null,
        'portas' => $_POST['portas'] ? (int)$_POST['portas'] : null,
        'preco' => (float)$_POST['preco'],
        'preco_promocional' => !empty($_POST['preco_promocional']) ? (float)$_POST['preco_promocional'] : null,
        'status' => $_POST['status'],
        'destaque' => isset($_POST['destaque']) ? 1 : 0,
        'descricao' => $_POST['descricao'] ?: null,
        'opcionais' => trim($_POST['opcionais'] ?? '') ?: null,
        'foto_capa' => $todasFotos[0] ?? null,
        'fotos' => json_encode(array_slice($todasFotos, 1)),
    ];

    if ($id) {
        updateRow('cars', $data, $id);
        flash('Carro atualizado!');
    } else {
        $data['created_by'] = (int)$user['id'];
        insertGetId('cars', $data);
        flash('Carro cadastrado!');
    }
}

/**
 * Importa leads de um CSV.
 * Formato esperado (com cabeçalho):
 *   nome,telefone,email,mensagem,origem
 * Aceita também ;  ou \t como separador. UTF-8 ou ISO-8859-1.
 */
function importarLeadsCSV(?array $file, int $userId): array {
    if (!$file || ($file['error'] ?? 999) !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'Nenhum arquivo enviado ou erro no upload.'];
    }
    $tmp = $file['tmp_name'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['csv','txt'])) {
        return ['ok' => false, 'error' => 'Arquivo precisa ser .csv ou .txt'];
    }

    // Detecta encoding e converte se necessário
    $content = file_get_contents($tmp);
    if (!mb_check_encoding($content, 'UTF-8')) {
        $content = mb_convert_encoding($content, 'UTF-8', 'ISO-8859-1');
    }
    // Remove BOM
    $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

    // Detecta separador (procura na primeira linha)
    $firstLine = strtok($content, "\n");
    $sep = ',';
    foreach ([';', "\t", '|'] as $s) {
        if (substr_count($firstLine, $s) > substr_count($firstLine, $sep)) $sep = $s;
    }

    $lines = preg_split('/\r\n|\n|\r/', $content);
    $header = str_getcsv(array_shift($lines), $sep);
    $header = array_map(fn($h) => mb_strtolower(trim($h)), $header);

    // Mapeia colunas
    $col = function($name) use ($header) {
        $name = mb_strtolower($name);
        foreach ($header as $i => $h) {
            if ($h === $name || strpos($h, $name) !== false) return $i;
        }
        return null;
    };
    $idxNome     = $col('nome');
    $idxTelefone = $col('telefone') ?? $col('celular') ?? $col('fone') ?? $col('whatsapp');
    $idxEmail    = $col('email') ?? $col('e-mail');
    $idxMsg      = $col('mensagem') ?? $col('observa') ?? $col('coment');
    $idxOrigem   = $col('origem') ?? $col('canal');

    if ($idxNome === null || $idxTelefone === null) {
        return ['ok' => false, 'error' => 'CSV precisa ter pelo menos as colunas "nome" e "telefone".'];
    }

    $imported = 0; $skipped = 0;
    foreach ($lines as $line) {
        if (trim($line) === '') { continue; }
        $row = str_getcsv($line, $sep);
        $nome     = trim($row[$idxNome] ?? '');
        $telefone = trim($row[$idxTelefone] ?? '');
        if ($nome === '' || $telefone === '') { $skipped++; continue; }

        $origem = $idxOrigem !== null ? mb_strtolower(trim($row[$idxOrigem] ?? '')) : 'outro';
        if (!in_array($origem, ['site','whatsapp','instagram','indicacao','outro'], true)) {
            $origem = 'outro';
        }

        insertGetId('leads', [
            'nome'           => $nome,
            'telefone'       => $telefone,
            'email'          => $idxEmail !== null ? (trim($row[$idxEmail] ?? '') ?: null) : null,
            'mensagem'       => $idxMsg !== null ? (trim($row[$idxMsg] ?? '') ?: null) : null,
            'origem'         => $origem,
            'status'         => 'novo',
            'responsavel_id' => null,
        ]);
        $imported++;
    }

    return ['ok' => true, 'imported' => $imported, 'skipped' => $skipped];
}
