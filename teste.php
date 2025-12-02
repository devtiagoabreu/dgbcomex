<?php
// teste.php - Arquivo de teste básico
echo "<h1>DGB COMEX - Teste</h1>";
echo "<p>PHP está funcionando!</p>";
echo "<p>Data: " . date('d/m/Y H:i:s') . "</p>";
echo "<p>Domínio: " . $_SERVER['HTTP_HOST'] . "</p>";

// Testar conexão com API
$api_url = 'http://94550ac37bb5.sn.mynetname.net:58244/health';
$response = @file_get_contents($api_url);

if ($response) {
    echo "<p style='color: green;'>✅ Conexão com API: OK</p>";
    $data = json_decode($response, true);
    echo "<pre>" . print_r($data, true) . "</pre>";
} else {
    echo "<p style='color: red;'>❌ Conexão com API: FALHOU</p>";
}
?>