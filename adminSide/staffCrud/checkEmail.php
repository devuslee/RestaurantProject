<?php
// Include your config file to connect to the database
require_once "../config.php";

// Get the email from the AJAX request
if (isset($_POST['email'])) {
    $email = $_POST['email'];

    $sql = "SELECT account_id FROM Accounts WHERE email = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            echo "Email is already taken.";
        } else {
            echo "Email is available.";
        }
    }
    
}
?>