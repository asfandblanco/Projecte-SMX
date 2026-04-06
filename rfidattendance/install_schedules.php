<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
  header("location: login.php");
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Instalación de Horarios</title>
  	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/manageusers.css"/>
	<script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.js"></script>
</head>
<body>
<?php include'header.php';?>
<main>
	<h1 class="slideInDown animated">Instalación del Sistema de Horarios</h1>

	<section class="container py-lg-5">
		<div class="alert_install"></div>

		<div class="row">
			<div class="col-lg-12 mt-4">
				<div class="panel">
			      <div class="panel-heading" style="font-size: 19px;">Configuración Inicial</div>
			      <div class="panel-body">
			      	<p>Para activar el sistema de horarios, necesitas crear la tabla correspondiente en la base de datos.</p>
			      	<p><strong>Horarios por defecto para 'Informatica':</strong></p>
			      	<ul>
			      		<li>Lunes a Viernes: 14:55 - 15:15</li>
			      		<li>Miércoles: 15:55 - 16:15</li>
			      	</ul>
			      	<button type="button" id="install_schedules" class="btn btn-success">Instalar Sistema de Horarios</button>
			      </div>
			    </div>
			</div>
		</div>
	</section>
</main>
</body>
<script>
$(document).ready(function(){
	$('#install_schedules').click(function(){
		if (confirm('¿Estás seguro de instalar el sistema de horarios? Esto creará una nueva tabla en la base de datos.')) {
			$.ajax({
				url: 'do_install_schedules.php',
				type: 'POST',
				data: { 'install': 1 },
				success: function(response){
					if (response == 'success') {
						$('.alert_install').fadeIn(500);
						$('.alert_install').html('<p class="alert alert-success">Sistema de horarios instalado correctamente. <a href="schedule_management.php">Ir a gestión de horarios</a></p>');
					} else {
						$('.alert_install').fadeIn(500);
						$('.alert_install').html('<p class="alert alert-danger">Error en la instalación: ' + response + '</p>');
					}
				},
				error: function(){
					$('.alert_install').fadeIn(500);
					$('.alert_install').html('<p class="alert alert-danger">Error de conexión</p>');
				}
			});
		}
	});
});
</script>
</html>