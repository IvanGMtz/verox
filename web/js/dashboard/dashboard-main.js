let mapaVentas = null;
let heatmapLayer = null;
let chartCategorias = null;

function inicializarDashboard() {
    inicializarSelectores();
    inicializarMapa();
    cargarDashboard();
}

function inicializarSelectores() {
    const anioSelector = document.getElementById('anio_selector');
    const mesSelector = document.getElementById('mes_selector');

    const anioActual = new Date().getFullYear();
    for (let i = 0; i <= 4; i++) {
        const option = document.createElement('option');
        option.value = anioActual - i;
        option.textContent = anioActual - i;
        anioSelector.appendChild(option);
    }

    const mesActual = new Date().getMonth() + 1;
    mesSelector.value = mesActual;
    anioSelector.value = anioActual;
    
    actualizarPeriodoActual();
}

function actualizarPeriodoActual() {
    const mes = document.getElementById('mes_selector').value;
    const anio = document.getElementById('anio_selector').value;
    const meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                   'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    document.getElementById('periodo_actual').textContent = `${meses[mes]} ${anio}`;
}

function inicializarMapa() {
    mapaVentas = L.map('mapa_ventas').setView([37.0902, -95.7129], 4);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18
    }).addTo(mapaVentas);
}

function cargarDashboard() {
    const mes = document.getElementById('mes_selector').value;
    const anio = document.getElementById('anio_selector').value;
    
    actualizarPeriodoActual();
    mostrarCargando();
    
    // Realizar petición AJAX
    fetch(baseUrl + '/dashboard/data', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `mes=${mes}&anio=${anio}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            actualizarMetricas(data.metricas);
            actualizarTopProductos(data.top_productos);
            actualizarChartCategorias(data.top_categorias);
            actualizarTablaDepartamentos(data.ventas_departamento);
            actualizarMapaCalor(data.coordenadas_ventas, data.ventas_estado);
        } else {
            mostrarError(data.error || 'Error al cargar los datos');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarError('Error al conectar con el servidor');
    });
}

function actualizarMetricas(metricas) {
    document.getElementById('total_ventas').textContent = metricas.total_ventas.toLocaleString();
    document.getElementById('total_ordenes').textContent = metricas.total_ordenes.toLocaleString();
    document.getElementById('total_productos').textContent = metricas.total_productos.toLocaleString();
    document.getElementById('total_dinero').textContent = '$' + metricas.total_dinero.toLocaleString('es-CO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function actualizarTopProductos(productos) {
    const container = document.getElementById('top_productos');
    
    if (!productos || productos.length === 0) {
        container.innerHTML = `
            <div class="list-group-item text-center text-muted">
                <i class="fas fa-inbox"></i>
                <p class="mt-2 mb-0">No hay datos disponibles</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = '';

    const totalMaximo = productos[0].total || 1;
    
    productos.forEach((producto, index) => {
        const porcentaje = (producto.total / totalMaximo * 100).toFixed(1);
        const badgeClass = index === 0 ? 'bg-warning' : index === 1 ? 'bg-secondary' : index === 2 ? 'bg-danger' : 'bg-info';
        
        const item = document.createElement('div');
        item.className = 'list-group-item';
        item.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center">
                    <span class="badge ${badgeClass} text-white badge-ranking mr-2">${index + 1}</span>
                    <div>
                        <strong>${producto.nombre}</strong>
                        <br>
                        <small class="text-muted">Ref: ${producto.referencia}</small>
                    </div>
                </div>
                <div class="text-right">
                    <strong>$${producto.total.toLocaleString('es-CO', {maximumFractionDigits: 0})}</strong>
                    <br>
                    <small class="text-muted">${Math.round(producto.cantidad)} und.</small>
                </div>
            </div>
            <div class="progress">
                <div class="progress-bar ${badgeClass}" role="progressbar" 
                     style="width: ${porcentaje}%" 
                     aria-valuenow="${porcentaje}" aria-valuemin="0" aria-valuemax="100">
                    ${porcentaje}%
                </div>
            </div>
        `;
        container.appendChild(item);
    });
}

function actualizarChartCategorias(categorias) {
    const canvas = document.getElementById('chart_categorias');
    const ctx = canvas.getContext('2d');

    if (chartCategorias) {
        chartCategorias.destroy();
    }
    
    if (!categorias || categorias.length === 0) {
        ctx.font = '14px Arial';
        ctx.fillStyle = '#6c757d';
        ctx.textAlign = 'center';
        ctx.fillText('No hay datos disponibles', canvas.width / 2, canvas.height / 2);
        return;
    }
    
    const labels = categorias.map(c => c.nombre);
    const data = categorias.map(c => c.total);
    const colores = [
        'rgba(54, 162, 235, 0.8)',
        'rgba(255, 99, 132, 0.8)',
        'rgba(255, 206, 86, 0.8)',
        'rgba(75, 192, 192, 0.8)',
        'rgba(153, 102, 255, 0.8)'
    ];
    
    chartCategorias = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colores,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            return `${label}: $${value.toLocaleString('es-CO', {maximumFractionDigits: 0})}`;
                        }
                    }
                }
            }
        }
    });
}

function actualizarTablaDepartamentos(departamentos) {
    const tbody = document.getElementById('tabla_departamentos');
    
    if (!departamentos || Object.keys(departamentos).length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center text-muted">
                    <i class="fas fa-inbox"></i> No hay datos disponibles
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = '';

    const departamentosArray = Object.entries(departamentos).map(([nombre, data]) => ({
        nombre: nombre,
        total: data.total,
        ordenes: data.ordenes
    }));
    
    departamentosArray.forEach((dept, index) => {
        const badgeClass = index === 0 ? 'bg-warning' : index === 1 ? 'bg-secondary' : index === 2 ? 'bg-danger' : 'bg-light text-dark';
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="align-middle">
                <span class="badge ${badgeClass} badge-ranking">${index + 1}</span>
            </td>
            <td class="align-middle">
                <strong>${dept.nombre}</strong>
            </td>
            <td class="text-center align-middle">
                <span class="badge badge-primary">${dept.ordenes}</span>
            </td>
            <td class="text-right align-middle">
                <strong>$${dept.total.toLocaleString('es-CO', {maximumFractionDigits: 0})}</strong>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function actualizarMapaCalor(coordenadas, ventasEstado) {
    if (heatmapLayer) {
        mapaVentas.removeLayer(heatmapLayer);
    }

    mapaVentas.eachLayer(layer => {
        if (layer instanceof L.CircleMarker) {
            mapaVentas.removeLayer(layer);
        }
    });
    
    if (!coordenadas || coordenadas.length === 0) {
        if (ventasEstado && Object.keys(ventasEstado).length > 0) {
            agregarMarcadoresEstados(ventasEstado);
        }
        return;
    }

    const montoMaximo = Math.max(...coordenadas.map(c => c.monto));

    coordenadas.forEach(punto => {
        const radio = Math.sqrt(punto.monto / montoMaximo) * 30 + 5;
        
        const marker = L.circleMarker([punto.lat, punto.lng], {
            radius: radio,
            fillColor: '#ff6b6b',
            color: '#c92a2a',
            weight: 2,
            opacity: 0.8,
            fillOpacity: 0.5
        }).addTo(mapaVentas);
        
        marker.bindPopup(`
            <strong>${punto.estado || 'Ubicación'}</strong><br>
            Ventas: $${punto.monto.toLocaleString('es-CO', {maximumFractionDigits: 0})}
        `);
    });

    if (coordenadas.length > 0) {
        const bounds = L.latLngBounds(coordenadas.map(c => [c.lat, c.lng]));
        mapaVentas.fitBounds(bounds, { padding: [50, 50] });
    }
}

function agregarMarcadoresEstados(ventasEstado) {
    const coordenadasEstados = {
        'California': [36.7783, -119.4179],
        'Texas': [31.9686, -99.9018],
        'Florida': [27.6648, -81.5158],
        'New York': [43.2994, -74.2179],
        'Illinois': [40.6331, -89.3985],
        'Pennsylvania': [41.2033, -77.1945],
        'Ohio': [40.4173, -82.9071],
        'Georgia': [32.1656, -82.9001],
        'North Carolina': [35.7596, -79.0193],
        'Michigan': [44.3148, -85.6024]
    };
    
    const montoMaximo = Math.max(...Object.values(ventasEstado).map(v => v.total));
    
    Object.entries(ventasEstado).forEach(([estado, data]) => {
        const coords = coordenadasEstados[estado];
        if (coords) {
            const radio = Math.sqrt(data.total / montoMaximo) * 30 + 10;
            
            const marker = L.circleMarker(coords, {
                radius: radio,
                fillColor: '#4dabf7',
                color: '#1971c2',
                weight: 2,
                opacity: 0.8,
                fillOpacity: 0.5
            }).addTo(mapaVentas);
            
            marker.bindPopup(`
                <strong>${estado}</strong><br>
                Órdenes: ${data.ordenes}<br>
                Total: $${data.total.toLocaleString('es-CO', {maximumFractionDigits: 0})}
            `);
        }
    });
}

function mostrarCargando() {
    document.getElementById('total_ventas').innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    document.getElementById('total_ordenes').innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    document.getElementById('total_productos').innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    document.getElementById('total_dinero').innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
}

function mostrarError(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: mensaje,
        confirmButtonColor: '#3085d6'
    });
}