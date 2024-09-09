/* global Swal, moment, Cookies */
window.alert = function(message, type = 'warning'){
  Swal.fire({
    icon: type,
    title: message,
    timer: 4000,
    timerProgressBar: true
  });
};
var customRanges = {
    "Hoy": [
        moment().startOf('day').format('DD/MM/YYYY'),
        moment().endOf('day').format('DD/MM/YYYY')
    ],
    "Ayer": [
        moment().subtract(1, 'day').startOf('day').format('DD/MM/YYYY'),
        moment().subtract(1, 'day').endOf('day').format('DD/MM/YYYY')
    ],
    "Esta semana": [
        moment().startOf('week').format('DD/MM/YYYY'),
        moment().endOf('week').format('DD/MM/YYYY')
    ],
    "Semana pasada": [
        moment().subtract(1, 'week').startOf('week').format('DD/MM/YYYY'),
        moment().subtract(1, 'week').endOf('week').format('DD/MM/YYYY')
    ],
    "Este mes": [
        moment().startOf('month').format('DD/MM/YYYY'),
        moment().endOf('month').format('DD/MM/YYYY')
    ],
    "Mes pasado": [
        moment().subtract(1, 'month').startOf('month').format('DD/MM/YYYY'),
        moment().subtract(1, 'month').endOf('month').format('DD/MM/YYYY')
    ]
};
var localeRanges = {
    "format": "DD/MM/YYYY",
    "separator": " - ",
    "applyLabel": "OK",
    "cancelLabel": "Cancelar",
    "fromLabel": "Desde",
    "toLabel": "Hasta",
    "customRangeLabel": "Seleccionar fechas",
    "weekLabel": "S",
    "daysOfWeek": [
        "Do",
        "Lu",
        "Ma",
        "Mi",
        "Ju",
        "Vi",
        "Sa"
    ],
    "monthNames": [
        "Enero",
        "Febrero",
        "Marzo",
        "Abril",
        "Mayo",
        "Junio",
        "Julio",
        "Agosto",
        "Septiembre",
        "Octubre",
        "Noviembre",
        "Diciembre"
    ],
    "firstDay": 1
};
$('form').submit(function(){
  $(this).find('[type=submit]').attr('disabled',true);
});
$(window).on('shown.bs.modal', function() { 
  $('form').submit(function(){
    $(this).find('[type=submit]').attr('disabled',true);
  });
});
$(document).ajaxComplete(function( event, request, settings ) {
  $('form').find('[type=submit]').removeAttr('disabled');
});
$('.dataTable').footable({
  "sorting": {
      "enabled": true
  },
  "paging": {
      "enabled": true,
      "size": 20
  },
  "filtering": {
      "enabled": true
  }
});
$('#ItemIdmarketingdigitaldisabled, #ItemIdzoomdisabled, #ItemIdgruposenalesdisabled').click(function(){
  alert('No estás habilitado para ésta sección. Debes estar activo en binario.', 'info');
});
