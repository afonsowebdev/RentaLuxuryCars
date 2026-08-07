<?php
/**
 * admin/relatorios.php — Relatórios de desempenho
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

exigirAdmin();

$pdo = obterLigacaoBD();

$receitaPorMes = $pdo->query(
    "SELECT DATE_FORMAT(criado_em, '%Y-%m') AS mes, SUM(preco_total) AS total
     FROM reservas
     WHERE estado != 'cancelada' AND criado_em >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
     GROUP BY mes ORDER BY mes ASC"
)->fetchAll();

$topVeiculos = $pdo->query(
    "SELECT v.marca, v.modelo, COUNT(r.id) AS total_reservas, COALESCE(SUM(r.preco_total), 0) AS receita
     FROM veiculos v
     LEFT JOIN reservas r ON r.veiculo_id = v.id AND r.estado != 'cancelada'
     GROUP BY v.id
     ORDER BY total_reservas DESC, receita DESC
     LIMIT 8"
)->fetchAll();

$taxaOcupacao = $pdo->query(
    "SELECT
        (SELECT COUNT(*) FROM reservas WHERE estado IN ('confirmada', 'ativa')) AS ocupados,
        (SELECT COUNT(*) FROM veiculos WHERE disponivel = 1) AS total_disponiveis"
)->fetch();

$dadosGraficoReceita = [
    'labels' => array_map(static fn ($l) => $l['mes'], $receitaPorMes),
    'valores' => array_map(static fn ($l) => (float) $l['total'], $receitaPorMes),
];

$tituloPagina = 'Relatórios — Painel Admin';
$paginaAtivaAdmin = 'relatorios';
require_once __DIR__ . '/../includes/admin-header.php';
?>

<div class="admin-charts">
    <div class="chart-card">
        <h3 class="chart-card__titulo">Receita nos Últimos 12 Meses</h3>
        <div class="chart-card__canvas-wrapper">
            <canvas id="graficoReservas" data-dados='<?= e(json_encode($dadosGraficoReceita)) ?>'></canvas>
        </div>
    </div>
    <div class="chart-card">
        <h3 class="chart-card__titulo">Taxa de Ocupação da Frota</h3>
        <div class="chart-card__canvas-wrapper flex-center" style="flex-direction:column;">
            <?php
            $total = max(1, (int) $taxaOcupacao['total_disponiveis']);
            $percentagem = round(min(100, ($taxaOcupacao['ocupados'] / $total) * 100));
            ?>
            <div class="estatisticas__valor" style="font-size:3rem;"><?= $percentagem ?>%</div>
            <p class="mt-sm" style="color:var(--cor-cinza-claro);"><?= (int) $taxaOcupacao['ocupados'] ?> de <?= $total ?> veículos com reservas ativas</p>
        </div>
    </div>
</div>

<div class="admin-painel">
    <div class="admin-painel__cabecalho">
        <h3 class="admin-painel__titulo">Veículos Mais Reservados</h3>
    </div>
    <div class="tabela-wrapper">
        <table class="tabela">
            <thead>
                <tr>
                    <th>Veículo</th>
                    <th>Total de Reservas</th>
                    <th>Receita Gerada</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topVeiculos as $v): ?>
                    <tr>
                        <td><?= e($v['marca'] . ' ' . $v['modelo']) ?></td>
                        <td><?= (int) $v['total_reservas'] ?></td>
                        <td><?= formatarPreco($v['receita']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>
