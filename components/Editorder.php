<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pos_db", "root", "");
} catch (PDOException $f) {
    echo $f->getMessage();
}

session_start();
if ($_SESSION["useremail"] == "" || $_SESSION["role"] == "user") {
    header("location:index.php");
}

include_once "header.php";

function fill_product($pdo, $pid) {
    $output = '';
    $select = $pdo->prepare("select * from tbl_product order by pname asc");
    $select->execute();
    $result = $select->fetchAll();

    foreach ($result as $row) {
        $output .= '<option value="' . $row["pid"] . '"';
        if ($pid == $row['pid']) {
            $output .= 'selected';
        }
        $output .= '>' . $row["pname"] . '</option>';
    }

    return $output;
}

$id = $_GET["id"];
$select = $pdo->prepare("select * from tbl_invoice where invoice_id = $id");
$select->execute();

$row = $select->fetch(PDO::FETCH_ASSOC);
$customer_name = $row["customer_name"];
$order_date = date('Y-m-d', strtotime($row['order_date']));
$subtotal = $row["subtotal"];
$tax = $row["tax"];
$discount = $row["discount"];
$total = $row["total"];
$paid = $row["paid"];
$due = $row["due"];
$payment_type = $row["payment_type"];

$select = $pdo->prepare("select * from table_invoice_details where invoice_id = $id");
$select->execute();

$row_invoice_details = $select->fetchAll(PDO::FETCH_ASSOC);


if (isset($_POST["btnupdateorder"])) {
    $txt_customer_name = $_POST["customer_name"]; // Updated name attribute
    $txt_order_date = date('Y-m-d', strtotime($_POST['order_date'])); // Updated name attribute
    $txt_total = $_POST["total"];
    $txt_paid = $_POST["paid"];
    $txt_payment_type = $_POST["rb"];
 
   
    $arr_total = $_POST['total'];


    $delete_invoice_details = $pdo->prepare("delete from table_invoice_details where invoice_id=$id");
    $delete_invoice_details->execute();
    $update_invoice = $pdo->prepare("update tbl_invoice set customer_name=:cust,order_date=:orderdate,subtotal=:stotal,tax=:tax,discount=:disc,total=:total,paid=:paid,due=:due,payment_type=:ptype where invoice_id=$id ");
    $update_invoice->bindParam(':cust', $txt_customer_name);
    $update_invoice->bindParam(':orderdate', $txt_order_date);
    $update_invoice->bindParam(':stotal', $txt_subtotal);
    $update_invoice->bindParam(':tax', $txt_tax);
    $update_invoice->bindParam(':disc', $txt_discount);
    $update_invoice->bindParam(':total', $txt_total);
    $update_invoice->bindParam(':paid', $txt_paid);
    $update_invoice->bindParam(':due', $txt_due);
    $update_invoice->bindParam(':ptype', $txt_payment_type);
    $update_invoice->execute();
    $invoice_id = $pdo->lastInsertId();

        echo '<script type="text/javascript">
        window.location = "orderlist.php"
   </script>';
 }
 

?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
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
     <section class="content container-fluid">
    <div class="row justify-content-center">
            <!-- left column -->
            <div class="col-md-6">
                <!-- general form elements -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Enter New Details</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form action="" method="post">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Customer Name</label>
                                <input type="text" class="form-control" id="exampleInputEmail1" name="customer_name" value="<?php echo $customer_name; ?>"
                                    required>
                            </div>
                            <div class="form-group">
                                <label>Order Date</label>
                                <input type="text" class="form-control" value="<?php echo $order_date; ?>" name="order_date" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Total</label>
                                <input type="text" class="form-control" value="<?php echo $total; ?>" name="total" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Paid</label>
                                <input type="text" class="form-control" value="<?php echo $paid; ?>" name="paid" readonly required>
                            </div>

                            <div class="form-group">
                                <label for="exampleInputPassword1">Change</label>
                                <input type="text" class="form-control" value="<?php echo $due;?>" name="due" readonly required>
                            </div>

                            <label>Payment Method:</label>
                                <div class="form-group clearfix">
                                
                                <div class="icheck-primary d-inline">
                                    <input type="radio" id="radioPrimary1" name="rb" value="<?php echo $payment_type; ?>" checked>
                                    <label for="radioPrimary1">Cash
                                    </label>
                                </div>
                                <div class="icheck-primary d-inline">
                                    <input type="radio" id="radioPrimary2" name="rb" value="<?php echo $payment_type; ?>" checked>
                                    <label for="radioPrimary2">Card
                                    </label>
                                </div>
                                </div>

                        <!-- /.card-body -->
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary" name="btnupdateorder">Update Item</button>
                        </div>
                    </form>
                </div>
                <!-- /.card -->
            </div>
        </div>
    </section>
</div>

<script>
    // JavaScript and jQuery code here
</script>
