<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/sweetalert2/sweetalert2.js"></script>
<script src="plugins/sweetalert2/sweetalert2.all.js"></script>
<script src="plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<?php
include_once("connectdb.php");

// Start the session
session_start();

if (isset($_POST["btn_login"])) {
  $password = $_POST["txt_password"];
  $username = $_POST["txt_username"];

  // Convert the input username to lowercase (you can use strtoupper for uppercase)
  $username = strtolower($username);

  $select = $pdo->prepare("SELECT * FROM tbl_user WHERE LOWER(username) = :username AND password = :password");
  $select->execute(array(':username' => $username, ':password' => $password));
  $row = $select->fetch(PDO::FETCH_ASSOC);

  if ($row) {
      // Update the session with the new role
      $_SESSION["userid"] = $row["userid"];
      $_SESSION["username"] = $row["username"];
      $_SESSION["useremail"] = $row["useremail"];
      $_SESSION["role"] = $row["role"];

      // Insert a new login history record for the user
      $user_id = $_SESSION["userid"];
      $login_date = date("Y-m-d");
      $login_time = date("H:i:s");

      $insert_login_history = $pdo->prepare("INSERT INTO tbl_login_history (user_id, login_date, login_time) VALUES (:user_id, :login_date, :login_time)");
      $insert_login_history->bindParam(":user_id", $user_id);
      $insert_login_history->bindParam(":login_date", $login_date);
      $insert_login_history->bindParam(":login_time", $login_time);
      $insert_login_history->execute();

      // Redirect based on the role
      if ($_SESSION["role"] == "Admin") {
          header('Location: dashboard.php');
      } elseif ($_SESSION["role"] == "Cashier") {
          header('Location: order_user.php');
      }

      echo "<script type='text/javascript'>
      alert('Logged in Successful by " . $_SESSION["username"] . "');
      </script>";
      exit(); // Make sure to exit after header redirect
  } else {
      echo "<script type='text/javascript'>
      alert('Username or Password is Wrong. Details not match.');
      </script>";
  }
}


?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Point of Sales System</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="plugins/sweetalert2/sweetalert2.css">
  <link rel="stylesheet" href="plugins/sweetalert2/sweetalert2.min.css">

  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="stylesheet" href="css/login.css">
</head>
<body class="hold-transition login-page" style="background-image: url('image/bg.jpg'); background-size: cover;">
<div class="login-box">
  
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <!-- Adjusted Logo -->
      <img src="pos.jpg" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="width: 100px; height: 80px; display: block;  margin: -10px auto 20px; ">
      <div class="login-logo">
   <a href="index.php"><b>JETAPS</b>POS</a> 
  </div>
      <form action="" method="post">
      <div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="Username" name="txt_username" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope envelope"></span>
            </div>
        </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="Password" name="txt_password" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock lock"></span>
            </div>
          </div>
        </div>
        <!-- Show Password Checkbox -->
          <div class="input-group-append">
              <div class="input-group-text">
                  <input type="checkbox" class="mr-1" id="showPasswordCheckbox"></input> Show Password
              </div>
           </div>
        <div class="row">
          <div class="col-8">
            
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block" name="btn_login">Log In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
</body>
<!-- JavaScript for Show Password -->
<script>
document.querySelector('#showPasswordCheckbox').addEventListener('change', function() {
    const passwordInput = document.querySelector('input[name="txt_password"]');
    passwordInput.type = this.checked ? "text" : "password";
});
</script>
</html>
