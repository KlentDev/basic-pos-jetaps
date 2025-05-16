<?php

try {
    $pdo = new PDO("mysql:host=localhost;dbname=pos_db", "root", "");
} catch (PDOException $f) {
    echo $f->getMessage();
}

// include_once "conectdb.php";
session_start();

if ($_SESSION["useremail"] == "" || $_SESSION["role"] == "user") {
    header("location:index.php");
}

include_once "header.php";

if (isset($_POST["btnupdate"])) {
    $oldpassword_txt = $_POST["txtoldpassword"];
    $newpassword_txt = $_POST["txtnewpassword"];
    $confirmpassword_txt = $_POST["txtconfirmnewpassword"];

    $email = $_SESSION["useremail"];

    $select = $pdo->prepare("select * from tbl_user where useremail='$email'");
    $select->execute();
    $row = $select->fetch(PDO::FETCH_ASSOC);
    $useremail_db = $row["useremail"];
    $password_db = $row["password"];

    if ($oldpassword_txt == $password_db) {
        if ($newpassword_txt == $confirmpassword_txt) {
            $update = $pdo->prepare("update tbl_user set password=:pass where useremail=:email");
            $update->bindParam(":pass", $confirmpassword_txt);
            $update->bindParam(":email", $email);

            if ($update->execute()) {
                echo "<script type='text/javascript'>
                jQuery(function validation(){
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: ' Your password is changed to your entered one',
                        showConfirmButton: false,
                        timer: 2000
                    })
                });
                </script>";
            } else {
                echo "<script type='text/javascript'>
                jQuery(function validation(){
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: ' Unable to update password',
                        showConfirmButton: false,
                        timer: 2000
                    })
                });
                </script>";
            }
        } else {
            echo "<script type='text/javascript'>
                jQuery(function validation(){
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: ' New password does not match the confirmed password',
                        showConfirmButton: false,
                        timer: 2000
                    })
                });
                </script>";
        }
    } else {
        echo "<script type='text/javascript'>
            jQuery(function validation(){
                Swal.fire({
                    position: 'center',
                    icon: 'warning',
                    title: ' Your entered old password is wrong',
                    showConfirmButton: false,
                    timer: 2000
                })
            });
            </script>";
    }
}
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                  
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard.php">Back</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
             
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Enter new Password</h3>
                            </div>
                            <form action="" method="post">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Old Password</label>
                                        <input type="text" class="form-control" id="exampleInputPassword1" placeholder="Password" name="txtoldpassword" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">New Password</label>
                                        <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" name="txtnewpassword" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Confirm New Password</label>
                                        <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" name="txtconfirmnewpassword" required>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-danger" name="btnupdate">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
include_once "footer.php";
?>
