

<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>JETAPS | POS</title>
  
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  
  <!-- jQuery -->
 <script src="plugins/jquery/jquery.js"></script>
  <script src="plugins/jquery/jquery.min.js"></script>
  
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  
  <!-- Select2 -->
  <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

<!-- date-range-picker -->
<script src="plugins/daterangepicker/daterangepicker.js"></script>


<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<!-- Bootstrap 4 -->

<script src="plugins/sweetalert2/sweetalert2.js"></script>
<script src="plugins/sweetalert2/sweetalert2.all.js"></script>
<script src="plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>


<!-- DataTables -->
  


  

  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  
  
  <script src="plugins/datatables/jquery.dataTables.js"></script>
  <script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="plugins/jszip/jszip.min.js"></script>
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>

  
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">


 <!-- Navbar -->
 <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
     
      
    </ul>

    <!-- SEARCH FORM -->
    

  <!-- Right navbar links -->
<ul class="navbar-nav ml-auto">
  <!-- Messages Dropdown Menu -->
  
  <!-- Notifications Dropdown Menu -->
  <?php
// Count the number of products with low stock
$lowStockCount = 0;
$overStockCount = 0;

$selectLowStock = $pdo->prepare("SELECT * FROM tbl_product WHERE pstock < 10 AND pstock > 0 ORDER BY pid DESC");
$selectLowStock->execute();

$selectOverStock = $pdo->prepare("SELECT * FROM tbl_product WHERE pstock > 500 ORDER BY pid DESC");
$selectOverStock->execute();

while ($row = $selectLowStock->fetch(PDO::FETCH_OBJ)) {
    $quantity = $row->pstock;

    if ($quantity < 10 && $quantity > 0) {
        $lowStockCount++;
    }
}

while ($row = $selectOverStock->fetch(PDO::FETCH_OBJ)) {
    $quantity = $row->pstock;

    if ($quantity > 500) {
        $overStockCount++;
    }
}

// Display the bell icons with the badges and dropdowns
echo "
<li class='nav-item dropdown'>
    <a href='productlist.php' class='nav-link dropdown-toggle' id='lowStockDropdown' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
        <i class='fas fa-exclamation-triangle '></i>
        " . ($lowStockCount > 0 ? "<span class='badge badge-warning'>$lowStockCount</span>" : "") . "
    </a>
    <div class='dropdown-menu' aria-labelledby='lowStockDropdown'>
        <h6 class='dropdown-header'>Low on Stock</h6>";

// Display the products with low stock in the dropdown
$selectLowStock->execute();
while ($row = $selectLowStock->fetch(PDO::FETCH_OBJ)) {
    $quantity = $row->pstock;

    // Exclude products with overstock from low stock dropdown
    if ($quantity < 10 && $quantity > 0 && $quantity <= 200) {
        echo "<a class='dropdown-item' href='productlist.php'>$row->pname (Stock: $quantity)</a>";
    }
}

echo "
    </div>
</li>";

// Second bell icon for products with overstock
echo "
<li class='nav-item dropdown'>
    <a href='productlist.php' class='nav-link dropdown-toggle' id='overStockDropdown' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
        <i class='fas fa-bell'></i>
        " . ($overStockCount > 0 ? "<span class='badge badge-danger'>$overStockCount</span>" : "") . "
    </a>
    <div class='dropdown-menu' aria-labelledby='overStockDropdown'>
        <h6 class='dropdown-header'>Overstock</h6>";

// Display the products with overstock in the dropdown
$selectOverStock->execute();
while ($row = $selectOverStock->fetch(PDO::FETCH_OBJ)) {
    $quantity = $row->pstock;

    echo "<a class='dropdown-item' href='productlist.php'>$row->pname (Stock: $quantity)</a>";
}

echo "
    </div>
</li>";

// JavaScript script to initialize the Bootstrap dropdowns
echo "
<script>
    $(document).ready(function() {
        $('#lowStockDropdown').dropdown();
    });
</script>
<script>
    $(document).ready(function() {
        $('#overStockDropdown').dropdown();
    });
