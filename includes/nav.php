<?php
/**
 * Navegação principal do site.
 */

declare(strict_types=1);

$paginaAtual = basename($_SERVER['SCRIPT_NAME'] ?? '');
$utilizadorSessao = estaAutenticado() ? utilizadorAtual() : null;
?>
<header class="navbar" id="navbar" data-navbar>
    <div class="navbar__container">
        <a href="/index.php" class="navbar__logo">
            <span class="navbar__logo-texto">Renta<span class="navbar__logo-acento">Luxury</span>Cars</span>
        </a>

        <button class="navbar__toggle" id="navToggle" aria-label="Abrir menu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>

        <nav class="navbar__menu" id="navMenu">
            <ul class="navbar__lista">
                <li><a href="/index.php" class="<?= $paginaAtual === 'index.php' ? 'ativo' : '' ?>">Início</a></li>
                <li><a href="/frota.php" class="<?= $paginaAtual === 'frota.php' ? 'ativo' : '' ?>">Frota</a></li>
                <li><a href="/sobre.php" class="<?= $paginaAtual === 'sobre.php' ? 'ativo' : '' ?>">Sobre Nós</a></li>
                <li><a href="/contacto.php" class="<?= $paginaAtual === 'contacto.php' ? 'ativo' : '' ?>">Contacto</a></li>
            </ul>

            <div class="navbar__acoes">
                <?php if ($utilizadorSessao): ?>
                    <span class="navbar__utilizador"><i class="fa-solid fa-user"></i> <?= e($utilizadorSessao['nome']) ?></span>
                <?php endif; ?>
                <a href="/frota.php" class="btn btn--primario btn--sm">Reservar Agora</a>
            </div>
        </nav>
    </div>
</header>
