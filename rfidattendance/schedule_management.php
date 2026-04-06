<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
  header("location: login.php");
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Gestió d'Horaris</title>
   	<meta charset="utf-8">
   	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/manageusers.css"/>
	<script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
	<script type="text/javascript" src="js/bootbox.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.js"></script>
	<script src="js/schedule_management.js"></script>
</head>
<body>
<?php include'header.php';?>
<main>
	<h1 class="slideInDown animated">Configurar horaris per departament</h1>

	<section class="container py-lg-5">
		<div class="alert_schedule"></div>

		<!-- Schedule Management -->
		<div class="row">
			<div class="col-lg-12 mt-4">
				<div class="panel">
			      <div class="panel-heading" style="font-size: 19px;">Horaris de treball:
			      	<button type="button" class="btn btn-success" data-toggle="modal" data-target="#new-schedule" style="font-size: 18px; float: right; margin-top: -6px;">Nou horari</button>
			      </div>
			      <div class="panel-body">
			      		<div id="schedules"></div>
			      </div>
			    </div>
			</div>
		</div>
		<!-- //Schedule Management -->

		<!-- New Schedule Modal -->
		<div class="modal fade" id="new-schedule" tabindex="-1" role="dialog" aria-labelledby="Nou Horari" aria-hidden="true">
		  <div class="modal-dialog modal-dialog-centered" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h3 class="modal-title" id="exampleModalLongTitle">Afegeix un nou horari:</h3>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Tanca">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <form action="" method="POST" enctype="multipart/form-data">
			      <div class="modal-body">
			      	<label for="department"><b>Departament:</b></label>
			      	<select name="department" id="department" required>
			      		<option value="">Selecciona departament...</option>
			      		<?php
			      			require'connectDB.php';
			      			$sql = "SELECT DISTINCT device_dep FROM devices ORDER BY device_dep ASC";
			      			$result = mysqli_stmt_init($conn);
			      			if (!mysqli_stmt_prepare($result, $sql)) {
			      			    echo '<option value="">Error en carregar els departaments</option>';
			      			} else {
			      			    mysqli_stmt_execute($result);
			      			    $resultl = mysqli_stmt_get_result($result);
			      			    while ($row = mysqli_fetch_assoc($resultl)){
			      			    	echo '<option value="'.$row['device_dep'].'">'.$row['device_dep'].'</option>';
			      			    }
			      			}
			      		?>
			      	</select><br>

<label for="day_of_week"><b>Dia de la setmana:</b></label>
		       	<select name="day_of_week" id="day_of_week" required>
		       		<option value="">Selecciona dia...</option>
		       		<option value="1">Dilluns</option>
		       		<option value="2">Dimarts</option>
		       		<option value="3">Dimecres</option>
		       		<option value="4">Dijous</option>
		       		<option value="5">Divendres</option>
		       		<option value="6">Dissabte</option>
		       		<option value="7">Diumenge</option>
			      	</select><br>

			      	<label for="start_time"><b>Hora d'entrada:</b></label>
			      	<input type="time" name="start_time" id="start_time" required/><br>

			      	<label for="end_time"><b>Hora de sortida:</b></label>
			      	<input type="time" name="end_time" id="end_time" required/><br>
			      </div>
			      <div class="modal-footer">
		        <button type="button" name="schedule_add" id="schedule_add" class="btn btn-success">Crear horari</button>
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel·lar</button>
			      </div>
			  </form>
		    </div>
		  </div>
		</div>
		<!-- //New Schedule Modal -->
	</section>
</main>
</body>
</html>