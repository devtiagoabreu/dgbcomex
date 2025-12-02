<?php
$api_url = 'http://94550ac37bb5.sn.mynetname.net:58244';
$data = date('dmY'); // 28112025

$endpoints = [
    'Health' => '/health',
    'Faturamento' => '/faturamento/' . $data,
    'Faturamento Dia' => '/faturamento-dia/' . $data,
    'Contas Pagas' => '/contas-pagas/' . $data,
    'Dashboard Completo' => '/dashboard-completo/' . $data
];

echo "<h1>Teste Detalhado - Endpoints da API</h1>";

foreach ($endpoints as $name => $endpoint) {
    $url = $api_url . $endpoint;
    $context = stream_context_create([
        'http' => ['timeout' => 10],
        'ssl' => ['verify_peer' => false]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    
    echo "<div style='border: 1px solid #ccc; padding: 15px; margin: 10px 0;'>";
    echo "<h3>🔍 $name</h3>";
    echo "<p><strong>URL:</strong> $url</p>";
    
    if ($response) {
        $data = json_decode($response, true);
        echo "<p style='color: green;'>✅ CONECTADO</p>";
        echo "<pre>" . print_r($data, true) . "</pre>";
    } else {
        echo "<p style='color: red;'>❌ FALHOU - " . (error_get_last()['message'] ?? 'Erro desconhecido') . "</p>";
    }
    echo "</div>";
}
?>