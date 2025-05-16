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

// Initialize selected start and end years for Annual Sales
$selectedStartYear = isset($_POST["start_year"]) ? $_POST["start_year"] : date('Y');
$selectedEndYear = isset($_POST["end_year"]) ? $_POST["end_year"] : date('Y');

// Default values for monthly sales
$selectedMonth = date('m');
$selectedYear = date('Y');

if (isset($_POST["btnFilter"])) {
    // Get the selected month and year from the form for monthly sales
    $selectedMonth = isset($_POST["month"]) ? $_POST["month"] : date('m');
    $selectedYear = isset($_POST["year"]) ? $_POST["year"] : date('Y');
}

if (isset($_POST["btnaddsales"])) {
    // Insert the sales data into the database
    $dailysales = $_POST["order_date"];
    $totalsales = $_POST["total"];

    $insert = $pdo->prepare("INSERT INTO tbl_invoice (order_date, total) VALUES (:dailysales, :totalsales)");
    $insert->bindParam(":dailysales", $dailysales);
    $insert->bindParam(":totalsales", $totalsales);

    if ($insert->execute()) {
        // Update tbl_sales with daily sales data
        $updateSales = $pdo->prepare("UPDATE tbl_sales SET daily = daily + :totalsales WHERE 1");
        $updateSales->bindParam(":totalsales", $totalsales);
        $updateSales->execute();

        echo "<script type='text/javascript'>
        jQuery(function validation(){
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Saved',
                text: 'Daily Sales Added Successfully',
                showConfirmButton: false,
                timer: 3000
            })
        });
        </script>";
    } else {
        echo "<script type='text/javascript'>
        jQuery(function validation(){
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Failed',
                text: 'Unable to Add Daily Sales',
                showConfirmButton: false,
                timer: 3000
            })
        });
        </script>";
    }
}

