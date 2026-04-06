$(document).ready(function(){

	// Load schedules on page load
	$.ajax({
      	url: "schedule_up.php",
      	type: 'POST',
      	data: {
        'load_schedules': 1,
  		}
      	}).done(function(data) {
  			$('#schedules').html(data);
    	});

	// Add Schedule
	$(document).on('click', '#schedule_add', function(){

		var department = $('#department').val();
		var day_of_week = $('#day_of_week').val();
		var start_time = $('#start_time').val();
		var end_time = $('#end_time').val();

		$.ajax({
		  url: 'schedule_config.php',
		  type: 'POST',
		  data: {
		    'schedule_add': 1,
		    'department': department,
		    'day_of_week': day_of_week,
		    'start_time': start_time,
		    'end_time': end_time,
		  },
		  success: function(response){
		    $('#department').val('');
		    $('#day_of_week').val('');
		    $('#start_time').val('');
		    $('#end_time').val('');

		    if (response == 1) {
		    	$('.alert_schedule').fadeIn(500);
			    $('.alert_schedule').html('<p class="alert alert-success">Horario añadido correctamente</p>');
		        $('#new-schedule').modal('hide');
		        setTimeout(function () {
			        $('.alert_schedule').fadeOut(500);
			        // Reload schedules
			        $.ajax({
				      	url: "schedule_up.php",
				      	type: 'POST',
				      	data: {
				        'load_schedules': 1,
				  		}
				      	}).done(function(data) {
				  			$('#schedules').html(data);
				    	});
			    }, 2000);
		    }
		    else {
	    		$('.alert_schedule').fadeIn(500);
		    	$('.alert_schedule').html(response);

		    	setTimeout(function () {
			        $('.alert_schedule').fadeOut(500);
			    }, 2000);
		    }
		  }
		});
	});

	// Delete Schedule
	$(document).on('click', '.schedule_del', function(){
		var schedule_id = $(this).data('id');
		var btn = $(this);

		bootbox.confirm("¿Estás seguro de eliminar este horario?", function(result) {
			if (result) {
				$.ajax({
				  url: 'schedule_config.php',
				  type: 'POST',
				  data: {
				    'schedule_del': 1,
				    'schedule_id': schedule_id,
				  },
				  success: function(response){
				    if (response == 1) {
				    	btn.closest('tr').fadeOut(500);
				    	$('.alert_schedule').fadeIn(500);
					    $('.alert_schedule').html('<p class="alert alert-success">Horario eliminado correctamente</p>');
				        setTimeout(function () {
					        $('.alert_schedule').fadeOut(500);
					    }, 2000);
				    }
				    else {
				    	$('.alert_schedule').fadeIn(500);
				    	$('.alert_schedule').html('<p class="alert alert-danger">Error al eliminar: ' + response + '</p>');
				    	setTimeout(function () {
					        $('.alert_schedule').fadeOut(500);
					    }, 2000);
				    }
				  }
				});
			}
		});
	});

	// Toggle Schedule Status
	$(document).on('click', '.schedule_toggle', function(){
		var schedule_id = $(this).data('id');
		var btn = $(this);

		$.ajax({
		  url: 'schedule_config.php',
		  type: 'POST',
		  data: {
		    'schedule_toggle': 1,
		    'schedule_id': schedule_id,
		  },
		  success: function(response){
		    if (response == 1) {
		    	$('.alert_schedule').fadeIn(500);
			    $('.alert_schedule').html('<p class="alert alert-success">Estado del horario actualizado</p>');
		        setTimeout(function () {
			        $('.alert_schedule').fadeOut(500);
			        // Reload schedules
			        $.ajax({
				      	url: "schedule_up.php",
				      	type: 'POST',
				      	data: {
				        'load_schedules': 1,
				  		}
				      	}).done(function(data) {
				  			$('#schedules').html(data);
				    	});
			    }, 2000);
		    }
		    else {
		    	$('.alert_schedule').fadeIn(500);
		    	$('.alert_schedule').html('<p class="alert alert-danger">Error al actualizar: ' + response + '</p>');
		    	setTimeout(function () {
			        $('.alert_schedule').fadeOut(500);
			    }, 2000);
		    }
		  }
		});
	});
});