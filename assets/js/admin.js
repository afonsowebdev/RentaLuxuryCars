/**
 * admin.js — Comportamento do painel de administração
 */

document.addEventListener('DOMContentLoaded', () => {
  inicializarSidebar();
  inicializarConfirmacoes();
  inicializarPesquisaTabela();
  inicializarGraficos();
});

function inicializarSidebar() {
  const toggle = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('adminSidebar');
  if (!toggle || !sidebar) return;

  toggle.addEventListener('click', () => sidebar.classList.toggle('aberta'));
}

function inicializarConfirmacoes() {
  document.querySelectorAll('[data-confirmar]').forEach((el) => {
    el.addEventListener('click', (evento) => {
      const mensagem = el.dataset.confirmar || 'Tem a certeza?';
      if (!window.confirm(mensagem)) {
        evento.preventDefault();
      }
    });
  });
}

function inicializarPesquisaTabela() {
  document.querySelectorAll('[data-pesquisa-tabela]').forEach((input) => {
    const tabelaId = input.dataset.pesquisaTabela;
    const tabela = document.getElementById(tabelaId);
    if (!tabela) return;

    input.addEventListener('input', () => {
      const termo = input.value.trim().toLowerCase();
      tabela.querySelectorAll('tbody tr').forEach((linha) => {
        linha.style.display = linha.textContent.toLowerCase().includes(termo) ? '' : 'none';
      });
    });
  });
}

function inicializarGraficos() {
  if (typeof Chart === 'undefined') return;

  const reservasCanvas = document.getElementById('graficoReservas');
  if (reservasCanvas) {
    const dados = JSON.parse(reservasCanvas.dataset.dados || '{}');
    new Chart(reservasCanvas, {
      type: 'line',
      data: {
        labels: dados.labels || [],
        datasets: [{
          label: 'Reservas',
          data: dados.valores || [],
          borderColor: '#C9A84C',
          backgroundColor: 'rgba(201, 168, 76, 0.15)',
          tension: 0.35,
          fill: true,
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { color: '#252525' }, ticks: { color: '#C0C0C0' } },
          y: { grid: { color: '#252525' }, ticks: { color: '#C0C0C0' } },
        },
      },
    });
  }

  const categoriasCanvas = document.getElementById('graficoCategorias');
  if (categoriasCanvas) {
    const dados = JSON.parse(categoriasCanvas.dataset.dados || '{}');
    new Chart(categoriasCanvas, {
      type: 'doughnut',
      data: {
        labels: dados.labels || [],
        datasets: [{
          data: dados.valores || [],
          backgroundColor: ['#C9A84C', '#E8C96B', '#9B7D35', '#4CAF7C', '#4A9EF0'],
          borderWidth: 0,
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { color: '#C0C0C0' } } },
      },
    });
  }
}