</script>";
?>



  <li class="nav-item">
    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
      <i class="fas fa-expand-arrows-alt"></i>
    </a>
  </li>
</ul>

  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary bg-navy elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
  <img src="./pos.jpg" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
  <span class="brand-text font-weight-light">JETAPS | POS</span> <br><br>
  <span id="currentDateTime" style="font-size: 20px; margin-left: 17px;"></span>
    </a>
    <script>
  // JavaScript to display the current date and time
  function updateDateTime() {
    const now = new Date();
    const dateElement = document.getElementById("currentDateTime");
    dateElement.innerHTML = now.toLocaleString(); // Adjust the date and time format as needed
  }

  // Call the function to update the date and time
  updateDateTime();

  // Update the date and time every second (1000 milliseconds)
  setInterval(updateDateTime, 1000);
</script>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 mr-3 text-center">
        <div class="info">
          <a href="#" class="d-block text-center">Welcome 
           <?php echo $_SESSION['username'];?> </a>
            
        </div>
      </div>

      <!-- SidebarSearch Form -->
     
      <!-- Sidebar Menu -->

    <nav class="mt-2">
   <ul class="nav nav-pills nav-sidebar flex-column" data-widget="tree" role="menu" data-accordion="false">
   <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == '') ? 'menu-open' : ''; ?>">
      <a href="dashboard.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
      </a>
    </li>


  
    <!-- <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'addproducts.php')? 'menu-open': ''; ?>">
      <a id="selling-form-link" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'addproducts.php') ? 'active' : ''; ?>" href="addproducts.php" role="button">
        <i class="fa fa-plus nav-icon"></i>
        <p>Add Items</p>
      </a>
    </li> -->
    <li class="nav-item has-treeview">
  <a href="javascript:void(0);" class="nav-link" id="inventoryToggle">
  <i class="fa fa-list-alt nav-icon"></i>
    <p>Inventory <i class="right fas fa-angle-right rotate-icon" style="transform: <?php echo (basename($_SERVER['PHP_SELF']) == 'productlist.php' || basename($_SERVER['PHP_SELF']) == 'category.php') ? 'rotate(90deg);' : ''; ?>"></i></p>
  </a>
  <ul class="nav nav-treeview" id="inventorySubmenu" style="<?php echo (basename($_SERVER['PHP_SELF']) == 'productlist.php' || basename($_SERVER['PHP_SELF']) == 'category.php') ? 'display: block;' : 'display: none;'; ?>">
    <li class="nav-item">
      <a href="addproducts.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'addproducts.php') ? 'active' : ''; ?>">
      <i class="fab fa-product-hunt nav-icon ml-3"></i>
        <p>Products</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="productlist.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'productlist.php') ? 'active' : ''; ?>">
        <i class="fas fa-box nav-icon ml-3"></i>
        <p> View Products</p>
      </a>
    </li>
    <!-- <li class="nav-item">
      <a href="category.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'category.php') ? 'active' : ''; ?>">
      <i class="fas fa-th-list nav-icon ml-3"></i>
        <p>Categories</p>
      </a>
    </li> -->
  </ul>
</li>

<li class="nav-item has-treeview <?php echo (basename($_SERVER['PHP_SELF']) == 'sales.php' || basename($_SERVER['PHP_SELF']) == 'orderlist.php') ? 'menu-open' : ''; ?>">
  <a href="javascript:void(0);" class="nav-link" id="generatedToggle">
    <i class="fa fa-folder nav-icon"></i>
    <p>Generated Report <i class="right fas fa-angle-right rotate-icon" style="<?php echo (basename($_SERVER['PHP_SELF']) == 'sales.php' || basename($_SERVER['PHP_SELF']) == 'orderlist.php') ? 'transform: rotate(90deg);' : ''; ?>"></i></p>
  </a>
  <ul class="nav nav-treeview" id="generatedSubmenu" style="<?php echo (basename($_SERVER['PHP_SELF']) == 'overall-sales.php' || basename($_SERVER['PHP_SELF']) == 'orderlist.php') ? 'display: block;' : 'display: none;'; ?>">
    <li class="nav-item">
      <a href="overall-sales.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'overall-sales.php') ? 'active' : ''; ?>">
        <i class="fas fa-money-bill nav-icon ml-3"></i>
        <p>Sales Report</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="orderlist.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'orderlist.php') ? 'active' : ''; ?>">
      <i class="fa fa-list-ol nav-icon ml-3"></i>
        <p>Transaction History</p>
      </a>
    </li>
  </ul>
