<?php
//Connect to database
require 'connectDB.php';
date_default_timezone_set('Europe/Madrid');
$d = date("Y-m-d");
$t = date("H:i:s");

// Log all requests for debugging
$log_file = 'rfid_log.txt';
$log_entry = date("Y-m-d H:i:s") . " - Request: " . $_SERVER['REQUEST_URI'] . "\n";
file_put_contents($log_file, $log_entry, FILE_APPEND);

// Function to check if current time is within allowed schedule
function isWithinSchedule($device_dep, $current_time, $conn) {
    $day_of_week = date('N'); // 1=Monday, 7=Sunday

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

        // Check if current time is within the allowed schedule
        if ($current_time >= $start_time && $current_time <= $end_time) {
            return true;
        }
        return false;
    }

    return true; // If no schedule set, assume within
}

if (isset($_GET['card_uid']) && isset($_GET['device_token'])) {

    $card_uid = $_GET['card_uid'];
    $device_uid = $_GET['device_token'];

    file_put_contents($log_file, date("Y-m-d H:i:s") . " - Processing card_uid: $card_uid, device_token: $device_uid\n", FILE_APPEND);

    $sql = "SELECT * FROM devices WHERE device_uid=?";
    $result = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($result, $sql)) {
        echo "SQL_Error_Select_device";
        exit();
    }
    else{
        mysqli_stmt_bind_param($result, "s", $device_uid);
        mysqli_stmt_execute($result);
        $resultl = mysqli_stmt_get_result($result);
        if ($row = mysqli_fetch_assoc($resultl)){
            $device_mode = $row['device_mode'];
            $device_dep = $row['device_dep'];
            file_put_contents($log_file, date("Y-m-d H:i:s") . " - Device found: mode=$device_mode, dep=$device_dep\n", FILE_APPEND);
            if ($device_mode == 1) {
                $sql = "SELECT * FROM users WHERE card_uid=?";
                $result = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($result, $sql)) {
                    echo "SQL_Error_Select_card";
                    exit();
                }
                else{
                    mysqli_stmt_bind_param($result, "s", $card_uid);
                    mysqli_stmt_execute($result);
                    $resultl = mysqli_stmt_get_result($result);
                    if ($row = mysqli_fetch_assoc($resultl)){
                        file_put_contents($log_file, date("Y-m-d H:i:s") . " - User found: $row[username], add_card=$row[add_card], device_uid=$row[device_uid]\n", FILE_APPEND);
                        //*****************************************************
                        //An existed Card has been detected for Login or Logout
                        if ($row['add_card'] == 1){
                        if ($row['device_uid'] == $device_uid || $row['device_uid'] == 0){
                                $Uname = $row['username'];
                                $Number = $row['serialnumber'];

                                // Check if user is within allowed schedule
                                $within_schedule = isWithinSchedule($device_dep, $t, $conn);
                                if (!$within_schedule) {
                                    file_put_contents($log_file, date("Y-m-d H:i:s") . " - Out of schedule for dep=$device_dep, time=$t\n", FILE_APPEND);
                                }
                                file_put_contents($log_file, date("Y-m-d H:i:s") . " - Within schedule\n", FILE_APPEND);

                                $sql = "SELECT * FROM users_logs WHERE card_uid=? AND checkindate=? AND card_out=0";
                                $result = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($result, $sql)) {
                                    echo "SQL_Error_Select_logs";
                                    exit();
                                }
                                else{
                                    mysqli_stmt_bind_param($result, "ss", $card_uid, $d);
                                    mysqli_stmt_execute($result);
                                    $resultl = mysqli_stmt_get_result($result);
                                    //*****************************************************
                                    //Login
                                    if (!$row = mysqli_fetch_assoc($resultl)){
                                        file_put_contents($log_file, date("Y-m-d H:i:s") . " - Login for $Uname\n", FILE_APPEND);

                                        $sql = "INSERT INTO users_logs (username, serialnumber, card_uid, device_uid, device_dep, checkindate, timein, timeout) VALUES (? ,?, ?, ?, ?, ?, ?, ?)";
                                        $result = mysqli_stmt_init($conn);
                                        if (!mysqli_stmt_prepare($result, $sql)) {
                                            echo "SQL_Error_Select_login1";
                                            exit();
                                        }
                                        else{
                                            $timeout = "00:00:00";
                                            mysqli_stmt_bind_param($result, "sdssssss", $Uname, $Number, $card_uid, $device_uid, $device_dep, $d, $t, $timeout);
                                            mysqli_stmt_execute($result);

                                            echo "login".$Uname;
                                            exit();
                                        }
                                    }
                                    //*****************************************************
                                    //Logout
                                    else{
                                        file_put_contents($log_file, date("Y-m-d H:i:s") . " - Logout for $Uname\n", FILE_APPEND);
                                        $sql="UPDATE users_logs SET timeout=?, card_out=1 WHERE card_uid=? AND checkindate=? AND card_out=0";
                                        $result = mysqli_stmt_init($conn);
                                        if (!mysqli_stmt_prepare($result, $sql)) {
                                            echo "SQL_Error_insert_logout1";
                                            exit();
                                        }
                                        else{
                                            mysqli_stmt_bind_param($result, "sss", $t, $card_uid, $d);
                                            mysqli_stmt_execute($result);

                                            echo "logout".$Uname;
                                            exit();
                                        }
                                    }
                                }
                            }
                            else {
                                file_put_contents($log_file, date("Y-m-d H:i:s") . " - Not Allowed! device mismatch\n", FILE_APPEND);
                                echo "Not Allowed!";
                                exit();
                            }
                        }
                        else if ($row['add_card'] == 0){
                            file_put_contents($log_file, date("Y-m-d H:i:s") . " - Not registered\n", FILE_APPEND);
                            echo "Not registerd!";
                            exit();
                        }
                    }
                    else{
                        file_put_contents($log_file, date("Y-m-d H:i:s") . " - Card not found in users table\n", FILE_APPEND);
                        echo "Not found!";
                        exit();
                    }
                }
            }
            else if ($device_mode == 0) {
                //New Card has been added
                $sql = "SELECT * FROM users WHERE card_uid=?";
                $result = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($result, $sql)) {
                    echo "SQL_Error_Select_card";
                    exit();
                }
                else{
                    mysqli_stmt_bind_param($result, "s", $card_uid);
                    mysqli_stmt_execute($result);
                    $resultl = mysqli_stmt_get_result($result);
                    //The Card is available
                    if ($row = mysqli_fetch_assoc($resultl)){
                        $sql = "SELECT card_select FROM users WHERE card_select=1";
                        $result = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($result, $sql)) {
                            echo "SQL_Error_Select";
                            exit();
                        }
                        else{
                            mysqli_stmt_execute($result);
                            $resultl = mysqli_stmt_get_result($result);
                            
                            if ($row = mysqli_fetch_assoc($resultl)) {
                                $sql="UPDATE users SET card_select=0";
                                $result = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($result, $sql)) {
                                    echo "SQL_Error_insert";
                                    exit();
                                }
                                else{
                                    mysqli_stmt_execute($result);

                                    $sql="UPDATE users SET card_select=1 WHERE card_uid=?";
                                    $result = mysqli_stmt_init($conn);
                                    if (!mysqli_stmt_prepare($result, $sql)) {
                                        echo "SQL_Error_insert_An_available_card";
                                        exit();
                                    }
                                    else{
                                        mysqli_stmt_bind_param($result, "s", $card_uid);
                                        mysqli_stmt_execute($result);

                                        echo "available";
                                        exit();
                                    }
                                }
                            }
                            else{
                                $sql="UPDATE users SET card_select=1 WHERE card_uid=?";
                                $result = mysqli_stmt_init($conn);
                                if (!mysqli_stmt_prepare($result, $sql)) {
                                    echo "SQL_Error_insert_An_available_card";
                                    exit();
                                }
                                else{
                                    mysqli_stmt_bind_param($result, "s", $card_uid);
                                    mysqli_stmt_execute($result);

                                    echo "available";
                                    exit();
                                }
                            }
                        }
                    }
                    //The Card is new
                    else{
                        $sql="UPDATE users SET card_select=0";
                        $result = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($result, $sql)) {
                            echo "SQL_Error_insert";
                            exit();
                        }
                        else{
                            mysqli_stmt_execute($result);
                            $sql = "INSERT INTO users (card_uid, card_select, device_uid, device_dep, user_date) VALUES (?, 1, ?, ?, CURDATE())";
                            $result = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($result, $sql)) {
                                echo "SQL_Error_Select_add";
                                exit();
                            }
                            else{
                                mysqli_stmt_bind_param($result, "sss", $card_uid, $device_uid, $device_dep );
                                mysqli_stmt_execute($result);

                                echo "succesful";
                                exit();
                            }
                        }
                    }
                }    
            }
        }
        else{
            file_put_contents($log_file, date("Y-m-d H:i:s") . " - Invalid Device! device_token not found\n", FILE_APPEND);
            echo "Invalid Device!";
            exit();
        }
    }          
}
else {
    file_put_contents($log_file, date("Y-m-d H:i:s") . " - Missing parameters: card_uid or device_token\n", FILE_APPEND);
    echo "Missing parameters";
}
?>