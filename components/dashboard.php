<?php

try {
  $pdo = new PDO("mysql:host=localhost;dbname=pos_db", "root", "");
  //echo "connection Sucessfull";

} catch (PDOException $f) {

  echo $f->getmessage();
}


//include_once"conectdb.php";
session_start();
if (empty($_SESSION["useremail"])) {
    header("location:index.php");
}elseif ( $_SESSION["role"] !== "Admin") {
    header("location:order_user.php");
}

include_once "header.php";
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">

        </div><!-- /.col -->
          <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
      <li class="breadcrumb-item"> Admin Dashboard</li>
      <li class="breadcrumb-item active"><a href="changepassword.php">Change Password</a></li>
    </ol>
  </div>
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->
  <script src = "js/logout.js"> </script>
  <!-- Main content -->
  <div class="content">

    <!-- Content Row -->
    <div class="row align-items-center">

      <!-- Earnings (Monthly) Card Example -->
     
      <div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-success shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <a href="registration.php"> <!-- Set the href attribute to "productdetails.php" -->
                        <div class="text-xs font-weight-bold text-navy text-uppercase mb-1">
                            Total Users
                        </div>
                        <?php
                        $con = mysqli_connect("localhost", "root", "", "pos_db");
                        if (!$con) {
                            die("Connection Failed:" . mysqli_connect_error());
                        }
                        ?>
                        <?php
                         $results = mysqli_query($con, "SELECT COUNT(*) AS userid FROM tbl_user") or die(mysqli_error());
                         while ($rows = mysqli_fetch_array($results)) {
                             echo $rows['userid'];
                        }
                        ?>
                    </a>
                </div>
                <div class="col-auto">
                <i class="fas fa-user fa-2x user-icon text-teal"></i>
                </div>
            </div>
        </div>
    </div>
</div>


      <!-- Earnings (Monthly) Card Example -->
      <div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-success shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <a href="productlist.php"> <!-- Set the href attribute to "productdetails.php" -->
                        <div class="text-xs font-weight-bold text-navy text-uppercase mb-1">
                            TOTAL STOCKS
                        </div>
                        <?php
                        $con = mysqli_connect("localhost", "root", "", "pos_db");
                        if (!$con) {
                            die("Connection Failed:" . mysqli_connect_error());
                        }
                        ?>
                        <?php
                    $results = mysqli_query($con, "SELECT sum(pstock) FROM tbl_product") or die(mysqli_error());
                    while ($rows = mysqli_fetch_array($results)) {
                      echo $rows['sum(pstock)'];
                    }
                    ?>
                    </a>
                </div>
                <div class="col-auto">
                    <i class="fab fa-product-hunt fa-2x text-gray-300 product-icon text-teal"></i>
                </div>
            </div>
        </div>
    </div>
</div>


      <!-- Pending Requests Card Example -->
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
              <a href="overall-sales.php"> 
                <div class="text-xs font-weight-bold text-navy text-uppercase mb-1">Total Sales</div>
                <?php
              $con = mysqli_connect("localhost", "root", "", "pos_db");
              if (!$con) {
                  die("Connection Failed:" . mysqli_connect_error());
              }

              $results = mysqli_query($con, "SELECT sum(total) FROM tbl_invoice") or die(mysqli_error());
              while ($rows = mysqli_fetch_array($results)) {
                  $totalEarnings = number_format($rows['sum(total)'], 2);
                  echo $totalEarnings;
              }
              ?>

              </a>
              </div>
              <div class="col-auto">
                <i class="fas fa-money-bill fa-2x text-gray-300 sales-icon text-teal"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Earnings (Monthly) Card Example -->
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
              <a href="orderlist.php"> 
                <div class="text-xs font-weight-bold text-navy text-uppercase mb-1">
                  Total Invoice</div>
                  <?php
                        $con = mysqli_connect("localhost", "root", "", "pos_db");
                        if (!$con) {
                            die("Connection Failed:" . mysqli_connect_error());
                        }
                        ?>
                        <?php
                         $results = mysqli_query($con, "SELECT COUNT(*) AS invoice_id FROM tbl_invoice") or die(mysqli_error());
                         while ($rows = mysqli_fetch_array($results)) {
                             echo $rows['invoice_id'];
                        }
                        ?>
                </a>
              </div>
              <div class="col-auto">
                <i class="fas fa-file-alt fa-2x text-gray-300 invoice-icon text-teal"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  <!-- Content Row -->
<div class="row">
    <!-- Pie Chart for Product and Total Remaining Stocks -->
    <!--  -->

  <!-- Bar Chart for Product Sold Rankings -->
