/**
 * reporte-export.js
 * Funciones corregidas y completas para exportar reportes detallados a Excel
 */

// Variables globales que deben ser llenadas desde el controlador
var reporteData = reporteData || [];
var facturasPendientes = facturasPendientes || [];

function exportarExcel(type, fn, dl) {
  var wb = XLSX.utils.book_new();
  let rtipo = document.getElementById("reporteTipo").value;
  let start_Date = document.getElementById("start").value;
  let stop_Date = document.getElementById("stop").value;
  let asesor = document.getElementById("asesores2") ? document.getElementById("asesores2").value : 'TODOS';
  
  // Hoja 1: Resumen Ejecutivo
  crearHojaResumenEjecutivo(wb, rtipo, start_Date, stop_Date, asesor);
  
  // Hojas según tipo de reporte
  if (rtipo === "Total" || rtipo === "Asesor") {
    crearHojaTransaccionesDetalladas(wb);
    crearHojaClientesNuevos(wb);
    crearHojaClientesRecurrentes(wb);
    crearHojaFacturasPendientes(wb);
    crearHojaEstadisticasPorAsesor(wb);
    crearHojaProductosVendidos(wb);
  } else if (rtipo === "Categoria") {
    let tag = document.getElementById("Categoria").value;
    crearHojaProductosPorCategoria(wb, tag);
    crearHojaResumenCategoria(wb, tag);
  } else if (rtipo === "Top") {
    crearHojaTopProductos(wb);
    crearHojaAnalisisTop(wb);
  }
  
  // Nombre del archivo
  let tag = (rtipo === "Total" || rtipo === "Top") ? "" : document.getElementById(rtipo).value;
  let filename = 'Reporte_' + rtipo + 
    (asesor && asesor !== 'TODOS' ? '_' + asesor : '') + 
    (tag !== "" ? "_" + tag : "") + 
    '_' + start_Date + '_a_' + stop_Date + '.' + (type || 'xlsx');
  
  return dl ?
    XLSX.write(wb, { bookType: type, bookSST: true, type: 'base64' }) :
    XLSX.writeFile(wb, fn || filename);
}

// ==================== FUNCIÓN AUXILIAR: CALCULAR MÉTRICAS ====================
function calcularMetricasPrincipales() {
  let metricas = {
    totalTransacciones: 0,
    totalUnidades: 0,
    totalEfectivo: 0,
    totalEnvios: 0,
    granTotal: 0,
    ticketPromedio: 0,
    unidadesPorTransaccion: 0,
    valorPromedioUnidad: 0,
    cantPagosCompletos: 0,
    montoPagosCompletos: 0,
    cantAbonos: 0,
    montoAbonos: 0,
    cantPagosRestantes: 0,
    montoPagosRestantes: 0,
    clientesNuevosUnicos: 0,
    transaccionesNuevos: 0,
    efectivoNuevos: 0,
    ticketPromedioNuevos: 0
  };

  let clientesNuevosSet = new Set();

  reporteData.forEach(transaccion => {
    metricas.totalTransacciones++;
    
    let montoTransaccion = parseFloat(transaccion.monto_transaccion || 0);
    let costoEnvio = parseFloat(transaccion.shipment_cost || 0);
    
    metricas.totalEfectivo += montoTransaccion;
    metricas.totalEnvios += costoEnvio;
    metricas.granTotal += (montoTransaccion + costoEnvio);
    
    // Contar por tipo de transacción
    if (transaccion.tipo_transaccion === 'Pago Completo') {
      metricas.cantPagosCompletos++;
      metricas.montoPagosCompletos += montoTransaccion;
    } else if (transaccion.tipo_transaccion === 'Abono 1' || transaccion.tipo_transaccion === 'Abono 2') {
      metricas.cantAbonos++;
      metricas.montoAbonos += montoTransaccion;
    } else if (transaccion.tipo_transaccion === 'Pago Restante') {
      metricas.cantPagosRestantes++;
      metricas.montoPagosRestantes += montoTransaccion;
    }
    
    // Contar unidades
    if (transaccion.items) {
      transaccion.items.forEach(item => {
        metricas.totalUnidades += parseFloat(item.product_qty || 0);
      });
    }
    
    // Clientes nuevos
    if (transaccion.client_type === 'Nuevo' && transaccion.cliente_id) {
      clientesNuevosSet.add(transaccion.cliente_id);
      metricas.transaccionesNuevos++;
      metricas.efectivoNuevos += montoTransaccion;
    }
  });

  metricas.clientesNuevosUnicos = clientesNuevosSet.size;
  metricas.ticketPromedio = metricas.totalTransacciones > 0 ? 
    metricas.totalEfectivo / metricas.totalTransacciones : 0;
  metricas.unidadesPorTransaccion = metricas.totalTransacciones > 0 ? 
    metricas.totalUnidades / metricas.totalTransacciones : 0;
  metricas.valorPromedioUnidad = metricas.totalUnidades > 0 ? 
    metricas.totalEfectivo / metricas.totalUnidades : 0;
  metricas.ticketPromedioNuevos = metricas.transaccionesNuevos > 0 ? 
    metricas.efectivoNuevos / metricas.transaccionesNuevos : 0;

  return metricas;
}

