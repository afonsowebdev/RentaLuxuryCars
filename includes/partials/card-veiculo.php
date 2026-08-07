<?php
/**
 * Partial: cartão de veículo.
 * Espera a variável $veiculo (array de uma linha da tabela `veiculos`).
 */

declare(strict_types=1);
?>
<article class="card-veiculo"
         data-veiculo
         data-categoria="<?= e($veiculo['categoria']) ?>"
         data-marca="<?= e($veiculo['marca']) ?>"
         data-nome="<?= e($veiculo['marca'] . ' ' . $veiculo['modelo']) ?>"
         data-preco="<?= e((string) $veiculo['preco_dia']) ?>"
         data-lugares="<?= e((string) $veiculo['lugares']) ?>"
         data-destaque="<?= e((string) $veiculo['destaque']) ?>">
    <a href="/veiculo.php?slug=<?= urlencode($veiculo['slug']) ?>" class="card-veiculo__imagem">
        <img src="/<?= e($veiculo['imagem_principal']) ?>" alt="<?= e($veiculo['marca'] . ' ' . $veiculo['modelo']) ?>" loading="lazy">
        <div class="card-veiculo__overlay"></div>
        <span class="badge badge--categoria card-veiculo__badge"><?= e(obterLabelCategoria($veiculo['categoria'])) ?></span>
    </a>
    <div class="card-veiculo__corpo">
        <div class="card-veiculo__marca"><?= e($veiculo['marca']) ?></div>
        <h3 class="card-veiculo__nome"><a href="/veiculo.php?slug=<?= urlencode($veiculo['slug']) ?>"><?= e($veiculo['modelo']) ?></a></h3>
        <div class="card-veiculo__specs">
            <span><i class="fa-solid fa-gauge-high"></i> <?= e((string) $veiculo['cavalos']) ?> cv</span>
            <span><i class="fa-solid fa-users"></i> <?= e((string) $veiculo['lugares']) ?> lug.</span>
            <span><i class="fa-solid fa-gear"></i> <?= e(ucfirst($veiculo['transmissao'])) ?></span>
        </div>
        <div class="card-veiculo__rodape">
            <div class="card-veiculo__preco">
                <div class="card-veiculo__preco-valor"><?= formatarPreco($veiculo['preco_dia']) ?></div>
                <div class="card-veiculo__preco-label">por dia</div>
            </div>
            <a href="/veiculo.php?slug=<?= urlencode($veiculo['slug']) ?>" class="btn btn--secundario btn--sm">Ver Detalhes</a>
        </div>
    </div>
</article>
