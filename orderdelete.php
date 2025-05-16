<?php

try {
    $pdo = new PDO("mysql:host=localhost;dbname=pos_db", "root", "");
    // echo "connection Successful";
} catch (PDOException $f) {
    echo $f->getMessage();
}

session_start();
if ($_SESSION["useremail"] == "" || $_SESSION["role"] == "user") {
    header("location:index.php");
}

$id = $_POST["invoice_id"];

// Delete from tbl_invoice
$sql_invoice = "DELETE FROM tbl_invoice WHERE invoice_id = :id";
$delete_invoice = $pdo->prepare($sql_invoice);
$delete_invoice->bindParam(":id", $id);

// Delete from tbl_invoice_details
$sql_details = "DELETE FROM tbl_invoice_details WHERE invoice_id = :id";
$delete_details = $pdo->prepare($sql_details);
$delete_details->bindParam(":id", $id);

// Execute both queries
if ($delete_invoice->execute() && $delete_details->execute()) {
    // Send a success response if needed
} else {
    // Send an error response if needed
    echo "Error in deleting";
}


?>