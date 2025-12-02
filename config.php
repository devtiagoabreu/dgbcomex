<?php
// Configurações do sistema
define('API_BASE_URL', 'dgbcomex.com.br');
define('SITE_NAME', 'Dashboard COMEX');
define('REFRESH_TIME', 300000); // 5 minutos em milissegundos

// Configuração de data
date_default_timezone_set("America/Sao_Paulo");
$DATA_ATUAL = date("dmY"); // Formato: 28112025

// Cores do tema
$theme_colors = [
    'primary' => '#2c3e50',
    'secondary' => '#34495e',
    'success' => '#27ae60',
    'info' => '#3498db',
    'warning' => '#f39c12',
    'danger' => '#e74c3c',
    'light' => '#ecf0f1',
    'dark' => '#2c3e50'
];

// Configurações de ambiente
$environment = [
    'curl_available' => function_exists('curl_version'),
    'url_fopen_available' => ini_get('allow_url_fopen'),
    'php_version' => PHP_VERSION,
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'
];
?>