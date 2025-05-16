<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pos_db", "root", "");
    // echo "Connection Successful";
} catch (PDOException $f) {
    echo $f->getMessage();
}

// include_once "conectdb.php";

session_start();
if ($_SESSION["useremail"] == "") {
    header("location:index.php");
}
include_once "header.php";

if (isset($_POST["btnSave"])) {
    $category = $_POST["txtcategory"];

    if (empty($category)) {
        $error = "<script type='text/javascript'>
        jQuery(function validation(){
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Failed',
                text: 'Field is empty',
                showConfirmButton: false,
                timer: 3000
            })
        });
        </script>";

        echo $error;
    }

    if (!isset($error)) {
        $insert = $pdo->prepare("insert into tbl_category(category) values(:category)");
        $insert->bindParam(":category", $category);

        if ($insert->execute()) {
            echo "<script type='text/javascript'>
            jQuery(function validation(){
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Done',
                    text: 'Category added successfully',
                    showConfirmButton: false,
                    timer: 3000
                })
            });
            </script>";
        }
    }
}

?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Registration</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">LOGOUT</a></li>
                        <li class="breadcrumb-item active">Admin Dashboard</li>
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
                                <h3 class="card-title">Enter new User Info</h3>
                            </div>
                            <form role="form" action="" method="post">
                                <div class="card-body">
                                    <?php
                                    if (isset($_POST["btnedit"])) {
                                    } else {
                                        echo '
                                        <div class="form-group">
                                            <label>Add Items</label>
                                            <input type="text" class="form-control" placeholder="Enter Item name" name="txtcategory">
                                        </div>';
                                    }
                                    ?>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-info" name="btnSave">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-22">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">DATA</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <td>#</td>
                                        <td>CATEGORY</td>
                                        <td>EDIT</td>
                                        <td>DELETE</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $select = $pdo->prepare("select * from tbl_category order by catid desc");
                                    $select->execute();
                                    while ($row = $select->fetch(PDO::FETCH_OBJ)) {
                                        echo "
                                        <tr>
                                            <td>$row->catid</td>
                                            <td>$row->category</td>
                                            <td><button type=\"submit\" value=\" " . $row->catid . "\"\"  class=\"btn btn-danger\" name=\"btndelete\">DELETE</button></td>
                                        </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
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
