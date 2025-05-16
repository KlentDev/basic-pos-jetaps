
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

$id = '';
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $delete = $pdo->prepare("DELETE FROM tbl_user WHERE userid = :id");
    $delete->bindParam(":id", $id);
    
    if ($delete->execute()) {
        echo "
            <script type='text/javascript'>
                Swal.fire({
                  position: 'center',
                  icon: 'success',
                  title: 'Deleted',
                  text: 'User deleted successfully',
                  showConfirmButton: false,
                  timer: 3000
                });
            </script>";
    } else {
        echo "
            <script type='text/javascript'>
                Swal.fire({
                  position: 'center',
                  icon: 'error',
                  title: 'Error',
                  text: 'Failed to delete user',
                  showConfirmButton: false,
                  timer: 3000
                });
            </script>";
    }
}

if (isset($_POST["btnSave"])) {
    $username = $_POST["txtregname"];
    $useremail = $_POST["txtregemail"];
    $userrole = $_POST["txtselect_options"];
    $userpassword = $_POST["txtregpassword"];

    $selectName = $pdo->prepare("select username from tbl_user where username=:name");
    $selectName->bindParam(":name", $username);
    $selectName->execute();

    if ($selectName->rowCount() > 0) {
        echo "
            <script type='text/javascript'>
                Swal.fire({
                  position: 'center',
                  icon: 'error',
                  title: 'Warning',
                  text: 'Someone is already registered with this Username',
                  showConfirmButton: false,
                  timer: 3000
                });
            </script>";
    } else {
        $insert = $pdo->prepare("insert into tbl_user(username, useremail, password, role) values(:name, :email, :pass, :role)");
        $insert->bindParam(":name", $username);
        $insert->bindParam(":email", $useremail);
        $insert->bindParam(":pass", $userpassword);
        $insert->bindParam(":role", $userrole);

        if ($insert->execute()) {
            echo "
                <script type='text/javascript'>
                    Swal.fire({
                      position: 'center',
                      icon: 'success',
                      title: 'Saved',
                      text: 'Credentials saved successfully',
                      showConfirmButton: false,
                      timer: 3000
                    });
                </script>";
        } else {
            echo "
                <script type='text/javascript'>
                    Swal.fire({
                      position: 'center',
                      icon: 'error',
                      title: 'Failed',
                      text: 'Unable to save credentials',
                      showConfirmButton: false,
                      timer: 3000
                    });
                </script>";
        }
    }
}
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
            <li class="breadcrumb-item active">User</li>
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
            
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

     <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
             
              
              
              <div class="card card-info">
              <div class="card-header">
                <h3 class="card-title">Enter new User Info</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" action="" method="post">

                <div class="card-body">
                  

                   <div class="form-group">
                    <label for="exampleInputPassword1">Name</label>
                    <input type="text" class="form-control"  placeholder="Enter Username" name="txtregname" required>
                  </div>
                   
                   
                   
                  <div class="form-group">
                    <label for="exampleInputPassword1">Email</label>
                    <input type="text" class="form-control"  placeholder="Enter Email" name="txtregemail" required>
                  </div>
                 
                 <div class="form-group">
                    <label for="exampleInputPassword1">Password</label>
                    <input type="password" class="form-control"  placeholder="Enter Password" name="txtregpassword" required>
                  </div>
    
                  <div class="form-group">
                        <label> Role</label>
                        <select class="form-control" name="txtselect_options" required>
                         <option value="" disabled selected>Select Role</option>
                          <option>Cashier</option>
                           <option>Admin</option>
                          
                          
                        </select>
                      </div>
        
                </div>
                
    
               
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-success" name="btnSave">Save</button>
                </div>
              </form>
              
             
              
              
              
              
              
            </div>
              
              
              
              
              
              
              
              
              
            </div>

           
            </div><!-- /.card -->
          </div>
          <!-- /.col-md-6 -->
                  

          <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
        
        <table id="tablereg" class="table table-striped">
            
           <thead>
             <tr class="bg-lightblue">
                 
                 <td class='justify-center' style='text-align: center;'>#</td>
                 <td class='justify-center' style='text-align: center;'>Name</td>
                 <td class='justify-center' style='text-align: center;'>Email</td>
                 <td  class='justify-center' style='text-align: center;'>Password</td>
                 <td  class='justify-center' style='text-align: center;'>Role</td>
                <td  class='justify-center' style='text-align: center;'>Action</td>
             </tr>  
               
               
           </thead>
            
            
            
           <tbody>
    <?php
    $select = $pdo->prepare("select * from tbl_user order by userid desc");
    $select->execute();
    
    while ($row = $select->fetch(PDO::FETCH_OBJ)) {
        echo "
            <tr id='user-$row->userid'> <!-- Add an ID to the row for easier identification -->
                <td  class='justify-center' style='text-align: center;'>$row->userid</td>
                <td  class='justify-center' style='text-align: center;'>$row->username</td>
                <td  class='justify-center' style='text-align: center;'>$row->useremail</td>
                <td  class='justify-center' style='text-align: center;'>$row->password</td>
                <td class='justify-center' style='text-align: center;'>$row->role</td>
                <td  class='justify-center' style='text-align: center;'>
                    <button id='$row->userid' class='btn btn-danger dltBttn' type='button'>
                        <span class='fas fa-trash' style='color:#ffffff' data-toggle='tooltip' title='Delete User'></span>
                    </button>

                      
                    <a href=\"edituser.php?id=".$row->userid."\" 
                    class= \"btn btn-primary\" role=\"button\" ><span class=\"fas fa-edit\" name=\"editBtn\"    style=\"color:#ffffff\" data-toggle=\"tooltip\" title=\"Edit User\"></span>
                    </
                </td>
            </tr>
        ";
    }
    ?>
</tbody>
            
            
        </table>
        
        
        
        
      </div><!-- /.container-fluid -->
      
      
      
      
      
      
      
      
      
      
    </div>
    


<script>

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
                    url: "userdelete.php",
                    type: "post",
                    data: {
                        userid: id
                    },
                    success: function(data) {
                        tdh.parents("tr").hide();
                    }
                });

                Swal.fire(
                    'Deleted!',
                    'User has been deleted.',
                    'success'
                );
            }
        });
    });
</script>



    
    <!-- /.content -->
  
  
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
 
  <!-- /.control-sidebar -->

 <?php
include_once "footer.php";


?>