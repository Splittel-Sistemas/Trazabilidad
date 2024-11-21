
    // Gráfico de Producción Mensual
    const ctxProduccion = document.getElementById('produccionMensualChart').getContext('2d');
    new Chart(ctxProduccion, {
        type: 'bar',
        data: {
            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre'],
            datasets: [{
                label: 'Producción (KM)',
                data: [180, 200, 190, 220, 250, 230, 240, 260, 210, 200, 250],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
        }
    });

    // Gráfico de Pedidos por Estado
    const ctxPedidos = document.getElementById('pedidosEstadoChart').getContext('2d');
    new Chart(ctxPedidos, {
        type: 'pie',
        data: {
            labels: ['Entregados', 'En Proceso', 'Cancelados'],
            datasets: [{
                data: [50, 30, 5],
                backgroundColor: ['#28a745', '#ffc107', '#dc3545']
            }]
        },
        options: {
            responsive: true,
        }
    });
