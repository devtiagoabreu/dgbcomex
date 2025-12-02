// JavaScript para funcionalidades adicionais do dashboard

class Dashboard {
    constructor() {
        this.init();
    }

    init() {
        this.initTooltips();
        this.initAutoRefresh();
        this.initSmoothScroll();
        this.initPerformanceCharts();
    }

    initTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    initAutoRefresh() {
        // Atualização automática já configurada no PHP
        console.log('Auto-refresh configurado para 5 minutos');
    }

    initSmoothScroll() {
        // Adiciona scroll suave para âncoras
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    }

    initPerformanceCharts() {
        // Inicializar gráficos de performance (pode ser expandido com Chart.js)
        console.log('Performance charts initialized');
    }

    // Método para atualizar dados em tempo real (futura implementação)
    async updateData() {
        try {
            const response = await fetch('/api/dashboard-data');
            const data = await response.json();
            this.updateKPICards(data);
        } catch (error) {
            console.error('Erro ao atualizar dados:', error);
        }
    }

    updateKPICards(data) {
        // Atualizar os valores dos cards KPI
        const cards = {
            'faturamento-mes': data.faturamento?.Faturamento,
            'faturamento-dia': data.faturamento_dia?.Faturamento,
            'contas-pagas': data.contas_pagas?.ContasPagas,
            'custos-adm-anual': data.custos_administrativos_anual?.Porc_Administrativo,
            'custos-adm-mensal': data.custos_administrativos_mensal?.Porc_Administrativo,
            'descontos': data.descontos?.Desconto,
            'estornos': data.estornos?.Estorno,
            'devolucoes': data.devolucoes?.Devolucao,
            'contas-receber': data.contas_receber_programado?.ValorTotal,
            'contas-pagar': data.contas_pagar_programado?.ValorTotal
        };

        for (const [cardId, value] of Object.entries(cards)) {
            const element = document.getElementById(cardId);
            if (element) {
                this.animateValueChange(element, value);
            }
        }

        // Atualizar timestamp
        document.getElementById('last-update').textContent = new Date().toLocaleTimeString();
    }

    animateValueChange(element, newValue) {
        // Animação suave para mudança de valores
        element.style.transform = 'scale(1.1)';
        setTimeout(() => {
            element.textContent = this.formatValue(newValue, element.id);
            element.style.transform = 'scale(1)';
        }, 150);
    }

    formatValue(value, cardId) {
        if (value === undefined || value === null) return 'N/A';
        
        if (cardId.includes('custos-adm')) {
            return (value * 100).toFixed(2) + '%';
        } else if (typeof value === 'number') {
            return 'R$ ' + value.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        
        return value;
    }
}

// Inicializar dashboard quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    window.dashboard = new Dashboard();
});

// Efeitos de hover nos cards
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.kpi-card');
    
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});

// Atualizar relógio em tempo real
function updateClock() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('pt-BR');
    const dateString = now.toLocaleDateString('pt-BR');
    
    const clockElements = document.querySelectorAll('.status-info span:last-child');
    clockElements.forEach(element => {
        if (element.querySelector('.fa-clock')) {
            element.innerHTML = `<i class="fas fa-clock me-1"></i>${timeString}`;
        }
        if (element.querySelector('.fa-calendar')) {
            element.innerHTML = `<i class="fas fa-calendar me-1"></i>${dateString}`;
        }
    });
}

setInterval(updateClock, 1000);