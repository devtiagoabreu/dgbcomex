<?php
class ApiClient {
    private $api_base_url;
    
    public function __construct($base_url) {
        $this->api_base_url = $base_url;
    }
    
    private function makeRequest($endpoint) {
        $url = $this->api_base_url . $endpoint;
        
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'User-Agent: DashboardCOMEX/1.0'
                ],
                'timeout' => 30,
                'ignore_errors' => true
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]);
        
        try {
            $response = @file_get_contents($url, false, $context);
            
            if ($response === FALSE) {
                error_log("API Request Failed: $url");
                return null;
            }
            
            $data = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("JSON Decode Error: " . json_last_error_msg());
                return null;
            }
            
            return $data;
            
        } catch (Exception $e) {
            error_log("API Exception: " . $e->getMessage());
            return null;
        }
    }
    
    public function getDashboardData($data) {
        $result = $this->makeRequest("/dashboard-completo/" . $data);
        
        // Se falhar, busca dados individualmente
        if (!$result || empty($result['faturamento'])) {
            $result = $this->fetchIndividualData($data);
        }
        
        return $result;
    }
    
    private function fetchIndividualData($data) {
        $individualData = [];
        
        $endpoints = [
            'faturamento' => "/faturamento/" . $data,
            'faturamento_dia' => "/faturamento-dia/" . $data,
            'contas_pagas' => "/contas-pagas/" . $data,
            'custos_administrativos_anual' => "/custos-administrativos-anual",
            'custos_administrativos_mensal' => "/custos-administrativos-mensal",
            'descontos' => "/descontos/" . $data,
            'devolucoes' => "/devolucoes/" . $data,
            'estornos' => "/estornos/" . $data,
            'contas_receber_programado' => "/contas-receber-programado",
            'contas_pagar_programado' => "/contas-pagar-programado"
        ];
        
        foreach ($endpoints as $key => $endpoint) {
            $response = $this->makeRequest($endpoint);
            
            // Tratar valores nulos/vazios
            if ($response === null || $response === '') {
                $individualData[$key] = $this->getDefaultValue($key);
            } else {
                $individualData[$key] = $response;
            }
        }
        
        return $individualData;
    }
    
    private function getDefaultValue($key) {
        $defaults = [
            'faturamento' => ['Faturamento' => 0],
            'faturamento_dia' => ['Faturamento' => 0],
            'contas_pagas' => ['ContasPagas' => 0],
            'custos_administrativos_anual' => ['Porc_Administrativo' => 0],
            'custos_administrativos_mensal' => ['Porc_Administrativo' => 0],
            'descontos' => ['Desconto' => 0],
            'devolucoes' => ['Devolucao' => 0],
            'estornos' => ['Estorno' => 0],
            'contas_receber_programado' => ['ValorTotal' => 0],
            'contas_pagar_programado' => ['ValorTotal' => 0]
        ];
        
        return $defaults[$key] ?? ['value' => 0];
    }
    
    public function getHealth() {
        return $this->makeRequest("/health");
    }
}
?>