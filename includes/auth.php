<?php
/**
 * Autenticação, sessões e proteção CSRF.
 */

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

const MAX_TENTATIVAS_LOGIN = 5;
const BLOQUEIO_LOGIN_MINUTOS = 15;

function iniciarSessaoSegura(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => (($_SERVER['HTTPS'] ?? '') === 'on'),
    ]);

    session_start();
}

function login(string $email, string $password): array
{
    $pdo = obterLigacaoBD();

    $stmt = $pdo->prepare('SELECT * FROM utilizadores WHERE email = ? AND ativo = 1');
    $stmt->execute([$email]);
    $utilizador = $stmt->fetch();

    if ($utilizador && !empty($utilizador['bloqueado_ate']) && strtotime($utilizador['bloqueado_ate']) > time()) {
        return ['sucesso' => false, 'erro' => 'Conta temporariamente bloqueada. Tente novamente mais tarde.'];
    }

    if (!$utilizador || !password_verify($password, $utilizador['password_hash'])) {
        if ($utilizador) {
            registarTentativaFalhada($pdo, (int) $utilizador['id'], (int) $utilizador['tentativas_login']);
        }
        return ['sucesso' => false, 'erro' => 'Credenciais inválidas.'];
    }

    $stmt = $pdo->prepare('UPDATE utilizadores SET tentativas_login = 0, bloqueado_ate = NULL WHERE id = ?');
    $stmt->execute([$utilizador['id']]);

    iniciarSessaoSegura();
    session_regenerate_id(true);

    $_SESSION['utilizador_id'] = (int) $utilizador['id'];
    $_SESSION['utilizador_tipo'] = $utilizador['tipo'];
    $_SESSION['utilizador_nome'] = $utilizador['nome'];

    return ['sucesso' => true, 'utilizador' => $utilizador];
}

function registarTentativaFalhada(PDO $pdo, int $id, int $tentativasAtuais): void
{
    $tentativas = $tentativasAtuais + 1;
    $bloqueadoAte = null;

    if ($tentativas >= MAX_TENTATIVAS_LOGIN) {
        $bloqueadoAte = (new DateTime("+" . BLOQUEIO_LOGIN_MINUTOS . " minutes"))->format('Y-m-d H:i:s');
    }

    $stmt = $pdo->prepare('UPDATE utilizadores SET tentativas_login = ?, bloqueado_ate = ? WHERE id = ?');
    $stmt->execute([$tentativas, $bloqueadoAte, $id]);
}

function logout(): void
{
    iniciarSessaoSegura();
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
}

function estaAutenticado(): bool
{
    iniciarSessaoSegura();
    return isset($_SESSION['utilizador_id']);
}

function eAdmin(): bool
{
    iniciarSessaoSegura();
    return estaAutenticado() && ($_SESSION['utilizador_tipo'] ?? '') === 'admin';
}

function exigirAdmin(): void
{
    if (!eAdmin()) {
        redirecionar('/admin/login.php');
    }
}

function utilizadorAtual(): ?array
{
    if (!estaAutenticado()) {
        return null;
    }

    $pdo = obterLigacaoBD();
    $stmt = $pdo->prepare('SELECT id, nome, apelido, email, telefone, tipo FROM utilizadores WHERE id = ?');
    $stmt->execute([$_SESSION['utilizador_id']]);
    $utilizador = $stmt->fetch();

    return $utilizador ?: null;
}

function gerarTokenCSRF(): string
{
    iniciarSessaoSegura();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verificarTokenCSRF(?string $token): bool
{
    iniciarSessaoSegura();

    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

function campoCSRF(): string
{
    $token = gerarTokenCSRF();
    return '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
}
