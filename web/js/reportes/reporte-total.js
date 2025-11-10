/**
 * reporte-total.js
 * Funciones específicas para el reporte de total vendido y por asesor
 */

function mostrarReporteTotal(reporte, startDate, stopDate, filtros) {
  // Limpiar contenedores
  document.getElementById("cards_resumen_principal").innerHTML = "";
  document.getElementById("cards_kpis").innerHTML = "";
  document.getElementById("cards_desglose").innerHTML = "";
  document.getElementById("detail_data_total").innerHTML = "";
  
  // Mostrar filtro de asesor y actualizar indicador
  document.getElementById("asesorSelector2").style.display = "block";
  actualizarIndicadorVista(filtros.category);
  
  let cantidad = 0;
  let totalPrendas = 0;
  let total = 0;
  let totalEnvios = 0;
  let totalNuevos = 0;
  let totalVentaNetaNuevos = 0;
  let totalAbonos = 0;
  let totalPagosCompletos = 0;
  let totalPagosRestantes = 0;
  let clientesNuevosUnicos = new Set();
  
  for (let i = 0; i < reporte.length; i++) {
    if (filtros.category !== "TODOS" && reporte[i].asesor !== filtros.category) {
      continue;
    }
    
    cantidad++;
    const montoTransaccion = parseFloat(reporte[i].monto_transaccion) || 0;
    const shipmentCost = parseFloat(reporte[i].shipment_cost) || 0;
    const price = parseFloat(reporte[i].price) || 0;
    totalEnvios += shipmentCost;
    total += montoTransaccion;

    if (reporte[i].tipo_transaccion === 'Pago Completo') {
      totalPagosCompletos += montoTransaccion;
    } else if (reporte[i].tipo_transaccion.includes('Abono')) {
      totalAbonos += montoTransaccion;
    } else if (reporte[i].tipo_transaccion === 'Pago Restante') {
      totalPagosRestantes += montoTransaccion;
    }
    
    if (reporte[i].client_type === 'Nuevo') {
      totalNuevos++;
      totalVentaNetaNuevos += montoTransaccion;
      if (reporte[i].cliente_id) {
        clientesNuevosUnicos.add(reporte[i].cliente_id);
      }
    }

    let factorProporcional = price > 0 ? montoTransaccion / price : 0;
    reporte[i].items.forEach(item => {
      totalPrendas += (item.product_qty * factorProporcional);
    });
    
    const originalId = reporte[i].original_id || reporte[i].id;
    const date = reporte[i].date;
    const tipoClass = getTipoTransaccionClass(reporte[i].tipo_transaccion);
    const tipoTrans = reporte[i].tipo_transaccion;
    const clientType = reporte[i].client_type;
    const clientState = reporte[i].client_state;
    
    $("#detail_data_total").append(
      '<tr>' +
        '<td class="text-center font-weight-bold">' + originalId + '</td>' +
        '<td class="text-center">' + date + '</td>' +
        '<td class="text-center"><span class="badge ' + tipoClass + '">' + tipoTrans + '</span></td>' +
        '<td class="text-center">' + clientType + '</td>' +
        '<td class="text-center">' + clientState + '</td>' +
        '<td class="text-center">' + montoTransaccion.toFixed(2) + '</td>' +
        '<td class="text-center">' + shipmentCost.toFixed(2) + '</td>' +
        '<td class="text-center font-weight-bold">' + price.toFixed(2) + '</td>' +
      '</tr>'
    );
  }

  const ticketPromedio = cantidad > 0 ? total / cantidad : 0;
  const unidadesPorVenta = cantidad > 0 ? totalPrendas / cantidad : 0;

  if (filtros.category === "TODOS") {
    kpiGenerales = {
      ticketPromedio: ticketPromedio,
      unidadesPorVenta: unidadesPorVenta
    };
  }

  // Generar Cards de Resumen Principal
  generarCardsResumenPrincipal(cantidad, totalPrendas, total, totalEnvios);
  
  // Generar Cards de KPIs
  generarCardsKPIs(ticketPromedio, unidadesPorVenta, filtros);
  
  // Generar Cards de Desglose
  generarCardsDesglose(
    totalPagosCompletos, 
    totalAbonos, 
    totalPagosRestantes,
    clientesNuevosUnicos.size,
    totalNuevos,
    totalVentaNetaNuevos
  );
}

