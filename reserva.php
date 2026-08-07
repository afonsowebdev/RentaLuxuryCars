<?php
/**
 * reserva.php — Formulário de reserva multi-step
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';

$pdo = obterLigacaoBD();
$slug = $_GET['veiculo'] ?? '';

$stmt = $pdo->prepare('SELECT * FROM veiculos WHERE slug = ? AND disponivel = 1 LIMIT 1');
$stmt->execute([$slug]);
$veiculo = $stmt->fetch();

if (!$veiculo) {
    redirecionar('/frota.php');
}

$extras = $pdo->query('SELECT * FROM extras WHERE ativo = 1')->fetchAll();
$extrasSelecionados = array_map('strval', $_GET['extras'] ?? []);
$dataInicio = $_GET['inicio'] ?? '';
$dataFim = $_GET['fim'] ?? '';

$utilizadorSessao = estaAutenticado() ? utilizadorAtual() : null;

$tituloPagina = 'Reservar ' . $veiculo['marca'] . ' ' . $veiculo['modelo'] . ' — RentaLuxuryCars';
require_once __DIR__ . '/includes/header.php';
?>

<main class="pagina-reserva">
    <div class="secao__container">
        <div class="secao__cabecalho">
            <p class="eyebrow">Reserva</p>
            <h1 class="titulo-secao">Finalize a Sua <span>Reserva</span></h1>
        </div>

        <div class="progresso-passos">
            <div class="progresso-passos__passo ativo">
                <span class="progresso-passos__numero">1</span>
                <span class="progresso-passos__label">Veículo &amp; Datas</span>
            </div>
            <div class="progresso-passos__passo">
                <span class="progresso-passos__numero">2</span>
                <span class="progresso-passos__label">Dados Pessoais</span>
            </div>
            <div class="progresso-passos__passo">
                <span class="progresso-passos__numero">3</span>
                <span class="progresso-passos__label">Confirmação</span>
            </div>
        </div>

        <form id="reservaForm" data-preco-dia="<?= e((string) $veiculo['preco_dia']) ?>" data-veiculo-id="<?= e((string) $veiculo['id']) ?>">
            <?= campoCSRF() ?>
            <input type="hidden" name="veiculo_id" value="<?= e((string) $veiculo['id']) ?>">

            <!-- PASSO 1 -->
            <div class="reserva-passo ativo">
                <div class="reserva-resumo-veiculo">
                    <img src="/<?= e($veiculo['imagem_principal']) ?>" alt="<?= e($veiculo['marca'] . ' ' . $veiculo['modelo']) ?>">
                    <div>
                        <h4><?= e($veiculo['marca'] . ' ' . $veiculo['modelo']) ?></h4>
                        <span><?= formatarPreco($veiculo['preco_dia']) ?> / dia</span>
                    </div>
                </div>

                <div class="campo-grupo">
                    <div class="campo">
                        <label for="data_inicio">Data de Início <span class="obrigatorio">*</span></label>
                        <input type="date" id="data_inicio" name="data_inicio" value="<?= e($dataInicio) ?>" min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="campo">
                        <label for="data_fim">Data de Fim <span class="obrigatorio">*</span></label>
                        <input type="date" id="data_fim" name="data_fim" value="<?= e($dataFim) ?>" min="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>

                <div class="campo-grupo">
                    <div class="campo">
                        <label for="local_entrega">Local de Entrega <span class="obrigatorio">*</span></label>
                        <select id="local_entrega" name="local_entrega" required>
                            <option value="Lisboa">Lisboa</option>
                            <option value="Porto">Porto</option>
                            <option value="Faro">Faro</option>
                            <option value="Aeroporto de Lisboa">Aeroporto de Lisboa</option>
                        </select>
                    </div>
                    <div class="campo">
                        <label for="local_devolucao">Local de Devolução <span class="obrigatorio">*</span></label>
                        <select id="local_devolucao" name="local_devolucao" required>
                            <option value="Lisboa">Lisboa</option>
                            <option value="Porto">Porto</option>
                            <option value="Faro">Faro</option>
                            <option value="Aeroporto de Lisboa">Aeroporto de Lisboa</option>
                        </select>
                    </div>
                </div>

                <span id="avisoDisponibilidade"></span>

                <h4 class="mt-lg">Extras Opcionais</h4>
                <div class="extras-lista">
                    <?php foreach ($extras as $extra): ?>
                        <div class="extras-lista__item">
                            <label>
                                <input type="checkbox" name="extras[]" value="<?= e($extra['slug']) ?>" data-preco="<?= e((string) $extra['preco_dia']) ?>" <?= in_array($extra['slug'], $extrasSelecionados, true) ? 'checked' : '' ?>>
                                <span><i class="<?= e($extra['icone']) ?>"></i> <?= e($extra['nome']) ?><br><small><?= e($extra['descricao']) ?></small></span>
                            </label>
                            <span class="preco">+<?= formatarPreco($extra['preco_dia']) ?>/dia</span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="reserva-total-flutuante">
                    <span>Total Estimado (<span id="resumoDias">0 dias</span>)</span>
                    <strong id="resumoTotal">0,00 €</strong>
                </div>

                <div class="reserva-navegacao">
                    <span></span>
                    <button type="button" class="btn btn--primario" data-reserva-avancar>Continuar <i class="fa-solid fa-arrow-right btn__icon"></i></button>
                </div>
            </div>

            <!-- PASSO 2 -->
            <div class="reserva-passo">
                <div class="campo-grupo">
                    <div class="campo">
                        <label for="cliente_nome">Nome <span class="obrigatorio">*</span></label>
                        <input type="text" id="cliente_nome" name="cliente_nome" value="<?= e($utilizadorSessao['nome'] ?? '') ?>" required>
                    </div>
                    <div class="campo">
                        <label for="cliente_apelido">Apelido <span class="obrigatorio">*</span></label>
                        <input type="text" id="cliente_apelido" name="cliente_apelido" value="<?= e($utilizadorSessao['apelido'] ?? '') ?>" required>
                    </div>
                </div>
                <div class="campo-grupo">
                    <div class="campo">
                        <label for="cliente_email">Email <span class="obrigatorio">*</span></label>
                        <input type="email" id="cliente_email" name="cliente_email" value="<?= e($utilizadorSessao['email'] ?? '') ?>" required>
                    </div>
                    <div class="campo">
                        <label for="cliente_telefone">Telefone <span class="obrigatorio">*</span></label>
                        <input type="tel" id="cliente_telefone" name="cliente_telefone" value="<?= e($utilizadorSessao['telefone'] ?? '') ?>" required>
                    </div>
                </div>
                <div class="campo-grupo">
                    <div class="campo">
                        <label for="cliente_nascimento">Data de Nascimento <span class="obrigatorio">*</span></label>
                        <input type="date" id="cliente_nascimento" name="cliente_nascimento" required>
                    </div>
                    <div class="campo">
                        <label for="cliente_nif">NIF</label>
                        <input type="text" id="cliente_nif" name="cliente_nif">
                    </div>
                </div>
                <div class="campo">
                    <label for="cliente_carta_conducao">Nº Carta de Condução <span class="obrigatorio">*</span></label>
                    <input type="text" id="cliente_carta_conducao" name="cliente_carta_conducao" required>
                </div>

                <div class="reserva-navegacao">
                    <button type="button" class="btn btn--ghost" data-reserva-voltar><i class="fa-solid fa-arrow-left btn__icon"></i> Voltar</button>
                    <button type="button" class="btn btn--primario" data-reserva-avancar>Continuar <i class="fa-solid fa-arrow-right btn__icon"></i></button>
                </div>
            </div>

            <!-- PASSO 3 -->
            <div class="reserva-passo">
                <h4>Resumo da Reserva</h4>
                <div class="reserva-resumo-veiculo">
                    <div>
                        <p><strong>Veículo:</strong> <?= e($veiculo['marca'] . ' ' . $veiculo['modelo']) ?></p>
                        <p><strong>Cliente:</strong> <span data-resumo="cliente_nome"></span> <span data-resumo="cliente_apelido"></span></p>
                        <p><strong>Email:</strong> <span data-resumo="cliente_email"></span></p>
                        <p><strong>Telefone:</strong> <span data-resumo="cliente_telefone"></span></p>
                        <p><strong>Período:</strong> <span data-resumo="data_inicio"></span> a <span data-resumo="data_fim"></span></p>
                        <p><strong>Entrega:</strong> <span data-resumo="local_entrega"></span> &rarr; <strong>Devolução:</strong> <span data-resumo="local_devolucao"></span></p>
                    </div>
                </div>

                <div class="campo">
                    <label for="metodo_pagamento">Método de Pagamento <span class="obrigatorio">*</span></label>
                    <select id="metodo_pagamento" name="metodo_pagamento" required>
                        <option value="transferencia">Transferência Bancária</option>
                        <option value="multibanco">Multibanco</option>
                        <option value="cartao">Cartão de Crédito</option>
                        <option value="presencial">Pagamento Presencial</option>
                    </select>
                </div>

                <div class="campo">
                    <label for="notas">Notas Adicionais</label>
                    <textarea id="notas" name="notas" placeholder="Pedidos especiais, horário de chegada, etc."></textarea>
                </div>

                <label class="opcao mb-lg">
                    <input type="checkbox" required>
                    Aceito os <a href="#" class="texto-ouro">Termos e Condições</a> de aluguer <span class="obrigatorio">*</span>
                </label>

                <div class="reserva-total-flutuante">
                    <span>Base: <span id="resumoBase">0,00 €</span> + Extras: <span id="resumoExtras">0,00 €</span></span>
                    <strong>Total: <span id="resumoTotal2">0,00 €</span></strong>
                </div>

                <div class="reserva-navegacao">
                    <button type="button" class="btn btn--ghost" data-reserva-voltar><i class="fa-solid fa-arrow-left btn__icon"></i> Voltar</button>
                    <button type="submit" class="btn btn--primario btn--lg">Confirmar Reserva</button>
                </div>
            </div>
        </form>
    </div>
</main>

<?php
$scriptsAdicionais = ['/assets/js/reserva.js'];
require_once __DIR__ . '/includes/footer.php';
?>
