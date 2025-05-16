<?php
// Database Connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pos_db", "root", "");
} catch (PDOException $e) {
    echo $e->getMessage();
}

session_start();
if (empty($_SESSION["useremail"])) {
    header("location:index.php");
} elseif ($_SESSION["role"] !== "Admin") {
    header("location:order_user.php");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['id'];
    $stock_txt = $_POST['txtstock'];

    // Update the product stock
    $update = $pdo->prepare("UPDATE tbl_product SET pstock=:pstock WHERE pid = :id");
    $update->bindParam(":pstock", $stock_txt);
    $update->bindParam(":id", $product_id, PDO::PARAM_INT);

    if ($update->execute()) {
        echo 'Product updated successfully';

        // Add logic to update the notification buttons here
        // You can use a similar approach as in the editproduct.php file
    } else {
        http_response_code(500);
        echo 'Failed to update product';
    }
}
?>
