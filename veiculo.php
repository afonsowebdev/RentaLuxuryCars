<?php
/**
 * veiculo.php — Página individual de um veículo
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';

$pdo = obterLigacaoBD();
$slug = $_GET['slug'] ?? '';

$stmt = $pdo->prepare('SELECT * FROM veiculos WHERE slug = ? LIMIT 1');
$stmt->execute([$slug]);
$veiculo = $stmt->fetch();

if (!$veiculo) {
    http_response_code(404);
    $tituloPagina = 'Veículo não encontrado — RentaLuxuryCars';
    require_once __DIR__ . '/includes/header.php';
    echo '<main class="pagina-veiculo secao__container secao"><h1>Veículo não encontrado</h1><p>O veículo que procura não existe ou já não está disponível.</p><a href="/frota.php" class="btn btn--primario mt-lg">Voltar à Frota</a></main>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$imagens = json_decode($veiculo['imagens'] ?? '[]', true) ?: [$veiculo['imagem_principal']];
$caracteristicas = json_decode($veiculo['caracteristicas'] ?? '[]', true) ?: [];

$stmtRelacionados = $pdo->prepare('SELECT * FROM veiculos WHERE categoria = ? AND id != ? AND disponivel = 1 LIMIT 3');
$stmtRelacionados->execute([$veiculo['categoria'], $veiculo['id']]);
$relacionados = $stmtRelacionados->fetchAll();

$extras = $pdo->query('SELECT * FROM extras WHERE ativo = 1')->fetchAll();

$tituloPagina = $veiculo['marca'] . ' ' . $veiculo['modelo'] . ' — RentaLuxuryCars';
$descricaoPagina = mb_substr((string) $veiculo['descricao'], 0, 160);
require_once __DIR__ . '/includes/header.php';
?>

<main class="pagina-veiculo">
    <div class="secao__container">
        <div class="veiculo-cabecalho">
            <span class="veiculo-cabecalho__marca"><?= e($veiculo['marca']) ?></span>
            <h1 class="veiculo-cabecalho__nome"><?= e($veiculo['modelo']) ?> <span style="color:var(--cor-cinza-claro);font-size:0.6em;">(<?= e((string) $veiculo['ano']) ?>)</span></h1>
            <div class="veiculo-cabecalho__badges">
                <span class="badge badge--categoria"><?= e(obterLabelCategoria($veiculo['categoria'])) ?></span>
                <span class="badge <?= $veiculo['disponivel'] ? 'badge--disponivel' : 'badge--indisponivel' ?>"><?= $veiculo['disponivel'] ? 'Disponível' : 'Indisponível' ?></span>
            </div>
        </div>

        <div class="veiculo-layout">
            <div class="veiculo-galeria-coluna">
                <div class="swiper galeria-veiculo__principal">
                    <div class="swiper-wrapper">
                        <?php foreach ($imagens as $img): ?>
                            <div class="swiper-slide"><img src="/<?= e($img) ?>" alt="<?= e($veiculo['marca'] . ' ' . $veiculo['modelo']) ?>"></div>
                        <?php endforeach; ?>
                    </div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
                <div class="swiper galeria-veiculo__thumbs">
                    <div class="swiper-wrapper">
                        <?php foreach ($imagens as $img): ?>
                            <div class="swiper-slide"><img src="/<?= e($img) ?>" alt="Miniatura"></div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <p class="mt-xl"><?= nl2br(e($veiculo['descricao'])) ?></p>

                <div class="ficha-tecnica">
                    <h3>Ficha Técnica</h3>
                    <div class="ficha-tecnica__grid">
                        <div class="ficha-tecnica__item">
                            <i class="fa-solid fa-gauge-high"></i>
                            <span class="ficha-tecnica__valor"><?= e((string) $veiculo['cavalos']) ?> cv</span>
                            <span class="ficha-tecnica__label">Potência</span>
                        </div>
                        <div class="ficha-tecnica__item">
                            <i class="fa-solid fa-car-burst"></i>
                            <span class="ficha-tecnica__valor"><?= e((string) $veiculo['aceleracao']) ?></span>
                            <span class="ficha-tecnica__label">0-100 km/h</span>
                        </div>
                        <div class="ficha-tecnica__item">
                            <i class="fa-solid fa-tachometer-alt"></i>
                            <span class="ficha-tecnica__valor"><?= e((string) $veiculo['velocidade_maxima']) ?> km/h</span>
                            <span class="ficha-tecnica__label">Vel. Máxima</span>
                        </div>
                        <div class="ficha-tecnica__item">
                            <i class="fa-solid fa-gears"></i>
                            <span class="ficha-tecnica__valor"><?= e(ucfirst($veiculo['transmissao'])) ?></span>
                            <span class="ficha-tecnica__label">Transmissão</span>
                        </div>
                        <div class="ficha-tecnica__item">
                            <i class="fa-solid fa-users"></i>
                            <span class="ficha-tecnica__valor"><?= e((string) $veiculo['lugares']) ?></span>
                            <span class="ficha-tecnica__label">Lugares</span>
                        </div>
                        <div class="ficha-tecnica__item">
                            <i class="fa-solid fa-oil-can"></i>
                            <span class="ficha-tecnica__valor"><?= e((string) $veiculo['motor']) ?></span>
                            <span class="ficha-tecnica__label">Motor</span>
                        </div>
                    </div>

                    <h3 class="mt-xl">Equipamento e Características</h3>
                    <ul class="caracteristicas-lista">
                        <?php foreach ($caracteristicas as $carac): ?>
                            <li><i class="fa-solid fa-circle-check"></i> <?= e($carac) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <aside class="widget-reserva">
                <div class="widget-reserva__preco">
                    <span class="widget-reserva__preco-valor"><?= formatarPreco($veiculo['preco_dia']) ?></span>
                    <span class="widget-reserva__preco-label">/ dia</span>
                </div>

                <form id="formWidgetReserva" action="/reserva.php" method="GET" data-preco-dia="<?= e((string) $veiculo['preco_dia']) ?>" data-veiculo-id="<?= e((string) $veiculo['id']) ?>">
                    <input type="hidden" name="veiculo" value="<?= e($veiculo['slug']) ?>">

                    <div class="campo">
                        <label for="widgetInicio">Data de Início <span class="obrigatorio">*</span></label>
                        <input type="date" id="widgetInicio" name="inicio" min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="campo">
                        <label for="widgetFim">Data de Fim <span class="obrigatorio">*</span></label>
                        <input type="date" id="widgetFim" name="fim" min="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div id="avisoDisponibilidadeVeiculo"></div>

                    <div class="extras-lista">
                        <?php foreach ($extras as $extra): ?>
                            <div class="extras-lista__item">
                                <label>
                                    <input type="checkbox" name="extras[]" value="<?= e($extra['slug']) ?>" data-preco="<?= e((string) $extra['preco_dia']) ?>">
                                    <span><i class="<?= e($extra['icone']) ?>"></i> <?= e($extra['nome']) ?><br><small><?= e($extra['descricao']) ?></small></span>
                                </label>
                                <span class="preco">+<?= formatarPreco($extra['preco_dia']) ?>/dia</span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="widget-reserva__resumo">
                        <div><span>Dias</span><span id="widgetDias">0</span></div>
                        <div><span>Base</span><span id="widgetBase">0,00 €</span></div>
                        <div><span>Extras</span><span id="widgetExtras">0,00 €</span></div>
                        <div class="widget-reserva__resumo-total"><span>Total</span><span id="widgetTotal">0,00 €</span></div>
                    </div>

                    <button type="submit" class="btn btn--primario btn--full mt-lg">Reservar Agora</button>
                </form>
                <p class="widget-reserva__disponibilidade">Sem taxas escondidas — cancelamento gratuito até 48h antes.</p>
            </aside>
        </div>

        <?php if ($relacionados): ?>
        <div class="veiculos-relacionados">
            <div class="secao__cabecalho">
                <h2 class="titulo-secao">Veículos <span>Relacionados</span></h2>
            </div>
            <div class="grid">
                <?php foreach ($relacionados as $veiculo): ?>
                    <?php require __DIR__ . '/includes/partials/card-veiculo.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<script>
(function () {
    const form = document.getElementById('formWidgetReserva');
    if (!form) return;
    const precoDia = Number(form.dataset.precoDia || 0);
    const veiculoId = form.dataset.veiculoId;

    function calcular() {
        const inicio = form.inicio.value;
        const fim = form.fim.value;
        let dias = 0;
        if (inicio && fim) {
            const d = Math.round((new Date(fim) - new Date(inicio)) / 86400000);
            dias = d > 0 ? d : 0;
        }
        const base = dias * precoDia;
        let extras = 0;
        form.querySelectorAll('[name="extras[]"]:checked').forEach((el) => { extras += dias * Number(el.dataset.preco || 0); });
        const fmt = (v) => v.toLocaleString('pt-PT', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
        document.getElementById('widgetDias').textContent = dias;
        document.getElementById('widgetBase').textContent = fmt(base);
        document.getElementById('widgetExtras').textContent = fmt(extras);
        document.getElementById('widgetTotal').textContent = fmt(base + extras);
    }

    function verificarDisponibilidade() {
        const aviso = document.getElementById('avisoDisponibilidadeVeiculo');
        if (!form.inicio.value || !form.fim.value) { aviso.innerHTML = ''; return; }
        const params = new URLSearchParams({ veiculo_id: veiculoId, inicio: form.inicio.value, fim: form.fim.value });
        fetch('/api/disponibilidade.php?' + params.toString())
            .then((r) => r.json())
            .then((d) => {
                aviso.innerHTML = d.disponivel
                    ? '<span class="badge badge--disponivel">Disponível</span>'
                    : '<span class="badge badge--indisponivel">Indisponível nestas datas</span>';
            })
            .catch(() => {});
    }

    form.addEventListener('change', () => { calcular(); verificarDisponibilidade(); });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
