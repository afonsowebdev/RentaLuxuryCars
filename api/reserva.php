<?php
/**
 * api/reserva.php — POST: cria uma nova reserva
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

definirHeadersSeguranca();

if (!pedidoEhPost()) {
    respostaJson(['sucesso' => false, 'erro' => 'Método não permitido.'], 405);
}

if (!verificarTokenCSRF($_POST['csrf_token'] ?? null)) {
    respostaJson(['sucesso' => false, 'erro' => 'Token de segurança inválido.'], 403);
}

function validarDataApi(string $data): bool
{
    $dt = DateTime::createFromFormat('Y-m-d', $data);
    return $dt !== false && $dt->format('Y-m-d') === $data;
}

$erros = [];

$veiculoId = (int) ($_POST['veiculo_id'] ?? 0);
$dataInicio = trim((string) ($_POST['data_inicio'] ?? ''));
$dataFim = trim((string) ($_POST['data_fim'] ?? ''));
$localEntrega = trim((string) ($_POST['local_entrega'] ?? ''));
$localDevolucao = trim((string) ($_POST['local_devolucao'] ?? ''));
$clienteNome = trim((string) ($_POST['cliente_nome'] ?? ''));
$clienteApelido = trim((string) ($_POST['cliente_apelido'] ?? ''));
$clienteEmail = trim((string) ($_POST['cliente_email'] ?? ''));
$clienteTelefone = trim((string) ($_POST['cliente_telefone'] ?? ''));
$clienteNif = trim((string) ($_POST['cliente_nif'] ?? ''));
$clienteCarta = trim((string) ($_POST['cliente_carta_conducao'] ?? ''));
$clienteNascimento = trim((string) ($_POST['cliente_nascimento'] ?? ''));
$metodoPagamento = $_POST['metodo_pagamento'] ?? 'transferencia';
$notas = trim((string) ($_POST['notas'] ?? ''));
$extrasEnviados = array_map('strval', $_POST['extras'] ?? []);

if ($veiculoId <= 0) $erros['veiculo'] = 'Veículo inválido.';
if (!validarDataApi($dataInicio)) $erros['data_inicio'] = 'Data de início inválida.';
if (!validarDataApi($dataFim)) $erros['data_fim'] = 'Data de fim inválida.';
if (validarDataApi($dataInicio) && validarDataApi($dataFim) && $dataFim <= $dataInicio) {
    $erros['datas'] = 'A data de fim deve ser posterior à data de início.';
}
if ($localEntrega === '') $erros['local_entrega'] = 'Indique o local de entrega.';
if ($localDevolucao === '') $erros['local_devolucao'] = 'Indique o local de devolução.';
if ($clienteNome === '') $erros['cliente_nome'] = 'Nome obrigatório.';
if ($clienteApelido === '') $erros['cliente_apelido'] = 'Apelido obrigatório.';
if (!filter_var($clienteEmail, FILTER_VALIDATE_EMAIL)) $erros['cliente_email'] = 'Email inválido.';
if ($clienteTelefone === '') $erros['cliente_telefone'] = 'Telefone obrigatório.';
if ($clienteCarta === '') $erros['cliente_carta_conducao'] = 'Número da carta de condução obrigatório.';
if (!validarDataApi($clienteNascimento)) $erros['cliente_nascimento'] = 'Data de nascimento inválida.';
if (!in_array($metodoPagamento, ['transferencia', 'multibanco', 'cartao', 'presencial'], true)) {
    $erros['metodo_pagamento'] = 'Método de pagamento inválido.';
}

if ($erros) {
    respostaJson(['sucesso' => false, 'erros' => $erros], 422);
}

$pdo = obterLigacaoBD();

$stmt = $pdo->prepare('SELECT * FROM veiculos WHERE id = ? AND disponivel = 1');
$stmt->execute([$veiculoId]);
$veiculo = $stmt->fetch();

if (!$veiculo) {
    respostaJson(['sucesso' => false, 'erros' => ['veiculo' => 'Veículo não encontrado ou indisponível.']], 404);
}

$stmtConflito = $pdo->prepare(
    "SELECT COUNT(*) FROM reservas
     WHERE veiculo_id = ? AND estado NOT IN ('cancelada')
     AND data_inicio < ? AND data_fim > ?"
);
$stmtConflito->execute([$veiculoId, $dataFim, $dataInicio]);

if ((int) $stmtConflito->fetchColumn() > 0) {
    respostaJson(['sucesso' => false, 'erros' => ['datas' => 'O veículo já está reservado neste período.']], 409);
}

$dias = calcularDias($dataInicio, $dataFim);
$precoBase = $dias * (float) $veiculo['preco_dia'];

$precoExtras = 0.0;
if ($extrasEnviados) {
    $placeholders = implode(', ', array_fill(0, count($extrasEnviados), '?'));
    $stmtExtras = $pdo->prepare("SELECT preco_dia FROM extras WHERE slug IN ({$placeholders}) AND ativo = 1");
    $stmtExtras->execute($extrasEnviados);
    foreach ($stmtExtras->fetchAll() as $extra) {
        $precoExtras += $dias * (float) $extra['preco_dia'];
    }
}

$precoTotal = $precoBase + $precoExtras;

$referencia = gerarReferenciaReserva();
$tentativas = 0;
do {
    $stmtVerifica = $pdo->prepare('SELECT COUNT(*) FROM reservas WHERE referencia = ?');
    $stmtVerifica->execute([$referencia]);
    if ((int) $stmtVerifica->fetchColumn() === 0) {
        break;
    }
    $referencia = gerarReferenciaReserva();
    $tentativas++;
} while ($tentativas < 5);

$utilizadorId = estaAutenticado() ? (int) $_SESSION['utilizador_id'] : null;

try {
    $pdo->beginTransaction();

    $stmtInserir = $pdo->prepare(
        'INSERT INTO reservas
         (referencia, veiculo_id, utilizador_id, cliente_nome, cliente_apelido, cliente_email, cliente_telefone,
          cliente_nif, cliente_carta_conducao, cliente_nascimento, data_inicio, data_fim, local_entrega, local_devolucao,
          extras, dias, preco_base, preco_extras, preco_total, estado, notas, metodo_pagamento)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );

    $stmtInserir->execute([
        $referencia, $veiculoId, $utilizadorId, $clienteNome, $clienteApelido, $clienteEmail, $clienteTelefone,
        $clienteNif ?: null, $clienteCarta, $clienteNascimento, $dataInicio, $dataFim, $localEntrega, $localDevolucao,
        json_encode($extrasEnviados, JSON_UNESCAPED_UNICODE), $dias, $precoBase, $precoExtras, $precoTotal,
        'pendente', $notas ?: null, $metodoPagamento,
    ]);

    $reservaId = (int) $pdo->lastInsertId();

    $stmtBloqueio = $pdo->prepare(
        'INSERT INTO disponibilidade (veiculo_id, reserva_id, data_inicio, data_fim, motivo) VALUES (?, ?, ?, ?, ?)'
    );
    $stmtBloqueio->execute([$veiculoId, $reservaId, $dataInicio, $dataFim, 'Reserva ' . $referencia]);

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    respostaJson(['sucesso' => false, 'erro' => 'Erro ao criar a reserva. Tente novamente.'], 500);
}

respostaJson(['sucesso' => true, 'referencia' => $referencia]);