// ==================== HOJA 1: RESUMEN EJECUTIVO ====================
function crearHojaResumenEjecutivo(wb, rtipo, startDate, stopDate, asesor) {
  let data = [];
  
  // Encabezado
  data.push(['RESUMEN EJECUTIVO']);
  data.push([]);
  data.push(['Tipo de Reporte:', rtipo]);
  data.push(['Período:', startDate + ' a ' + stopDate]);
  if (asesor && asesor !== 'TODOS') {
    data.push(['Asesor:', asesor]);
  }
  data.push(['Fecha de Generación:', new Date().toLocaleString('es-ES')]);
  data.push([]);
  
  // Métricas principales
  let metricas = calcularMetricasPrincipales();
  
  data.push(['MÉTRICAS PRINCIPALES']);
  data.push(['Métrica', 'Valor']);
  data.push(['Total Transacciones', metricas.totalTransacciones]);
  data.push(['Total Unidades Vendidas', metricas.totalUnidades.toFixed(2)]);
  data.push(['Total Efectivo Recibido', '$' + metricas.totalEfectivo.toFixed(2)]);
  data.push(['Total Costos de Envío', '$' + metricas.totalEnvios.toFixed(2)]);
  data.push(['Gran Total', '$' + metricas.granTotal.toFixed(2)]);
  data.push([]);
  
  // KPIs
  data.push(['INDICADORES CLAVE (KPIs)']);
  data.push(['Indicador', 'Valor']);
  data.push(['Ticket Promedio', '$' + metricas.ticketPromedio.toFixed(2)]);
  data.push(['Unidades por Transacción', metricas.unidadesPorTransaccion.toFixed(2)]);
  data.push(['Valor Promedio por Unidad', '$' + metricas.valorPromedioUnidad.toFixed(2)]);
  data.push([]);
  
  // Desglose de transacciones
  data.push(['DESGLOSE DE TRANSACCIONES']);
  data.push(['Tipo de Pago', 'Cantidad', 'Monto Total']);
  data.push(['Pagos Completos', metricas.cantPagosCompletos, '$' + metricas.montoPagosCompletos.toFixed(2)]);
  data.push(['Abonos', metricas.cantAbonos, '$' + metricas.montoAbonos.toFixed(2)]);
  data.push(['Pagos Restantes', metricas.cantPagosRestantes, '$' + metricas.montoPagosRestantes.toFixed(2)]);
  data.push([]);
  
  // Clientes nuevos
  if (metricas.clientesNuevosUnicos > 0) {
    data.push(['CLIENTES NUEVOS']);
    data.push(['Métrica', 'Valor']);
    data.push(['Clientes Únicos Nuevos', metricas.clientesNuevosUnicos]);
    data.push(['Transacciones de Nuevos', metricas.transaccionesNuevos]);
    data.push(['Efectivo de Nuevos', '$' + metricas.efectivoNuevos.toFixed(2)]);
    data.push(['Ticket Promedio Nuevos', '$' + metricas.ticketPromedioNuevos.toFixed(2)]);
  }
  
  var ws = XLSX.utils.aoa_to_sheet(data);
  
  // Estilos y anchos de columna
  ws['!cols'] = [
    {wch: 35},
    {wch: 20},
    {wch: 20}
  ];
  
  XLSX.utils.book_append_sheet(wb, ws, "Resumen Ejecutivo");
}

