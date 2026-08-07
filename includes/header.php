<?php
/**
 * Cabeçalho HTML comum a todas as páginas públicas.
 * Espera (opcionalmente) as variáveis: $tituloPagina, $descricaoPagina
 */

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

definirHeadersSeguranca();
iniciarSessaoSegura();

$tituloPagina ??= 'RentaLuxuryCars — Aluguer de Carros de Luxo em Portugal';
$descricaoPagina ??= 'Alugue Lamborghini, Ferrari, Porsche, Bentley, Rolls-Royce, McLaren, Aston Martin e Maserati em Portugal. Experiência, velocidade e luxo.';
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($tituloPagina) ?></title>
    <meta name="description" content="<?= e($descricaoPagina) ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="icon" type="image/svg+xml" href="/assets/images/logo/favicon.svg">
</head>
<body>
<?php require_once __DIR__ . '/nav.php'; ?>
