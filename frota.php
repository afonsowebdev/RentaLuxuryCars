<?php
/**
 * frota.php — Catálogo de veículos com filtros client-side
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';

$pdo = obterLigacaoBD();
$veiculos = $pdo->query('SELECT * FROM veiculos WHERE disponivel = 1 ORDER BY destaque DESC, marca ASC')->fetchAll();

$marcas = array_values(array_unique(array_column($veiculos, 'marca')));
sort($marcas);
$categorias = obterCategoriasVeiculos();
$precoMaximo = $veiculos ? (int) ceil(max(array_column($veiculos, 'preco_dia')) / 100) * 100 : 1500;

$categoriaSelecionada = $_GET['categoria'] ?? '';

$tituloPagina = 'Frota Completa — RentaLuxuryCars';
require_once __DIR__ . '/includes/header.php';
?>

<main class="pagina-frota">
    <div class="secao__container">
        <div class="secao__cabecalho">
            <p class="eyebrow">Catálogo Completo</p>
            <h1 class="titulo-secao">A Nossa <span>Frota</span></h1>
            <p class="subtitulo-secao">Supercarros, Gran Turismos, berlinas e SUV de luxo — encontre o automóvel perfeito para a sua ocasião.</p>
        </div>

        <div class="frota-layout">
            <aside class="filtros">
                <div class="filtros__titulo">
                    Filtros
                    <button type="button" class="filtros__limpar" id="frotaLimparFiltros">Limpar Tudo</button>
                </div>

                <div class="filtros__grupo">
                    <div class="filtros__grupo-titulo">Categoria</div>
                    <div class="filtros__opcoes">
                        <?php foreach ($categorias as $valor => $label): ?>
                            <label class="opcao">
                                <input type="checkbox" data-filtro-categoria value="<?= e($valor) ?>" <?= $categoriaSelecionada === $valor ? 'checked' : '' ?>>
                                <?= e($label) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filtros__grupo">
                    <div class="filtros__grupo-titulo">Marca</div>
                    <div class="filtros__opcoes">
                        <?php foreach ($marcas as $marca): ?>
                            <label class="opcao">
                                <input type="checkbox" data-filtro-marca value="<?= e($marca) ?>">
                                <?= e($marca) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filtros__grupo">
                    <div class="filtros__grupo-titulo">Preço Máximo / Dia</div>
                    <input type="range" data-filtro-preco min="100" max="<?= e((string) $precoMaximo) ?>" step="50" value="<?= e((string) $precoMaximo) ?>" class="input">
                    <span id="frotaPrecoLabel"><?= e((string) $precoMaximo) ?> €</span>
                </div>

                <div class="filtros__grupo">
                    <div class="filtros__grupo-titulo">Lugares (mínimo)</div>
                    <div class="filtros__opcoes">
                        <label class="opcao"><input type="radio" name="lugares" data-filtro-lugares value="2"> 2+</label>
                        <label class="opcao"><input type="radio" name="lugares" data-filtro-lugares value="4"> 4+</label>
                        <label class="opcao"><input type="radio" name="lugares" data-filtro-lugares value="5"> 5+</label>
                    </div>
                </div>
            </aside>

            <div class="frota-resultados">
                <div class="frota-resultados__topo">
                    <span class="frota-resultados__contagem" id="frotaContagem"><?= count($veiculos) ?> veículos encontrados</span>
                    <select id="frotaOrdenar" class="input" style="width:auto;">
                        <option value="destaque">Em Destaque</option>
                        <option value="preco-asc">Preço: Mais Baixo</option>
                        <option value="preco-desc">Preço: Mais Alto</option>
                        <option value="nome">Nome (A-Z)</option>
                    </select>
                </div>

                <div class="grid" id="gridFrota">
                    <?php foreach ($veiculos as $veiculo): ?>
                        <?php require __DIR__ . '/includes/partials/card-veiculo.php'; ?>
                    <?php endforeach; ?>
                </div>

                <div class="frota-resultados__vazio" id="frotaVazio" style="display:none;">
                    <i class="fa-solid fa-car-side" style="font-size:2.5rem;color:var(--cor-ouro);margin-bottom:1rem;"></i>
                    <p>Nenhum veículo corresponde aos filtros selecionados.</p>
                </div>

                <div class="paginacao" id="frotaPaginacao"></div>
            </div>
        </div>
    </div>
</main>

<?php
$scriptsAdicionais = ['/assets/js/frota.js'];
require_once __DIR__ . '/includes/footer.php';
?>
