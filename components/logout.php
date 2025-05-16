<?php
include_once("connectdb.php");

session_start();



// Check if the user is logged in
if (isset($_SESSION["userid"])) {
    try {
        // Establish a connection to the database
        $pdo = new PDO("mysql:host=localhost;dbname=pos_db", "root", "");
        // Set the timezone for the MySQL connection to UTC
        $pdo->exec("SET time_zone = '+00:00'");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }

    // Capture user ID before destroying the session
    $user_id = $_SESSION["userid"];

    // Insert logout history when a user logs out
    $logout_date = date("Y-m-d");
    $logout_time = date("H:i:s");

    // Convert logout time to UTC before saving to the database
    $logout_time_utc = new DateTime($logout_time, new DateTimeZone('UTC'));
    $logout_time_utc->modify('+12 hours'); // Add 12 hours to get UTC time

    // Update the latest login history record for the user with logout details
    $update_logout_history = $pdo->prepare("UPDATE tbl_login_history SET logout_time = :logout_time WHERE user_id = :user_id AND logout_time IS NULL ORDER BY login_id DESC LIMIT 1");
    $update_logout_history->bindParam(":user_id", $user_id);
    $update_logout_history->bindParam(":logout_time", $logout_time_utc->format('H:i:s'));

    // Execute the query and check for success
    if ($update_logout_history->execute()) {
        // Clear session and redirect
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();
    } else {
        echo "Error updating logout history.";
        // Handle the case where the update fails
    }
} else {
    // If the user is not logged in, redirect to index.php
    header("Location: index.php");
    exit();
}
?>
