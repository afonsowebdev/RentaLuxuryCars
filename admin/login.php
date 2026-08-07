<?php
/**
 * admin/login.php — Autenticação do painel administrativo
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

definirHeadersSeguranca();

if (eAdmin()) {
    redirecionar('/admin/dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — RentaLuxuryCars</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-body">
<div class="admin-login">
    <div class="admin-login__card">
        <div class="admin-login__logo">Renta<span>Luxury</span>Cars <small style="display:block;font-size:0.55em;color:var(--cor-cinza-claro);letter-spacing:0.1em;text-transform:uppercase;">Painel Administrativo</small></div>

        <div id="loginAlerta"></div>

        <form id="formLogin">
            <?= campoCSRF() ?>
            <div class="campo">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required autofocus>
            </div>
            <div class="campo">
                <label for="password">Palavra-passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn--primario btn--full">Entrar</button>
        </form>
    </div>
</div>

<script>
document.getElementById('formLogin').addEventListener('submit', function (evento) {
    evento.preventDefault();
    const form = evento.target;
    const alertaEl = document.getElementById('loginAlerta');
    const botao = form.querySelector('[type="submit"]');
    botao.disabled = true;

    const dados = new FormData(form);
    dados.append('action', 'login');

    fetch('/api/auth.php', { method: 'POST', body: dados })
        .then((r) => r.json())
        .then((resposta) => {
            if (resposta.sucesso) {
                window.location.href = '/admin/dashboard.php';
            } else {
                alertaEl.innerHTML = '<div class="alerta alerta--erro"><i class="fa-solid fa-circle-exclamation"></i> ' + (resposta.erro || 'Credenciais inválidas.') + '</div>';
                botao.disabled = false;
            }
        })
        .catch(() => {
            alertaEl.innerHTML = '<div class="alerta alerta--erro">Erro de ligação. Tente novamente.</div>';
            botao.disabled = false;
        });
});
</script>
</body>
</html>