</li>



<li class="nav-item has-treeview <?php echo (basename($_SERVER['PHP_SELF']) == 'user.php' || basename($_SERVER['PHP_SELF']) == 'user.php') ? 'menu-open' : ''; ?>">
  <a href="javascript:void(0);" class="nav-link" id="settingsToggle">
  <i class="fas fa-cog nav-icon"></i>

    <p>Settings <i class="right fas fa-angle-right rotate-icon" style="<?php echo (basename($_SERVER['PHP_SELF']) == 'user.php' || basename($_SERVER['PHP_SELF']) == 'user.php') ? 'transform: rotate(90deg);' : ''; ?>"></i></p>
  </a>
  <ul class="nav nav-treeview" id="settingsSubmenu" style="<?php echo (basename($_SERVER['PHP_SELF']) == 'changepassword.php' || basename($_SERVER['PHP_SELF']) == 'changepassword.php') ? 'display: block;' : 'display: none;'; ?>">
    <li class="nav-item">
      <a href="changepassword.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'changepassword.php') ? 'active' : ''; ?>">
        <i class="fas fa-key nav-icon ml-3"></i>
        <p>Change Password</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="registration.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'registration.php') ? 'active' : ''; ?>">
      <i class="fa fa-user-plus nav-icon ml-3"></i>
        <p>User</p>
      </a>
    </li>
    <li class="nav-item">
      <a href="loginhistory.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'loginhistory.php') ? 'active' : ''; ?>">
      <i class="fas fa-history nav-icon ml-3"></i>
      <p>Login History</p>

      </a>
    </li>
  </ul>
</li>



<li class="nav-item">
    <a href="logout.php" class="nav-link">
        <i class="fas fa-sign-out-alt nav-icon"></i>
        <p>Logout</p>
    </a>
</li>



</nav>




<script src = "js/logout.js"> </script>
<script>
  document.getElementById('inventoryToggle').addEventListener('click', function() {
    var submenu = document.getElementById('inventorySubmenu');
    var icon = document.querySelector('#inventoryToggle .rotate-icon');

    // Check if the submenu is currently visible
    var isSubMenuVisible = submenu.style.display === 'block';

    if (!isSubMenuVisible) {
      submenu.style.display = 'block';
      icon.style.transform = 'rotate(90deg)'; // Rotate the arrow icon
    } else {
      submenu.style.display = 'none';
      icon.style.transform = 'rotate(0deg)'; // Reset the rotation
    }
  });
</script>

<script>
  document.getElementById('settingsToggle').addEventListener('click', function() {
    var submenu = document.getElementById('settingsSubmenu');
    var icon = document.querySelector('#settingsToggle .rotate-icon');

    // Check if the submenu is currently visible
    var isSubMenuVisible = submenu.style.display === 'block';

    if (!isSubMenuVisible) {
      submenu.style.display = 'block';
      icon.style.transform = 'rotate(90deg)'; // Rotate the arrow icon
    } else {
      submenu.style.display = 'none';
      icon.style.transform = 'rotate(0deg)'; // Reset the rotation
    }
  });
</script>
<script>
  document.getElementById('generatedToggle').addEventListener('click', function() {
    var submenu = document.getElementById('generatedSubmenu');
    var icon = document.querySelector('#generatedToggle .rotate-icon');

    // Check if the submenu is currently visible
    var isSubMenuVisible = submenu.style.display === 'block';

    if (!isSubMenuVisible) {
      submenu.style.display = 'block';
      icon.style.transform = 'rotate(90deg)'; // Rotate the arrow icon
    } else {
      submenu.style.display = 'none';
      icon.style.transform = 'rotate(0deg)'; // Reset the rotation
    }
  });
</script>




 



      
    </div>
    <!-- /.sidebar -->
  </aside>