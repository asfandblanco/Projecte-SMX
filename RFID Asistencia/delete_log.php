<?php
session_start();

if (!isset($_SESSION['Admin-name'])) {
    echo "Not authorized";
    exit();
}

if (isset($_POST['delete_id'])) {
    require 'connectDB.php';
    
    $delete_id = intval($_POST['delete_id']);
    
    // Verificar que la conexión existe
    if (!$conn) {
        echo "Database connection failed";
        exit();
    }
    
    $sql = "DELETE FROM users_logs WHERE id = ?";
    $stmt = mysqli_stmt_init($conn);
    
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "SQL Prepare Error: " . mysqli_error($conn);
        exit();
    }
    
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        echo "SQL Execute Error: " . mysqli_error($conn);
        mysqli_stmt_close($stmt);
        exit();
    }
    
    $affected_rows = mysqli_affected_rows($conn);
    
    if ($affected_rows > 0) {
        echo 1;
    }
    else {
        echo "No rows affected. ID: " . $delete_id;
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
else {
    echo "Invalid request - no delete_id";
}
?>