// ==================== HOJA 2: TRANSACCIONES DETALLADAS ====================
function crearHojaTransaccionesDetalladas(wb) {
  let data = [];
  
  // Encabezados
  data.push([
    'Order ID',
    'Fecha',
    'Tipo Transacción',
    'Tipo Cliente',
    'Estado Cliente',
    'Cliente ID',
    'Cliente Nombre',
    'Cliente Apellidos',
    'Email',
    'Teléfono',
    'Asesor',
    'Monto Transacción',
    'Costo Envío',
    'Total Factura',
    'Estado Pago',
    'Total Abonos',
    'Saldo Pendiente',
    'Cantidad Items'
  ]);
  
  // Datos
  reporteData.forEach(function(transaccion) {
    data.push([
      transaccion.original_id || transaccion.id,
      transaccion.date,
      transaccion.tipo_transaccion,
      transaccion.client_type || '',
      transaccion.client_state || '',
      transaccion.cliente_id || '',
      transaccion.cliente_nombre || '',
      transaccion.cliente_apellidos || '',
      transaccion.cliente_email || '',
      transaccion.cliente_telefono || '',
      transaccion.asesor || '',
      parseFloat(transaccion.monto_transaccion || 0).toFixed(2),
      parseFloat(transaccion.shipment_cost || 0).toFixed(2),
      parseFloat(transaccion.price || 0).toFixed(2),
      transaccion.estado_pago || '',
      parseFloat(transaccion.total_abonos || 0).toFixed(2),
      parseFloat(transaccion.saldo_pendiente || 0).toFixed(2),
      transaccion.items ? transaccion.items.length : 0
    ]);
  });
  
  // Totales
  let totalMonto = 0;
  let totalEnvio = 0;
  let totalFactura = 0;
  reporteData.forEach(t => {
    totalMonto += parseFloat(t.monto_transaccion || 0);
    totalEnvio += parseFloat(t.shipment_cost || 0);
    totalFactura += parseFloat(t.price || 0);
  });
  
  data.push([]);
  data.push([
    'TOTALES',
    '',
    '',
    '',
    '',
    '',
    '',
    '',
    '',
    '',
    '',
    totalMonto.toFixed(2),
    totalEnvio.toFixed(2),
    totalFactura.toFixed(2),
    '',
    '',
    '',
    ''
  ]);
  
  var ws = XLSX.utils.aoa_to_sheet(data);
  
  ws['!cols'] = [
    {wch: 12}, {wch: 12}, {wch: 18}, {wch: 12}, {wch: 15},
    {wch: 12}, {wch: 20}, {wch: 20}, {wch: 30}, {wch: 15},
    {wch: 20}, {wch: 15}, {wch: 12}, {wch: 15}, {wch: 15},
    {wch: 12}, {wch: 15}, {wch: 12}
  ];
  
  XLSX.utils.book_append_sheet(wb, ws, "Transacciones Detalladas");
}

// ==================== HOJA 3: CLIENTES NUEVOS ====================
function crearHojaClientesNuevos(wb) {
  var clientesNuevosUnicos = new Map();

  reporteData.forEach(function(transaccion) {
    if (transaccion.cliente_id && transaccion.client_type === 'Nuevo') {
      if (!clientesNuevosUnicos.has(transaccion.cliente_id)) {
        clientesNuevosUnicos.set(transaccion.cliente_id, {
          'Cliente ID': transaccion.cliente_id,
          'Nombre Completo': (transaccion.cliente_nombre || '') + ' ' + (transaccion.cliente_apellidos || ''),
          'Email': transaccion.cliente_email || '',
          'Teléfono': transaccion.cliente_telefono || '',
          'Asesor': transaccion.asesor || '',
          'Estado': transaccion.client_state || '',
          'Primera Compra': transaccion.date,
          'Total Gastado': 0,
          'Transacciones': 0,
          'Unidades Compradas': 0,
          'Ticket Promedio': 0
        });
      }
      var cliente = clientesNuevosUnicos.get(transaccion.cliente_id);
      cliente['Total Gastado'] += parseFloat(transaccion.monto_transaccion) || 0;
      cliente['Transacciones'] += 1;
      
      if (transaccion.items) {
        transaccion.items.forEach(item => {
          cliente['Unidades Compradas'] += parseFloat(item.product_qty || 0);
        });
      }
    }
  });

  var clientesData = [];
  clientesNuevosUnicos.forEach(function(cliente) {
    cliente['Ticket Promedio'] = cliente['Transacciones'] > 0 ? 
      (cliente['Total Gastado'] / cliente['Transacciones']).toFixed(2) : '0.00';
    cliente['Total Gastado'] = cliente['Total Gastado'].toFixed(2);
    cliente['Unidades Compradas'] = cliente['Unidades Compradas'].toFixed(0);
    clientesData.push(cliente);
  });

  clientesData.sort((a, b) => parseFloat(b['Total Gastado']) - parseFloat(a['Total Gastado']));

  if (clientesData.length > 0) {
    clientesData.forEach((cliente, index) => {
      cliente['Ranking'] = index + 1;
    });
    
    clientesData = clientesData.map(c => ({
      'Ranking': c['Ranking'],
      'Cliente ID': c['Cliente ID'],
      'Nombre Completo': c['Nombre Completo'],
      'Email': c['Email'],
      'Teléfono': c['Teléfono'],
      'Asesor': c['Asesor'],
      'Estado': c['Estado'],
      'Primera Compra': c['Primera Compra'],
      'Transacciones': c['Transacciones'],
      'Unidades Compradas': c['Unidades Compradas'],
      'Total Gastado': c['Total Gastado'],
      'Ticket Promedio': c['Ticket Promedio']
    }));
    
    var ws = XLSX.utils.json_to_sheet(clientesData);
    ws['!cols'] = [
      {wch: 8}, {wch: 12}, {wch: 30}, {wch: 35}, {wch: 15},
      {wch: 20}, {wch: 15}, {wch: 15}, {wch: 12}, {wch: 12},
      {wch: 15}, {wch: 15}
    ];
    
    XLSX.utils.book_append_sheet(wb, ws, "Clientes Nuevos");
  }
}

