<?php
/**
 * contacto.php — Formulário de contacto
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';

$tituloPagina = 'Contacto — RentaLuxuryCars';
$descricaoPagina = 'Entre em contacto com a RentaLuxuryCars para reservas, dúvidas ou pedidos especiais.';
require_once __DIR__ . '/includes/header.php';
?>

<main class="pagina-contacto">
    <div class="secao__container">
        <div class="secao__cabecalho">
            <p class="eyebrow">Fale Connosco</p>
            <h1 class="titulo-secao">Entre em <span>Contacto</span></h1>
            <p class="subtitulo-secao">A nossa equipa está disponível para o ajudar a planear a experiência perfeita.</p>
        </div>

        <div class="contacto-layout">
            <div class="contacto-info">
                <div class="contacto-info__item">
                    <i class="fa-solid fa-location-dot"></i>
                    <div><h4>Morada</h4><p>Av. da Liberdade 110, 1250-146 Lisboa, Portugal</p></div>
                </div>
                <div class="contacto-info__item">
                    <i class="fa-solid fa-phone"></i>
                    <div><h4>Telefone</h4><p>+351 210 000 000</p></div>
                </div>
                <div class="contacto-info__item">
                    <i class="fa-solid fa-envelope"></i>
                    <div><h4>Email</h4><p>reservas@luxdrive.pt</p></div>
                </div>
                <div class="contacto-info__item">
                    <i class="fa-solid fa-clock"></i>
                    <div><h4>Horário</h4><p>Todos os dias, 08h00 — 22h00</p></div>
                </div>

                <div class="contacto-info__mapa">
                    <iframe src="https://www.openstreetmap.org/export/embed.html?bbox=-9.155%2C38.715%2C-9.140%2C38.725&layer=mapnik" loading="lazy" title="Mapa da localização"></iframe>
                </div>
            </div>

            <div class="contacto-form-wrapper">
                <div id="contactoAlerta"></div>
                <form id="formContacto">
                    <?= campoCSRF() ?>
                    <div class="campo-grupo">
                        <div class="campo">
                            <label for="nome">Nome <span class="obrigatorio">*</span></label>
                            <input type="text" id="nome" name="nome" required>
                        </div>
                        <div class="campo">
                            <label for="email">Email <span class="obrigatorio">*</span></label>
                            <input type="email" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="campo-grupo">
                        <div class="campo">
                            <label for="telefone">Telefone</label>
                            <input type="tel" id="telefone" name="telefone">
                        </div>
                        <div class="campo">
                            <label for="assunto">Assunto</label>
                            <select id="assunto" name="assunto">
                                <option value="Reserva">Reserva</option>
                                <option value="Informacoes">Informações Gerais</option>
                                <option value="Parceria">Parceria</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                    </div>
                    <div class="campo">
                        <label for="mensagem">Mensagem <span class="obrigatorio">*</span></label>
                        <textarea id="mensagem" name="mensagem" required></textarea>
                    </div>
                    <button type="submit" class="btn btn--primario btn--full">Enviar Mensagem</button>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
document.getElementById('formContacto').addEventListener('submit', function (evento) {
    evento.preventDefault();
    const form = evento.target;
    const alertaEl = document.getElementById('contactoAlerta');
    const botao = form.querySelector('[type="submit"]');
    botao.disabled = true;

    fetch('/api/contacto.php', { method: 'POST', body: new FormData(form) })
        .then((r) => r.json())
        .then((dados) => {
            if (dados.sucesso) {
                alertaEl.innerHTML = '<div class="alerta alerta--sucesso"><i class="fa-solid fa-circle-check"></i> Mensagem enviada com sucesso. Entraremos em contacto brevemente.</div>';
                form.reset();
            } else {
                alertaEl.innerHTML = '<div class="alerta alerta--erro"><i class="fa-solid fa-circle-exclamation"></i> ' + (dados.erro || 'Ocorreu um erro. Tente novamente.') + '</div>';
            }
        })
        .catch(() => {
            alertaEl.innerHTML = '<div class="alerta alerta--erro">Erro de ligação. Tente novamente.</div>';
        })
        .finally(() => { botao.disabled = false; });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
