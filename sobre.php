<?php
/**
 * sobre.php — Sobre Nós
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';

$tituloPagina = 'Sobre Nós — RentaLuxuryCars';
$descricaoPagina = 'Conheça a história, missão e valores da RentaLuxuryCars, especialista em aluguer de automóveis de luxo em Portugal.';
require_once __DIR__ . '/includes/header.php';
?>

<main class="pagina-sobre">
    <section class="sobre-hero">
        <div class="secao__container text-center">
            <p class="eyebrow">A Nossa História</p>
            <h1 class="titulo-secao">Redefinindo o <span>Luxo</span> em Portugal</h1>
            <p class="subtitulo-secao" style="margin-left:auto;margin-right:auto;">Há mais de uma década a proporcionar experiências automóveis inesquecíveis aos clientes mais exigentes.</p>
        </div>
    </section>

    <section class="secao">
        <div class="secao__container">
            <div class="sobre-historia">
                <img src="/assets/images/hero/sobre-nos.svg" alt="Showroom RentaLuxuryCars" loading="lazy">
                <div>
                    <p class="eyebrow">Desde 2013</p>
                    <h2 class="titulo-secao">A Nossa <span>Missão</span></h2>
                    <p>A RentaLuxuryCars nasceu da paixão por automóveis excecionais e do desejo de trazer a Portugal um serviço de aluguer à altura dos padrões internacionais mais elevados.</p>
                    <p class="mt-md">Hoje, servimos executivos, turistas de alto poder de compra e entusiastas do automobilismo, oferecendo uma frota cuidadosamente selecionada das marcas mais icónicas do mundo — sempre com discrição, profissionalismo e atenção ao detalhe.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="secao secao--escura">
        <div class="secao__container">
            <div class="secao__cabecalho secao__cabecalho--centro">
                <p class="eyebrow">Os Nossos Valores</p>
                <h2 class="titulo-secao">O Que Nos <span>Move</span></h2>
            </div>

            <div class="valores-grid">
                <div class="card" data-gsap>
                    <div class="card__icon"><i class="fa-solid fa-gem"></i></div>
                    <h3 class="card__titulo">Excelência</h3>
                    <p class="card__texto">Cada veículo da nossa frota é mantido ao mais alto padrão de qualidade e apresentação.</p>
                </div>
                <div class="card" data-gsap>
                    <div class="card__icon"><i class="fa-solid fa-handshake"></i></div>
                    <h3 class="card__titulo">Confiança</h3>
                    <p class="card__texto">Transparência total em cada reserva, sem custos escondidos ou surpresas.</p>
                </div>
                <div class="card" data-gsap>
                    <div class="card__icon"><i class="fa-solid fa-user-secret"></i></div>
                    <h3 class="card__titulo">Discrição</h3>
                    <p class="card__texto">Privacidade e confidencialidade garantidas para todos os nossos clientes.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="secao">
        <div class="secao__container">
            <div class="secao__cabecalho secao__cabecalho--centro">
                <p class="eyebrow">A Nossa Equipa</p>
                <h2 class="titulo-secao">Profissionais <span>Dedicados</span></h2>
            </div>

            <div class="equipa-grid">
                <div class="equipa-membro">
                    <img src="/assets/images/hero/equipa-1.svg" alt="Membro da equipa" loading="lazy">
                    <h4>Francisco Afonso</h4>
                    <span>Fundador &amp; CEO</span>
                </div>
                <div class="equipa-membro">
                    <img src="/assets/images/hero/equipa-2.svg" alt="Membro da equipa" loading="lazy">
                    <h4>Beatriz Costa</h4>
                    <span>Diretora de Operações</span>
                </div>
                <div class="equipa-membro">
                    <img src="/assets/images/hero/equipa-3.svg" alt="Membro da equipa" loading="lazy">
                    <h4>Miguel Santos</h4>
                    <span>Gestor de Frota</span>
                </div>
                <div class="equipa-membro">
                    <img src="/assets/images/hero/equipa-4.svg" alt="Membro da equipa" loading="lazy">
                    <h4>Ana Ferreira</h4>
                    <span>Relações com Clientes</span>
                </div>
            </div>
        </div>
    </section>

    <section class="secao secao--grafite text-center">
        <div class="secao__container">
            <h2 class="titulo-secao">Pronto para Viver a <span>Experiência</span>?</h2>
            <a href="/frota.php" class="btn btn--primario btn--lg mt-lg">Explorar a Frota</a>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
