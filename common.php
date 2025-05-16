<?php


session_start();

if (!empty($_SESSION["useremail"]) && $_SESSION["role"] === "Admin") {
    $user_id = $_SESSION["userid"];
    $login_date = date("Y-m-d");
    $login_time = date("H:i:s");

    $insert_login_history = $pdo->prepare("INSERT INTO tbl_login_history (user_id, login_date, login_time) VALUES (:user_id, :login_date, :login_time)");
    $insert_login_history->bindParam(":user_id", $user_id);
    $insert_login_history->bindParam(":login_date", $login_date);
    $insert_login_history->bindParam(":login_time", $login_time);
    $insert_login_history->execute();
}
?>
