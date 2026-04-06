<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
    echo "No autoritzat";
    exit();
}

if (isset($_POST['install'])) {
    require 'connectDB.php';

    // SQL per crear la taula
    $create_table_sql = "
    CREATE TABLE IF NOT EXISTS `department_schedules` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `device_dep` varchar(20) NOT NULL,
      `day_of_week` int(1) NOT NULL COMMENT '1=Dilluns, 2=Dimarts, 3=Dimecres, 4=Dijous, 5=Divendres, 6=Dissabte, 7=Diumenge',
      `start_time` time NOT NULL,
      `end_time` time NOT NULL,
      `is_active` tinyint(1) NOT NULL DEFAULT '1',
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `unique_dept_day` (`device_dep`, `day_of_week`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
    ";

    // Executar creació de la taula
    if (mysqli_query($conn, $create_table_sql)) {

        // Inserir horaris per defecte per a Informàtica
        $insert_sql = "
        INSERT IGNORE INTO `department_schedules` (`device_dep`, `day_of_week`, `start_time`, `end_time`, `is_active`) VALUES
        ('Informatica', 1, '14:55:00', '15:15:00', 1),
        ('Informatica', 2, '14:55:00', '15:15:00', 1),
        ('Informatica', 3, '15:55:00', '16:15:00', 1),
        ('Informatica', 4, '14:55:00', '15:15:00', 1),
        ('Informatica', 5, '14:55:00', '15:15:00', 1);
        ";

        if (mysqli_query($conn, $insert_sql)) {
            echo "success";
        } else {
            echo "Error en inserir les dades per defecte: " . mysqli_error($conn);
        }
    } else {
        echo "Error en crear la taula: " . mysqli_error($conn);
    }

    mysqli_close($conn);
} else {
    echo "Sol·licitud invàlida";
}
?>