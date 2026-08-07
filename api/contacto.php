<?php
/**
 * api/contacto.php — POST: regista uma mensagem de contacto
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

definirHeadersSeguranca();

if (!pedidoEhPost()) {
    respostaJson(['sucesso' => false, 'erro' => 'Método não permitido.'], 405);
}

if (!verificarTokenCSRF($_POST['csrf_token'] ?? null)) {
    respostaJson(['sucesso' => false, 'erro' => 'Token de segurança inválido.'], 403);
}

$nome = trim((string) ($_POST['nome'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$telefone = trim((string) ($_POST['telefone'] ?? ''));
$assunto = trim((string) ($_POST['assunto'] ?? ''));
$mensagem = trim((string) ($_POST['mensagem'] ?? ''));

if ($nome === '' || $mensagem === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respostaJson(['sucesso' => false, 'erro' => 'Preencha corretamente o nome, email e mensagem.'], 422);
}

$pdo = obterLigacaoBD();
$stmt = $pdo->prepare(
    'INSERT INTO contactos (nome, email, telefone, assunto, mensagem) VALUES (?, ?, ?, ?, ?)'
);
$stmt->execute([$nome, $email, $telefone ?: null, $assunto ?: null, $mensagem]);

respostaJson(['sucesso' => true]);
