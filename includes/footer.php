<?php
/**
 * Rodapé HTML comum a todas as páginas públicas.
 * Espera (opcionalmente) um array $scriptsAdicionais com caminhos de JS extra.
 */

declare(strict_types=1);

$scriptsAdicionais ??= [];
?>
    <footer class="footer">
        <div class="footer__container">
            <div class="footer__coluna footer__coluna--marca">
                <span class="footer__logo">Renta<span class="footer__logo-acento">Luxury</span>Cars</span>
                <p class="footer__descricao">Aluguer de automóveis de luxo e supercarros em Portugal. Experiência, exclusividade e confiança para cada viagem.</p>
                <div class="footer__social">
                    <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin"></i></a>
                    <a href="#" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>

            <div class="footer__coluna">
                <h4>Navegação</h4>
                <ul>
                    <li><a href="/index.php">Início</a></li>
                    <li><a href="/frota.php">Frota</a></li>
                    <li><a href="/sobre.php">Sobre Nós</a></li>
                    <li><a href="/contacto.php">Contacto</a></li>
                </ul>
            </div>

            <div class="footer__coluna">
                <h4>Categorias</h4>
                <ul>
                    <li><a href="/frota.php?categoria=supercar">Supercarros</a></li>
                    <li><a href="/frota.php?categoria=gran_turismo">Gran Turismo</a></li>
                    <li><a href="/frota.php?categoria=berlina_luxo">Berlinas de Luxo</a></li>
                    <li><a href="/frota.php?categoria=suv_luxo">SUV de Luxo</a></li>
                </ul>
            </div>

            <div class="footer__coluna">
                <h4>Contactos</h4>
                <ul class="footer__contactos">
                    <li><i class="fa-solid fa-location-dot"></i> Av. da Liberdade, Lisboa, Portugal</li>
                    <li><i class="fa-solid fa-phone"></i> +351 210 000 000</li>
                    <li><i class="fa-solid fa-envelope"></i> reservas@luxdrive.pt</li>
                </ul>
            </div>
        </div>

        <div class="footer__base">
            <p>&copy; <?= date('Y') ?> RentaLuxuryCars. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/gsap@3/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3/dist/ScrollTrigger.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="/assets/js/main.js"></script>
    <?php foreach ($scriptsAdicionais as $script): ?>
        <script src="<?= e($script) ?>"></script>
    <?php endforeach; ?>
</body>
</html>
