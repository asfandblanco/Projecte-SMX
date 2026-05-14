<?php  
session_start();
?>
<div class="table-responsive" style="max-height: 500px;"> 
  <table class="table">
    <thead class="table-primary">
      <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Num. Serie</th>
        <th>UID de targeta</th>
        <th>Departament</th>
        <th>Data</th>
        <th>Hora entrada</th>
        <th>Acció</th>
      </tr>
    </thead>
    <tbody class="table-secondary">
      <?php

        //Connect to database
        require'connectDB.php';

        // Function to check if time is within allowed schedule
        function isWithinSchedule($device_dep, $check_date, $check_time, $conn) {
            $day_of_week = date('N', strtotime($check_date)); // Day of week for the check date

            $sql = "SELECT * FROM department_schedules WHERE device_dep=? AND day_of_week=? AND is_active=1";
            $result = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($result, $sql)) {
                return true; // If error, assume within
            }

            mysqli_stmt_bind_param($result, "si", $device_dep, $day_of_week);
            mysqli_stmt_execute($result);
            $resultl = mysqli_stmt_get_result($result);

            if ($row = mysqli_fetch_assoc($resultl)) {
                $start_time = $row['start_time'];
                $end_time = $row['end_time'];

                // Check if check time is within the allowed schedule
                if ($check_time >= $start_time && $check_time <= $end_time) {
                    return true;
                }
                return false;
            }

            return true; // If no schedule set, assume within
        }
        $searchQuery = " ";
        $Start_date = " ";
        $End_date = " ";
        $Start_time = " ";
        $End_time = " ";
        $Card_sel = " ";

        if (isset($_POST['log_date'])) {
          //Start date filter
          if ($_POST['date_sel_start'] != 0) {
              $Start_date = $_POST['date_sel_start'];
              $_SESSION['searchQuery'] = "checkindate='".$Start_date."'";
          }
          else{
              $Start_date = date("Y-m-d");
              $_SESSION['searchQuery'] = "checkindate='".date("Y-m-d")."'";
          }
          //End date filter
          if ($_POST['date_sel_end'] != 0) {
              $End_date = $_POST['date_sel_end'];
              $_SESSION['searchQuery'] = "checkindate BETWEEN '".$Start_date."' AND '".$End_date."'";
          }
          //Time-In filter
          if ($_POST['time_sel'] == "Time_in") {
            //Start time filter
            if ($_POST['time_sel_start'] != 0 && $_POST['time_sel_end'] == 0) {
                $Start_time = $_POST['time_sel_start'];
                $_SESSION['searchQuery'] .= " AND timein='".$Start_time."'";
            }
            elseif ($_POST['time_sel_start'] != 0 && $_POST['time_sel_end'] != 0) {
                $Start_time = $_POST['time_sel_start'];
            }
            //End time filter
            if ($_POST['time_sel_end'] != 0) {
                $End_time = $_POST['time_sel_end'];
                $_SESSION['searchQuery'] .= " AND timein BETWEEN '".$Start_time."' AND '".$End_time."'";
            }
          }
          //Time-out filter
          if ($_POST['time_sel'] == "Time_out") {
            //Start time filter
            if ($_POST['time_sel_start'] != 0 && $_POST['time_sel_end'] == 0) {
                $Start_time = $_POST['time_sel_start'];
                $_SESSION['searchQuery'] .= " AND timeout='".$Start_time."'";
            }
            elseif ($_POST['time_sel_start'] != 0 && $_POST['time_sel_end'] != 0) {
                $Start_time = $_POST['time_sel_start'];
            }
            //End time filter
            if ($_POST['time_sel_end'] != 0) {
                $End_time = $_POST['time_sel_end'];
                $_SESSION['searchQuery'] .= " AND timeout BETWEEN '".$Start_time."' AND '".$End_time."'";
            }
          }
          //Card filter
          if ($_POST['card_sel'] != 0) {
              $Card_sel = $_POST['card_sel'];
              $_SESSION['searchQuery'] .= " AND card_uid='".$Card_sel."'";
          }
          //Department filter
          if ($_POST['dev_uid'] != 0) {
              $dev_uid = $_POST['dev_uid'];
              $_SESSION['searchQuery'] .= " AND device_uid='".$dev_uid."'";
          }
        }
        
        if ($_POST['select_date'] == 1) {
            $Start_date = date("Y-m-d");
            $_SESSION['searchQuery'] = "checkindate='".$Start_date."'";
        }

        // $sql = "SELECT * FROM users_logs WHERE checkindate=? AND pic_date BETWEEN ? AND ? ORDER BY id ASC";
        $sql = "SELECT * FROM users_logs WHERE ".$_SESSION['searchQuery']." ORDER BY id DESC";
        $result = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($result, $sql)) {
            echo '<p class="error">SQL Error</p>';
        }
        else{
            mysqli_stmt_execute($result);
            $resultl = mysqli_stmt_get_result($result);
            if (mysqli_num_rows($resultl) > 0){
                while ($row = mysqli_fetch_assoc($resultl)){
                    $within_schedule = isWithinSchedule($row['device_dep'], $row['checkindate'], $row['timein'], $conn);
                    $timein_style = $within_schedule ? '' : ' style="color: red !important; font-weight: bold !important;"';
        ?>
                  <TR>
                  <TD><?php echo $row['id'];?></TD>
                  <TD><?php echo $row['username'];?></TD>
                  <TD><?php echo $row['serialnumber'];?></TD>
                  <TD><?php echo $row['card_uid'];?></TD>
                  <TD><?php echo $row['device_dep'];?></TD>
                  <TD><?php echo date("d/m/Y", strtotime($row['checkindate']));?></TD>
                  <TD<?php echo $timein_style; ?>><?php echo $row['timein'];?></TD>
                  <TD><button type="button" class="btn btn-danger btn-sm delete-log" data-id="<?php echo $row['id'];?>" title="Eliminar aquest registre"><span class="glyphicon glyphicon-trash"></span> Eliminar</button></TD>
                  </TR>
      <?php
                }
            }
        }
        // echo $sql;
      ?>
    </tbody>
  </table>
</div>