// ==================== HOJA 4: CLIENTES RECURRENTES ====================
function crearHojaClientesRecurrentes(wb) {
  var clientesRecurrentesUnicos = new Map();

  reporteData.forEach(function(transaccion) {
    if (transaccion.cliente_id && transaccion.client_type === 'Antiguo') {
      if (!clientesRecurrentesUnicos.has(transaccion.cliente_id)) {
        clientesRecurrentesUnicos.set(transaccion.cliente_id, {
          'Cliente ID': transaccion.cliente_id,
          'Nombre Completo': (transaccion.cliente_nombre || '') + ' ' + (transaccion.cliente_apellidos || ''),
          'Email': transaccion.cliente_email || '',
          'Teléfono': transaccion.cliente_telefono || '',
          'Asesor': transaccion.asesor || '',
          'Estado': transaccion.client_state || '',
          'Total Gastado (Período)': 0,
          'Transacciones (Período)': 0,
          'Unidades Compradas': 0,
          'Ticket Promedio': 0
        });
      }
      var cliente = clientesRecurrentesUnicos.get(transaccion.cliente_id);
      cliente['Total Gastado (Período)'] += parseFloat(transaccion.monto_transaccion) || 0;
      cliente['Transacciones (Período)'] += 1;
      
      if (transaccion.items) {
        transaccion.items.forEach(item => {
          cliente['Unidades Compradas'] += parseFloat(item.product_qty || 0);
        });
      }
    }
  });

  var clientesData = [];
  clientesRecurrentesUnicos.forEach(function(cliente) {
    cliente['Ticket Promedio'] = cliente['Transacciones (Período)'] > 0 ? 
      (cliente['Total Gastado (Período)'] / cliente['Transacciones (Período)']).toFixed(2) : '0.00';
    cliente['Total Gastado (Período)'] = cliente['Total Gastado (Período)'].toFixed(2);
    cliente['Unidades Compradas'] = cliente['Unidades Compradas'].toFixed(0);
    clientesData.push(cliente);
  });

  clientesData.sort((a, b) => parseFloat(b['Total Gastado (Período)']) - parseFloat(a['Total Gastado (Período)']));

  if (clientesData.length > 0) {
    clientesData.forEach((cliente, index) => {
      cliente['Ranking'] = index + 1;
    });
    
    clientesData = clientesData.map(c => ({
      'Ranking': c['Ranking'],
      'Cliente ID': c['Cliente ID'],
      'Nombre Completo': c['Nombre Completo'],
      'Email': c['Email'],
      'Teléfono': c['Teléfono'],
      'Asesor': c['Asesor'],
      'Estado': c['Estado'],
      'Transacciones (Período)': c['Transacciones (Período)'],
      'Unidades Compradas': c['Unidades Compradas'],
      'Total Gastado (Período)': c['Total Gastado (Período)'],
      'Ticket Promedio': c['Ticket Promedio']
    }));
    
    var ws = XLSX.utils.json_to_sheet(clientesData);
    ws['!cols'] = [
      {wch: 8}, {wch: 12}, {wch: 30}, {wch: 35}, {wch: 15},
      {wch: 20}, {wch: 15}, {wch: 12}, {wch: 12}, {wch: 18},
      {wch: 15}
    ];
    
    XLSX.utils.book_append_sheet(wb, ws, "Clientes Recurrentes");
  }
}

