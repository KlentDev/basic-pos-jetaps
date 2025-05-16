<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pos_db", "root", "");
} catch (PDOException $f) {
    echo $f->getMessage();
}

session_start();
if (empty($_SESSION["useremail"])) {
    header("location:index.php");
} elseif ($_SESSION["role"] !== "Admin") {
    header("location:order_user.php");
}

include_once "header.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="sales.php">Back</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content container-fluid">
        <?php
        // Your PHP code for displaying product details
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=pos_db", "root", "");
            // echo "Connection Successful";
        } catch (PDOException $f) {
            echo $f->getMessage();
        }

        if (isset($_GET['date'])) {
            $selectedDate = $_GET['date'];
            // Fetch and display product details for the selected date
            $selectDetails = $pdo->prepare("SELECT
                pid.pname AS product_name,
                inv_det.qty,
                inv_det.price AS total,
                pid.image
            FROM tbl_invoice AS inv
            LEFT JOIN tbl_invoice_details AS inv_det ON inv.invoice_id = inv_det.invoice_id
            LEFT JOIN tbl_product AS pid ON inv_det.product_id = pid.pid
            WHERE inv.order_date = :selectedDate");
            $selectDetails->bindParam(":selectedDate", $selectedDate);
            $selectDetails->execute();
        ?>
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Total Sales</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST">
                            <table id="example1" class="table table-bordered table-hover">
                                <thead class="bg-lightblue">
                                    <th class='justify-center' style='text-align: center;'>Product Name</th>
                                    <th class='justify-center' style='text-align: center;'>Quantity</th>
                                    <th class='justify-center' style='text-align: center;'>Total</th>
                                    <th class='justify-center' style='text-align: center;'>Image</th>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = $selectDetails->fetch(PDO::FETCH_OBJ)) {
                                    ?>
                                        <tr>
                                            <td class='justify-center' style='text-align: center;'><?= $row->product_name ?></td>
                                            <td class='justify-center' style='text-align: center;'><?= $row->qty ?></td>
                                            <td class='justify-center' style='text-align: center;'>₱<?= number_format($row->total * $row->qty, 2) ?></td>
                                            <td class='justify-center' style='text-align: center;'>
                                                <img src='<?= $row->image ?>' width='50' height='50'>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        <?php
        } else {
            echo "No date selected.";
        }
        ?>
    </section>
</div>

<script>
    $(document).ready(function () {
        $("body").tooltip({ selector: '[data-toggle=tooltip]' });
    });
</script>

<script>
    $(function () {
        $("#example1").DataTable({
            "responsive": true, "lengthChange": false, "autoWidth": false, "order": [[0, "asc"]],
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        $("[data-toggle='tooltip']").tooltip();
    });
</script>

<?php
include_once "footer.php";
?>
