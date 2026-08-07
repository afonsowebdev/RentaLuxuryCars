/**
 * frota.js — Filtros, ordenação e paginação client-side do catálogo
 */

document.addEventListener('DOMContentLoaded', () => {
  const grid = document.getElementById('gridFrota');
  if (!grid) return;

  const cartoes = Array.from(grid.querySelectorAll('[data-veiculo]'));
  const contagemEl = document.getElementById('frotaContagem');
  const vazioEl = document.getElementById('frotaVazio');
  const paginacaoEl = document.getElementById('frotaPaginacao');
  const ordenarEl = document.getElementById('frotaOrdenar');
  const limparBtn = document.getElementById('frotaLimparFiltros');

  const POR_PAGINA = 6;
  let paginaAtual = 1;

  const filtros = {
    categorias: new Set(),
    marcas: new Set(),
    precoMax: Infinity,
    lugares: 0,
  };

  document.querySelectorAll('[data-filtro-categoria]').forEach((el) => {
    el.addEventListener('change', () => {
      atualizarConjunto(filtros.categorias, el.value, el.checked);
      paginaAtual = 1;
      aplicarFiltros();
    });
  });

  document.querySelectorAll('[data-filtro-marca]').forEach((el) => {
    el.addEventListener('change', () => {
      atualizarConjunto(filtros.marcas, el.value, el.checked);
      paginaAtual = 1;
      aplicarFiltros();
    });
  });

  const precoInput = document.querySelector('[data-filtro-preco]');
  if (precoInput) {
    const precoLabel = document.getElementById('frotaPrecoLabel');
    precoInput.addEventListener('input', () => {
      filtros.precoMax = Number(precoInput.value);
      if (precoLabel) precoLabel.textContent = `${Number(precoInput.value).toLocaleString('pt-PT')} €`;
      paginaAtual = 1;
      aplicarFiltros();
    });
  }

  document.querySelectorAll('[data-filtro-lugares]').forEach((el) => {
    el.addEventListener('change', () => {
      filtros.lugares = el.checked ? Number(el.value) : 0;
      paginaAtual = 1;
      aplicarFiltros();
    });
  });

  if (ordenarEl) {
    ordenarEl.addEventListener('change', () => {
      paginaAtual = 1;
      aplicarFiltros();
    });
  }

  if (limparBtn) {
    limparBtn.addEventListener('click', () => {
      filtros.categorias.clear();
      filtros.marcas.clear();
      filtros.precoMax = Infinity;
      filtros.lugares = 0;
      document.querySelectorAll('[data-filtro-categoria], [data-filtro-marca], [data-filtro-lugares]').forEach((el) => { el.checked = false; });
      if (precoInput) precoInput.value = precoInput.max;
      paginaAtual = 1;
      aplicarFiltros();
    });
  }

  function atualizarConjunto(conjunto, valor, incluir) {
    if (incluir) conjunto.add(valor);
    else conjunto.delete(valor);
  }

  function passaNosFiltros(cartao) {
    const categoria = cartao.dataset.categoria;
    const marca = cartao.dataset.marca;
    const preco = Number(cartao.dataset.preco);
    const lugares = Number(cartao.dataset.lugares);

    if (filtros.categorias.size && !filtros.categorias.has(categoria)) return false;
    if (filtros.marcas.size && !filtros.marcas.has(marca)) return false;
    if (preco > filtros.precoMax) return false;
    if (filtros.lugares && lugares < filtros.lugares) return false;

    return true;
  }

  function ordenarCartoes(lista) {
    const modo = ordenarEl ? ordenarEl.value : 'destaque';

    return lista.sort((a, b) => {
      if (modo === 'preco-asc') return Number(a.dataset.preco) - Number(b.dataset.preco);
      if (modo === 'preco-desc') return Number(b.dataset.preco) - Number(a.dataset.preco);
      if (modo === 'nome') return a.dataset.nome.localeCompare(b.dataset.nome);
      return Number(b.dataset.destaque) - Number(a.dataset.destaque);
    });
  }

  function aplicarFiltros() {
    const visiveis = ordenarCartoes(cartoes.filter(passaNosFiltros));
    const totalPaginas = Math.max(1, Math.ceil(visiveis.length / POR_PAGINA));
    paginaAtual = Math.min(paginaAtual, totalPaginas);

    cartoes.forEach((c) => { c.style.display = 'none'; });

    const inicio = (paginaAtual - 1) * POR_PAGINA;
    visiveis.slice(inicio, inicio + POR_PAGINA).forEach((c) => { c.style.display = ''; });

    grid.style.display = visiveis.length ? '' : 'none';
    if (vazioEl) vazioEl.style.display = visiveis.length ? 'none' : '';
    if (contagemEl) contagemEl.textContent = `${visiveis.length} veículo${visiveis.length === 1 ? '' : 's'} encontrado${visiveis.length === 1 ? '' : 's'}`;

    renderizarPaginacao(totalPaginas);
  }

  function renderizarPaginacao(totalPaginas) {
    if (!paginacaoEl) return;
    paginacaoEl.innerHTML = '';

    if (totalPaginas <= 1) return;

    for (let i = 1; i <= totalPaginas; i++) {
      const item = document.createElement('span');
      item.textContent = String(i);
      item.className = i === paginaAtual ? 'ativo' : '';
      item.addEventListener('click', () => {
        paginaAtual = i;
        aplicarFiltros();
        grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
      paginacaoEl.appendChild(item);
    }
  }

  aplicarFiltros();
});
