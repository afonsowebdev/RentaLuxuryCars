/**
 * main.js — Comportamento global do site (navbar, animações, swipers)
 */

document.addEventListener('DOMContentLoaded', () => {
  inicializarNavbar();
  inicializarMenuMobile();
  inicializarAnimacoesScroll();
  inicializarContadores();
  inicializarSwiperDepoimentos();
  inicializarSwiperGaleria();
});

function inicializarNavbar() {
  const navbar = document.querySelector('[data-navbar]');
  if (!navbar) return;

  const aoScroll = () => {
    navbar.classList.toggle('navbar--scrolled', window.scrollY > 40);
  };

  aoScroll();
  window.addEventListener('scroll', aoScroll, { passive: true });
}

function inicializarMenuMobile() {
  const toggle = document.getElementById('navToggle');
  const menu = document.getElementById('navMenu');
  if (!toggle || !menu) return;

  toggle.addEventListener('click', () => {
    const aberto = menu.classList.toggle('navbar__menu--aberto');
    toggle.setAttribute('aria-expanded', String(aberto));
    document.body.style.overflow = aberto ? 'hidden' : '';
  });

  menu.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
      menu.classList.remove('navbar__menu--aberto');
      toggle.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
    });
  });
}

function inicializarAnimacoesScroll() {
  if (typeof gsap === 'undefined') return;
  gsap.registerPlugin(ScrollTrigger);

  document.querySelectorAll('[data-gsap]').forEach((el, indice) => {
    gsap.to(el, {
      opacity: 1,
      y: 0,
      duration: 0.8,
      delay: (indice % 3) * 0.1,
      ease: 'power2.out',
      scrollTrigger: {
        trigger: el,
        start: 'top 85%',
      },
    });
  });

  const heroMidia = document.querySelector('.hero__midia img, .hero__midia video');
  if (heroMidia) {
    gsap.to(heroMidia, {
      yPercent: 15,
      ease: 'none',
      scrollTrigger: {
        trigger: '.hero',
        start: 'top top',
        end: 'bottom top',
        scrub: true,
      },
    });
  }
}

function inicializarContadores() {
  const contadores = document.querySelectorAll('[data-contador]');
  if (!contadores.length) return;

  const observador = new IntersectionObserver((entradas) => {
    entradas.forEach((entrada) => {
      if (!entrada.isIntersecting) return;
      animarContador(entrada.target);
      observador.unobserve(entrada.target);
    });
  }, { threshold: 0.5 });

  contadores.forEach((el) => observador.observe(el));
}

function animarContador(el) {
  const alvo = parseInt(el.dataset.contador, 10) || 0;
  const duracao = 1500;
  const inicio = performance.now();

  function passo(agora) {
    const progresso = Math.min((agora - inicio) / duracao, 1);
    const valor = Math.floor(progresso * alvo);
    el.textContent = valor.toLocaleString('pt-PT');
    if (progresso < 1) requestAnimationFrame(passo);
    else el.textContent = alvo.toLocaleString('pt-PT');
  }

  requestAnimationFrame(passo);
}

function inicializarSwiperDepoimentos() {
  const container = document.querySelector('.swiper-depoimentos');
  if (!container || typeof Swiper === 'undefined') return;

  new Swiper('.swiper-depoimentos', {
    slidesPerView: 1,
    spaceBetween: 24,
    loop: true,
    autoplay: { delay: 5000, disableOnInteraction: false },
    pagination: { el: '.swiper-pagination', clickable: true },
    breakpoints: {
      768: { slidesPerView: 2 },
      1024: { slidesPerView: 3 },
    },
  });
}

function inicializarSwiperGaleria() {
  const principal = document.querySelector('.galeria-veiculo__principal');
  if (!principal || typeof Swiper === 'undefined') return;

  const thumbs = new Swiper('.galeria-veiculo__thumbs', {
    slidesPerView: 4,
    spaceBetween: 12,
    watchSlidesProgress: true,
  });

  new Swiper('.galeria-veiculo__principal', {
    slidesPerView: 1,
    spaceBetween: 0,
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    thumbs: { swiper: thumbs },
  });
}
