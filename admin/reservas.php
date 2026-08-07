<?php
/**
 * admin/reservas.php — Gestão de reservas
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

exigirAdmin();

$pdo = obterLigacaoBD();
$mensagem = null;
$erro = null;

if (pedidoEhPost()) {
    if (!verificarTokenCSRF($_POST['csrf_token'] ?? null)) {
        $erro = 'Token de segurança inválido.';
    } else {
        $id = (int) ($_POST['id'] ?? 0);
        $acao = $_POST['acao'] ?? '';

        $estadosValidos = ['pendente', 'confirmada', 'ativa', 'concluida', 'cancelada'];

        if ($acao === 'atualizar_estado' && in_array($_POST['estado'] ?? '', $estadosValidos, true)) {
            $stmt = $pdo->prepare('UPDATE reservas SET estado = ? WHERE id = ?');
            $stmt->execute([$_POST['estado'], $id]);
            $mensagem = 'Estado da reserva atualizado.';
        } elseif ($acao === 'marcar_pago') {
            $stmt = $pdo->prepare('UPDATE reservas SET pago = 1 WHERE id = ?');
            $stmt->execute([$id]);
            $mensagem = 'Reserva marcada como paga.';
        }
    }
}

$filtroEstado = $_GET['estado'] ?? '';
$sql = "SELECT r.*, v.marca, v.modelo, v.imagem_principal FROM reservas r JOIN veiculos v ON v.id = r.veiculo_id";
$parametros = [];

if ($filtroEstado !== '') {
    $sql .= ' WHERE r.estado = ?';
    $parametros[] = $filtroEstado;
}
$sql .= ' ORDER BY r.criado_em DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($parametros);
$reservas = $stmt->fetchAll();

$tituloPagina = 'Reservas — Painel Admin';
$paginaAtivaAdmin = 'reservas';
require_once __DIR__ . '/../includes/admin-header.php';
?>

<?php if ($mensagem): ?><div class="alerta alerta--sucesso"><i class="fa-solid fa-circle-check"></i> <?= e($mensagem) ?></div><?php endif; ?>
<?php if ($erro): ?><div class="alerta alerta--erro"><i class="fa-solid fa-circle-exclamation"></i> <?= e($erro) ?></div><?php endif; ?>

<div class="admin-painel">
    <div class="tabela-topo">
        <h3 class="admin-painel__titulo">Todas as Reservas (<?= count($reservas) ?>)</h3>
        <div style="display:flex;gap:1rem;align-items:center;">
            <input type="text" class="input tabela-topo__pesquisa" placeholder="Pesquisar..." data-pesquisa-tabela="tabelaReservas">
            <select class="input" onchange="window.location.href='/admin/reservas.php' + (this.value ? '?estado=' + this.value : '')" style="width:auto;">
                <option value="">Todos os Estados</option>
                <option value="pendente" <?= $filtroEstado === 'pendente' ? 'selected' : '' ?>>Pendente</option>
                <option value="confirmada" <?= $filtroEstado === 'confirmada' ? 'selected' : '' ?>>Confirmada</option>
                <option value="ativa" <?= $filtroEstado === 'ativa' ? 'selected' : '' ?>>Ativa</option>
                <option value="concluida" <?= $filtroEstado === 'concluida' ? 'selected' : '' ?>>Concluída</option>
                <option value="cancelada" <?= $filtroEstado === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
            </select>
        </div>
    </div>
    <div class="tabela-wrapper">
        <table class="tabela" id="tabelaReservas">
            <thead>
                <tr>
                    <th>Referência</th>
                    <th>Cliente</th>
                    <th>Veículo</th>
                    <th>Período</th>
                    <th>Total</th>
                    <th>Pago</th>
                    <th>Estado</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservas as $reserva): ?>
                    <tr>
                        <td><?= e($reserva['referencia']) ?></td>
                        <td><?= e($reserva['cliente_nome'] . ' ' . $reserva['cliente_apelido']) ?><br><small style="color:var(--cor-cinza-md);"><?= e($reserva['cliente_email']) ?></small></td>
                        <td><?= e($reserva['marca'] . ' ' . $reserva['modelo']) ?></td>
                        <td><?= formatarData($reserva['data_inicio']) ?> — <?= formatarData($reserva['data_fim']) ?></td>
                        <td><?= formatarPreco($reserva['preco_total']) ?></td>
                        <td>
                            <?php if ($reserva['pago']): ?>
                                <span class="badge badge--disponivel">Pago</span>
                            <?php else: ?>
                                <form method="POST" style="display:inline;">
                                    <?= campoCSRF() ?>
                                    <input type="hidden" name="acao" value="marcar_pago">
                                    <input type="hidden" name="id" value="<?= e((string) $reserva['id']) ?>">
                                    <button type="submit" class="btn btn--ghost btn--sm">Marcar Pago</button>
                                </form>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" style="display:flex;gap:0.5rem;align-items:center;">
                                <?= campoCSRF() ?>
                                <input type="hidden" name="acao" value="atualizar_estado">
                                <input type="hidden" name="id" value="<?= e((string) $reserva['id']) ?>">
                                <select name="estado" class="input" style="width:auto;padding:0.4rem;" onchange="this.form.submit()">
                                    <?php foreach (['pendente', 'confirmada', 'ativa', 'concluida', 'cancelada'] as $estado): ?>
                                        <option value="<?= e($estado) ?>" <?= $reserva['estado'] === $estado ? 'selected' : '' ?>><?= e(obterLabelEstadoReserva($estado)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </td>
                        <td>
                            <div class="tabela__acoes">
                                <a href="mailto:<?= e($reserva['cliente_email']) ?>" title="Contactar"><i class="fa-solid fa-envelope"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$reservas): ?>
                    <tr><td colspan="8">Nenhuma reserva encontrada.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>
