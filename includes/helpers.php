<?php
/**
 * Funções auxiliares partilhadas por todo o site.
 */

declare(strict_types=1);

require_once __DIR__ . '/db.php';

function e(?string $valor): string
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

function formatarPreco(float|string $valor): string
{
    return number_format((float) $valor, 2, ',', ' ') . ' €';
}

function formatarData(string $data, string $formato = 'd/m/Y'): string
{
    $dt = DateTime::createFromFormat('Y-m-d', $data) ?: new DateTime($data);
    return $dt->format($formato);
}

function gerarSlug(string $texto): string
{
    $texto = strtolower(trim($texto));
    $texto = iconv('UTF-8', 'ASCII//TRANSLIT', $texto) ?: $texto;
    $texto = preg_replace('/[^a-z0-9]+/', '-', $texto) ?? '';
    return trim($texto, '-');
}

function gerarReferenciaReserva(): string
{
    $ano = date('Y');
    $sufixo = strtoupper(substr(bin2hex(random_bytes(4)), 0, 4));
    return "LUX-{$ano}-{$sufixo}";
}

function calcularDias(string $inicio, string $fim): int
{
    $dtInicio = new DateTime($inicio);
    $dtFim = new DateTime($fim);
    $diff = $dtInicio->diff($dtFim)->days;
    return max(1, $diff);
}

function redirecionar(string $caminho): never
{
    header("Location: {$caminho}");
    exit;
}

function respostaJson(array $dados, int $codigo = 200): never
{
    http_response_code($codigo);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit;
}

function pedidoEhPost(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
}

function obterCategoriasVeiculos(): array
{
    return [
        'supercar'      => 'Supercarro',
        'gran_turismo'  => 'Gran Turismo',
        'suv_luxo'      => 'SUV de Luxo',
        'berlina_luxo'  => 'Berlina de Luxo',
        'cabrio'        => 'Cabrio',
    ];
}

function obterLabelCategoria(string $categoria): string
{
    return obterCategoriasVeiculos()[$categoria] ?? $categoria;
}

function obterLabelEstadoReserva(string $estado): string
{
    return match ($estado) {
        'pendente'   => 'Pendente',
        'confirmada' => 'Confirmada',
        'ativa'      => 'Ativa',
        'concluida'  => 'Concluída',
        'cancelada'  => 'Cancelada',
        default      => ucfirst($estado),
    };
}

function definirHeadersSeguranca(): void
{
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}
