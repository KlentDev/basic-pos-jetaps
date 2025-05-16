<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pos_db", "root", "");
    // echo "Connection Successful";
} catch (PDOException $f) {
    echo $f->getMessage();
}

session_start();
if ($_SESSION["useremail"] == "") {
    header("location:index.php");
}
include_once "headeruser.php";

if (isset($_POST["btnaddsales"])) {
    // Insert the sales data into the database
    $dailysales = $_POST["order_date"];
    $totalsales = $_POST["paid"];

    $insert = $pdo->prepare("INSERT INTO tbl_invoice (order_date, paid) VALUES (:dailysales, :totalsales)");
    $insert->bindParam(":dailysales", $dailysales);
    $insert->bindParam(":totalsales", $totalsales);

    if ($insert->execute()) {
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

// You can add the code to retrieve and display the sales report here
?>


<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Generate Sales</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="logout.php">Logout</a></li>
                        <li class="breadcrumb-item active">Admin Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

<!-- Content Wrapper. Contains page content -->

    <!-- Main content -->
    <section class="content container-fluid">
        <!-- Right column -->
        <div class="col-md-12"> <!-- Adjust the column width as needed -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">DATA</h3>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Total Sales</th>
                                       
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $select = $pdo->prepare("select * from tbl_invoice order by order_date desc");
                                    $select->execute();

                                    while ($row = $select->fetch(PDO::FETCH_OBJ)) {
                                        echo "
                                            <tr>
                                                <td>$row->order_date</td>
                                                <td>$row->paid</td>
                                                
                                            </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(function () {
        $("#example1").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        $('#example2').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
    });

document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("datepicker").addEventListener("change", function() {
        var selectedDate = this.value;

        // Use AJAX to fetch total sales for the selected date
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "fetch_daily.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = xhr.responseText;
                document.getElementById("totalsales").value = response;
            }
        };
        xhr.send("order_date=" + selectedDate);
    });
});
</script>




<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <div class="p-3">
        <h5>Title</h5>
        <p>Sidebar content</p>
    </div>
</aside>
<?php
include_once "footer.php";
?>