function actualizarIndicadorVista(asesor) {
  const indicador = document.getElementById("indicadorVista");
  
  if (asesor === "TODOS") {
    indicador.innerHTML = `
      <div class="d-flex align-items-center">
        <i class="fas fa-chart-bar fa-2x text-primary mr-3"></i>
        <div>
          <h4 class="mb-0 font-weight-bold">Reporte General</h4>
          <small class="text-muted">Mostrando datos de todos los asesores</small>
        </div>
      </div>
    `;
  } else {
    indicador.innerHTML = `
      <div class="d-flex align-items-center">
        <i class="fas fa-user-circle fa-2x text-info mr-3"></i>
        <div>
          <h4 class="mb-0 font-weight-bold">${asesor}</h4>
          <small class="text-muted">Reporte individual del asesor</small>
        </div>
      </div>
    `;
  }
}

function generarCardsResumenPrincipal(cantidad, totalPrendas, total, totalEnvios) {
  const html = `
    <div class="col-lg-2 col-md-6 mb-3">
      <div class="card bg-primary text-white shadow">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h2 class="mb-0 font-weight-bold">${cantidad}</h2>
              <p class="mb-0">Transacciones</p>
            </div>
            <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-2 col-md-6 mb-3">
      <div class="card bg-success text-white shadow">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h2 class="mb-0 font-weight-bold">${totalPrendas.toFixed(0)}</h2>
              <p class="mb-0">Unidades</p>
            </div>
            <i class="fas fa-box fa-3x opacity-50"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-warning text-white shadow">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h2 class="mb-0 font-weight-bold">${total.toFixed(2)}</h2>
              <p class="mb-0">Total Efectivo</p>
            </div>
            <i class="fas fa-dollar-sign fa-3x opacity-50"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-2 col-md-6 mb-3">
      <div class="card bg-info text-white shadow">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h2 class="mb-0 font-weight-bold">${totalEnvios.toFixed(2)}</h2>
              <p class="mb-0">Total Envíos</p>
            </div>
            <i class="fas fa-truck fa-3x opacity-50"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card bg-dark text-white shadow">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h2 class="mb-0 font-weight-bold">${(total + totalEnvios).toFixed(2)}</h2>
              <p class="mb-0">Gran Total</p>
            </div>
            <i class="fas fa-chart-line fa-3x opacity-50"></i>
          </div>
        </div>
      </div>
    </div>
  `;
  $("#cards_resumen_principal").html(html);
}

function generarCardsKPIs(ticketPromedio, unidadesPorVenta, filtros) {
  let html = '';
  
  if (filtros.category !== "TODOS" && kpiGenerales) {
    const minTicket = kpiGenerales.ticketPromedio * (1 - toleranciaKPI);
    const minUnidades = kpiGenerales.unidadesPorVenta * (1 - toleranciaKPI);
    const cumpleTicket = ticketPromedio >= minTicket;
    const cumpleUnidades = unidadesPorVenta >= minUnidades;
    
    html = '<div class="col-lg-6 col-md-6 mb-3">' +
      '<div class="card shadow border-left-' + (cumpleTicket ? 'success' : 'danger') + '">' +
        '<div class="card-body">' +
          '<div class="row align-items-center">' +
            '<div class="col">' +
              '<h5 class="text-' + (cumpleTicket ? 'success' : 'danger') + ' font-weight-bold mb-1">TICKET PROMEDIO</h5>' +
              '<h3 class="mb-0 font-weight-bold">' + ticketPromedio.toFixed(2) + '</h3>' +
              '<small class="text-muted">Meta: ' + minTicket.toFixed(2) + '</small>' +
            '</div>' +
            '<div class="col-auto">' +
              '<i class="fas fa-receipt fa-3x text-' + (cumpleTicket ? 'success' : 'danger') + ' opacity-50"></i>' +
            '</div>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>' +
    '<div class="col-lg-6 col-md-6 mb-3">' +
      '<div class="card shadow border-left-' + (cumpleUnidades ? 'success' : 'danger') + '">' +
        '<div class="card-body">' +
          '<div class="row align-items-center">' +
            '<div class="col">' +
              '<h5 class="text-' + (cumpleUnidades ? 'success' : 'danger') + ' font-weight-bold mb-1">UNIDADES POR VENTA</h5>' +
              '<h3 class="mb-0 font-weight-bold">' + unidadesPorVenta.toFixed(2) + '</h3>' +
              '<small class="text-muted">Meta: ' + minUnidades.toFixed(2) + '</small>' +
            '</div>' +
            '<div class="col-auto">' +
              '<i class="fas fa-cubes fa-3x text-' + (cumpleUnidades ? 'success' : 'danger') + ' opacity-50"></i>' +
            '</div>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>';
  } else {
    html = '<div class="col-lg-6 col-md-6 mb-3">' +
      '<div class="card shadow border-left-info">' +
        '<div class="card-body">' +
          '<div class="row align-items-center">' +
            '<div class="col">' +
              '<h5 class="text-info font-weight-bold mb-1">TICKET PROMEDIO</h5>' +
              '<h3 class="mb-0 font-weight-bold">' + ticketPromedio.toFixed(2) + '</h3>' +
            '</div>' +
            '<div class="col-auto">' +
              '<i class="fas fa-receipt fa-3x text-info opacity-50"></i>' +
            '</div>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>' +
    '<div class="col-lg-6 col-md-6 mb-3">' +
      '<div class="card shadow border-left-info">' +
        '<div class="card-body">' +
          '<div class="row align-items-center">' +
            '<div class="col">' +
              '<h5 class="text-info font-weight-bold mb-1">UNIDADES POR VENTA</h5>' +
              '<h3 class="mb-0 font-weight-bold">' + unidadesPorVenta.toFixed(2) + '</h3>' +
            '</div>' +
            '<div class="col-auto">' +
              '<i class="fas fa-cubes fa-3x text-info opacity-50"></i>' +
            '</div>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>';
  }
  
  $("#cards_kpis").html(html);
}

