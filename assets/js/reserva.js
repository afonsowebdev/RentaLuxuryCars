/**
 * reserva.js — Formulário de reserva multi-step, cálculo dinâmico e submissão
 */

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('reservaForm');
  if (!form) return;

  const passos = Array.from(form.querySelectorAll('.reserva-passo'));
  const indicadores = Array.from(document.querySelectorAll('.progresso-passos__passo'));
  const btnAvancar = document.querySelectorAll('[data-reserva-avancar]');
  const btnVoltar = document.querySelectorAll('[data-reserva-voltar]');

  const precoDia = Number(form.dataset.precoDia || 0);
  const veiculoId = form.dataset.veiculoId;

  let passoAtual = 0;

  function mostrarPasso(indice) {
    passos.forEach((p, i) => p.classList.toggle('ativo', i === indice));
    indicadores.forEach((el, i) => {
      el.classList.toggle('ativo', i === indice);
      el.classList.toggle('concluido', i < indice);
    });
    passoAtual = indice;
    window.scrollTo({ top: form.offsetTop - 120, behavior: 'smooth' });
  }

  function validarPassoAtual() {
    const camposObrigatorios = passos[passoAtual].querySelectorAll('[required]');
    let valido = true;

    camposObrigatorios.forEach((campo) => {
      const grupo = campo.closest('.campo') || campo.parentElement;
      if (!campo.checkValidity()) {
        valido = false;
        grupo?.classList.add('campo--erro');
      } else {
        grupo?.classList.remove('campo--erro');
      }
    });

    if (passoAtual === 0) {
      const inicio = form.querySelector('[name="data_inicio"]');
      const fim = form.querySelector('[name="data_fim"]');
      if (inicio && fim && inicio.value && fim.value && fim.value <= inicio.value) {
        valido = false;
        fim.closest('.campo')?.classList.add('campo--erro');
        alert('A data de fim deve ser posterior à data de início.');
      }
    }

    return valido;
  }

  btnAvancar.forEach((btn) => {
    btn.addEventListener('click', () => {
      if (!validarPassoAtual()) return;
      if (passoAtual < passos.length - 1) mostrarPasso(passoAtual + 1);
      if (passoAtual === passos.length - 1) preencherResumo();
    });
  });

  btnVoltar.forEach((btn) => {
    btn.addEventListener('click', () => {
      if (passoAtual > 0) mostrarPasso(passoAtual - 1);
    });
  });

  function calcularDias() {
    const inicio = form.querySelector('[name="data_inicio"]')?.value;
    const fim = form.querySelector('[name="data_fim"]')?.value;
    if (!inicio || !fim) return 0;

    const dtInicio = new Date(inicio);
    const dtFim = new Date(fim);
    const dias = Math.round((dtFim - dtInicio) / (1000 * 60 * 60 * 24));
    return dias > 0 ? dias : 0;
  }

  function calcularTotais() {
    const dias = calcularDias();
    const precoBase = dias * precoDia;

    let precoExtras = 0;
    form.querySelectorAll('[name="extras[]"]:checked').forEach((extra) => {
      precoExtras += dias * Number(extra.dataset.preco || 0);
    });

    return { dias, precoBase, precoExtras, total: precoBase + precoExtras };
  }

  function atualizarResumoPrecos() {
    const { dias, precoBase, precoExtras, total } = calcularTotais();
    const fmt = (v) => `${v.toLocaleString('pt-PT', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} €`;

    setTexto('#resumoDias', `${dias} dia${dias === 1 ? '' : 's'}`);
    setTexto('#resumoBase', fmt(precoBase));
    setTexto('#resumoExtras', fmt(precoExtras));
    setTexto('#resumoTotal', fmt(total));
    setTexto('#resumoTotal2', fmt(total));
  }

  function setTexto(seletor, texto) {
    const el = document.querySelector(seletor);
    if (el) el.textContent = texto;
  }

  form.addEventListener('change', (evento) => {
    if (['data_inicio', 'data_fim'].includes(evento.target.name) || evento.target.name === 'extras[]') {
      atualizarResumoPrecos();
      verificarDisponibilidade();
    }
  });

  function verificarDisponibilidade() {
    const inicio = form.querySelector('[name="data_inicio"]')?.value;
    const fim = form.querySelector('[name="data_fim"]')?.value;
    const avisoEl = document.getElementById('avisoDisponibilidade');
    if (!inicio || !fim || !veiculoId || !avisoEl) return;

    const params = new URLSearchParams({ veiculo_id: veiculoId, inicio, fim });

    fetch(`/api/disponibilidade.php?${params.toString()}`)
      .then((resposta) => resposta.json())
      .then((dados) => {
        if (dados.disponivel) {
          avisoEl.textContent = 'Veículo disponível para as datas selecionadas.';
          avisoEl.className = 'badge badge--disponivel';
        } else {
          avisoEl.textContent = 'Datas indisponíveis — escolha outro período.';
          avisoEl.className = 'badge badge--indisponivel';
        }
      })
      .catch(() => {});
  }

  function preencherResumo() {
    atualizarResumoPrecos();
    ['cliente_nome', 'cliente_apelido', 'cliente_email', 'cliente_telefone', 'data_inicio', 'data_fim', 'local_entrega', 'local_devolucao'].forEach((nome) => {
      const campo = form.querySelector(`[name="${nome}"]`);
      const alvo = document.querySelector(`[data-resumo="${nome}"]`);
      if (campo && alvo) alvo.textContent = campo.value;
    });
  }

  form.addEventListener('submit', (evento) => {
    evento.preventDefault();
    if (!validarPassoAtual()) return;

    const btnSubmeter = form.querySelector('[type="submit"]');
    if (btnSubmeter) {
      btnSubmeter.disabled = true;
      btnSubmeter.innerHTML = '<span class="spinner spinner--sm"></span> A processar...';
    }

    fetch('/api/reserva.php', {
      method: 'POST',
      body: new FormData(form),
    })
      .then((resposta) => resposta.json())
      .then((dados) => {
        if (dados.sucesso) {
          window.location.href = `/confirmacao.php?ref=${encodeURIComponent(dados.referencia)}`;
        } else {
          alert(dados.erros ? Object.values(dados.erros).join('\n') : 'Ocorreu um erro ao processar a reserva.');
          if (btnSubmeter) {
            btnSubmeter.disabled = false;
            btnSubmeter.innerHTML = 'Confirmar Reserva';
          }
        }
      })
      .catch(() => {
        alert('Erro de ligação. Tente novamente.');
        if (btnSubmeter) {
          btnSubmeter.disabled = false;
          btnSubmeter.innerHTML = 'Confirmar Reserva';
        }
      });
  });

  atualizarResumoPrecos();
  mostrarPasso(0);
});
