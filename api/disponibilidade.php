<?php
/**
 * api/disponibilidade.php — GET: verifica disponibilidade de um veículo num período
 * Parâmetros: veiculo_id, inicio (Y-m-d), fim (Y-m-d)
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';

definirHeadersSeguranca();

$veiculoId = (int) ($_GET['veiculo_id'] ?? 0);
$inicio = $_GET['inicio'] ?? '';
$fim = $_GET['fim'] ?? '';

if ($veiculoId <= 0 || !validarData($inicio) || !validarData($fim) || $fim <= $inicio) {
    respostaJson(['disponivel' => false, 'erro' => 'Parâmetros inválidos.'], 400);
}

$pdo = obterLigacaoBD();

$stmt = $pdo->prepare(
    "SELECT data_inicio, data_fim FROM reservas
     WHERE veiculo_id = ? AND estado NOT IN ('cancelada')
     AND data_inicio < ? AND data_fim > ?"
);
$stmt->execute([$veiculoId, $fim, $inicio]);
$conflitos = $stmt->fetchAll();

respostaJson([
    'disponivel' => count($conflitos) === 0,
    'datas_bloqueadas' => array_map(static fn ($linha) => [
        'inicio' => $linha['data_inicio'],
        'fim' => $linha['data_fim'],
    ], $conflitos),
]);

function validarData(string $data): bool
{
    $dt = DateTime::createFromFormat('Y-m-d', $data);
    return $dt !== false && $dt->format('Y-m-d') === $data;
}
