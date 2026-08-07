<?php
/**
 * confirmacao.php — Página de sucesso após reserva
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';

$pdo = obterLigacaoBD();
$referencia = $_GET['ref'] ?? '';

$stmt = $pdo->prepare(
    'SELECT r.*, v.marca, v.modelo, v.imagem_principal
     FROM reservas r
     JOIN veiculos v ON v.id = r.veiculo_id
     WHERE r.referencia = ? LIMIT 1'
);
$stmt->execute([$referencia]);
$reserva = $stmt->fetch();

if (!$reserva) {
    redirecionar('/frota.php');
}

$tituloPagina = 'Reserva Confirmada — RentaLuxuryCars';
require_once __DIR__ . '/includes/header.php';
?>

<main class="pagina-confirmacao">
    <div class="secao__container">
        <div class="confirmacao-card">
            <div class="confirmacao-check">
                <svg viewBox="0 0 52 52">
                    <circle cx="26" cy="26" r="24"></circle>
                    <path d="M14 27l7 7 17-17"></path>
                </svg>
            </div>

            <h1>Reserva Confirmada!</h1>
            <p class="mt-md">Obrigado, <?= e($reserva['cliente_nome']) ?>. A sua reserva do <?= e($reserva['marca'] . ' ' . $reserva['modelo']) ?> foi registada com sucesso.</p>

            <div class="confirmacao-referencia"><?= e($reserva['referencia']) ?></div>

            <div class="confirmacao-instrucoes">
                <h4>Detalhes da Reserva</h4>
                <p><strong>Veículo:</strong> <?= e($reserva['marca'] . ' ' . $reserva['modelo']) ?></p>
                <p><strong>Período:</strong> <?= formatarData($reserva['data_inicio']) ?> a <?= formatarData($reserva['data_fim']) ?> (<?= (int) $reserva['dias'] ?> dias)</p>
                <p><strong>Local de Entrega:</strong> <?= e($reserva['local_entrega']) ?></p>
                <p><strong>Total:</strong> <?= formatarPreco($reserva['preco_total']) ?></p>

                <h4 class="mt-lg">Instruções de Pagamento</h4>
                <?php switch ($reserva['metodo_pagamento']):
                    case 'transferencia': ?>
                        <p>Realize a transferência bancária para o IBAN <strong>PT50 0000 0000 0000 0000 0000 0</strong>, indicando a referência <strong><?= e($reserva['referencia']) ?></strong> no descritivo. A confirmação final ocorre após receção do valor.</p>
                        <?php break;
                    case 'multibanco': ?>
                        <p>Referência Multibanco: <strong>Entidade 12345 / Referência 123 456 789</strong>. Efetue o pagamento em qualquer terminal ou homebanking até 48h antes da data de início.</p>
                        <?php break;
                    case 'cartao': ?>
                        <p>Em breve entraremos em contacto para processar o pagamento com cartão de crédito de forma segura.</p>
                        <?php break;
                    default: ?>
                        <p>O pagamento será processado presencialmente no momento da entrega do veículo.</p>
                <?php endswitch; ?>

                <ul>
                    <li>Receberá um email de confirmação em <?= e($reserva['cliente_email']) ?>.</li>
                    <li>Traga consigo um documento de identificação e a carta de condução válida.</li>
                    <li>Para qualquer questão, contacte-nos através de reservas@luxdrive.pt.</li>
                </ul>
            </div>

            <div class="confirmacao-acoes">
                <a href="/frota.php" class="btn btn--secundario">Ver Mais Veículos</a>
                <a href="/index.php" class="btn btn--primario">Voltar ao Início</a>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
