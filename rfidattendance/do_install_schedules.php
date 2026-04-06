<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
    echo "Not authorized";
    exit();
}

if (isset($_POST['install'])) {
    require 'connectDB.php';

    // SQL para crear la tabla
    $create_table_sql = "
    CREATE TABLE IF NOT EXISTS `department_schedules` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `device_dep` varchar(20) NOT NULL,
      `day_of_week` int(1) NOT NULL COMMENT '1=Lunes, 2=Martes, 3=Miércoles, 4=Jueves, 5=Viernes, 6=Sábado, 7=Domingo',
      `start_time` time NOT NULL,
      `end_time` time NOT NULL,
      `is_active` tinyint(1) NOT NULL DEFAULT '1',
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `unique_dept_day` (`device_dep`, `day_of_week`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
    ";

    // Ejecutar creación de tabla
    if (mysqli_query($conn, $create_table_sql)) {

        // Insertar horarios por defecto para Informatica
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
            echo "Error al insertar datos por defecto: " . mysqli_error($conn);
        }
    } else {
        echo "Error al crear tabla: " . mysqli_error($conn);
    }

    mysqli_close($conn);
} else {
    echo "Invalid request";
}
?>