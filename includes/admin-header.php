<?php
/**
 * Cabeçalho HTML comum às páginas do painel admin.
 * Espera a variável $tituloPagina e (opcional) $paginaAtivaAdmin.
 */

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

exigirAdmin();
definirHeadersSeguranca();

$tituloPagina ??= 'Painel Admin — RentaLuxuryCars';
$paginaAtivaAdmin ??= '';
$admin = utilizadorAtual();

$itensMenu = [
    'dashboard'  => ['label' => 'Dashboard', 'icon' => 'fa-gauge-high', 'href' => '/admin/dashboard.php'],
    'veiculos'   => ['label' => 'Veículos', 'icon' => 'fa-car', 'href' => '/admin/veiculos.php'],
    'reservas'   => ['label' => 'Reservas', 'icon' => 'fa-calendar-check', 'href' => '/admin/reservas.php'],
    'clientes'   => ['label' => 'Clientes', 'icon' => 'fa-users', 'href' => '/admin/clientes.php'],
    'relatorios' => ['label' => 'Relatórios', 'icon' => 'fa-chart-line', 'href' => '/admin/relatorios.php'],
];
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($tituloPagina) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-body">
<div class="admin-layout">
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="admin-sidebar__logo">Renta<span>Luxury</span>Cars</div>
        <nav class="admin-sidebar__nav">
            <?php foreach ($itensMenu as $chave => $item): ?>
                <a href="<?= e($item['href']) ?>" class="admin-sidebar__link <?= $paginaAtivaAdmin === $chave ? 'ativo' : '' ?>">
                    <i class="fa-solid <?= e($item['icon']) ?>"></i> <?= e($item['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="admin-sidebar__rodape">
            <a href="/index.php" class="admin-sidebar__link"><i class="fa-solid fa-arrow-left"></i> Ver Site</a>
            <a href="/admin/logout.php" class="admin-sidebar__link"><i class="fa-solid fa-right-from-bracket"></i> Sair</a>
        </div>
    </aside>

    <main class="admin-main">
        <div class="admin-topbar">
            <button class="admin-topbar__toggle" id="sidebarToggle"><i class="fa-solid fa-bars"></i></button>
            <h1 class="admin-topbar__titulo"><?= e($tituloPagina) ?></h1>
            <span style="color:var(--cor-cinza-claro);font-size:0.85rem;"><i class="fa-solid fa-user-shield"></i> <?= e($admin['nome'] ?? '') ?></span>
        </div>