// ==================== HOJA 5: FACTURAS PENDIENTES ====================
function crearHojaFacturasPendientes(wb) {
  if (facturasPendientes && facturasPendientes.length > 0) {
    var data = [];
    
    data.push([
      'Ranking',
      'Order ID',
      'Cliente',
      'Asesor',
      'Total Factura',
      'Abonado',
      'Pendiente',
      '% Pagado',
      'Estado',
      'Fecha Creación'
    ]);
    
    let facturasPendientesOrdenadas = [...facturasPendientes].sort((a, b) => 
      parseFloat(b.pendiente || 0) - parseFloat(a.pendiente || 0)
    );
    
    facturasPendientesOrdenadas.forEach((factura, index) => {
      let total = parseFloat(factura.total || 0);
      let abonado = parseFloat(factura.abonado || 0);
      let pendiente = parseFloat(factura.pendiente || 0);
      let porcentajePagado = total > 0 ? ((abonado / total) * 100).toFixed(2) : '0.00';
      
      data.push([
        index + 1,
        factura.order_id || '',
        factura.cliente_nombre || '',
        factura.asesor || '',
        total.toFixed(2),
        abonado.toFixed(2),
        pendiente.toFixed(2),
        porcentajePagado + '%',
        factura.estado || 'Pendiente',
        factura.fecha_creacion || ''
      ]);
    });
    
    let totalFacturas = 0;
    let totalAbonado = 0;
    let totalPendiente = 0;
    facturasPendientes.forEach(f => {
      totalFacturas += parseFloat(f.total || 0);
      totalAbonado += parseFloat(f.abonado || 0);
      totalPendiente += parseFloat(f.pendiente || 0);
    });
    
    data.push([]);
    data.push([
      'TOTALES',
      '',
      '',
      '',
      totalFacturas.toFixed(2),
      totalAbonado.toFixed(2),
      totalPendiente.toFixed(2),
      totalFacturas > 0 ? ((totalAbonado / totalFacturas) * 100).toFixed(2) + '%' : '0.00%',
      '',
      ''
    ]);
    
    var ws = XLSX.utils.aoa_to_sheet(data);
    ws['!cols'] = [
      {wch: 8}, {wch: 12}, {wch: 30}, {wch: 20}, {wch: 15},
      {wch: 15}, {wch: 15}, {wch: 12}, {wch: 20}, {wch: 15}
    ];
    
    XLSX.utils.book_append_sheet(wb, ws, "Facturas Pendientes");
  }
}

