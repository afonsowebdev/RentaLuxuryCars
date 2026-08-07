<?php
/**
 * api/auth.php — POST: autenticação (login / logout)
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

definirHeadersSeguranca();

if (!pedidoEhPost()) {
    respostaJson(['sucesso' => false, 'erro' => 'Método não permitido.'], 405);
}

$acao = $_POST['action'] ?? '';

if ($acao === 'logout') {
    logout();
    respostaJson(['sucesso' => true]);
}

if ($acao === 'login') {
    if (!verificarTokenCSRF($_POST['csrf_token'] ?? null)) {
        respostaJson(['sucesso' => false, 'erro' => 'Token de segurança inválido.'], 403);
    }

    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        respostaJson(['sucesso' => false, 'erro' => 'Introduza um email e palavra-passe válidos.'], 422);
    }

    $resultado = login($email, $password);

    if (!$resultado['sucesso']) {
        respostaJson(['sucesso' => false, 'erro' => $resultado['erro']], 401);
    }

    respostaJson(['sucesso' => true]);
}

respostaJson(['sucesso' => false, 'erro' => 'Ação inválida.'], 400);
