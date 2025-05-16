<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pos_db", "root", "");
    echo "Connection Successful";
} catch (PDOException $f) {
    echo $f->getMessage();
}

// include_once "conectdb.php";

session_start();
if ($_SESSION["useremail"] == "" && $_SESSION["role"] == "user") {
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
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Categories</h1>
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

    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Categories Addition</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="post">
                            <?php
                                if (isset($_POST["btnedit"])) {
                                    $editid = $_POST["btnedit"];
                                    $select = $pdo->prepare("select * from tbl_category where catid='$editid'");
                                    $select->execute();
                                    if ($select) {
                                        $row = $select->fetch(PDO::FETCH_OBJ);
                                        echo "<div class=\"form-group\">
                                            <label for=\"exampleInputEmail1\">Items Name</label>
                                            <input type=\"hidden\" class=\"form-control\" placeholder=\"Enter Name\" name=\"txtid\" value=\"$row->catid\">
                                            <input type=\"text\" class=\"form-control\" id=\"exampleInputEmail1\" placeholder=\"Enter Name\" name=\"txtcategory\" value=\"$row->category\">
                                        </div>
                                        <button input type=\"submit\" class=\"btn btn-info\" name=\"btnupdate\">Update</button>";
                                    } else {
                                        echo "<div class=\"form-group\">
                                            <label for=\"exampleInputEmail1\">Items Name</label>
                                            <input type=\"hidden\" class=\"form-control\" placeholder=\"Enter Name\" name=\"txtid\">
                                            <button input type=\"submit\" class=\"btn btn-warning\" name=\"btnSave\">Update</button>";
                                    }
                            }
                            ?>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">DATA</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="post">
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
                                        echo "<tr>
                                            <td>$row->catid</td>
                                            <td>$row->category</td>
                                            <td><button input type=\"submit\" value=\"$row->catid\" class=\"btn btn-success\" name=\"btnedit\">EDIT</button></td>
                                            <td><button input type=\"submit\" value=\"$row->catid\" class=\"btn btn-danger\" name=\"btndelete\">DELETE</button></td>
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

<aside class