// ==================== HOJA 6: ESTADÍSTICAS POR ASESOR ====================
function crearHojaEstadisticasPorAsesor(wb) {
  let asesoresMap = new Map();
  
  reporteData.forEach(transaccion => {
    let asesor = transaccion.asesor || 'Sin Asesor';
    
    if (!asesoresMap.has(asesor)) {
      asesoresMap.set(asesor, {
        'Asesor': asesor,
        'Transacciones': 0,
        'Unidades Vendidas': 0,
        'Total Efectivo': 0,
        'Ticket Promedio': 0,
        'Unidades por Transacción': 0,
        'Pagos Completos': 0,
        'Abonos': 0,
        'Clientes Únicos': new Set()
      });
    }
    
    let asesorData = asesoresMap.get(asesor);
    asesorData['Transacciones']++;
    asesorData['Total Efectivo'] += parseFloat(transaccion.monto_transaccion || 0);
    
    if (transaccion.tipo_transaccion === 'Pago Completo') {
      asesorData['Pagos Completos']++;
    } else if (transaccion.tipo_transaccion.includes('Abono')) {
      asesorData['Abonos']++;
    }
    
    if (transaccion.cliente_id) {
      asesorData['Clientes Únicos'].add(transaccion.cliente_id);
    }
    
    if (transaccion.items) {
      transaccion.items.forEach(item => {
        asesorData['Unidades Vendidas'] += parseFloat(item.product_qty || 0);
      });
    }
  });
  
  let asesoresData = [];
  asesoresMap.forEach((data, asesor) => {
    data['Ticket Promedio'] = data['Transacciones'] > 0 ? 
      (data['Total Efectivo'] / data['Transacciones']).toFixed(2) : '0.00';
    data['Unidades por Transacción'] = data['Transacciones'] > 0 ? 
      (data['Unidades Vendidas'] / data['Transacciones']).toFixed(2) : '0.00';
    data['Clientes Únicos'] = data['Clientes Únicos'].size;
    data['Total Efectivo'] = data['Total Efectivo'].toFixed(2);
    data['Unidades Vendidas'] = data['Unidades Vendidas'].toFixed(0);
    
    asesoresData.push(data);
  });
  
  asesoresData.sort((a, b) => parseFloat(b['Total Efectivo']) - parseFloat(a['Total Efectivo']));
  
  asesoresData.forEach((asesor, index) => {
    asesor['Ranking'] = index + 1;
  });
  
  asesoresData = asesoresData.map(a => ({
    'Ranking': a['Ranking'],
    'Asesor': a['Asesor'],
    'Transacciones': a['Transacciones'],
    'Clientes Únicos': a['Clientes Únicos'],
    'Unidades Vendidas': a['Unidades Vendidas'],
    'Total Efectivo': a['Total Efectivo'],
    'Ticket Promedio': a['Ticket Promedio'],
    'Unidades por Transacción': a['Unidades por Transacción'],
    'Pagos Completos': a['Pagos Completos'],
    'Abonos': a['Abonos']
  }));
  
  var ws = XLSX.utils.json_to_sheet(asesoresData);
  ws['!cols'] = [
    {wch: 8}, {wch: 25}, {wch: 15}, {wch: 15}, {wch: 15},
    {wch: 15}, {wch: 15}, {wch: 20}, {wch: 15}, {wch: 12}
  ];
  
  XLSX.utils.book_append_sheet(wb, ws, "Estadísticas por Asesor");
}

// ==================== HOJA 7: PRODUCTOS VENDIDOS ====================
function crearHojaProductosVendidos(wb) {
  let productosMap = new Map();
  
  reporteData.forEach(transaccion => {
    if (transaccion.items) {
      transaccion.items.forEach(item => {
        let key = item.product_ref + '_' + item.product_talla + '_' + item.product_color;
        
        if (!productosMap.has(key)) {
          productosMap.set(key, {
            'Categoría': item.Categoría || '',
            'Referencia': item.product_ref || '',
            'Talla': item.product_talla || '',
            'Color': item.product_color || '',
            'Nombre': item.product_name || '',
            'Cantidad Vendida': 0,
            'Valor Total': 0,
            'Precio Promedio': 0
          });
        }
        
        let producto = productosMap.get(key);
        producto['Cantidad Vendida'] += parseFloat(item.product_qty || 0);
        producto['Valor Total'] += parseFloat(item.product_price || 0) * parseFloat(item.product_qty || 0);
      });
    }
  });
  
  let productosData = [];
  productosMap.forEach(producto => {
    producto['Precio Promedio'] = producto['Cantidad Vendida'] > 0 ?
      (producto['Valor Total'] / producto['Cantidad Vendida']).toFixed(2) : '0.00';
    producto['Cantidad Vendida'] = producto['Cantidad Vendida'].toFixed(0);
    producto['Valor Total'] = producto['Valor Total'].toFixed(2);
    
    productosData.push(producto);
  });
  
  productosData.sort((a, b) => parseFloat(b['Cantidad Vendida']) - parseFloat(a['Cantidad Vendida']));
  
  productosData.forEach((producto, index) => {
    producto['Ranking'] = index + 1;
  });
  
  productosData = productosData.map(p => ({
    'Ranking': p['Ranking'],
    'Categoría': p['Categoría'],
    'Referencia': p['Referencia'],
    'Nombre': p['Nombre'],
    'Talla': p['Talla'],
    'Color': p['Color'],
    'Cantidad Vendida': p['Cantidad Vendida'],
    'Valor Total': p['Valor Total'],
    'Precio Promedio': p['Precio Promedio']
  }));
  
  var ws = XLSX.utils.json_to_sheet(productosData);
  ws['!cols'] = [
    {wch: 8}, {wch: 20}, {wch: 15}, {wch: 30}, {wch: 10},
    {wch: 15}, {wch: 15}, {wch: 15}, {wch: 15}
  ];
  
  XLSX.utils.book_append_sheet(wb, ws, "Productos Vendidos");
}

