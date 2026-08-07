<?php
/**
 * Ligação PDO à base de dados MySQL.
 */

declare(strict_types=1);

if (!function_exists('carregarEnv')) {
    function carregarEnv(string $caminho): void
    {
        if (!is_file($caminho)) {
            return;
        }

        foreach (file($caminho, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linha) {
            $linha = trim($linha);
            if ($linha === '' || str_starts_with($linha, '#')) {
                continue;
            }
            if (!str_contains($linha, '=')) {
                continue;
            }
            [$chave, $valor] = explode('=', $linha, 2);
            $chave = trim($chave);
            $valor = trim($valor);
            if ($chave !== '' && getenv($chave) === false) {
                putenv("{$chave}={$valor}");
                $_ENV[$chave] = $valor;
            }
        }
    }
}

carregarEnv(__DIR__ . '/../.env');

function obterConfig(string $chave, string $default = ''): string
{
    $valor = getenv($chave);
    return $valor !== false ? $valor : $default;
}

function obterLigacaoBD(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = obterConfig('DB_HOST', '127.0.0.1');
    $porta = obterConfig('DB_PORT', '3306');
    $nome = obterConfig('DB_NAME', 'luxdrive');
    $utilizador = obterConfig('DB_USER', 'root');
    $password = obterConfig('DB_PASS', '');

    $dsn = "mysql:host={$host};port={$porta};dbname={$nome};charset=utf8mb4";

    $pdo = new PDO($dsn, $utilizador, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
