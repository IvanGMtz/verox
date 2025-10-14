/**
 * reporte-productos.js
 * Funciones específicas para el reporte de top productos
 */

function mostrarReporteProductos(reporte, startDate, stopDate) {
  // Limpiar contenedores
  document.getElementById("cards_resumen_productos").innerHTML = "";
  document.getElementById("detail_data_productos").innerHTML = "";
  
  let cantidad = 0;
  let total = 0;
  
  // Ordenar productos por cantidad vendida
  reporte.sort((a, b) => (a.cantidad < b.cantidad) ? 1 : ((b.cantidad < a.cantidad) ? -1 : 0));
  
  // Procesar cada producto
  for (let i = 0; i < reporte.length; i++) {
    cantidad += reporte[i].cantidad;
    total += reporte[i].total;
    
    const badge = i < 3 ? 
      '<span class="badge badge-warning"><i class="fas fa-trophy"></i> TOP ' + (i + 1) + '</span>' : 
      (i + 1);
    
    $("#detail_data_productos").append(
      '<tr>' +
        '<td class="text-center">' + badge + '</td>' +
        '<td class="font-weight-bold">' + reporte[i].nombre + '</td>' +
        '<td>' + reporte[i].talla + '</td>' +
        '<td>' + reporte[i].color + '</td>' +
        '<td class="text-center font-weight-bold">' + reporte[i].cantidad.toFixed(2) + '</td>' +
        '<td class="text-right">$' + reporte[i].total.toFixed(2) + '</td>' +
      '</tr>'
    );
  }
  
  // Generar Cards de Resumen
  generarCardsResumenProductos(reporte.length, cantidad, total);
}

function generarCardsResumenProductos(productosUnicos, cantidad, total) {
  const html = '<div class="col-lg-4 col-md-6 mb-3">' +
      '<div class="card bg-primary text-white shadow">' +
        '<div class="card-body">' +
          '<div class="d-flex justify-content-between align-items-center">' +
            '<div>' +
              '<h2 class="mb-0 font-weight-bold">' + productosUnicos + '</h2>' +
              '<p class="mb-0">Productos Únicos</p>' +
            '</div>' +
            '<i class="fas fa-star fa-3x opacity-50"></i>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>' +
    '<div class="col-lg-4 col-md-6 mb-3">' +
      '<div class="card bg-success text-white shadow">' +
        '<div class="card-body">' +
          '<div class="d-flex justify-content-between align-items-center">' +
            '<div>' +
              '<h2 class="mb-0 font-weight-bold">' + cantidad.toFixed(0) + '</h2>' +
              '<p class="mb-0">Unidades Vendidas</p>' +
            '</div>' +
            '<i class="fas fa-box fa-3x opacity-50"></i>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>' +
    '<div class="col-lg-4 col-md-6 mb-3">' +
      '<div class="card bg-warning text-white shadow">' +
        '<div class="card-body">' +
          '<div class="d-flex justify-content-between align-items-center">' +
            '<div>' +
              '<h2 class="mb-0 font-weight-bold">$' + total.toFixed(2) + '</h2>' +
              '<p class="mb-0">Total Vendido</p>' +
            '</div>' +
            '<i class="fas fa-dollar-sign fa-3x opacity-50"></i>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>';
  
  $("#cards_resumen_productos").html(html);
}