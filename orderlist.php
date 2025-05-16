<?php

try{
$pdo = new PDO("mysql:host=localhost;dbname=pos_db","root","");
//echo "connection Sucessfull";

}catch(PDOException $f){
    
    echo $f->getmessage();
    
}






session_start();
if (empty($_SESSION["useremail"])) {
    header("location:index.php");
} elseif ($_SESSION["role"] !== "Admin") {
    header("location:order_user.php");
}

include_once "header.php";



if (isset($_POST["btndelete"])) {
  $invoice_id = $_POST["btndelete"];
  $delete = $pdo->prepare("DELETE FROM tbl_product WHERE invoice_id = :invoice_id");
  $delete->bindParam(":invoice_id", $invoice_id);

  if ($delete->execute()) {
      displaySuccessMessage("Order  deleted successfully.");
  } else {
      displayErrorMessage("Order not deleted.");
  }
}



?>

 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">

          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">Invoice</a></li>
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>

   
   
   
   
   
   
    <!-- Main content -->
    <section class="content container-fluid">
              <div class="row">
              
               <!-- left column -->
         
            

          <!--/.col (left) -->
          <!-- right column -->


       <div class="col-md-12">
            
            <div class="card card-info">
              <div class="card-header">
                <h3 class="card-title">Transaction History</h3>
              </div>
              <div class="card-body">
        
      <!--  <form  action="" method="POST"> -->
        <table id="example2" class="table table-bordered table-hover">
            
          <thead>
             <tr class="bg-lightblue"> 
                 
                 <td class='justify-center' style='text-align: center;'>Invoice ID</td>
                 <td class='justify-center' style='text-align: center;'>Customer name</td>
                 <td class='justify-center' style='text-align: center;'>Order Date</td>
                 <td class='justify-center' style='text-align: center;'>Total</td>
                 <td class='justify-center' style='text-align: center;'>Paid</td>
                 
                 
                <!-- <td>EDIT</td> 
                
                 also add this in td below
                 
                 <td><button input type=\"submit\" value=\".$row->catid.\" class=\"btn btn-success\" name=\"btnedit\">EDIT</button></td>
                  
                   
                     -->
                 <td class='justify-center' style='text-align: center;'>Change</td>
                 <td class='justify-center' style='text-align: center;'>Payment Type</td>
                 <td class='justify-center' style='text-align: center;'>Warranty</td>
                 <td class='justify-center' style='text-align: center;'>Action</td>

                 
             </tr>  
      
           </thead>
       
            <tbody>
     
           <?php
                
                $select=$pdo->prepare("select * from tbl_invoice order by invoice_id desc");
                $select->execute();
                
                while($row=$select->fetch(PDO::FETCH_OBJ)){
                    $total_sales = number_format($row->total, 2);
                    $total_paid = number_format($row->paid, 2);
                    $total_due = number_format($row->due, 2);
                    $formattedDate = date('F j, Y', strtotime($row->order_date));
                  echo "
                    
                    <tr>
                    <td class='justify-center' style='text-align: center;'>$row->invoice_id</td>
                    <td class='justify-center' style='text-align: center;'>$row->customer_name</td>
                    <td class='justify-center' style='text-align: center;'>$formattedDate</td>
                    <td class='justify-center' style='text-align: center;'>₱$total_sales</td>
                    <td class='justify-center' style='text-align: center;'>₱$total_paid</td>
                    <td class='justify-center' style='text-align: center;'>₱$total_due</td>
                    <td class='justify-center' style='text-align: center;'>$row->payment_type</td>
                    <td class='justify-center' style='text-align: center;'>$row->warranty</td>
                    
                   <td class='justify-center' style='text-align: center;'>
            
                   <button data-invoice-id='$row->invoice_id' class='btn btn-danger dltBttn' type='button'>
                   <span class='fas fa-trash' style='color: #ffffff' data-toggle='tooltip' title='DELETE Order'></span>
                   </button>
                    <a href=\"invoice_80mm.php?id=".$row->invoice_id."\" 
                    class= \"btn btn-success\" role=\"button\" target=\"blank\" ><span class=\"fas fa-print\" name=\"PrintBtn\"    style=\"color:#ffffff\" data-toggle=\"tooltip\" title=\"Print Invoice\"></span>
                    </a>
                    
                    </td>
                    
                    
                    
                    </tr>";
                    
                }
                
                
                
                
                ?>
                    
            
                
            </tbody>
            
            
        </table>
                  <!-- </form> -->
         
             </div>
               </div>
           
                
              
            </div>
        
        
        <!-- /.right column -->
        
          </div> 

    </section>
    <!-- /.content -->
              </div>
    <script>

    $(document).ready(function () {
    var table = $("#example2").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "order": [[0, "desc"]],
        "buttons": [
            {
                extend: "excel",
                text: '<i class="fas fa-file-excel" style="color: orange;"></i> Excel',
                title: 'Transaction History',
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
                title: 'Transaction History',
                exportOptions: {
                    columns: ':visible',
                    modifier: {
                        footer: 'true'
                    }
                }
            },
            "colvis",
        ],
    });
    table.buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
    $("[data-toggle='tooltip']").tooltip();
});


   
</script>



<script>
  $(document).ready(function () {
        $(".dltBttn").click(function () {
            var tdh = $(this);
            var id = $(this).data("invoice-id");

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
                        url: "orderdelete.php",
                        type: "post",
                        data: {
                            invoice_id: id
                        },
                        success: function (data) {
                            tdh.closest("tr").remove();
                            Swal.fire('Deleted!', 'Order has been deleted.', 'success');
                        }
                    });
                }
            });
        });
    });
    </script>
  

 <?php
include_once "footer.php";


?>