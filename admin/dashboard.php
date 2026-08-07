<?php
/**
 * admin/dashboard.php — Painel principal com estatísticas
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

exigirAdmin();

$pdo = obterLigacaoBD();

$totalVeiculos = (int) $pdo->query('SELECT COUNT(*) FROM veiculos')->fetchColumn();
$totalReservas = (int) $pdo->query('SELECT COUNT(*) FROM reservas')->fetchColumn();
$totalClientes = (int) $pdo->query("SELECT COUNT(*) FROM utilizadores WHERE tipo = 'cliente'")->fetchColumn();
$receitaTotal = (float) $pdo->query("SELECT COALESCE(SUM(preco_total), 0) FROM reservas WHERE estado != 'cancelada'")->fetchColumn();

$reservasPorMes = $pdo->query(
    "SELECT DATE_FORMAT(criado_em, '%Y-%m') AS mes, COUNT(*) AS total
     FROM reservas
     WHERE criado_em >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
     GROUP BY mes ORDER BY mes ASC"
)->fetchAll();

$reservasPorCategoria = $pdo->query(
    "SELECT v.categoria, COUNT(*) AS total
     FROM reservas r JOIN veiculos v ON v.id = r.veiculo_id
     GROUP BY v.categoria"
)->fetchAll();

$ultimasReservas = $pdo->query(
    "SELECT r.*, v.marca, v.modelo
     FROM reservas r JOIN veiculos v ON v.id = r.veiculo_id
     ORDER BY r.criado_em DESC LIMIT 8"
)->fetchAll();

$dadosGraficoReservas = [
    'labels' => array_map(static fn ($linha) => $linha['mes'], $reservasPorMes),
    'valores' => array_map(static fn ($linha) => (int) $linha['total'], $reservasPorMes),
];

$categoriasLabels = obterCategoriasVeiculos();
$dadosGraficoCategorias = [
    'labels' => array_map(static fn ($linha) => $categoriasLabels[$linha['categoria']] ?? $linha['categoria'], $reservasPorCategoria),
    'valores' => array_map(static fn ($linha) => (int) $linha['total'], $reservasPorCategoria),
];

$tituloPagina = 'Dashboard — Painel Admin';
$paginaAtivaAdmin = 'dashboard';
require_once __DIR__ . '/../includes/admin-header.php';
?>

<div class="admin-stats">
    <div class="stat-card">
        <div class="stat-card__topo">
            <div class="stat-card__icon"><i class="fa-solid fa-car"></i></div>
        </div>
        <div class="stat-card__valor"><?= $totalVeiculos ?></div>
        <div class="stat-card__label">Veículos na Frota</div>
    </div>
    <div class="stat-card">
        <div class="stat-card__topo">
            <div class="stat-card__icon"><i class="fa-solid fa-calendar-check"></i></div>
        </div>
        <div class="stat-card__valor"><?= $totalReservas ?></div>
        <div class="stat-card__label">Reservas Totais</div>
    </div>
    <div class="stat-card">
        <div class="stat-card__topo">
            <div class="stat-card__icon"><i class="fa-solid fa-users"></i></div>
        </div>
        <div class="stat-card__valor"><?= $totalClientes ?></div>
        <div class="stat-card__label">Clientes Registados</div>
    </div>
    <div class="stat-card">
        <div class="stat-card__topo">
            <div class="stat-card__icon"><i class="fa-solid fa-sack-dollar"></i></div>
        </div>
        <div class="stat-card__valor"><?= formatarPreco($receitaTotal) ?></div>
        <div class="stat-card__label">Receita Total</div>
    </div>
</div>

<div class="admin-charts">
    <div class="chart-card">
        <h3 class="chart-card__titulo">Reservas nos Últimos 6 Meses</h3>
        <div class="chart-card__canvas-wrapper">
            <canvas id="graficoReservas" data-dados='<?= e(json_encode($dadosGraficoReservas)) ?>'></canvas>
        </div>
    </div>
    <div class="chart-card">
        <h3 class="chart-card__titulo">Reservas por Categoria</h3>
        <div class="chart-card__canvas-wrapper">
            <canvas id="graficoCategorias" data-dados='<?= e(json_encode($dadosGraficoCategorias)) ?>'></canvas>
        </div>
    </div>
</div>

<div class="admin-painel">
    <div class="admin-painel__cabecalho">
        <h3 class="admin-painel__titulo">Últimas Reservas</h3>
        <a href="/admin/reservas.php" class="btn btn--ghost btn--sm">Ver Todas</a>
    </div>
    <div class="tabela-wrapper">
        <table class="tabela">
            <thead>
                <tr>
                    <th>Referência</th>
                    <th>Cliente</th>
                    <th>Veículo</th>
                    <th>Período</th>
                    <th>Total</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ultimasReservas as $reserva): ?>
                    <tr>
                        <td><?= e($reserva['referencia']) ?></td>
                        <td><?= e($reserva['cliente_nome'] . ' ' . $reserva['cliente_apelido']) ?></td>
                        <td><?= e($reserva['marca'] . ' ' . $reserva['modelo']) ?></td>
                        <td><?= formatarData($reserva['data_inicio']) ?> — <?= formatarData($reserva['data_fim']) ?></td>
                        <td><?= formatarPreco($reserva['preco_total']) ?></td>
                        <td><span class="badge badge--estado-<?= e($reserva['estado']) ?>"><?= e(obterLabelEstadoReserva($reserva['estado'])) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$ultimasReservas): ?>
                    <tr><td colspan="6">Ainda não existem reservas.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>
