<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pos_db", "root", "");
} catch (PDOException $f) {
    echo $f->getMessage();
}

session_start();
if (empty($_SESSION["useremail"])) {
    header("location:index.php");
}elseif ( $_SESSION["role"] !== "Admin") {
    header("location:order_user.php");
}


include_once "header.php";

if (isset($_POST["btnadd"])) {
    $pname = $_POST["pname"];
    $pcategory = $_POST["pcategory"];
    $saleprice = $_POST["saleprice"];
    $image = $_POST["image"];
    $pstock = $_POST["pstock"];
 

    // Check if the product already exists
    $checkProduct = $pdo->prepare("SELECT * FROM tbl_product WHERE pname = :pname");
    $checkProduct->bindParam(':pname', $pname);
    $checkProduct->execute();

    if ($checkProduct->rowCount() > 0) {
        echo "<script type='text/javascript'>
            // Show an error message or redirect as needed
            alert('Product already exists');
        </script>";
    } else {
        // Insert the new product if it doesn't exist
        $insert = $pdo->prepare("INSERT INTO tbl_product (pname, pcategory, saleprice, image, pstock) VALUES (:pname, :pcategory, :saleprice, :image, :pstock)");
        $insert->bindParam(':pname', $pname);
        $insert->bindParam(':pcategory', $pcategory);
        $insert->bindParam(':saleprice', $saleprice);
        $insert->bindParam(':image', $image);
        $insert->bindParam(':pstock', $pstock);
       

        if ($insert->execute()) {
            echo "<script type='text/javascript'>
                // Show a success message or redirect as needed
                alert('Product added successfully');
            </script>";
        } else {
            echo "<script type='text/javascript'>
                // Show an error message or redirect as needed
                alert('Failed to add the product');
            </script>";
        }
    }
}
?>

<div class="content-wrapper">
    <section class="content-header">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">View Product</li>
                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content container-fluid">
        <div class="col-md-12">
            <div class="card card-primary">
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
                            <tbody><?php
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
                title: 'Product List',
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
                title: 'Product ',
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
