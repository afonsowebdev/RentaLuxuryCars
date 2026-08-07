<?php
/**
 * admin/clientes.php — Gestão de clientes
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

exigirAdmin();

$pdo = obterLigacaoBD();
$mensagem = null;

if (pedidoEhPost() && verificarTokenCSRF($_POST['csrf_token'] ?? null)) {
    if (($_POST['acao'] ?? '') === 'alternar_ativo') {
        $stmt = $pdo->prepare('UPDATE utilizadores SET ativo = NOT ativo WHERE id = ? AND tipo = "cliente"');
        $stmt->execute([(int) $_POST['id']]);
        $mensagem = 'Estado do cliente atualizado.';
    }
}

$clientes = $pdo->query(
    "SELECT u.*, COUNT(r.id) AS total_reservas, COALESCE(SUM(r.preco_total), 0) AS total_gasto
     FROM utilizadores u
     LEFT JOIN reservas r ON r.utilizador_id = u.id
     WHERE u.tipo = 'cliente'
     GROUP BY u.id
     ORDER BY u.criado_em DESC"
)->fetchAll();

$tituloPagina = 'Clientes — Painel Admin';
$paginaAtivaAdmin = 'clientes';
require_once __DIR__ . '/../includes/admin-header.php';
?>

<?php if ($mensagem): ?><div class="alerta alerta--sucesso"><i class="fa-solid fa-circle-check"></i> <?= e($mensagem) ?></div><?php endif; ?>

<div class="admin-painel">
    <div class="tabela-topo">
        <h3 class="admin-painel__titulo">Clientes (<?= count($clientes) ?>)</h3>
        <input type="text" class="input tabela-topo__pesquisa" placeholder="Pesquisar..." data-pesquisa-tabela="tabelaClientes">
    </div>
    <div class="tabela-wrapper">
        <table class="tabela" id="tabelaClientes">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Reservas</th>
                    <th>Total Gasto</th>
                    <th>Estado</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td><?= e($cliente['nome'] . ' ' . $cliente['apelido']) ?></td>
                        <td><?= e($cliente['email']) ?></td>
                        <td><?= e($cliente['telefone'] ?? '—') ?></td>
                        <td><?= (int) $cliente['total_reservas'] ?></td>
                        <td><?= formatarPreco($cliente['total_gasto']) ?></td>
                        <td><span class="badge <?= $cliente['ativo'] ? 'badge--disponivel' : 'badge--indisponivel' ?>"><?= $cliente['ativo'] ? 'Ativo' : 'Inativo' ?></span></td>
                        <td>
                            <form method="POST">
                                <?= campoCSRF() ?>
                                <input type="hidden" name="acao" value="alternar_ativo">
                                <input type="hidden" name="id" value="<?= e((string) $cliente['id']) ?>">
                                <button type="submit" class="btn btn--ghost btn--sm"><?= $cliente['ativo'] ? 'Desativar' : 'Ativar' ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$clientes): ?>
                    <tr><td colspan="7">Ainda não existem clientes registados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>
