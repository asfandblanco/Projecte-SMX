<?php
session_start();
require('connectDB.php');

if (isset($_POST['schedule_add'])) {

    $department = $_POST['department'];
    $day_of_week = $_POST['day_of_week'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    if (empty($department) || empty($day_of_week) || empty($start_time) || empty($end_time)) {
        echo '<p class="alert alert-danger">Tots els camps són obligatoris</p>';
    } elseif ($start_time >= $end_time) {
        echo '<p class="alert alert-danger">L'hora d'entrada ha de ser anterior a l'hora de sortida</p>';
    } else {
        $sql = "INSERT INTO department_schedules (device_dep, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE start_time=?, end_time=?";
        $result = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($result, $sql)){
            echo '<p class="alert alert-danger">Error SQL</p>';
        } else {
            mysqli_stmt_bind_param($result, "sissss", $department, $day_of_week, $start_time, $end_time, $start_time, $end_time);
            mysqli_stmt_execute($result);
            echo 1;
        }
        mysqli_stmt_close($result);
    }
    mysqli_close($conn);
}
elseif (isset($_POST['schedule_del'])) {

    $schedule_id = $_POST['schedule_id'];

    $sql = "DELETE FROM department_schedules WHERE id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo '<p class="alert alert-danger">Error SQL</p>';
    } else {
        mysqli_stmt_bind_param($stmt, "i", $schedule_id);
        mysqli_stmt_execute($stmt);
        echo 1;
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
}
elseif (isset($_POST['schedule_toggle'])) {

    $schedule_id = $_POST['schedule_id'];

    $sql = "UPDATE department_schedules SET is_active = NOT is_active WHERE id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo '<p class="alert alert-danger">Error SQL</p>';
    } else {
        mysqli_stmt_bind_param($stmt, "i", $schedule_id);
        mysqli_stmt_execute($stmt);
        echo 1;
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
}
?>
