// Test básico para verificar que el archivo se carga
console.log("=== user_log.js CARGADO ===");

// Verificar que jQuery está disponible
if (typeof $ === 'undefined') {
    console.error("ERROR: jQuery no está cargado");
} else {
    console.log("jQuery está disponible");
}

$(document).ready(function(){
  console.log("=== DOCUMENT READY ===");

  // Get Report passenger
  $(document).on('click', '#user_log', function(){
    console.log("Click en botón filtrar");

    var date_sel_start = $('#date_sel_start').val();
    var date_sel_end = $('#date_sel_end').val();
    var time_sel = $(".time_sel:checked").val();
    var time_sel_start = $('#time_sel_start').val();
    var time_sel_end = $('#time_sel_end').val();
    var card_sel = $('#card_sel option:selected').val();
    var dev_uid = $('#dev_sel option:selected').val();

    $.ajax({
      url: 'user_log_up.php',
      type: 'POST',
      data: {
        'log_date': 1,
        'date_sel_start': date_sel_start,
        'date_sel_end': date_sel_end,
        'time_sel': time_sel,
        'time_sel_start': time_sel_start,
        'time_sel_end': time_sel_end,
        'card_sel': card_sel,
        'dev_uid': dev_uid,
      },
      success: function(response){

        $('.up_info2').fadeIn(500);
        $('.up_info2').text("The Filter has been selected!");

        $('#Filter-export').modal('hide');
        setTimeout(function () {
            $('.up_info2').fadeOut(500);
        }, 5000);

        $.ajax({
          url: "user_log_up.php",
          type: 'POST',
          data: {
            'log_date': 1,
            'date_sel_start': date_sel_start,
            'date_sel_end': date_sel_end,
            'time_sel': time_sel,
            'time_sel_start': time_sel_start,
            'time_sel_end': time_sel_end,
            'dev_uid': dev_uid,
            'card_sel': card_sel,
            'select_date': 0,
          }
          }).done(function(data) {
          $('#userslog').html(data);
          console.log("Tabla recargada después de filtrar");
        });
      }
    });
  });

  // Evento para el botón eliminar - funcionalidad completa
  $(document).on('click', '.delete-log', function(e){
    e.preventDefault();
    console.log("=== CLICK EN DELETE-LOG ===");

    var log_id = $(this).data('id');
    console.log("ID a eliminar:", log_id);

    if(!log_id) {
      console.error("ERROR: No se encontró data-id");
      alert("Error: No se pudo obtener el ID del registro");
      return;
    }

    if (confirm("¿Estás seguro que quieres eliminar este registro?")) {
      console.log("Usuario confirmó eliminación. Enviando AJAX...");

      $.ajax({
        url: 'delete_log.php',
        type: 'POST',
        data: {
          'delete_id': log_id,
        },
        success: function(response){
          console.log("=== RESPUESTA DEL SERVIDOR ===");
          console.log("Response:", response);
          console.log("Response trim:", response.trim());

          if (response.trim() == '1') {
            console.log("Eliminación exitosa");

            // Recargar tabla para sincronizar con BD
            $.ajax({
              url: "user_log_up.php",
              type: 'POST',
              data: {
                  'select_date': 0,
              }
            }).done(function(data) {
              console.log("Tabla recargada después de eliminar");
              $('#userslog').html(data);

              $('.up_info2').fadeIn(500);
              $('.up_info2').html('<p class="alert alert-success">Registre eliminat correctament</p>');
              setTimeout(function () {
                  $('.up_info2').fadeOut(500);
              }, 3000);
            });
          }
          else {
            console.error("Error en eliminación:", response);
            $('.up_info2').fadeIn(500);
            $('.up_info2').html('<p class="alert alert-danger">Error al eliminar: ' + response + '</p>');
            setTimeout(function () {
                $('.up_info2').fadeOut(500);
            }, 3000);
          }
        },
        error: function(xhr, status, error){
          console.error("=== ERROR AJAX ===");
          console.error("Error:", error);
          console.error("Status:", status);
          console.error("Response:", xhr.responseText);
          alert("Error en la solicitud: " + error);
        }
      });
    } else {
      console.log("Usuario canceló la eliminación");
    }
  });
});