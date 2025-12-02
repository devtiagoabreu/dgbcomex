<?php
require_once 'config.php';
require_once 'api_client.php';

// Inicializar cliente da API
$api_client = new ApiClient(API_BASE_URL);

// Funções auxiliares corrigidas
function formatarMoeda($valor) {
    if ($valor === null || $valor === '') return 'R$ 0,00';
    return 'R$ ' . number_format(floatval($valor), 2, ',', '.');
}

function formatarPorcentagem($valor) {
    if ($valor === null || $valor === '') return '0,00%';
    return number_format(floatval($valor) * 100, 2, ',', '.') . '%';
}

// Buscar dados
$dashboard_data = $api_client->getDashboardData($DATA_ATUAL);
$health_status = $api_client->getHealth();

// Garantir que todos os campos existam
$dashboard_data = array_merge([
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
], $dashboard_data ?: []);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #34495e;
            --success: #27ae60;
            --info: #3498db;
            --warning: #f39c12;
            --danger: #e74c3c;
            --purple: #9b59b6;
            --teal: #1abc9c;
            --orange: #e67e22;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        
        .dashboard-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .kpi-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 25px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }
        
        .kpi-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            opacity: 0.8;
        }
        
        .kpi-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .kpi-label {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 10px;
        }
        
        .kpi-trend {
            font-size: 0.8rem;
            opacity: 0.8;
        }
        
        .status-bar {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 15px 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            margin-bottom: 2rem;
        }
        
        .dashboard-footer {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            margin-top: 3rem;
            padding: 2rem 0;
        }
        
        .text-primary { color: var(--primary) !important; }
        .text-success { color: var(--success) !important; }
        .text-info { color: var(--info) !important; }
        .text-warning { color: var(--warning) !important; }
        .text-danger { color: var(--danger) !important; }
        .text-secondary { color: var(--secondary) !important; }
        .text-dark { color: var(--dark) !important; }
        .text-purple { color: var(--purple) !important; }
        .text-teal { color: var(--teal) !important; }
        .text-orange { color: var(--orange) !important; }
        
        @media (max-width: 768px) {
            .kpi-card {
                padding: 20px;
            }
            
            .kpi-value {
                font-size: 1.5rem;
            }
            
            .kpi-icon {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="dashboard-header">
        <nav class="navbar navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <i class="fas fa-chart-line me-2"></i>
                    <?php echo SITE_NAME; ?>
                </a>
                <span class="navbar-text">
                    <i class="fas fa-calendar me-1"></i>
                    <?php echo date('d/m/Y'); ?>
                </span>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="container-fluid py-4">
        <!-- Status Bar -->
        <div class="row">
            <div class="col-12">
                <div class="status-bar">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle me-1"></i>
                                Sistema Online
                            </span>
                            <span class="ms-3">
                                <i class="fas fa-clock me-1"></i>
                                <?php echo date('H:i:s'); ?>
                            </span>
                        </div>
                        <button class="btn btn-outline-light btn-sm" onclick="location.reload()">
                            <i class="fas fa-sync-alt me-1"></i>Atualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="row g-4">
            <!-- Faturamento Mês -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="kpi-card">
                    <div class="kpi-icon text-primary">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="kpi-content">
                        <h3 class="kpi-value text-primary">
                            <?php echo formatarMoeda($dashboard_data['faturamento']['Faturamento']); ?>
                        </h3>
                        <p class="kpi-label">Faturamento | Mês</p>
                        <div class="kpi-trend text-success">
                            <i class="fas fa-arrow-up me-1"></i>
                            <small>Meta: R$ 4.000.000,00 | Batemos: 
                                <?php 
                                    $faturamento = $dashboard_data['faturamento']['Faturamento'];
                                    $meta = 4000000;
                                    $porcentagem = ($faturamento / $meta) * 100;
                                    echo number_format($porcentagem, 2, ',', '.') . '% da Meta';
                                ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Faturamento Dia -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="kpi-card">
                    <div class="kpi-icon text-info">
                        <i class="fas fa-sun"></i>
                    </div>
                    <div class="kpi-content">
                        <h3 class="kpi-value text-info">
                            <?php echo formatarMoeda($dashboard_data['faturamento_dia']['Faturamento']); ?>
                        </h3>
                        <p class="kpi-label">Faturamento | Hoje</p>
                        <div class="kpi-trend text-success">
                            <i class="fas fa-arrow-up me-1"></i>
                            <small>
                                    <?php 
                                        $faturamento_dia = $dashboard_data['faturamento_dia']['Faturamento'];
                                        $faturamento_mes = $dashboard_data['faturamento']['Faturamento'];
                                        $porcentagem = ($faturamento_dia / $faturamento_mes) * 100;
                                        echo number_format($porcentagem, 2, ',', '.') . '%';
                                    ?> do mês
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contas Pagas -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="kpi-card">
                    <div class="kpi-icon text-success">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="kpi-content">
                        <h3 class="kpi-value text-success">
                            <?php echo formatarMoeda($dashboard_data['contas_pagas']['ContasPagas']); ?>
                        </h3>
                        <p class="kpi-label">Contas Pagas</p>
                        <div class="kpi-trend text-success">
                            <i class="fas fa-check-circle me-1"></i>
                            <small>Em dia</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Custos Administrativos Anual -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="kpi-card">
                    <div class="kpi-icon text-warning">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div class="kpi-content">
                        <h3 class="kpi-value text-warning">
                            <?php echo formatarPorcentagem($dashboard_data['custos_administrativos_anual']['Porc_Administrativo']); ?>
                        </h3>
                        <p class="kpi-label">Custos Adm. | Anual</p>
                        <div class="kpi-trend text-muted">
                            <i class="fas fa-chart-line me-1"></i>
                            <small>12 meses</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Custos Administrativos Mensal -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="kpi-card">
                    <div class="kpi-icon text-secondary">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="kpi-content">
                        <h3 class="kpi-value text-secondary">
                            <?php echo formatarPorcentagem($dashboard_data['custos_administrativos_mensal']['Porc_Administrativo']); ?>
                        </h3>
                        <p class="kpi-label">Custos Adm. | Mensal</p>
                        <div class="kpi-trend text-muted">
                            <i class="fas fa-calendar-alt me-1"></i>
                            <small>Este mês</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Descontos -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="kpi-card">
                    <div class="kpi-icon text-danger">
                        <i class="fas fa-tag"></i>
                    </div>
                    <div class="kpi-content">
                        <h3 class="kpi-value text-danger">
                            <?php echo formatarMoeda($dashboard_data['descontos']['Desconto']); ?>
                        </h3>
                        <p class="kpi-label">Descontos Concedidos</p>
                        <div class="kpi-trend text-muted">
                            <i class="fas fa-percentage me-1"></i>
                            <small>Total acumulado</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estornos -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="kpi-card">
                    <div class="kpi-icon text-dark">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div class="kpi-content">
                        <h3 class="kpi-value text-dark">
                            <?php echo formatarMoeda($dashboard_data['estornos']['Estorno']); ?>
                        </h3>
                        <p class="kpi-label">Estornos Realizados</p>
                        <div class="kpi-trend text-muted">
                            <i class="fas fa-redo me-1"></i>
                            <small>Transações revertidas</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Devoluções -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="kpi-card">
                    <div class="kpi-icon text-purple">
                        <i class="fas fa-undo"></i>
                    </div>
                    <div class="kpi-content">
                        <h3 class="kpi-value text-purple">
                            <?php echo formatarMoeda($dashboard_data['devolucoes']['Devolucao']); ?>
                        </h3>
                        <p class="kpi-label">Devoluções</p>
                        <div class="kpi-trend text-muted">
                            <i class="fas fa-box-open me-1"></i>
                            <small>Produtos devolvidos</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contas a Receber -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="kpi-card">
                    <div class="kpi-icon text-teal">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <div class="kpi-content">
                        <h3 class="kpi-value text-teal">
                            <?php echo formatarMoeda($dashboard_data['contas_receber_programado']['ValorTotal']); ?>
                        </h3>
                        <p class="kpi-label">A Receber | Programado</p>
                        <div class="kpi-trend text-muted">
                            <i class="fas fa-arrow-down me-1"></i>
                            <small>Entradas futuras</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contas a Pagar -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="kpi-card">
                    <div class="kpi-icon text-orange">
                        <i class="fas fa-money-check-alt"></i>
                    </div>
                    <div class="kpi-content">
                        <h3 class="kpi-value text-orange">
                            <?php echo formatarMoeda($dashboard_data['contas_pagar_programado']['ValorTotal']); ?>
                        </h3>
                        <p class="kpi-label">A Pagar | Programado</p>
                        <div class="kpi-trend text-muted">
                            <i class="fas fa-arrow-up me-1"></i>
                            <small>Saídas futuras</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumo Financeiro -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="kpi-card">
                    <h4 class="mb-4"><i class="fas fa-balance-scale me-2"></i>Resumo Financeiro</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">Saldo Disponível:</span>
                                <strong class="text-success">
                                    <?php
                                    $receber = $dashboard_data['contas_receber_programado']['ValorTotal'] ?? 0;
                                    $pagar = $dashboard_data['contas_pagar_programado']['ValorTotal'] ?? 0;
                                    $saldo = $receber - $pagar;
                                    echo formatarMoeda($saldo);
                                    ?>
                                </strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">Performance do Mês:</span>
                                <strong class="text-info">
					<?php 
                                            $faturamento = $dashboard_data['faturamento']['Faturamento'];
                                            $meta = 4000000;
                                            $porcentagem = ($faturamento / $meta) * 100;
                                            echo number_format($porcentagem, 2, ',', '.') . '%';
                                    	?>
				</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2024 DGB COMEX. Todos os direitos reservados.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        <i class="fas fa-database me-1"></i>
                        Última atualização: <span id="last-update"><?php echo date('H:i:s'); ?></span>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Atualização automática
        setInterval(function() {
            location.reload();
        }, <?php echo REFRESH_TIME; ?>);

        // Atualizar relógio em tempo real
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('pt-BR');
            document.getElementById('last-update').textContent = timeString;
        }
        
        setInterval(updateClock, 1000);
    </script>
</body>
</html>