function generarCardsDesglose(pagosCompletos, abonos, pagosRestantes, clientesUnicos, transaccionesNuevas, efectivoNuevos) {
  const html = '<div class="col-lg-4 col-md-6 mb-3">' +
      '<div class="card shadow border-left-success">' +
        '<div class="card-body">' +
          '<div class="row align-items-center">' +
            '<div class="col">' +
              '<h6 class="text-success font-weight-bold mb-1">PAGOS COMPLETOS</h6>' +
              '<h4 class="mb-0 font-weight-bold">'+ pagosCompletos.toFixed(2) + '</h4>' +
            '</div>' +
            '<div class="col-auto">' +
              '<i class="fas fa-check-circle fa-2x text-success"></i>' +
            '</div>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>' +
    '<div class="col-lg-4 col-md-6 mb-3">' +
      '<div class="card shadow border-left-warning">' +
        '<div class="card-body">' +
          '<div class="row align-items-center">' +
            '<div class="col">' +
              '<h6 class="text-warning font-weight-bold mb-1">ABONOS</h6>' +
              '<h4 class="mb-0 font-weight-bold">' + abonos.toFixed(2) + '</h4>' +
            '</div>' +
            '<div class="col-auto">' +
              '<i class="fas fa-coins fa-2x text-warning"></i>' +
            '</div>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>' +
    '<div class="col-lg-4 col-md-6 mb-3">' +
      '<div class="card shadow border-left-info">' +
        '<div class="card-body">' +
          '<div class="row align-items-center">' +
            '<div class="col">' +
              '<h6 class="text-info font-weight-bold mb-1">PAGOS RESTANTES</h6>' +
              '<h4 class="mb-0 font-weight-bold">' + pagosRestantes.toFixed(2) + '</h4>' +
            '</div>' +
            '<div class="col-auto">' +
              '<i class="fas fa-money-bill-wave fa-2x text-info"></i>' +
            '</div>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>' +
    '<div class="col-lg-6 col-md-6 mb-3">' +
      '<div class="card shadow border-left-primary">' +
        '<div class="card-body">' +
          '<div class="row align-items-center">' +
            '<div class="col">' +
              '<h6 class="text-primary font-weight-bold mb-1">CLIENTES ÚNICOS NUEVOS</h6>' +
              '<h4 class="mb-0 font-weight-bold">' + clientesUnicos + '</h4>' +
              '<small class="text-muted">Transacciones: ' + transaccionesNuevas + '</small>' +
            '</div>' +
            '<div class="col-auto">' +
              '<i class="fas fa-user-plus fa-2x text-primary"></i>' +
            '</div>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>' +
    '<div class="col-lg-6 col-md-6 mb-3">' +
      '<div class="card shadow border-left-success">' +
        '<div class="card-body">' +
          '<div class="row align-items-center">' +
            '<div class="col">' +
              '<h6 class="text-success font-weight-bold mb-1">EFECTIVO CLIENTES NUEVOS</h6>' +
              '<h4 class="mb-0 font-weight-bold">'+ efectivoNuevos.toFixed(2) + '</h4>' +
            '</div>' +
            '<div class="col-auto">' +
              '<i class="fas fa-hand-holding-usd fa-2x text-success"></i>' +
            '</div>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>';
  
  $("#cards_desglose").html(html);
}

function cambiarAsesor() {
  let asesor = document.getElementById("asesores2").value;
  let start_Date = document.getElementById("start").value;
  let stop_Date = document.getElementById("stop").value;
  
  mostrarReporteTotal(reporteData, start_Date, stop_Date, {
    category: asesor
  });
}