/**
 * reporte-main.js
 * Funciones principales y utilidades para el módulo de reportes
 */

// Funciones de utilidad
function mostrarFechaActual() {
  const hoy = new Date();
  const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
  document.getElementById('fecha_actual').textContent = hoy.toLocaleDateString('es-ES', opciones);
}

function setHoy() {
  const hoy = new Date().toISOString().split('T')[0];
  document.getElementById('start').value = hoy;
  document.getElementById('stop').value = hoy;
}

function setHoyYGenerar() {
  setHoy();
  solicitarReporte();
}

function cambiarTipoReporte() {
  let selectorValue = document.getElementById("reporteTipo").value;
  document.getElementById("categoriasSelector").style.display = selectorValue === "Categoria" ? "block" : "none";
  document.getElementById("asesorselector").style.display = selectorValue === "Asesor" ? "block" : "none";
}

function solicitarReporte() {
  let start_Date = document.getElementById("start").value;
  let stop_Date = document.getElementById("stop").value;
  
  if (!start_Date || !stop_Date) {
    alert("Debes indicar las fechas requeridas");
    return;
  }

  let rtipo = document.getElementById("reporteTipo").value;
  let tag = (rtipo === "Total" || rtipo === "Top") ? null : document.getElementById(rtipo).value;
  
  let params = {
    start: start_Date,
    stop: stop_Date,
    reporte_tipo: rtipo,
    reporte_tag: tag,
  };
  
  $.post(
    baseUrl + "/store/get_reporte",
    params,
    function(reporte) {
      reporte = reporte.filter(item => item.asesor !== 'MATEO CASTRO');
      reporteData = reporte;
      
      cargarFacturasPendientes(start_Date, stop_Date);
      mostrarReportePorTipo(reporte, rtipo, start_Date, stop_Date, tag);
    }
  ).fail(function(error) {
    console.error("Error al cargar reporte:", error);
    alert("Error al cargar el reporte. Por favor intenta nuevamente.");
  });
}

function mostrarReportePorTipo(reporte, tipo, startDate, stopDate, tag) {
  // Ocultar todos los reportes
  document.getElementById("reporte_total_vendido").style.display = "none";
  document.getElementById("reporte_categorias").style.display = "none";
  document.getElementById("reporte_productos").style.display = "none";
  
  // Mostrar contenedor principal
  document.getElementById("reporte_container").style.display = "block";
  document.getElementById("search_report").style.display = "none";
  
  // Cargar el reporte correspondiente
  if (tipo === "Total" || tipo === "Asesor") {
    document.getElementById("reporte_total_vendido").style.display = "block";
    mostrarReporteTotal(reporte, startDate, stopDate, { category: "TODOS" });
  } else if (tipo === "Categoria") {
    document.getElementById("reporte_categorias").style.display = "block";
    mostrarReporteCategorias(reporte, startDate, stopDate, tag, {
      refFilter: "",
      tallaFilter: "",
      colorFilter: ""
    });
  } else if (tipo === "Top") {
    document.getElementById("reporte_productos").style.display = "block";
    mostrarReporteProductos(reporte, startDate, stopDate);
  }
}

function cargarFacturasPendientes(start_Date, stop_Date) {
  $.post(
    baseUrl + "/store/get_facturas_pendientes",
    { start: start_Date, stop: stop_Date },
    function(facturas) {
      facturasPendientes = facturas;
      mostrarFacturasPendientes(facturas);
    }
  ).fail(function() {
    document.getElementById("facturas_pendientes_section").style.display = "none";
  });
}

function mostrarFacturasPendientes(facturas) {
  const tbody = document.getElementById("facturas_pendientes_body");
  tbody.innerHTML = "";
  
  if (facturas && facturas.length > 0) {
    document.getElementById("facturas_pendientes_section").style.display = "block";
    
    facturas.forEach(factura => {
      const tr = document.createElement('tr');
      const orderId = factura.order_id || '';
      const clienteNombre = factura.cliente_nombre || '';
      const asesor = factura.asesor || '';
      const total = parseFloat(factura.total || 0).toFixed(2);
      const abonado = parseFloat(factura.abonado || 0).toFixed(2);
      const pendiente = parseFloat(factura.pendiente || 0).toFixed(2);
      const estado = factura.estado || 'Pendiente';
      
      tr.innerHTML = '<td class="font-weight-bold">' + orderId + '</td>' +
        '<td>' + clienteNombre + '</td>' +
        '<td>' + asesor + '</td>' +
        '<td class="text-right">$' + total + '</td>' +
        '<td class="text-right text-success">$' + abonado + '</td>' +
        '<td class="text-right text-danger font-weight-bold">$' + pendiente + '</td>' +
        '<td class="text-center"><span class="badge badge-warning">' + estado + '</span></td>';
      tbody.appendChild(tr);
    });
  } else {
    document.getElementById("facturas_pendientes_section").style.display = "none";
  }
}

function nuevoReporte() {
  // Limpiar todos los contenedores
  document.getElementById("cards_resumen_principal").innerHTML = "";
  document.getElementById("cards_kpis").innerHTML = "";
  document.getElementById("cards_desglose").innerHTML = "";
  document.getElementById("cards_resumen_categoria").innerHTML = "";
  document.getElementById("cards_resumen_productos").innerHTML = "";
  
  document.getElementById("detail_data_total").innerHTML = "";
  document.getElementById("detail_data_categoria").innerHTML = "";
  document.getElementById("detail_data_productos").innerHTML = "";
  
  document.getElementById("facturas_pendientes_body").innerHTML = "";
  document.getElementById("facturas_pendientes_section").style.display = "none";

  // Resetear selectores
  document.getElementById("asesores2").value = "TODOS";
  if (document.getElementById("Categoria")) {
    document.getElementById("Categoria").selectedIndex = 0;
  }
  if (document.getElementById("Asesor")) {
    document.getElementById("Asesor").selectedIndex = 0;
  }

  // Ocultar reportes y mostrar búsqueda
  document.getElementById("reporte_container").style.display = "none";
  document.getElementById("search_report").style.display = "block";
}

function getTipoTransaccionClass(tipoTransaccion) {
  switch(tipoTransaccion) {
    case 'Pago Completo':
      return 'badge-success';
    case 'Abono 1':
    case 'Abono 2':
      return 'badge-warning';
    case 'Pago Restante':
      return 'badge-info';
    default:
      return 'badge-secondary';
  }
}