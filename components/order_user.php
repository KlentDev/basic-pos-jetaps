<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pos_db", "root", "");
} catch (PDOException $ex) {
    echo $ex->getMessage();
}

session_start();
if (empty($_SESSION["useremail"])) {
    header("location:index.php");
} elseif ($_SESSION["role"] !== "Cashier") {
    header("location:dashboard.php");
}

include_once "headeruser.php";

function fill_product($pdo)
{
    $output = "";
    $select = $pdo->prepare("SELECT * FROM tbl_product WHERE pstock > 0 ORDER BY pname ASC");
    $select->execute();
    $result = $select->fetchAll();

    foreach ($result as $row) {
        $output .= '<option value="' . $row["pid"] . '">' . $row["pname"] . '</option>';
    }

    return $output;
}

if (isset($_POST["btnsaveorder"])) {
    $customer_name = $_POST["txtcustomer"];
    $order_date = date("Y-m-d H:i:s", strtotime($_POST['orderdate']));
    $order_time = $order_date;
    $subtotal = $_POST["txtsubtotal"];
    $warranty = $_POST["txtwarranty"];
    $discount = $_POST["txtdiscount"];
    $total = $_POST["txttotal"];
    $paid = $_POST["txtpaid"];
    $due = $_POST["txtdue"];
    $payment_type = $_POST["rb"];
    $arr_productid = $_POST['productid'];
    $arr_productname = $_POST['productname'];
    $arr_stock = $_POST['stock'];
    $arr_qty = $_POST['qty'];
    $arr_price = $_POST['price'];
    $arr_total = $_POST['total'];

    $insert = $pdo->prepare("INSERT INTO tbl_invoice(customer_name, order_date, order_time, subtotal,  discount, total, warranty, paid, due, payment_type) VALUES(:cust, :orderdate, :ordertime, :stotal,  :disc, :total, :warranty, :paid, :due, :ptype)");

    $insert->bindParam(':cust', $customer_name);
    $insert->bindParam(':orderdate', $order_date);
    $insert->bindParam(':ordertime', $order_time);
    $insert->bindParam(':stotal', $subtotal);
    $insert->bindParam(':warranty', $warranty);
    $insert->bindParam(':disc', $discount);
    $insert->bindParam(':total', $total);
    $insert->bindParam(':paid', $paid);
    $insert->bindParam(':due', $due);
    $insert->bindParam(':ptype', $payment_type);

    if ($insert->execute()) {
        $invoice_id = $pdo->lastInsertId();

        if ($invoice_id) {
            for ($i = 0; $i < count($arr_productid); $i++) {
                // Check if stock is zero
                if ($arr_stock[$i] == 0) {
                    echo "
                        <script type='text/javascript'>
                            Swal.fire({
                              position: 'center',
                              icon: 'error',
                              title: 'Warning',
                              text: 'Product {$arr_productname[$i]} is out of stock',
                              showConfirmButton: false,
                              timer: 3000
                            });
                        </script>";
                } else {
                    $rem_qty = $arr_stock[$i] - $arr_qty[$i];
                    if ($rem_qty < 0) {
                        echo "
                            <script type='text/javascript'>
                                Swal.fire({
                                  position: 'center',
                                  icon: 'error',
                                  title: 'Warning',
                                  text: 'Order is Not Complete',
                                  showConfirmButton: false,
                                  timer: 3000
                                });
                            </script>";
                    } else {
                        $update = $pdo->prepare("UPDATE tbl_product SET pstock = :rem_qty WHERE pid = :product_id");
                        $update->bindParam(':rem_qty', $rem_qty);
                        $update->bindParam(':product_id', $arr_productid[$i]);
                        $update->execute();
                    }

                    $insert = $pdo->prepare("INSERT INTO tbl_invoice_details(invoice_id, product_id, product_name, qty, price, order_date, order_time) VALUES(:invid, :pid, :pname, :qty, :price, :orderdate, :ordertime)");
                    $insert->bindParam(":invid", $invoice_id);
                    $insert->bindParam(":pid", $arr_productid[$i]);
                    $insert->bindParam(":pname", $arr_productname[$i]);
                    $insert->bindParam(":qty", $arr_qty[$i]);
                    $insert->bindParam(":price", $arr_price[$i]);
                    $insert->bindParam(":orderdate", $order_date);
                    $insert->bindParam(":ordertime", $order_time);
                    $insert->execute();
                }
            }
            echo '
                <script type="text/javascript">
                    Swal.fire({
                      position: "center",
                      icon: "success",
                      title: "Order Saved",
                      text: "Order details saved successfully",
                      showConfirmButton: false,
                      timer: 3000
                    });
                </script>';
        } else {
            echo '
                <script type="text/javascript">
                    Swal.fire({
                      position: "center",
                      icon: "error",
                      title: "Failed",
                      text: "Unable to save order details",
                      showConfirmButton: false,
                      timer: 3000
                    });
                </script>';
        }
    } else {
        echo '
            <script type="text/javascript">
                Swal.fire({
                  position: "center",
                  icon: "error",
                  title: "Failed",
                  text: "Unable to save order",
                  showConfirmButton: false,
                  timer: 3000
                });
            </script>';
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
                        <li class="breadcrumb-item active">Cashier POS</li>
                        <li class="breadcrumb-item"><a href="logout.php">Logout</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content container-fluid">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">POINT OF SALES FORM DATA</h3>
            </div>
            <div class="card-body">
                <form role="form" action="" method="post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Customer Name</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Enter Name" name="txtcustomer" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date:</label>
                                <div class="input-group date" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input" data-target="#reservationdate" id="reservationdate" name="orderdate" required>
                                    <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <table id="producttable" class="table table-bordered table-hover">
                                <thead>
                                    <tr class="bg-lightblue">
                                        <th class='justify-center' style='text-align: center;'>No.</th>
                                        <th class='justify-center' style='text-align: center;'>Search Product</th>
                                        <th class='justify-center' style='text-align: center;'>Stock</th>
                                        <th class='justify-center' style='text-align: center;'>Price</th>
                                        <th class='justify-center' style='text-align: center;'>Enter Quantity</th>
                                        <th class='justify-center' style='text-align: center;'>Total</th>
                                        <th class='justify-center' style='text-align: center;'>
                                            <button type="button" name="add" class="btn btn-success btn-sm btnadd">
                                                <span class="fas fa-plus"></span>
                                            </button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Subtotal</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-money-bill-alt"></i></span>
                                    </div>
                                    <input  type="text" class="form-control" name="txtsubtotal" id="txtsubtotal" required readonly>
                                </div>
                            </div>
                         <div class="form-group">
                            <label>Warranty</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-money-bill-alt"></i></span>
                                </div>
                                <input type="text" class="form-control" name="txtwarranty" id="txtwarranty" required>
                            </div>
                        </div>

                           
                            <div class="form-group">
                                <label>Discount</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-money-bill-alt"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="txtdiscount" id="txtdiscount" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Total</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-wallet"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="txttotal" id="txttotal" required readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Paid</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-wallet"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="txtpaid" id="txtpaid" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Change</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-wallet"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="txtdue" id="txtdue" required readonly>

                                </div>
                            </div>
                            <label>Payment Method:</label>
                            <div class="form-group clearfix">
                                <div class="icheck-primary d-inline">
                                    <input type="radio" id="radioPrimary1" name="rb" value="cash" checked>
                                    <label for="radioPrimary1">Cash</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div align="center">
                        <input type="submit" name="btnsaveorder" value="Save Order" class="btn btn-success">
                    </div>
                
                </form>
            </div>
        </div>
    </section>
</div>



<script>
    
    $(document).ready(function(){
                      
                      $('.btnadd').click(function(){
        
                     //     alert("You clicked the element with and ID of 'test-element'");
        var html='';
        html+='<tr>';
        html+='<td><input type="hidden" class="form-control pname" name="productname[]" ></td>';
                          html+='<td><select class="form-control productid" name="productid[]" style="width: 250px"; ><option value="">Select Option</option> <?php echo fill_product($pdo);  ?> </select></td>';
                          html+='<td><input type="text" class="form-control stock" name="stock[]" readonly></td>';
                          html+='<td><input type="text" class="form-control price" name="price[]" readonly></td>';
                          html+='<td><input type="number" min="1" class="form-control qty" name="qty[]" required></td>';
                          html+='<td><input type="text" class="form-control total" name="total[]" readonly></td>';
                          
                          html+='<td><button type="button" name="add" class= "btn btn-danger btn-sm btntbldlt" ><span class="fas fa-minus"></span></button></td>';
                          
            $('#producttable').append(html);
                          
                          
                          
                          
                          
                          
            $('.productid').select2()
                          
                $('.productid').on('change' , function(e){
                    
                    
                    var productid = this.value;
                     var tr=$(this).parent().parent();
              //var id = productid;
                    $.ajax({
                        
                        url:'getproduct.php',
                        method:'get',
                        data:{myyid: productid},
                     
       success:function(data){
                          
    //   alert(id);
                        //  console.log(data);
           tr.find(".pname").val(data["pname"]);
                         tr.find(".stock").val(data["pstock"]);
           tr.find(".price").val(data["saleprice"]);
           tr.find(".qty").val(1);
           tr.find(".total").val(tr.find(".qty").val() * tr.find(".price").val());
        
           calculate(0,0);
                        }
                
                    });
                    
                    
                });
                          
                         
        
        
    });
                      
        
        
     

        $("#producttable").delegate(".qty","keyup change" , function(){
            
            var quantity = $(this);
            var tr=$(this).parent().parent();
            if( (quantity.val()-0)> (tr.find(".stock").val()-0)){
               
               swal.fire("warning!", "Sorry Quantity not available");
                
                quantity.val(1);
                tr.find(".total").val(quantity.val() * tr.find(".price").val());
                                
                calculate(0,0);
               
               }else{
                   
                   tr.find(".total").val(quantity.val() * tr.find(".price").val());
                   calculate(0,0);
                   
               }
            
            
            
            
            
        });
        
        
        
                      function calculate(dis,paid){
                          
            var subtotal=0;
                          var tax=0;
                          var discount= dis;
                          var nrt_total=0;
                          var paid_amt= paid;
                          var due=0;
                          $(".total").each(function(){
                              
                              subtotal = subtotal+($(this).val()*1);
                              
                              
                              
                              
                          })
                          tax=0.00*subtotal;
                          net_total=tax+subtotal;
                          net_total=net_total-discount;
                          due=paid_amt-net_total;
                          $("#txtsubtotal").val(subtotal.toFixed(2));
                          $("#txttax").val(tax.toFixed(2));
                         $("#txttotal").val(net_total.toFixed(2));
                          $("#txtdiscount").val(discount);
                          $("#txtdue").val(due.toFixed(2));
                          
                          
                          
                          
                      } //function calculate end here
        
        $("#txtdiscount").keyup(function(){
            
            var discount = $(this).val();
            calculate(discount,0);
            
            
        });
        $("#txtpaid").keyup(function(){
            
            var paid =$(this).val();
            var discount =$("#txtdiscount").val();
            calculate(discount,paid);
            
                 
            
        });
        
        
            
                      
                      });
    
    

                
    
    
</script>
  
  
  <script>

$(document).on("click", ".btntbldlt", function(){ 
    $(this).closest('tr').remove();
    calculate(0,0);
    $("#txtpaid").val(0);
});

</script>
  
   
   <script>
    
    
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2()

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })

  
});
    
    
</script>


<script>

$(document).ready(function(){
    
$("#reservationdate").datetimepicker({pickTime: true });

});
</script>


<script>
    $(document).ready(function () {

        $('.btnadd').prop('disabled', true); // Initially disable the "Add" button

        // Function to check if the required fields are filled before enabling the "Add" button
        function checkFields() {
            var date = $('#reservationdate').val();
            var customerName = $('input[name="txtcustomer"]').val();

            if (date !== '' && customerName !== '') {
                $('.btnadd').prop('disabled', false);
            } else {
                $('.btnadd').prop('disabled', true);
            }
        }

        // Datepicker initialization
        $("#reservationdate").datetimepicker({
            pickTime: true,
            format: 'YYYY-MM-DD HH:mm:ss'
        });

        // Event listener for date change
        $('#reservationdate').on('change.datetimepicker', function (e) {
            checkFields();
        });

        // Event listener for customer name input
        $('input[name="txtcustomer"]').on('input', function () {
            checkFields();
        });

        $('.btnadd').click(function () {
            // ... (Your existing JavaScript code for adding a new row remains unchanged)
        });

        // ... (Your existing JavaScript code remains unchanged)
    });
</script>

 <?php
include_once "footer.php";


?>