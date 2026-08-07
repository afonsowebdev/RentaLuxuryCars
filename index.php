<?php
/**
 * index.php — Landing page da RentaLuxuryCars
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';

$pdo = obterLigacaoBD();

$stmtDestaque = $pdo->query('SELECT * FROM veiculos WHERE destaque = 1 AND disponivel = 1 ORDER BY criado_em DESC LIMIT 3');
$veiculosDestaque = $stmtDestaque->fetchAll();

$tituloPagina = 'RentaLuxuryCars — Experiência. Velocidade. Luxo.';
require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="hero__midia">
        <img src="/assets/images/hero/hero-principal.svg" alt="Supercarro de luxo ao entardecer" loading="eager">
    </div>
    <div class="hero__conteudo">
        <p class="eyebrow fade-in">Aluguer de Automóveis de Luxo em Portugal</p>
        <h1 class="hero__titulo fade-in">Experiência. <span>Velocidade.</span> Luxo.</h1>
        <p class="hero__subtitulo fade-in">Conduza os automóveis mais desejados do mundo — Lamborghini, Ferrari, Porsche, Bentley, Rolls-Royce e muito mais. Reserve em minutos, viva a experiência.</p>
        <div class="hero__ctas fade-in">
            <a href="/frota.php" class="btn btn--primario btn--lg">Ver Frota Completa</a>
            <a href="/contacto.php" class="btn btn--secundario btn--lg">Fale Connosco</a>
        </div>
    </div>
    <div class="hero__scroll"><i class="fa-solid fa-chevron-down"></i></div>
</section>

<div class="barra-reserva-wrapper">
    <div class="secao__container">
        <form class="barra-reserva" action="/frota.php" method="GET">
            <div class="campo">
                <label for="local">Local de Entrega</label>
                <select id="local" name="local">
                    <option value="lisboa">Lisboa</option>
                    <option value="porto">Porto</option>
                    <option value="faro">Faro</option>
                    <option value="aeroporto-lisboa">Aeroporto de Lisboa</option>
                </select>
            </div>
            <div class="campo">
                <label for="inicio">Data de Início</label>
                <input type="date" id="inicio" name="inicio" min="<?= date('Y-m-d') ?>">
            </div>
            <div class="campo">
                <label for="fim">Data de Fim</label>
                <input type="date" id="fim" name="fim" min="<?= date('Y-m-d') ?>">
            </div>
            <button type="submit" class="btn btn--primario btn--full"><i class="fa-solid fa-magnifying-glass"></i> Procurar</button>
        </form>
    </div>
</div>

<section class="secao">
    <div class="secao__container">
        <div class="secao__cabecalho secao__cabecalho--centro">
            <p class="eyebrow">A Nossa Seleção</p>
            <h2 class="titulo-secao">Frota em <span>Destaque</span></h2>
            <p class="subtitulo-secao">Os automóveis mais icónicos do mundo, prontos para a sua próxima experiência.</p>
        </div>

        <div class="grid" data-gsap>
            <?php foreach ($veiculosDestaque as $veiculo): ?>
                <?php require __DIR__ . '/includes/partials/card-veiculo.php'; ?>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-xl">
            <a href="/frota.php" class="btn btn--secundario">Ver Toda a Frota <i class="fa-solid fa-arrow-right btn__icon"></i></a>
        </div>
    </div>
</section>

<section class="secao secao--escura pilares">
    <div class="secao__container">
        <div class="secao__cabecalho secao__cabecalho--centro">
            <p class="eyebrow">A Nossa Promessa</p>
            <h2 class="titulo-secao">Porquê a <span>RentaLuxuryCars</span></h2>
        </div>

        <div class="pilares__grid">
            <div class="card" data-gsap>
                <div class="card__icon"><i class="fa-solid fa-shield-halved"></i></div>
                <h3 class="card__titulo">Seguro Total</h3>
                <p class="card__texto">Cobertura completa em todas as reservas, sem surpresas.</p>
            </div>
            <div class="card" data-gsap>
                <div class="card__icon"><i class="fa-solid fa-key"></i></div>
                <h3 class="card__titulo">Entrega Onde Quiser</h3>
                <p class="card__texto">Aeroporto, hotel ou morada — nós levamos o carro até si.</p>
            </div>
            <div class="card" data-gsap>
                <div class="card__icon"><i class="fa-solid fa-headset"></i></div>
                <h3 class="card__titulo">Apoio 24/7</h3>
                <p class="card__texto">Assistência dedicada disponível a qualquer hora do dia.</p>
            </div>
            <div class="card" data-gsap>
                <div class="card__icon"><i class="fa-solid fa-star"></i></div>
                <h3 class="card__titulo">Frota Exclusiva</h3>
                <p class="card__texto">As marcas mais desejadas do mundo, sempre revistas e impecáveis.</p>
            </div>
        </div>
    </div>
</section>

<section class="secao como-funciona">
    <div class="secao__container">
        <div class="secao__cabecalho secao__cabecalho--centro">
            <p class="eyebrow">Simples e Rápido</p>
            <h2 class="titulo-secao">Como <span>Funciona</span></h2>
        </div>

        <div class="como-funciona__passos">
            <div class="como-funciona__passo" data-gsap>
                <h3 class="como-funciona__titulo">Escolha o Veículo</h3>
                <p class="como-funciona__texto">Explore a nossa frota e selecione o automóvel perfeito para si.</p>
            </div>
            <div class="como-funciona__passo" data-gsap>
                <h3 class="como-funciona__titulo">Reserve Online</h3>
                <p class="como-funciona__texto">Preencha os seus dados e escolha as datas em poucos minutos.</p>
            </div>
            <div class="como-funciona__passo" data-gsap>
                <h3 class="como-funciona__titulo">Viva a Experiência</h3>
                <p class="como-funciona__texto">Receba o carro no local combinado e desfrute da condução.</p>
            </div>
        </div>
    </div>
</section>

<section class="secao secao--grafite estatisticas">
    <div class="secao__container">
        <div class="estatisticas__grid">
            <div data-gsap>
                <div class="estatisticas__valor"><span data-contador="8">0</span>+</div>
                <div class="estatisticas__label">Modelos Exclusivos</div>
            </div>
            <div data-gsap>
                <div class="estatisticas__valor"><span data-contador="500">0</span>+</div>
                <div class="estatisticas__label">Clientes Satisfeitos</div>
            </div>
            <div data-gsap>
                <div class="estatisticas__valor"><span data-contador="12">0</span></div>
                <div class="estatisticas__label">Anos de Experiência</div>
            </div>
            <div data-gsap>
                <div class="estatisticas__valor"><span data-contador="24">0</span>/7</div>
                <div class="estatisticas__label">Apoio ao Cliente</div>
            </div>
        </div>
    </div>
</section>

<section class="secao">
    <div class="secao__container">
        <div class="secao__cabecalho secao__cabecalho--centro">
            <p class="eyebrow">Testemunhos</p>
            <h2 class="titulo-secao">O Que Dizem os Nossos <span>Clientes</span></h2>
        </div>

        <div class="swiper swiper-depoimentos">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="card-depoimento">
                        <div class="card-depoimento__estrelas"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                        <p class="card-depoimento__texto">"Serviço impecável do início ao fim. O Huracán estava em condição perfeita e a entrega no aeroporto foi pontual."</p>
                        <div class="card-depoimento__autor">
                            <div>
                                <div class="card-depoimento__nome">Ricardo Mendes</div>
                                <div class="card-depoimento__origem">Lisboa, Portugal</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="card-depoimento">
                        <div class="card-depoimento__estrelas"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                        <p class="card-depoimento__texto">"Uma experiência verdadeiramente premium. O Rolls-Royce Ghost superou todas as expectativas para o nosso casamento."</p>
                        <div class="card-depoimento__autor">
                            <div>
                                <div class="card-depoimento__nome">Sofia Alves</div>
                                <div class="card-depoimento__origem">Porto, Portugal</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="card-depoimento">
                        <div class="card-depoimento__estrelas"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                        <p class="card-depoimento__texto">"Equipa extremamente profissional. Já é a terceira vez que alugo com a RentaLuxuryCars e nunca me desiludiram."</p>
                        <div class="card-depoimento__autor">
                            <div>
                                <div class="card-depoimento__nome">James Whitfield</div>
                                <div class="card-depoimento__origem">Londres, Reino Unido</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

<section class="secao secao--escura marcas">
    <div class="secao__container">
        <div class="secao__cabecalho secao__cabecalho--centro">
            <p class="eyebrow">As Nossas Marcas</p>
            <h2 class="titulo-secao">Excelência em Cada <span>Detalhe</span></h2>
        </div>

        <div class="marcas__grid">
            <div class="marcas__item">Lamborghini</div>
            <div class="marcas__item">Ferrari</div>
            <div class="marcas__item">Porsche</div>
            <div class="marcas__item">Bentley</div>
            <div class="marcas__item">Rolls-Royce</div>
            <div class="marcas__item">McLaren</div>
            <div class="marcas__item">Aston Martin</div>
            <div class="marcas__item">Maserati</div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
