<?php

try {
    $pdo = new PDO("mysql:host=localhost;dbname=pos_db", "root", "");
    // echo "Connection Successful";
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

if(isset($_POST["btndelete"])){
    
    $dltid=$_POST["btndelete"];
    
   $delete=$pdo->prepare("delete from tbl_category where catid='$dltid'");
    
    if ($delete->execute()){
        
         echo "<script type='text/javascript'>
               jQuery(function validation(){
                Swal.fire({
            position: 'center',
            icon: 'success',
            title: 'Deleted',
            text: 'Category deleted successfully',
            showConfirmButton: false,
            timer: 3000
        })
        
        });
        
        </script>";
        
    }else{
        echo  "<script type='text/javascript'>
            jQuery(function validation(){
          Swal.fire({
          position: 'center',
          icon: 'error',
          title: 'Can not Delete',
          text: 'Category not deleted',
          showConfirmButton: false,
          timer: 3000
        })
        }); 
        </script>";
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
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                       <li class="breadcrumb-item active">Category</li>
                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                       
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
    <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Category</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST">
                            <table id="example1" class="table table-bordered table-hover">
                                <thead>
                                    <tr class="bg-lightblue">
                                        <td class="justify-center" size ="100px" style="text-align: center; width: 100px; ">No.</td>
                                        <td class="justify-center" style="text-align: center;">Category name</td>
                                        <td  class="justify-center" style="text-align: center; width: 100px; ">Action</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $select = $pdo->prepare("select * from tbl_category order by catid desc");
                                    $select->execute();

                                    while ($row = $select->fetch(PDO::FETCH_OBJ)) {
                                        echo "
                                        <tr>
                                            <td class='justify-center' style='text-align: center;'>$row->catid</td>
                                            <td style='text-align: center;'>$row->category</td>
                                            <td style='text-align: center;'>
                                                <button id='$row->catid' class='btn btn-danger dltBttn' type='button'>
                                                    <span class='fas fa-trash' style='color: #ffffff' data-toggle='tooltip' title='DELETE CAT'></span>
                                                </button>
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
        </div>
    </section>
</div>

<!-- JavaScript to initialize the DataTables plugin -->
<script>
    $(function () {
        $("#example1").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
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

    $(".dltBttn").click(function() {
        var tdh = $(this);
        var id = $(this).attr("id");

        Swal.fire({
            title: 'Are you sure?',
            text: "Once Deleted You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "categorydelete.php",
                    type: "post",
                    data: {
                        catid: id
                    },
                    success: function(data) {
                        tdh.parents("tr").hide();
                    }
                });

                Swal.fire(
                    'Deleted!',
                    'Product has been deleted.',
                    'success'
                );
            }
        });
    });
</script>

<?php
include_once "footer.php";
?>
