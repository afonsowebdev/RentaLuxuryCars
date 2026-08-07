<?php
/**
 * admin/veiculos.php — CRUD de veículos
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
        $erro = 'Token de segurança inválido. Tente novamente.';
    } else {
        $acao = $_POST['acao'] ?? '';

        try {
            if ($acao === 'apagar') {
                $stmt = $pdo->prepare('DELETE FROM veiculos WHERE id = ?');
                $stmt->execute([(int) $_POST['id']]);
                $mensagem = 'Veículo removido com sucesso.';
            } else {
                $dados = [
                    'slug' => gerarSlug($_POST['marca'] . '-' . $_POST['modelo']),
                    'marca' => trim($_POST['marca']),
                    'modelo' => trim($_POST['modelo']),
                    'ano' => (int) $_POST['ano'],
                    'categoria' => $_POST['categoria'],
                    'preco_dia' => (float) $_POST['preco_dia'],
                    'preco_semana' => $_POST['preco_semana'] !== '' ? (float) $_POST['preco_semana'] : null,
                    'preco_fim_semana' => $_POST['preco_fim_semana'] !== '' ? (float) $_POST['preco_fim_semana'] : null,
                    'cavalos' => (int) $_POST['cavalos'],
                    'motor' => trim($_POST['motor']),
                    'transmissao' => $_POST['transmissao'],
                    'lugares' => (int) $_POST['lugares'],
                    'cor_exterior' => trim($_POST['cor_exterior']),
                    'cor_interior' => trim($_POST['cor_interior']),
                    'descricao' => trim($_POST['descricao']),
                    'imagem_principal' => trim($_POST['imagem_principal']) ?: null,
                    'imagens' => json_encode(array_values(array_filter(array_map('trim', explode(',', (string) $_POST['imagens'])))), JSON_UNESCAPED_UNICODE),
                    'disponivel' => isset($_POST['disponivel']) ? 1 : 0,
                    'destaque' => isset($_POST['destaque']) ? 1 : 0,
                ];

                if ($acao === 'criar') {
                    $colunas = array_keys($dados);
                    $placeholders = implode(', ', array_fill(0, count($colunas), '?'));
                    $stmt = $pdo->prepare('INSERT INTO veiculos (' . implode(', ', $colunas) . ') VALUES (' . $placeholders . ')');
                    $stmt->execute(array_values($dados));
                    $mensagem = 'Veículo criado com sucesso.';
                } elseif ($acao === 'atualizar') {
                    $id = (int) $_POST['id'];
                    $set = implode(', ', array_map(static fn ($col) => "{$col} = ?", array_keys($dados)));
                    $stmt = $pdo->prepare("UPDATE veiculos SET {$set} WHERE id = ?");
                    $stmt->execute([...array_values($dados), $id]);
                    $mensagem = 'Veículo atualizado com sucesso.';
                }
            }
        } catch (PDOException $e) {
            $erro = 'Erro ao guardar o veículo. Verifique os dados introduzidos.';
        }
    }
}

$veiculos = $pdo->query('SELECT * FROM veiculos ORDER BY criado_em DESC')->fetchAll();

$veiculoEditar = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare('SELECT * FROM veiculos WHERE id = ?');
    $stmt->execute([(int) $_GET['editar']]);
    $veiculoEditar = $stmt->fetch() ?: null;
}

$categorias = obterCategoriasVeiculos();

$tituloPagina = 'Veículos — Painel Admin';
$paginaAtivaAdmin = 'veiculos';
require_once __DIR__ . '/../includes/admin-header.php';
?>

<?php if ($mensagem): ?><div class="alerta alerta--sucesso"><i class="fa-solid fa-circle-check"></i> <?= e($mensagem) ?></div><?php endif; ?>
<?php if ($erro): ?><div class="alerta alerta--erro"><i class="fa-solid fa-circle-exclamation"></i> <?= e($erro) ?></div><?php endif; ?>

<div class="admin-painel">
    <div class="admin-painel__cabecalho">
        <h3 class="admin-painel__titulo"><?= $veiculoEditar ? 'Editar Veículo' : 'Adicionar Veículo' ?></h3>
        <?php if ($veiculoEditar): ?><a href="/admin/veiculos.php" class="btn btn--ghost btn--sm">Cancelar Edição</a><?php endif; ?>
    </div>

    <form method="POST" class="admin-form">
        <?= campoCSRF() ?>
        <input type="hidden" name="acao" value="<?= $veiculoEditar ? 'atualizar' : 'criar' ?>">
        <?php if ($veiculoEditar): ?><input type="hidden" name="id" value="<?= e((string) $veiculoEditar['id']) ?>"><?php endif; ?>

        <div class="admin-form__grid">
            <div class="campo">
                <label for="marca">Marca <span class="obrigatorio">*</span></label>
                <input type="text" id="marca" name="marca" value="<?= e($veiculoEditar['marca'] ?? '') ?>" required>
            </div>
            <div class="campo">
                <label for="modelo">Modelo <span class="obrigatorio">*</span></label>
                <input type="text" id="modelo" name="modelo" value="<?= e($veiculoEditar['modelo'] ?? '') ?>" required>
            </div>
            <div class="campo">
                <label for="ano">Ano <span class="obrigatorio">*</span></label>
                <input type="number" id="ano" name="ano" min="2000" max="2100" value="<?= e((string) ($veiculoEditar['ano'] ?? date('Y'))) ?>" required>
            </div>
            <div class="campo">
                <label for="categoria">Categoria <span class="obrigatorio">*</span></label>
                <select id="categoria" name="categoria" required>
                    <?php foreach ($categorias as $valor => $label): ?>
                        <option value="<?= e($valor) ?>" <?= ($veiculoEditar['categoria'] ?? '') === $valor ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label for="preco_dia">Preço/Dia (€) <span class="obrigatorio">*</span></label>
                <input type="number" step="0.01" id="preco_dia" name="preco_dia" value="<?= e((string) ($veiculoEditar['preco_dia'] ?? '')) ?>" required>
            </div>
            <div class="campo">
                <label for="preco_semana">Preço/Semana (€)</label>
                <input type="number" step="0.01" id="preco_semana" name="preco_semana" value="<?= e((string) ($veiculoEditar['preco_semana'] ?? '')) ?>">
            </div>
            <div class="campo">
                <label for="preco_fim_semana">Preço Fim de Semana (€)</label>
                <input type="number" step="0.01" id="preco_fim_semana" name="preco_fim_semana" value="<?= e((string) ($veiculoEditar['preco_fim_semana'] ?? '')) ?>">
            </div>
            <div class="campo">
                <label for="cavalos">Potência (cv)</label>
                <input type="number" id="cavalos" name="cavalos" value="<?= e((string) ($veiculoEditar['cavalos'] ?? '')) ?>">
            </div>
            <div class="campo">
                <label for="motor">Motor</label>
                <input type="text" id="motor" name="motor" value="<?= e($veiculoEditar['motor'] ?? '') ?>">
            </div>
            <div class="campo">
                <label for="transmissao">Transmissão</label>
                <select id="transmissao" name="transmissao">
                    <?php foreach (['manual', 'automatico', 'pdk', 'dct'] as $t): ?>
                        <option value="<?= e($t) ?>" <?= ($veiculoEditar['transmissao'] ?? '') === $t ? 'selected' : '' ?>><?= e(ucfirst($t)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label for="lugares">Lugares</label>
                <input type="number" id="lugares" name="lugares" value="<?= e((string) ($veiculoEditar['lugares'] ?? 2)) ?>">
            </div>
            <div class="campo">
                <label for="cor_exterior">Cor Exterior</label>
                <input type="text" id="cor_exterior" name="cor_exterior" value="<?= e($veiculoEditar['cor_exterior'] ?? '') ?>">
            </div>
            <div class="campo">
                <label for="cor_interior">Cor Interior</label>
                <input type="text" id="cor_interior" name="cor_interior" value="<?= e($veiculoEditar['cor_interior'] ?? '') ?>">
            </div>
        </div>

        <div class="campo">
            <label for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao"><?= e($veiculoEditar['descricao'] ?? '') ?></textarea>
        </div>

        <div class="campo-grupo">
            <div class="campo">
                <label for="imagem_principal">Imagem Principal (caminho)</label>
                <input type="text" id="imagem_principal" name="imagem_principal" placeholder="assets/images/cars/slug/1.svg" value="<?= e($veiculoEditar['imagem_principal'] ?? '') ?>">
            </div>
            <div class="campo">
                <label for="imagens">Galeria (caminhos separados por vírgula)</label>
                <input type="text" id="imagens" name="imagens" placeholder="assets/images/cars/slug/1.svg, assets/images/cars/slug/2.svg" value="<?= e(implode(', ', json_decode($veiculoEditar['imagens'] ?? '[]', true) ?: [])) ?>">
            </div>
        </div>

        <div class="campo-grupo">
            <label class="opcao"><input type="checkbox" name="disponivel" <?= ($veiculoEditar['disponivel'] ?? 1) ? 'checked' : '' ?>> Disponível para reserva</label>
            <label class="opcao"><input type="checkbox" name="destaque" <?= ($veiculoEditar['destaque'] ?? 0) ? 'checked' : '' ?>> Mostrar em destaque na página inicial</label>
        </div>

        <div class="admin-form__acoes">
            <button type="submit" class="btn btn--primario"><?= $veiculoEditar ? 'Guardar Alterações' : 'Adicionar Veículo' ?></button>
        </div>
    </form>
</div>

<div class="admin-painel">
    <div class="tabela-topo">
        <h3 class="admin-painel__titulo">Frota (<?= count($veiculos) ?>)</h3>
        <input type="text" class="input tabela-topo__pesquisa" placeholder="Pesquisar..." data-pesquisa-tabela="tabelaVeiculos">
    </div>
    <div class="tabela-wrapper">
        <table class="tabela" id="tabelaVeiculos">
            <thead>
                <tr>
                    <th>Imagem</th>
                    <th>Veículo</th>
                    <th>Categoria</th>
                    <th>Preço/Dia</th>
                    <th>Estado</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($veiculos as $veiculo): ?>
                    <tr>
                        <td><img class="tabela__imagem" src="/<?= e($veiculo['imagem_principal']) ?>" alt=""></td>
                        <td><?= e($veiculo['marca'] . ' ' . $veiculo['modelo']) ?></td>
                        <td><?= e(obterLabelCategoria($veiculo['categoria'])) ?></td>
                        <td><?= formatarPreco($veiculo['preco_dia']) ?></td>
                        <td><span class="badge <?= $veiculo['disponivel'] ? 'badge--disponivel' : 'badge--indisponivel' ?>"><?= $veiculo['disponivel'] ? 'Disponível' : 'Indisponível' ?></span></td>
                        <td>
                            <div class="tabela__acoes">
                                <a href="/admin/veiculos.php?editar=<?= e((string) $veiculo['id']) ?>" title="Editar"><i class="fa-solid fa-pen"></i></a>
                                <form method="POST" style="display:inline;">
                                    <?= campoCSRF() ?>
                                    <input type="hidden" name="acao" value="apagar">
                                    <input type="hidden" name="id" value="<?= e((string) $veiculo['id']) ?>">
                                    <button type="submit" class="perigo" title="Apagar" data-confirmar="Tem a certeza que deseja apagar este veículo?"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>
