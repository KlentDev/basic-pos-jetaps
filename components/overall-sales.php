<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JETAPS POS</title>

 <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- Add this inside the head section or at the end of your HTML body -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.colVis.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
</head>

<body>
<?php
            try {
                $pdo = new PDO("mysql:host=localhost;dbname=pos_db", "root", "");
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }

            session_start();

            if (empty($_SESSION["useremail"])) {
                header("location:index.php");
            } elseif ($_SESSION["role"] !== "Admin") {
                header("location:order_user.php");
            }

            include_once "functional.php";
            include_once "header.php";

            // Handle different views
            $view = isset($_POST['view']) ? $_POST['view'] : 'annual';
            $selectAnnual = null; 
            $selectMonthly = null;
            $selectSales = null;

            $selectedMonth = isset($_POST['month']) ? $_POST['month'] : date('m');
            $selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');

            $totalMonthlySalesEarnings = 0;
            $totalMonthlyQuantity = 0;

            $totalAnnualSalesEarnings = 0;
            $totalAnnualQuantity = 0;

            // Handle different views
            if ($view === 'daily') {
                $totalDailySalesEarnings = 0;
                $totalDailyQuantity = 0;

                // Set default start and end dates to the current date
                $startDate = date('Y-m-d');
                $endDate = date('Y-m-d');

                // Check if the form is submitted with a specific date
                if (isset($_POST["daily"])) {
                    // If submitted, update start and end dates accordingly
                    $startDate = isset($_POST["daily_date"]) ? date('Y-m-d', strtotime($_POST["daily_date"])) : $startDate;
                    $endDate = isset($_POST["daily_date_to"]) ? date('Y-m-d', strtotime($_POST["daily_date_to"])) : $endDate;
                }

                $selectSales = $pdo->prepare("SELECT 
                    inv.invoice_id,
                    inv.order_date,
                    inv_det.product_name,
                    discount,
                    subtotal,
                    SUM(inv_det.qty) AS total_qty,
                    SUM(inv_det.price *inv_det.qty - discount) AS total_earnings,
                    pid.image
                FROM tbl_invoice AS inv
                LEFT JOIN tbl_invoice_details AS inv_det ON inv.invoice_id = inv_det.invoice_id
                LEFT JOIN tbl_product AS pid ON inv_det.product_id = pid.pid
                WHERE DATE(inv.order_date) BETWEEN :startDate AND :endDate
                GROUP BY inv_det.product_name, inv.order_date, inv.invoice_id
                ORDER BY inv.order_date DESC");

                $selectSales->bindParam(":startDate", $startDate);
                $selectSales->bindParam(":endDate", $endDate);

                $selectSales->execute();

                $totalDailySalesEarnings = calculateTotalDailySalesEarnings($startDate, $endDate);
                $totalDailyQuantity = calculateTotalDailyQuantity($startDate, $endDate);

            } elseif ($view === 'monthly') {

                $totalMonthlySalesEarnings = 0;
                $totalMonthlyQuantity = 0;

                // Set default selected month to the current month
                $selectedMonth = isset($_POST['month']) ? $_POST['month'] : date('m');
                $selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');

                if (isset($_POST['submit_monthly'])) {
                    // Fetch data for the selected month
                    $selectMonthly = $pdo->prepare("SELECT 
                        inv.invoice_id,
                        inv_det.order_date,
                        inv_det.product_name,
                        discount,
                        subtotal,
                        SUM(inv_det.qty) AS total_qty,
                        SUM(inv_det.price *inv_det.qty - discount) AS total_earnings,
                        pid.image
                        FROM tbl_invoice AS inv
                        LEFT JOIN tbl_invoice_details AS inv_det ON inv.invoice_id = inv_det.invoice_id
                        LEFT JOIN tbl_product AS pid ON inv_det.product_id = pid.pid
                        WHERE MONTH(inv_det.order_date) = :selectedMonth AND YEAR(inv_det.order_date) = :selectedYear
                        GROUP BY inv_det.product_name, inv.order_date, inv.invoice_id
                        ORDER BY inv_det.order_date DESC");

                    $selectMonthly->bindParam(":selectedMonth", $selectedMonth);
                    $selectMonthly->bindParam(":selectedYear", $selectedYear);
                    $selectMonthly->execute();

                    $totalMonthlySalesEarnings = calculateTotalMonthlySalesEarnings($selectedMonth, $selectedYear);
                    $totalMonthlyQuantity = calculateTotalMonthlyQuantity($selectedMonth, $selectedYear);
                
                } elseif (isset($_POST['submit_range_monthly'])) {
                    // Fetch data for the selected date range
                    $totalMonthlySalesEarnings = 0;
                    $totalMonthlyQuantity = 0;

                    $fromMonthYear = isset($_POST['from_month_year']) ? $_POST['from_month_year'] : date('n-Y');
                    $toMonthYear = isset($_POST['to_month_year']) ? $_POST['to_month_year'] : date('n-Y');

                    list($fromMonth, $fromYear) = explode('-', $fromMonthYear);
                    list($toMonth, $toYear) = explode('-', $toMonthYear);

                    $selectMonthly = $pdo->prepare("SELECT 
                        inv.invoice_id,
                        inv_det.order_date,
                        inv_det.product_name,
                        discount,
                        subtotal,
                        SUM(inv_det.qty) AS total_qty,
                        SUM(inv_det.price *inv_det.qty - discount) AS total_earnings,
                        pid.image
                        FROM tbl_invoice AS inv
                        LEFT JOIN tbl_invoice_details AS inv_det ON inv.invoice_id = inv_det.invoice_id
                        LEFT JOIN tbl_product AS pid ON inv_det.product_id = pid.pid
                        WHERE (MONTH(inv_det.order_date) >= :fromMonth AND YEAR(inv_det.order_date) >= :fromYear)
                        AND (MONTH(inv_det.order_date) <= :toMonth AND YEAR(inv_det.order_date) <= :toYear)
                        GROUP BY inv_det.product_name, inv.order_date, inv.invoice_id
                        ORDER BY inv_det.order_date DESC");

                    $selectMonthly->bindParam(":fromMonth", $fromMonth);
                    $selectMonthly->bindParam(":fromYear", $fromYear);
                    $selectMonthly->bindParam(":toMonth", $toMonth);
                    $selectMonthly->bindParam(":toYear", $toYear);
                    $selectMonthly->execute();

                    $totalMonthlySalesEarnings = calculateTotalRangeMonthlySalesEarnings($fromMonth, $fromYear, $toMonth, $toYear);
                    $totalMonthlyQuantity = calculateTotalRangeMonthlyQuantity($fromMonth, $fromYear, $toMonth, $toYear);
                } else {
                    // Fetch data for the current month
                    $selectMonthly = $pdo->prepare("SELECT 
                        inv.invoice_id,
                        inv_det.order_date,
                        inv_det.product_name,
                        discount,
                       subtotal,
                        SUM(inv_det.qty) AS total_qty,
                        SUM(inv_det.price *inv_det.qty - discount) AS total_earnings,
                        pid.image
                        FROM tbl_invoice AS inv
                        LEFT JOIN tbl_invoice_details AS inv_det ON inv.invoice_id = inv_det.invoice_id
                        LEFT JOIN tbl_product AS pid ON inv_det.product_id = pid.pid
                        WHERE MONTH(inv_det.order_date) = :currentMonth AND YEAR(inv_det.order_date) = :currentYear
                        GROUP BY inv_det.product_name, inv.order_date, inv.invoice_id
                        ORDER BY inv_det.order_date DESC");

                    $currentMonth = date('m');
                    $currentYear = date('Y');

                    $selectMonthly->bindParam(":currentMonth", $currentMonth);
                    $selectMonthly->bindParam(":currentYear", $currentYear);
                    $selectMonthly->execute();

                    $totalMonthlySalesEarnings = calculateTotalMonthlySalesEarnings($currentMonth, $currentYear);
                    $totalMonthlyQuantity = calculateTotalMonthlyQuantity($currentMonth, $currentYear);
                }
             
                  
                        
            } elseif ($view === 'annual') {
                if (isset($_POST['submit_yearly'])) {
                    // Fetch data for the selected year
                    $selectAnnual = $pdo->prepare("SELECT 
                        inv.invoice_id,
                        inv.order_date,
                        inv_det.product_name,
                        discount,
                        subtotal,
                        SUM(inv_det.qty) AS total_qty,
                        SUM(inv_det.price * inv_det.qty - discount) AS total_earnings,
                        pid.image
                        FROM tbl_invoice AS inv
                        LEFT JOIN tbl_invoice_details AS inv_det ON inv.invoice_id = inv_det.invoice_id
                        LEFT JOIN tbl_product AS pid ON inv_det.product_id = pid.pid
                        WHERE YEAR(inv.order_date) = :selectedYear
                        GROUP BY inv_det.product_name, inv.order_date, inv.invoice_id
                        ORDER BY inv.order_date DESC");
            
                    $selectAnnual->bindParam(":selectedYear", $selectedYear);
                    $selectAnnual->execute();
            
                    // Update the function calls in your main code
                    $totalAnnualSalesEarnings = calculateYearlySalesEarnings($selectedYear);
                    $totalAnnualQuantity = calculateYearlyQuantity($selectedYear);
                } elseif (isset($_POST['submit_range_annual'])) {
                    // Date range calculation logic
                    $selectedStartDate = date('Y-m-d', strtotime($_POST['from_date']));
                    $selectedEndDate = date('Y-m-d', strtotime($_POST['to_date']));
            
                    $selectAnnual = $pdo->prepare("SELECT 
                        inv.invoice_id,
                        inv.order_date,
                        inv_det.product_name,
                        discount,
                        subtotal,
                        SUM(inv_det.qty) AS total_qty,
                        SUM(inv_det.price * inv_det.qty - discount) AS total_earnings,
                        pid.image
                        FROM tbl_invoice AS inv
                        LEFT JOIN tbl_invoice_details AS inv_det ON inv.invoice_id = inv_det.invoice_id
                        LEFT JOIN tbl_product AS pid ON inv_det.product_id = pid.pid
                        WHERE DATE(inv.order_date) BETWEEN :startDate AND :endDate
                        GROUP BY inv_det.product_name, inv.order_date, inv.invoice_id
                        ORDER BY inv.order_date DESC");
            
                    $selectAnnual->bindParam(":startDate", $selectedStartDate);
                    $selectAnnual->bindParam(":endDate", $selectedEndDate);
                    $selectAnnual->execute();
            
                    // Update the function calls in your main code
                    $totalAnnualSalesEarnings = calculateRangeYearlySalesEarnings($selectedStartDate, $selectedEndDate);
                    $totalAnnualQuantity = calculateRangeYearlyQuantity($selectedStartDate, $selectedEndDate);
                }
            }
            
?>






</body> 
   

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
                <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">Sales Report </li>
                            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                        </ol>
                    </div>
                </div>
            </div>

        <!-- Main content -->
        <section class="content container-fluid">
        <!-- Right column -->
        <div class="col-md-12">
            <!-- Adjust the column width as needed -->

            <!-- Dropdown for switching views -->
            <div class="mb-3">
    <form action="" method="POST">
        <label for="view">Select View:</label>
        <select name="view" id="view" onchange="this.form.submit()" class="form-select">
    <option value="daily" <?= ($view === 'daily') ? 'selected' : '' ?>>Daily</option>
    <option value="monthly" <?= ($view === 'monthly') ? 'selected' : '' ?>>Monthly</option>
    <option value="annual" <?= ($view === 'annual') ? 'selected' : '' ?>>Annual</option>
</select>

    </form>

    <div class="row">
     <!-- Right column -->
<div class="col-md-11">
<?php if ($view === 'monthly'): ?>
        
        <div class="mb-3">
            <div class="row">

                <!-- Monthly View - Left Column -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-blue">
                            MONTH-TO-DATE REVENUE
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <!-- Monthly View Left Content -->
                                <input type="hidden" name="view" value="monthly"> <!-- Add a hidden input field for the view -->
                                <label for="month">Month:</label>
                                                <select name="month" id="month" class="form-select">
                    <?php
                    $months = [
                        '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
                        '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
                        '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
                    ];

                    foreach ($months as $key => $month) {
                        $selected = ($key == $selectedMonth) ? 'selected' : '';
                        echo "<option value='$key' $selected>$month</option>";
                    }
                    ?>
                                </select>

                                                <label for="year">Year:</label>
                <select name="year" id="year" class="form-select">
                    <?php
                    $selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');
                    $startYear = date('Y');
                    $endYear = $startYear + 5; // Change this to adjust the number of future years
                    for ($i = $startYear; $i <= $endYear; $i++) {
                        $selected = ($i == $selectedYear) ? 'selected' : '';
                        echo "<option value='$i' $selected>$i</option>";
                    }
                    ?>
                </select>

                                <button type="submit" name="submit_monthly" class="btn btn-primary">Show</button>
                            </form>
                        </div>
                    </div>
                </div>


                <!-- Monthly View - Right Column -->
<div class="col-md-6">
    <div class="card">
        <div class="card-header bg-blue">
            CURRENT YEAR'S MONTHLY PERIODS
        </div>
        <div class="card-body">
            <form action="" method="POST">
                <!-- Monthly View Right Content -->
                <input type="hidden" name="view" value="monthly"> <!-- Change the value to indicate the range monthly view -->
                <label for="from_month_year">From:</label>
                <select name="from_month_year" id="from_month_year" class="form-select">
                    <?php
                    for ($i = 1; $i <= 12; $i++) {
                        $selected = (isset($_POST['from_month_year']) && $_POST['from_month_year'] == "$i-" . date('Y')) ? 'selected' : '';
                        echo "<option value='$i-" . date('Y') . "' $selected>" . date('F Y', mktime(0, 0, 0, $i, 1)) . "</option>";
                    }
                    ?>
                </select>

                <label for="to_month_year">To:</label>
                <select name="to_month_year" id="to_month_year" class="form-select">
                    <?php
                    for ($i = 1; $i <= 12; $i++) {
                        $selected = (isset($_POST['to_month_year']) && $_POST['to_month_year'] == "$i-" . date('Y')) ? 'selected' : '';
                        echo "<option value='$i-" . date('Y') . "' $selected>" . date('F Y', mktime(0, 0, 0, $i, 1)) . "</option>";
                    }
                    ?>
                </select>

                <button type="submit" name="submit_range_monthly" class="btn btn-primary">Show</button>
            </form>
        </div>
    </div>
</div>
</div>
</div>
<!---end of monthly period---->




            <?php elseif ($view === 'annual'): ?>
    <div class="mb-3">
        <div class="row">
            <!-- Annual View - Left Column -->
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header bg-blue">
                        YEAR-TO-DATE REVENUE
                    </div>
                    <div class="card-body">
                        <form action="" method="POST">
                        <input type="hidden" name="view" value="annual"> 
                            <label for="year">Year:</label>
                                                <select name="year" id="year" class="form-select">
                        <?php
                        $startYear = date('Y');
                        $endYear = $startYear + 5; // Change this to adjust the number of future years
                        for ($i = $startYear; $i <= $endYear; $i++) {
                            $selected = ($i == $selectedYear) ? 'selected' : '';
                            echo "<option value='$i' $selected>$i</option>";
                        }
                        ?>
                    </select>

                            <button type="submit" name="submit_yearly" class="btn btn-primary">Show</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Annual View - Right Column -->
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header bg-blue">
                        ANNUAL SALES TIME FRAME
                    </div>
                    <div class="card-body">
                        <form action="" method="POST">
                        <input type="hidden" name="view" value="annual"> 
                            <label for="from_date">From:</label>
                            <input type="date" name="from_date" id="from_date" class="form-control" value="<?= htmlspecialchars($selectedStartDate) ?>" required>

                            <label for="to_date">To:</label>
                            <input type="date" name="to_date" id="to_date" class="form-control" value="<?= htmlspecialchars($selectedEndDate) ?>" required>
                            <br>
                            <button type="submit" name="submit_range_annual" class="btn btn-primary">Show</button>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

                <!---end of anual perio---->
                
                <?php elseif ($view === 'daily'): ?>
    <div class="mb-3">
        <!-- Daily View - Left Column -->
        <div class="card">
            <div class="card-header bg-blue">
                DAILY REVENUE
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <!-- Add a hidden input field for the view -->
                    <input type="hidden" name="view" value="daily">
                    
                    <label for="daily_date">From:</label>
                    <input type="date" name="daily_date" id="daily_date" class="form-control" required
                           value="<?php echo isset($_POST['daily_date']) ? $_POST['daily_date'] : ''; ?>">

                    <label for="daily_date_to">To:</label>
                    <input type="date" name="daily_date_to" id="daily_date_to" class="form-control" required
                           value="<?php echo isset($_POST['daily_date_to']) ? $_POST['daily_date_to'] : ''; ?>">
                           <br>
                    <button type="submit" name="daily" class="btn btn-primary">Show</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Daily View - Right Column (Add content here if needed) -->
<?php endif; ?>

        <!-- Left column -->
        <div class="col-md-6">
            <!-- Additional content can be added here if needed -->
        </div>
    </div>

</div>
            

                <!-- Display sales table based on the selected view -->
                <div class="card card-info">
                    <link rel="stylesheet" href="css/buttons.css">
                    <div class="card-header">
                        <h3 class="card-title"> <?php echo ucfirst($view); ?> Sales</h3> 
                    </div>
                    <div class="card-body">
                        <form action="" method="POST">
                            <div class="table-responsive">
          <?php if ($view === 'daily'): ?>
    <!-- Display daily sales table -->
                        <table id="dailySalesTable" class="table table-bordered table-hover">
                            <thead>
                                <tr class="bg-lightblue">
                                    <th  class='justify-center' style='text-align: center;'>Invoice ID</th>
                                    <th  class='justify-center' style='text-align: center;'>Product Name</th>
                                    <th  class='justify-center' style='text-align: center;'>Date</th>
                                    <!-- <th  class='justify-center' style='text-align: center;'>Subtotal</th> -->
                                    <!-- <th  class='justify-center' style='text-align: center;'>Discount</th> -->
                                    <th class='justify-center' style='text-align: center;'>Total</th>
                                    <th class='justify-center' style='text-align: center;'>Total Purchase Items</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($selectSales->execute()) {
                                    // Check if there are rows in the result set
                                    if ($selectSales->rowCount() > 0) {
                                        while ($row = $selectSales->fetch(PDO::FETCH_ASSOC)) {
                                            // Process your data here
                                            echo "<tr>
                                            <td  class='justify-center' style='text-align: center;'>{$row['invoice_id']}</td>
                                            <td class='justify-center' style='text-align: center;'>{$row['product_name']}</td>
                                            <td class='justify-center' style='text-align: center;'>" . date('F j, Y', strtotime($row['order_date'])) . "</td>

                                            <td class='justify-center' style='text-align: center;'>₱" . number_format($row['total_earnings'] , 2) . "</td>
                                            <td class='justify-center' style='text-align: center;'>{$row['total_qty']}</td>
                                        </tr>";
                                        
                                        }
                                    } else {
                                        echo "<tr><td colspan='3'>No data found for the selected date range.</td></tr>";
                                    }
                                } else {
                                    // Handle the error
                                    $errorInfo = $selectSales->errorInfo();
                                    echo "<tr><td colspan='3'>Error: " . $errorInfo[2] . "</td></tr>";
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">Total Daily Sales Earnings</th>
                                    <th class='justify-center' style='text-align: center;'><?php echo '₱' . number_format($totalDailySalesEarnings, 2); ?></th>
                                    <th class='justify-center' style='text-align: center;'><?php echo $totalDailyQuantity; ?></th>
                                </tr>
                            </tfoot>
                        </table>
                                    <?php elseif ($view === 'monthly'): ?>
                                    <!-- Display monthly sales table -->
                                    <table id="monthlySalesTable" class="table table-bordered table-hover">
                                        <thead>
                                       <tr class="bg-lightblue">
                                                <th class='justify-center' style='text-align: center;'>Invoice ID</th>
                                                <th class='justify-center' style='text-align: center;'>Product Name</th>
                                                <th class='justify-center' style='text-align: center;'>Date</th>
                                                <th class='justify-center' style='text-align: center;'>Total Earnings</th>
                                                <th class='justify-center' style='text-align: center;'>Total Purchase Items</th>
                                               
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if ($selectMonthly !== null && $selectMonthly->rowCount() > 0): ?>
                                            <?php while ($row = $selectMonthly->fetch(PDO::FETCH_ASSOC)) : ?>
                                                <tr>
                                                    <td class='justify-center' style='text-align: center;'><?php echo $row['invoice_id']; ?></td>
                                                    <td class='justify-center' style='text-align: center;'><?php echo $row['product_name']; ?></td>
                                                    <td class='justify-center' style='text-align: center;'><?php echo date('F j, Y', strtotime($row['order_date'])); ?></td>
                                                    <td class='justify-center' style='text-align: center;'>₱<?php echo number_format($row['total_earnings']  , 2); ?></td>
                                                    <td class='justify-center' style='text-align: center;'><?php echo $row['total_qty']; ?></td>  
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td class='justify-center' style='text-align: center;'>No data found for the current month.</td>
                                                <!-- You can add additional cells with default values if needed -->
                                            </tr>
                                        <?php endif; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                            <th colspan="3" >Total Monthly Sales Earnings</th>
                                           <th   class='justify-center' style='text-align: center;'> <?php echo '₱' . number_format($totalMonthlySalesEarnings,2 ); ?></th>
                                           <th  class='justify-center' style='text-align: center;'> <?php echo $totalMonthlyQuantity; ?></th>
                                        
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <?php elseif ($view === 'annual'): ?>
    <!-- Display annual sales table -->
                                    <table id="annualSalesTable" class="table table-bordered table-hover">
                                        <thead>
                                            <tr class="bg-lightblue">
                                                <th class='justify-center' style='text-align: center;'>Invoice ID</th>
                                                <th class='justify-center' style='text-align: center;'>Product Name</th>
                                                <th class='justify-center' style='text-align: center;'>Date</th>
                                                <th class='justify-center' style='text-align: center;'>Total Earnings</th>
                                                <th class='justify-center' style='text-align: center;'>Total Purchase Items</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if ($selectAnnual !== null && $selectAnnual->rowCount() > 0) {
                                                while ($row = $selectAnnual->fetch(PDO::FETCH_ASSOC)) {
                                                    echo "<tr>
                                                        <td class='justify-center' style='text-align: center;'>{$row['invoice_id']}</td>
                                                        <td class='justify-center' style='text-align: center;'>{$row['product_name']}</td>
                                                        <td class='justify-center' style='text-align: center;'>" . date('F j, Y', strtotime($row['order_date'])) . "</td>
                                                        <td class='justify-center' style='text-align: center;'>₱" . number_format($row['total_earnings'], 2) . "</td>
                                                        <td class='justify-center' style='text-align: center;'>{$row['total_qty']}</td>
                                                    </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='3'>No data found for the selected year.</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3">Total Annual Sales Earnings</th>
                                                <th class='justify-center' style='text-align: center;'><?php echo '₱' . number_format($totalAnnualSalesEarnings, 2); ?></th>
                                                <th class='justify-center' style='text-align: center;'><?php echo $totalAnnualQuantity; ?></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                <?php endif; ?>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

    
   
<!-- jQuery -->


<?php if ($view === 'daily'): ?>
 <script src = "js/daily.js"></script>
<?php elseif ($view === 'monthly'): ?>
<script src = "js/monthly.js"></script>

<?php elseif ($view === 'annual'): ?>
    <script src = "js/annual.js"></script>
<?php endif; ?>


    <?php
    include_once "footer.php";
    ?>

   