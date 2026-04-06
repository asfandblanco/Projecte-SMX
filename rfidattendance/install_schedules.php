<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
  header("location: login.php");
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Instal·lació d'Horaris</title>
   	<meta charset="utf-8">
   	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/manageusers.css"/>
	<script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.js"></script>
</head>
<body>
<?php include'header.php';?>
<main>
	<h1 class="slideInDown animated">Instal·lació del Sistema d'Horaris</h1>

	<section class="container py-lg-5">
		<div class="alert_install"></div>

		<div class="row">
			<div class="col-lg-12 mt-4">
				<div class="panel">
			      <div class="panel-heading" style="font-size: 19px;">Configuració inicial</div>
			      <div class="panel-body">
			      	<p>Per activar el sistema d'horaris, cal crear la taula corresponent a la base de dades.</p>
			      	<p><strong>Horaris per defecte per a 'Informatica':</strong></p>
			      	<ul>
			      		<li>Dilluns a Divendres: 14:55 - 15:15</li>
			      		<li>Dimecres: 15:55 - 16:15</li>
			      	</ul>
			      	<button type="button" id="install_schedules" class="btn btn-success">Instal·lar sistema d'horaris</button>
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
		if (confirm('Estàs segur que vols instal·lar el sistema d\'horaris? Això crearà una nova taula a la base de dades.')) {
			$.ajax({
				url: 'do_install_schedules.php',
				type: 'POST',
				data: { 'install': 1 },
				success: function(response){
					if (response == 'success') {
						$('.alert_install').fadeIn(500);
						$('.alert_install').html('<p class="alert alert-success">Sistema d'horaris instal·lat correctament. <a href="schedule_management.php">Ves a la gestió d'horaris</a></p>');
					} else {
						$('.alert_install').fadeIn(500);
						$('.alert_install').html('<p class="alert alert-danger">Error en la instal·lació: ' + response + '</p>');
					}
				},
				error: function(){
					$('.alert_install').fadeIn(500);
					$('.alert_install').html('<p class="alert alert-danger">Error de connexió</p>');
				}
			});
		}
	});
});
</script>
</html>