// Monthly Sales Query
$selectMonthly = $pdo->prepare("SELECT 
    inv.invoice_id,
    inv.order_date,
    inv_det.product_name,
    inv_det.qty,
    inv_det.price AS total_earnings,
    pid.image
FROM tbl_invoice AS inv
LEFT JOIN tbl_invoice_details AS inv_det ON inv.invoice_id = inv_det.invoice_id
LEFT JOIN tbl_product AS pid ON inv_det.product_id = pid.pid
WHERE MONTH(inv.order_date) = :selectedMonth AND YEAR(inv.order_date) = :selectedYear
GROUP BY inv_det.product_name, inv.order_date
ORDER BY inv.order_date DESC");

$selectMonthly->bindParam(":selectedMonth", $selectedMonth);
$selectMonthly->bindParam(":selectedYear", $selectedYear);
$selectMonthly->execute();

// Annual Sales Query
$selectAnnual = $pdo->prepare("SELECT 
    inv.invoice_id,
    inv.order_date,
    SUM(inv_det.qty) AS total_qty,
    SUM(inv_det.price) AS total_earnings
FROM tbl_invoice AS inv
LEFT JOIN tbl_invoice_details AS inv_det ON inv.invoice_id = inv_det.invoice_id
WHERE YEAR(inv.order_date) BETWEEN :startYear AND :endYear
GROUP BY inv.invoice_id, inv.order_date
ORDER BY inv.order_date DESC");

$selectAnnual->bindParam(":startYear", $selectedStartYear);
$selectAnnual->bindParam(":endYear", $selectedEndYear);
$selectAnnual->execute();

// Monthly total sales
$totalMonthlySales = 0;

// Annual total sales
$totalAnnualSales = 0;

?>

<!-- ... (rest of your HTML code) ... -->

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <!-- Monthly Sales Column -->
                <div class="col-sm-6">
                    <!-- Monthly Sales Filter Form -->
                    <div class="card card-primary mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Filter Monthly Sales</h3>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <label for="month">Select Month:</label>
                                <select name="month" id="month" class="form-control">
                                    <?php
                                    for ($i = 1; $i <= 12; $i++) {
                                        $monthName = date("F", mktime(0, 0, 0, $i, 1));
                                        $selected = ($i == $selectedMonth) ? 'selected' : '';
                                        echo "<option value=\"$i\" $selected>$monthName</option>";
                                    }
                                    ?>
                                </select>

                                <label for="year" class="mt-2">Select Year:</label>
                                <select name="year" id="year" class="form-control">
                                    <?php
                                    $currentYear = date('Y');
                                    for ($i = $currentYear; $i >= $currentYear - 10; $i--) {
                                        $selected = ($i == $selectedYear) ? 'selected' : '';
                                        echo "<option value=\"$i\" $selected>$i</option>";
                                    }
                                    ?>
                                </select>

                                <button type="submit" class="btn btn-primary mt-2" name="btnFilter">Show Sales</button>
                            </form>
                        </div>
                    </div>

                    <!-- Monthly Sales Table -->
                    <div class="card card-info">
                        <div class="card-header">
                        <div>Total Monthly Sales: ₱<?php echo number_format($totalMonthlySales, 2); ?></div>
                        </div>
                        <div class="card-body">
                            <!-- Display total monthly sales -->
                            
                            <div class="table-responsive">
                                <table id="monthlyTable" class="table table-bordered table-hover">
                                    <thead>
                                        <tr class="bg-lightblue">
                                            <th class='justify-center' style='text-align: center;'>Invoice Id</th>
                                            <th class='justify-center' style='text-align: center;'>Product Name</th>
                                            <th class='justify-center' style='text-align: center;'>Date</th>
                                            <th class='justify-center' style='text-align: center;'>Total Earnings</th>
                                            <th class='justify-center' style='text-align: center;'>Image</th>
                                            <th class='justify-center' style='text-align: center;'>Total Purchase Items</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($row = $selectMonthly->fetch(PDO::FETCH_OBJ)) {
                                            // Format the order date to display the month name
                                            $formattedOrderDate = date('F j, Y', strtotime($row->order_date));
                                            // Format the total earnings with two decimal places
                                            $formattedTotalEarnings = number_format($row->total_earnings, 2);
                                            // Accumulate total monthly sales
                                            $totalMonthlySales += $row->total_earnings;
                                            echo "<tr>
                                                <td class='justify-center' style='text-align: center;'>$row->invoice_id</td>
                                                <td class='justify-center' style='text-align: center;'>$row->product_name</td>
                                                <td class='justify-center' style='text-align: center;'>$formattedOrderDate</td>
                                                <td class='justify-center' style='text-align: center;'>₱$formattedTotalEarnings</td>
                                                <td class='justify-center' style='text-align: center;'>
                                                    <img src='$row->image' width='100px' height='75px'>
                                                </td>
                                                <td class='justify-center' style='text-align: center;'>$row->qty</td>
                                            </tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Annual Sales Column -->
                <div class="col-sm-6">
                    <!-- Annual Sales Filter Form -->
                    <div class="card card-primary mb-3">
                        <div class="card-header">
                            <h3 class="card-title">Filter Annual Sales</h3>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <label for="start_year" class="mt-2">Select Start Year:</label>
                                <input type="number" name="start_year" id="start_year" class="form-control" value="<?php echo $selectedStartYear; ?>" />

                                <label for="end_year" class="mt-2">Select End Year:</label>
                                <input type="number" name="end_year" id="end_year" class="form-control" value="<?php echo $selectedEndYear; ?>" />

                                <button type="submit" class="btn btn-primary mt-2" name="btnFilter">Show Sales</button>
                            </form>
                        </div>
                    </div>

                    <!-- Annual Sales Table -->
                    <div class="card card-info">
                        <div class="card-header">
                                          <div>Total Annual Sales: ₱<?php echo number_format($totalAnnualSales, 2); ?></div>
                        </div>
                        <div class="card-body">
                            <!-- Display total annual sales -->
                           
                            <div class="table-responsive">
                                <table id="annualTable" class="table table-bordered table-hover">
                                    <thead>
                                        <tr class="bg-lightblue">
                                            <th class='justify-center' style='text-align: center;'>Invoice Id</th>
                                            <th class='justify-center' style='text-align: center;'>Date</th>
                                            <th class='justify-center' style='text-align: center;'>Total Earnings</th>
                                            <th class='justify-center' style='text-align: center;'>Total Purchase Items</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($row = $selectAnnual->fetch(PDO::FETCH_OBJ)) {
                                            // Format the order date to display the month name
                                            $formattedOrderDate = date('F j, Y', strtotime($row->order_date));
                                            // Format the total earnings with two decimal places
                                            $formattedTotalEarnings = number_format($row->total_earnings, 2);
                                            // Accumulate total annual sales
                                            $totalAnnualSales += $row->total_earnings;
                                            echo "<tr>
                                                <td class='justify-center' style='text-align: center;'>$row->invoice_id</td>
                                                <td class='justify-center' style='text-align: center;'>$formattedOrderDate</td>
                                                <td class='justify-center' style='text-align: center;'>₱$formattedTotalEarnings</td>
                                                <td class='justify-center' style='text-align: center;'>$row->total_qty</td>
                                            </tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(function () {
        $("#monthlyTable").DataTable({
            "lengthChange": false,
            "autoWidth": false,
            "order": [[0, "asc"]],
            "buttons": [
                {
                    extend: "csv",
                    text: '<i class="fas fa-file-csv" style="color: green;"></i> CSV',
                },
                {
                    extend: "pdf",
                    text: '<i class="fas fa-file-pdf" style="color: red;"></i> PDF',
                },
                "colvis",
            ],
        }).buttons().container().appendTo('#monthlyTable_wrapper .col-md-6:eq(0)');
        $("[data-toggle='tooltip']").tooltip();
    });
</script>

<script src="js/salesdlt.js"></script>

<script>
    $(function () {
        $("#annualTable").DataTable({
            "lengthChange": false,
            "autoWidth": false,
            "order": [[0, "asc"]],
            "buttons": [
                {
                    extend: "csv",
                    text: '<i class="fas fa-file-csv" style="color: green;"></i> CSV',
                },
                {
                    extend: "pdf",
                    text: '<i class="fas fa-file-pdf" style="color: red;"></i> PDF',
                },
                "colvis",
            ],
        }).buttons().container().appendTo('#annualTable_wrapper .col-md-6:eq(0)');
        $("[data-toggle='tooltip']").tooltip();
    });
</script>

<script>
    function printInvoice() {
        window.print();
    }
</script>

<?php
include_once "footer.php";
?>