<div class="container" style="width: 900px;">
    <div class="card shadow mb-4">
        <!-- Card Header - Dropdown -->
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <div style="text-align: center;">
      <h6 class="m-0 font-weight-bold text-primary">Top 10 Sold Product Rankings:</h6>
    </div>

        </div>
        <!-- Card Body -->
        <div class="card-body">
            <?php
            // Top 5 sold products
            $topSoldQuery = "SELECT p.pname, SUM(id.qty) as total_sold
                             FROM tbl_product p
                             LEFT JOIN tbl_invoice_details id ON p.pid = id.product_id
                             GROUP BY p.pname
                             ORDER BY total_sold DESC
                             LIMIT 10";
            $topSoldResult = mysqli_query($con, $topSoldQuery);
            while ($row = mysqli_fetch_assoc($topSoldResult)) {
                $topSoldDataPoints[] = array("Product" => $row['pname'], "y" => $row['total_sold']);
            }


            ?>
            <!DOCTYPE HTML>
            <html>
            <head>
                <script>
                    window.onload = function() {
                        // Top 5 sold products chart
                        var topSoldChart = new CanvasJS.Chart("topSoldChartContainer", {
                            animationEnabled: true,
                            data: [{
                                type: "bar",
                                yValueFormatString: "#,##0.\"\"",
                                indexLabel: "{Product}  ({y})",
                                dataPoints: <?php echo json_encode($topSoldDataPoints, JSON_NUMERIC_CHECK); ?>
                            }]
                        });
                        topSoldChart.render();
                    }
                </script>
            </head>
            <body>
                <div id="topSoldChartContainer" style="height: 300px; width: 100%;"></div>
                <script src="js/canvasjs.min.js"></script>
            </body>
        </div>
    </div>
</div>
</div>


<!-- Overall Sales Chart -->
<?php
$connect = mysqli_connect("localhost", "root", "", "pos_db");
$query = "SELECT i.order_date, i.total, i.discount, SUM(id.qty) as total_products
          FROM tbl_invoice i
          LEFT JOIN (
              SELECT invoice_id, SUM(qty) as qty
              FROM tbl_invoice_details
              GROUP BY invoice_id
          ) id ON i.invoice_id = id.invoice_id
          GROUP BY i.order_date, i.paid, i.discount";
$result = mysqli_query($connect, $query);
$chart_data = '';
while ($row = mysqli_fetch_array($result)) {
    $chart_data .= "{ order_date:'" . $row["order_date"] . "', total:" . $row["total"] .  ", discount:" . $row["discount"] . ", total_products:" . $row["total_products"] . "}, ";
}
$chart_data = substr($chart_data, 0, -2);
?>




 <head> 
  <style>
        .button-container {
            text-align: right;
            margin-top: 10px;

        }


  /* Customize the card styles */
  .card {
    background-color: #F5F5F5; /* Set the background color */
    border: 1px solid #E0E0E0; /* Set the border color */
    border-radius: 10px; /* Add rounded corners */
    margin: 10px; /* Add some margin for spacing */
  }

  /* Customize the card header styles */
  
  /* Customize the icon styles */
  .user-icon, .product-icon, .sales-icon, .invoice-icon {
    color: #007BFF; /* Set the icon color */
  }

  /* Customize the breadcrumb link styles */
  .breadcrumb a {
    color: #007BFF; /* Set the link color */
  }

  /* Style the user icon */

</style>

    <link rel="stylesheet" href="style/morris.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/raphael-min.js"></script>
    <script src="js/morris.min.js"></script>
 </head>
 <br /><br/>
  <div class="container" style="width:900px;">
  <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
  <h6 class="m-0 font-weight-bold text-primary">Overall Sales:</h6>
  <div class="button-container" style="margin-left: 340px;">
    <label for="dateRange" style="font-size: 18px;">Select</label>
    <input type="date" id="startDate"> To <input type="date" id="endDate">
    <button onclick="filterChart()" class="btn btn-info" style="font-size: 13px;">Select</button>
</div>

  </div>
  
    <div class = "card-body">
    <div id="chart"></div>


  
</div>
<script>
function filterChart() {
    var startDate = document.getElementById("startDate").value;
    var endDate = document.getElementById("endDate").value;

    // Filter the data based on the selected date range
    var filteredData = [];
    for (var i = 0; i < chartData.length; i++) {
        var orderDate = chartData[i].order_date;
        if (orderDate >= startDate && orderDate <= endDate) {
            filteredData.push(chartData[i]);
        }
    }

    // Update the Morris chart with the filtered data
    chart.setData(filteredData);
}

var chartData = [<?php echo $chart_data; ?>];

var chart = Morris.Bar({
    element: 'chart',
    data: chartData,
    xkey: 'order_date',
    ykeys: ['total', 'discount', 'total_products'],
    labels: ['Total Sales',  'Discount', 'Products Sold'],
    hideHover: 'auto',
    stacked: true
});

   
</script>
</div>
    </div>
  </div>
<?php
include_once "footer.php";


?>