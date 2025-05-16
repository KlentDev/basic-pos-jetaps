<?php
// Database Connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pos_db", "root", "");
} catch (PDOException $e) {
    echo $e->getMessage();
}

session_start();
if (empty($_SESSION["useremail"]) || $_SESSION["role"] == "user") {
    header("location: index.php");
    exit; // Ensure the script stops after the redirect
}

include_once "header.php";

$id = isset($_GET['id']) ? $_GET['id'] : '';

if (!empty($id)) {
    $select = $pdo->prepare("SELECT * FROM tbl_category WHERE catid = :id");
    $select->bindParam(':id', $id, PDO::PARAM_INT);
    $select->execute();
    $row = $select->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        $id_db = $row["catid"];
        $caategory_text = $row["category"];
    } else {
        // Handle invalid product ID
        echo "Category not found";
        exit;
    }
}

if (isset($_POST["btnupdatecategory"])) {
    $category_txt = $_POST["category"];
    
    $update = $pdo->prepare("UPDATE tbl_category SET category=:category WHERE catid = :id");
    $update->bindParam(":category", $category_txt);
    $update->bindParam(":id", $id, PDO::PARAM_INT);

    if ($update->execute()) {
        echo "<script type='text/javascript'>
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Saved',
                text: 'Item updated successfully',
                showConfirmButton: false,
                timer: 3000
            });
        </script>";
    } else {
        echo "<script type='text/javascript'>
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Failed',
                text: 'Unable to update item',
                showConfirmButton: false,
                timer: 3000
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
                    <h1 class="m-0">Edit Category</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="logout.php">LOGOUT</a></li>
                        <li class="breadcrumb-item active">Admin Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="row justify-content-center">
            <!-- center column -->
            <div class="col-md-6">
                <!-- general form elements -->
                <div class="card card-info">
                    <div class="card-header">
                    <a href = "category.php" <h3 class="card-title">View Category</h3> </a>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form action="" method="post">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Category Name</label>
                                <input type="text" class="form-control" id="exampleInputEmail1" name="category" value="<?php echo isset($category_txt) ? $category_txt : ''; ?>"
                                    required>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <button type="submit" class="btn btn-danger" name="btnupdatecategory">Update</button>
                        </div>
                    </form>
                </div>
                <!-- /.card -->
            </div>
        </div>
    </section>
</div>

<!-- /.content-wrapper -->

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
    <div class="p-3">
        <h5>Title</h5>
        <p>Sidebar content</p>
    </div>
</aside>
<!-- /.control-sidebar -->
<?php
include_once "footer.php";
?>
