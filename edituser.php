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

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $user = $pdo->prepare("SELECT * FROM tbl_user WHERE userid = :id");
    $user->bindParam(":id", $id);
    $user->execute();
    $userData = $user->fetch(PDO::FETCH_ASSOC);

    if ($userData) {
        if (isset($_POST["btnSave"])) {
            $username = $_POST["txtregname"];
            $useremail = $_POST["txtregemail"];
            $userrole = $_POST["txtselect_options"];
            $userpassword = $_POST["txtregpassword"];

            $update = $pdo->prepare("UPDATE tbl_user SET username = :name, useremail = :email, password = :pass, role = :role WHERE userid = :id");
            $update->bindParam(":name", $username);
            $update->bindParam(":email", $useremail);
            $update->bindParam(":pass", $userpassword);
            $update->bindParam(":role", $userrole);
            $update->bindParam(":id", $id);

            if ($update->execute()) {
                echo "
                    <script type='text/javascript'>
                        Swal.fire({
                          position: 'center',
                          icon: 'success',
                          title: 'Updated',
                          text: 'User information updated successfully',
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
                          text: 'Unable to update user information',
                          showConfirmButton: false,
                          timer: 3000
                        });
                    </script>";
            }
        }
    } else {
        echo "
            <script type='text/javascript'>
                Swal.fire({
                  position: 'center',
                  icon: 'error',
                  title: 'Error',
                  text: 'User not found',
                  showConfirmButton: false,
                  timer: 3000
                });
            </script>";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <!-- Include your CSS and other head content here -->
</head>
<body>
    <!-- The rest of your HTML code goes here -->

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="registration.php">Back</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content container-fluid">
            <div class="row justify-content-center">
                <!-- left column -->
                <div class="col-md-6">
                    <!-- general form elements -->
                    <div class="card card-info">
                        <div class="card-header">
        
                                Edit User
                            </a>
                        </div>

                        <!-- form start -->
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="card-body">
                                <div class="form-group">
                                <label for="exampleInputPassword1">Name</label>
                                    <input type="text" class="form-control" placeholder="Enter Username" name="txtregname" required value="<?= $userData['username'] ?>">
                                </div>
                                <div class="form-group">
                                <label for="exampleInputPassword1">Email</label>
                                    <input type="text" class="form-control" placeholder="Enter Email" name="txtregemail" required value="<?= $userData['useremail'] ?>">
                            </div>
                                <div class="form-group">
                                <label for="exampleInputPassword1">Password</label>
                                    <input type="password" class="form-control" placeholder="Enter Password" name="txtregpassword" required value="<?= $userData['password'] ?>">
                                </div>
                                <div class="form-group">
                                    <label>Role</label>
                                    <select class="form-control" name="txtselect_options" required>
                                        <option value="" disabled>Select Role</option>
                                        <option value="Cashier" <?= $userData['role'] == 'Cashier' ? 'selected' : '' ?>>Cashier</option>
                                        <option value="Admin" <?= $userData['role'] == 'Admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                </div>
                            </div>

                        
                            <div class="card-footer">
                            <button type="submit" class="btn btn-info" name="btnSave">Update</button>
                        
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Include your footer and any JavaScript libraries here -->
</body>
</html>


<?php 

include_once "footer.php";
?>