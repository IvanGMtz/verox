/**
 * reporte-categorias.js
 * Funciones específicas para el reporte de categorías
 */

function mostrarReporteCategorias(reporte, startDate, stopDate, tag, filtros) {
  // Limpiar contenedores
  document.getElementById("cards_resumen_categoria").innerHTML = "";
  document.getElementById("detail_data_categoria").innerHTML = "";
  
  let cantidad = 0;
  let totalUnid = 0;
  let total = 0;
  let totalEnvios = 0;
  let itemsReport = {};
  
  for (let i = 0; i < reporte.length; i++) {
    const montoTransaccion = parseFloat(reporte[i].monto_transaccion) || 0;
    const shipmentCost = parseFloat(reporte[i].shipment_cost) || 0;
    const price = parseFloat(reporte[i].price) || 0;
    
    totalEnvios += shipmentCost;
    total += montoTransaccion;
    cantidad++;
    
    let factorProporcional = price > 0 ? montoTransaccion / price : 0;
    
    reporte[i].items.forEach(item => {
      if (!itemsReport[item.product_ref + "_" + item.product_talla + "_" + item.product_color] &&
          item.Categoría === tag &&
          (filtros.refFilter !== "" ? (item.product_ref === filtros.refFilter) : true) &&
          (filtros.tallaFilter !== "" ? (item.product_talla === filtros.tallaFilter) : true) &&
          (filtros.colorFilter !== "" ? (item.product_color === filtros.colorFilter) : true)
      ) {
        itemsReport[item.product_ref + "_" + item.product_talla + "_" + item.product_color] = item;
        itemsReport[item.product_ref + "_" + item.product_talla + "_" + item.product_color].qty = 0;
        itemsReport[item.product_ref + "_" + item.product_talla + "_" + item.product_color].val = 0;
      }
    });
    
    reporte[i].items.forEach(item => {
      if (item.Categoría === tag &&
          (filtros.refFilter !== "" ? (item.product_ref === filtros.refFilter) : true) &&
          (filtros.tallaFilter !== "" ? (item.product_talla === filtros.tallaFilter) : true) &&
          (filtros.colorFilter !== "" ? (item.product_color === filtros.colorFilter) : true)
      ) {
        let cantidadReal = parseFloat(item.product_qty) || 0;
        let valorReal = parseFloat(item.product_price) * cantidadReal || 0;
        
        totalUnid += cantidadReal;
        itemsReport[item.product_ref + "_" + item.product_talla + "_" + item.product_color].qty += cantidadReal;
        itemsReport[item.product_ref + "_" + item.product_talla + "_" + item.product_color].val += valorReal;
      }
    });
  }
  
  let itemsArray = [];
  Object.keys(itemsReport).forEach(itemR => {
    itemsArray.push(itemsReport[itemR]);
  });
  
  // Generar Cards de Resumen
  generarCardsResumenCategoria(cantidad, totalUnid, total, totalEnvios);
  
  // Ordenar y mostrar items
  itemsArray.sort((a, b) => b.qty - a.qty);
  itemsArray.forEach(item => {
    $("#detail_data_categoria").append(
      '<tr>' +
        '<td>' + item.Categoría + '</td>' +
        '<td class="font-weight-bold">' + item.product_ref + '</td>' +
        '<td>' + item.product_talla + '</td>' +
        '<td>' + item.product_color + '</td>' +
        '<td class="text-center font-weight-bold">' + item.qty.toFixed(2) + '</td>' +
        '<td class="text-right">$' + item.val.toFixed(2) + '</td>' +
      '</tr>'
    );
  });
}

function generarCardsResumenCategoria(cantidad, totalUnid, total, totalEnvios) {
  const html = '<div class="col-lg-3 col-md-6 mb-3">' +
      '<div class="card bg-primary text-white shadow">' +
        '<div class="card-body">' +
          '<div class="d-flex justify-content-between align-items-center">' +
            '<div>' +
              '<h2 class="mb-0 font-weight-bold">' + cantidad + '</h2>' +
              '<p class="mb-0">Transacciones</p>' +
            '</div>' +
            '<i class="fas fa-shopping-cart fa-3x opacity-50"></i>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>' +
    '<div class="col-lg-3 col-md-6 mb-3">' +
      '<div class="card bg-success text-white shadow">' +
        '<div class="card-body">' +
          '<div class="d-flex justify-content-between align-items-center">' +
            '<div>' +
              '<h2 class="mb-0 font-weight-bold">' + totalUnid.toFixed(2) + '</h2>' +
              '<p class="mb-0">Unidades</p>' +
            '</div>' +
            '<i class="fas fa-box fa-3x opacity-50"></i>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>' +
    '<div class="col-lg-3 col-md-6 mb-3">' +
      '<div class="card bg-warning text-white shadow">' +
        '<div class="card-body">' +
          '<div class="d-flex justify-content-between align-items-center">' +
            '<div>' +
              '<h2 class="mb-0 font-weight-bold">$' + (total - totalEnvios).toFixed(2) + '</h2>' +
              '<p class="mb-0">Total Efectivo</p>' +
            '</div>' +
            '<i class="fas fa-dollar-sign fa-3x opacity-50"></i>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>' +
    '<div class="col-lg-3 col-md-6 mb-3">' +
      '<div class="card bg-info text-white shadow">' +
        '<div class="card-body">' +
          '<div class="d-flex justify-content-between align-items-center">' +
            '<div>' +
              '<h2 class="mb-0 font-weight-bold">$' + total.toFixed(2) + '</h2>' +
              '<p class="mb-0">Gran Total</p>' +
            '</div>' +
            '<i class="fas fa-chart-line fa-3x opacity-50"></i>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>';
  
  $("#cards_resumen_categoria").html(html);
}

function aplicarFiltrosCategoria() {
  let filtros = {
    refFilter: document.getElementById("refFilter").value,
    tallaFilter: document.getElementById("tallaFilter").value,
    colorFilter: document.getElementById("colorFilter").value,
  };
  
  let start_Date = document.getElementById("start").value;
  let stop_Date = document.getElementById("stop").value;
  let tag = document.getElementById("Categoria").value;
  
  mostrarReporteCategorias(reporteData, start_Date, stop_Date, tag, filtros);
}