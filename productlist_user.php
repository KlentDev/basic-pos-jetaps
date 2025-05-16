<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pos_db", "root", "");
} catch (PDOException $f) {
    echo $f->getMessage();
}

session_start();
if (empty($_SESSION["useremail"])) {
    header("location:index.php");
}elseif ( $_SESSION["role"] !== "Cashier") {
    header("location:dashboard.php");
}

include_once "headeruser.php";

if (isset($_POST["btndelete"])) {
    $dltid = $_POST["btndelete"];

    $delete = $pdo->prepare("DELETE FROM tbl_category WHERE catid = :dltid");
    $delete->bindParam(':dltid', $dltid);

    if ($delete->execute()) {
        echo "<script type='text/javascript'>
               $(document).ready(function() {
                   Swal.fire({
                       position: 'center',
                       icon: 'success',
                       title: 'Deleted',
                       text: 'Category deleted successfully',
                       showConfirmButton: false,
                       timer: 3000
                   });
               });
        </script>";
    } else {
        echo "<script type='text/javascript'>
               $(document).ready(function() {
                   Swal.fire({
                       position: 'center',
                       icon: 'error',
                       title: 'Cannot Delete',
                       text: 'Category not deleted',
                       showConfirmButton: false,
                       timer: 3000
                   });
               });
        </script>";
    }
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
                        <li class="breadcrumb-item active">View Product</li>
                        <li class="breadcrumb-item"><a href="order_user.php">Point of Sales</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content container-fluid">
        <div class="col-md-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Product List</h3>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
        
                        <table id="example1" class="table table-bordered table-hover">
                            <thead class="bg-lightblue">
                                <td class="justify-center" style="text-align: center;">Product name</td>
                                <td class="justify-center" style="text-align: center;">Category</td>
                                <td class="justify-center" style="text-align: center;">Sale Price</td>
                                <td class="justify-center" style="text-align: center;">Image </td>
                                <td class="justify-center" style="text-align: center;"> Available Stock</td>
                                <td class="justify-center" style="text-align: center;"> Status</td>
                            
                            </thead>
                            <tbody>
                                     <?php
                        $select = $pdo->prepare("select * from tbl_product order by pid desc");
                        $select->execute();
                        while ($row = $select->fetch(PDO::FETCH_OBJ)) {
                            $formattedTotalEarnings = number_format($row->saleprice, 2);
                            $quantity = $row->pstock;
                            
                            if ($quantity < 10 && $quantity > 0) {
                                $colorNote = 'Low on stock';
                                $badgeColor = 'badge-warning';
                            } elseif ($quantity >= 11 && $quantity <= 499) {
                                $colorNote = 'Normal';
                                $badgeColor = 'badge-success';
                            } elseif ($quantity === 0) {
                                $colorNote = 'No stocks';
                                $badgeColor = 'badge-danger';
                            } elseif ($quantity > 500) {
                                $colorNote = 'Overstock';
                                $badgeColor = 'badge-danger';
                            } 
                            echo "
                            <tr>
                                <td class='justify-center' style='text-align: center;'>$row->pname</td>
                                <td class='justify-center' style='text-align: center;'>$row->pcategory</td>
                                <td class='justify-center' style='text-align: center'>₱$formattedTotalEarnings</td>
                                <td class='justify-center' style='text-align: center;'><img src='$row->image' width='100' height='60'></td>
                                <td class='justify-center' style='text-align: center;'>$row->pstock</td>
                                <td class='justify-center' style='text-align: center;'>
                                    <span class='badge $badgeColor'>$colorNote</span>
                                </td>
                            </tr>";
                        }
                        ?>
                            
                            
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
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
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "order": [[0, "asc"]],
        "buttons": [
            {
                extend: "excel",
                text: '<i class="fas fa-file-excel" style="color: orange;"></i> Excel',
                title: 'Product List ',
                exportOptions: {
                    columns: ':visible',
                    modifier: {
                        footer: 'true'
                    }
                }
            },
            {
                extend: "pdf",
                text: '<i class="fas fa-file-pdf" style="color: red;"></i> PDF',
                title: 'Product List',
                exportOptions: {
                    columns: ':visible',
                    modifier: {
                        footer: 'true'
                    }
                }
            },
            "colvis",
        ],
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $("[data-toggle='tooltip']").tooltip();
});

</script>

<script>
    $(document).ready(function () {
        $(".dltBttn").click(function () {
            var id = $(this).attr("id");
            var buttonElement = $(this);

            Swal.fire({
                title: 'Are you sure?',
                text: "Once Deleted You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "productdelete.php",
                        type: "post",
                        data: {
                            pidd: id
                        },
                        success: function (data) {
                            buttonElement.closest("tr").remove();
                        }
                    });
                    Swal.fire('Deleted!', 'Product has been deleted.', 'success');
                }
            });
        });
    });
</script>

<?php
include_once "footer.php";
?>
