<?php
/**
 * admin/logout.php — Termina a sessão do administrador
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

logout();
redirecionar('/admin/login.php');