// ==================== HOJA 8: PRODUCTOS POR CATEGORÍA ====================
function crearHojaProductosPorCategoria(wb, categoria) {
  let productosMap = new Map();
  
  reporteData.forEach(transaccion => {
    if (transaccion.items) {
      transaccion.items.forEach(item => {
        if (item.Categoría === categoria) {
          let key = item.product_ref + '_' + item.product_talla + '_' + item.product_color;
          
          if (!productosMap.has(key)) {
            productosMap.set(key, {
              'Referencia': item.product_ref || '',
              'Nombre': item.product_name || '',
              'Talla': item.product_talla || '',
              'Color': item.product_color || '',
              'Cantidad Vendida': 0,
              'Valor Total': 0,
              'Precio Unitario': 0
            });
          }
          
          let producto = productosMap.get(key);
          producto['Cantidad Vendida'] += parseFloat(item.product_qty || 0);
          producto['Valor Total'] += parseFloat(item.product_price || 0) * parseFloat(item.product_qty || 0);
          producto['Precio Unitario'] = parseFloat(item.product_price || 0);
        }
      });
    }
  });
  
  let productosData = [];
  productosMap.forEach(producto => {
    producto['Cantidad Vendida'] = producto['Cantidad Vendida'].toFixed(0);
    producto['Valor Total'] = producto['Valor Total'].toFixed(2);
    producto['Precio Unitario'] = producto['Precio Unitario'].toFixed(2);
    
    productosData.push(producto);
  });
  
  productosData.sort((a, b) => parseFloat(b['Cantidad Vendida']) - parseFloat(a['Cantidad Vendida']));
  
  productosData.forEach((producto, index) => {
    producto['Ranking'] = index + 1;
  });
  
  productosData = productosData.map(p => ({
    'Ranking': p['Ranking'],
    'Referencia': p['Referencia'],
    'Nombre': p['Nombre'],
    'Talla': p['Talla'],
    'Color': p['Color'],
    'Cantidad Vendida': p['Cantidad Vendida'],
    'Valor Total': p['Valor Total'],
    'Precio Unitario': p['Precio Unitario']
  }));

  var ws = XLSX.utils.json_to_sheet(productosData);
  ws['!cols'] = [
    {wch: 8}, {wch: 15}, {wch: 30}, {wch: 10}, {wch: 15},
    {wch: 15}, {wch: 15}, {wch: 15}
  ];
  
  XLSX.utils.book_append_sheet(wb, ws, "Productos - " + categoria);
}

// ==================== HOJA 9: RESUMEN CATEGORÍA ====================
function crearHojaResumenCategoria(wb, categoria) {
  let data = [];
  
  data.push(['RESUMEN DE CATEGORÍA: ' + categoria]);
  data.push([]);
  
  let totalTransacciones = 0;
  let totalUnidades = 0;
  let totalValor = 0;
  let productosUnicos = new Set();
  
  reporteData.forEach(transaccion => {
    let tieneProductoCategoria = false;
    
    if (transaccion.items) {
      transaccion.items.forEach(item => {
        if (item.Categoría === categoria) {
          tieneProductoCategoria = true;
          totalUnidades += parseFloat(item.product_qty || 0);
          totalValor += parseFloat(item.product_price || 0) * parseFloat(item.product_qty || 0);
          productosUnicos.add(item.product_ref);
        }
      });
    }
    
    if (tieneProductoCategoria) {
      totalTransacciones++;
    }
  });
  
  data.push(['MÉTRICAS PRINCIPALES']);
  data.push(['Métrica', 'Valor']);
  data.push(['Transacciones con productos de esta categoría', totalTransacciones]);
  data.push(['Productos únicos (referencias)', productosUnicos.size]);
  data.push(['Total unidades vendidas', totalUnidades.toFixed(2)]);
  data.push(['Valor total',  totalValor.toFixed(2)]);
  data.push(['Valor promedio por unidad', totalUnidades > 0 ? (totalValor / totalUnidades).toFixed(2) : '$0.00']);
  data.push(['Unidades promedio por transacción', totalTransacciones > 0 ? (totalUnidades / totalTransacciones).toFixed(2) : '0.00']);
  
  var ws = XLSX.utils.aoa_to_sheet(data);
  ws['!cols'] = [
    {wch: 40},
    {wch: 20}
  ];
  
  XLSX.utils.book_append_sheet(wb, ws, "Resumen " + categoria);
}

