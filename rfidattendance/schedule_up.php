<?php
session_start();
?>
<div class="table-responsive">
	<table class="table">
		<thead>
	      <tr>
	        <th>Departament</th>
	        <th>Dia</th>
	        <th>Hora d'entrada</th>
	        <th>Hora de sortida</th>
	        <th>Estat</th>
	        <th>Accions</th>
	      </tr>
    	</thead>
    	<tbody>
			<?php
		    	require'connectDB.php';

$days = array(1=>'Dilluns', 2=>'Dimarts', 3=>'Dimecres', 4=>'Dijous', 5=>'Divendres', 6=>'Dissabte', 7=>'Diumenge');

		    	$sql = "SELECT * FROM department_schedules ORDER BY device_dep, day_of_week";
				$result = mysqli_stmt_init($conn);
				if (!mysqli_stmt_prepare($result, $sql)) {
				    echo '<p class="error">SQL Error</p>';
				} else {
				    mysqli_stmt_execute($result);
				    $resultl = mysqli_stmt_get_result($result);
				    echo '<form action="" method="POST" enctype="multipart/form-data">';
					    while ($row = mysqli_fetch_assoc($resultl)){

					    	$status = ($row["is_active"] == 1) ? '<span class="badge badge-success">Actiu</span>' : '<span class="badge badge-danger">Inactiu</span>';
					    	$toggle_btn = ($row["is_active"] == 1) ? 'Desactivar' : 'Activar';
					    	$toggle_class = ($row["is_active"] == 1) ? 'btn-warning' : 'btn-success';

					    	echo '<tr>
							        <td>'.$row["device_dep"].'</td>
							        <td>'.$days[$row["day_of_week"]].'</td>
							        <td>'.date("H:i", strtotime($row["start_time"])).'</td>
							        <td>'.date("H:i", strtotime($row["end_time"])).'</td>
							        <td>'.$status.'</td>
							        <td>
								    	<button type="button" class="btn btn-sm '.$toggle_class.' schedule_toggle" data-id="'.$row["id"].'" title="'.$toggle_btn.' horari"><span class="glyphicon glyphicon-off"></span> '.$toggle_btn.'</button>
								    	<button type="button" class="btn btn-sm btn-danger schedule_del" data-id="'.$row["id"].'" title="Eliminar horari"><span class="glyphicon glyphicon-trash"></span></button>
								    </td>
							      </tr>';
					    }
				    echo '</form>';
				}
		    ?>
    	</tbody>
	</table>
</div>