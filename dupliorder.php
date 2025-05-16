<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pos_db", "root", "");
    echo "Connection Successful";
} catch (PDOException $f) {
    echo $f->getMessage();
}

session_start();

if ($_SESSION["useremail"] == "" || $_SESSION["role"] == "user") {
    header("location:index.php");
}

include_once "header.php";

function fill_product($pdo) {
    $output = "";
    $select = $pdo->prepare("SELECT * FROM tbl_product ORDER BY pname ASC");
    $select->execute();
    $result = $select->fetchAll();
    foreach ($result as $row) {
        $output .= '<option value="' . $row["pid"] . '">' . $row["pname"] . '</option>';
    }
    return $output;
}

if (isset($_POST["btnsaveorder"])) {
    $customer_name = $_POST["txtcustomer"];
    $order_date = date("Y-m-d H:i:s", strtotime($_POST['orderdate']));
    $subtotal = $_POST["txtsubtotal"];
    $tax = $_POST["txttax"];
    $discount = $_POST["txtdiscount"];
    $total = $_POST["txttotal"];
    $paid = $_POST["txtpaid"];
    $due = $_POST["txtdue"];
    $payment_type = $_POST["rb"];
    
    $arr_productid = $_POST['productid'];
    $arr_productname = $_POST['productname'];
    $arr_stock = $_POST['stock'];
    $arr_qty = $_POST['qty'];
    $arr_price = $_POST['price'];
    $arr_total = $_POST['total'];

    $insert = $pdo->prepare("INSERT INTO tbl_invoice(customer_name, order_date, subtotal, tax, discount, total, paid, due, payment_type) VALUES (:cust, :orderdate, :stotal, :tax, :disc, :total, :paid, :due, :ptype)");
    $insert->bindParam(':cust', $customer_name);
    $insert->bindParam(':orderdate', $order_date);
    $insert->bindParam(':stotal', $subtotal);
    $insert->bindParam(':tax', $tax);
    $insert->bindParam(':disc', $discount);
    $insert->bindParam(':total', $total);
    $insert->bindParam(':paid', $paid);
    $insert->bindParam(':due', $due);
    $insert->bindParam(':ptype', $payment_type);
    $insert->execute();

    $invoice_id = $pdo->lastInsertId();
    
    if ($invoice_id != null) {
        for ($i = 0; $i < count($arr_productid); $i++) {
            $rem_qty = $arr_stock[$i] - $arr_qty[$i];
            if ($rem_qty < 0) {
                echo "Order is Not Complete";
            } else {
                $update = $pdo->prepare("UPDATE tbl_product SET pstock = :rem_qty WHERE pid = :pid");
                $update->bindParam(":rem_qty", $rem_qty);
                $update->bindParam(":pid", $arr_productid[$i]);
                $update->execute();
            }
            $insert = $pdo->prepare("INSERT INTO table_invoice_details(invoice_id, product_id, product_name, qty, price, order_date) VALUES (:invid, :pid, :pname, :qty, :price, :orderdate)");
            $insert->bindParam(":invid", $invoice_id);
            $insert->bindParam(":pid", $arr_productid[$i]);
            $insert->bindParam(":pname", $arr_productname[$i]);
            $insert->bindParam(":qty", $arr_qty[$i]);
            $insert->bindParam(":price", $arr_price[$i]);
            $insert->bindParam(":orderdate", $order_date);
            $insert->execute();
        }
        echo '<script type="text/javascript">window.location = "orderlist.php"</script>';
    }
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Categories</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="logout.php">LOGOUT</a></li>
                        <li class="breadcrumb-item active">Admin Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <!-- Main content -->
    <section class="content container-fluid">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Categories Addition</h3>
            </div>
            <div class="card-body">
                <form role="form" action="" method="post">
                    <!-- Form fields here -->
                </form>
            </div>
        </div>
    </section>
</div>
<!-- /.content-wrapper -->

<!-- Additional scripts and HTML may be present here -->
<?php
include_once "footer.php";
?>