// ==================== HOJA 10: TOP PRODUCTOS ====================
function crearHojaTopProductos(wb) {
  let data = [];
  
  data.push([
    'Ranking',
    'Nombre',
    'Talla',
    'Color',
    'Cantidad Vendida',
    'Total Vendido'
  ]);
  
  // reporteData para tipo "Top" ya viene con la estructura correcta del controlador
  let productosOrdenados = [...reporteData].sort((a, b) => 
    parseFloat(b.cantidad || 0) - parseFloat(a.cantidad || 0)
  );
  
  productosOrdenados.forEach((producto, index) => {
    data.push([
      index + 1,
      producto.nombre || '',
      producto.talla || '',
      producto.color || '',
      parseFloat(producto.cantidad || 0).toFixed(2),
      parseFloat(producto.total || 0).toFixed(2)
    ]);
  });
  
  // Totales
  let totalCantidad = 0;
  let totalValor = 0;
  productosOrdenados.forEach(p => {
    totalCantidad += parseFloat(p.cantidad || 0);
    totalValor += parseFloat(p.total || 0);
  });
  
  data.push([]);
  data.push([
    'TOTALES',
    '',
    '',
    '',
    totalCantidad.toFixed(2),
    totalValor.toFixed(2)
  ]);
  
  var ws = XLSX.utils.aoa_to_sheet(data);
  ws['!cols'] = [
    {wch: 8}, {wch: 30}, {wch: 10}, {wch: 15}, {wch: 15}, {wch: 15}
  ];
  
  XLSX.utils.book_append_sheet(wb, ws, "Top Productos");
}

// ==================== HOJA 11: ANÁLISIS TOP ====================
function crearHojaAnalisisTop(wb) {
  let data = [];
  
  data.push(['ANÁLISIS DE TOP PRODUCTOS']);
  data.push([]);
  
  let productosOrdenados = [...reporteData].sort((a, b) => 
    parseFloat(b.cantidad || 0) - parseFloat(a.cantidad || 0)
  );
  
  let totalCantidad = 0;
  let totalValor = 0;
  productosOrdenados.forEach(p => {
    totalCantidad += parseFloat(p.cantidad || 0);
    totalValor += parseFloat(p.total || 0);
  });
  
  data.push(['RESUMEN GENERAL']);
  data.push(['Métrica', 'Valor']);
  data.push(['Total productos únicos', productosOrdenados.length]);
  data.push(['Total unidades vendidas', totalCantidad.toFixed(2)]);
  data.push(['Total valor vendido', totalValor.toFixed(2)]);
  data.push(['Precio promedio por unidad', totalCantidad > 0 ? (totalValor / totalCantidad).toFixed(2) : '$0.00']);
  data.push([]);
  
  // Top 10
  data.push(['TOP 10 PRODUCTOS']);
  data.push(['Ranking', 'Nombre', 'Cantidad', 'Total', '% del Total']);
  
  let top10 = productosOrdenados.slice(0, Math.min(10, productosOrdenados.length));
  let totalTop10Cantidad = 0;
  let totalTop10Valor = 0;
  
  top10.forEach((producto, index) => {
    let cantidad = parseFloat(producto.cantidad || 0);
    let valor = parseFloat(producto.total || 0);
    let porcentaje = totalValor > 0 ? ((valor / totalValor) * 100).toFixed(2) : '0.00';
    
    totalTop10Cantidad += cantidad;
    totalTop10Valor += valor;
    
    data.push([
      index + 1,
      producto.nombre || '',
      cantidad.toFixed(2),
      valor.toFixed(2),
      porcentaje + '%'
    ]);
  });
  
  data.push([]);
  data.push([
    'TOTAL TOP 10',
    '',
    totalTop10Cantidad.toFixed(2),
    totalTop10Valor.toFixed(2),
    totalValor > 0 ? ((totalTop10Valor / totalValor) * 100).toFixed(2) + '%' : '0.00%'
  ]);
  
  data.push([]);
  data.push(['ANÁLISIS']);
  data.push(['El Top 10 representa el ' + (totalValor > 0 ? ((totalTop10Valor / totalValor) * 100).toFixed(2) : '0.00') + '% del total de ventas']);
  
  var ws = XLSX.utils.aoa_to_sheet(data);
  ws['!cols'] = [
    {wch: 15}, {wch: 35}, {wch: 15}, {wch: 15}, {wch: 15}
  ];
  
  XLSX.utils.book_append_sheet(wb, ws, "Análisis Top");
}