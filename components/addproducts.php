<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pos_db", "root", "");
} catch (PDOException $f) {
    echo $f->getMessage();
}

session_start();
if (empty($_SESSION["useremail"])) {
    header("location:index.php");
} elseif ($_SESSION["role"] !== "Admin") {
    header("location:order_user.php");
}

include_once "header.php";

if (isset($_POST["btndelete"])) {
    $pid = $_POST["btndelete"];
    $delete = $pdo->prepare("DELETE FROM tbl_product WHERE pid = :pid");
    $delete->bindParam(":pid", $pid);

    if ($delete->execute()) {
        displaySuccessMessage("Product deleted successfully.");
    } else {
        displayErrorMessage("Product not deleted.");
    }
}

if (isset($_POST["btnaddproduct"])) {
    $productname = $_POST["txtpname"];

    // Check if the product with the same name already exists
    $checkDuplicate = $pdo->prepare("SELECT COUNT(*) FROM tbl_product WHERE pname = :productname");
    $checkDuplicate->bindParam(":productname", $productname);
    $checkDuplicate->execute();
    $countDuplicate = $checkDuplicate->fetchColumn();

    if ($countDuplicate > 0) {
        displayErrorMessage("Product with the same name already exists. Please choose a different name.");
    } else {
        $category = $_POST["txtselect_option"];
        $purchaseprice = $_POST["txtprice"];
        $stock = $_POST["txtstock"];

        // Handle image upload
        $image = $_FILES["image"];

        if ($image["error"] === UPLOAD_ERR_OK) {
            $imagePath = "image/" . $image["name"]; // Define the path where the image will be saved

            if (move_uploaded_file($image["tmp_name"], $imagePath)) {
                if ($category === "new_category") {
                    $newCategoryName = $_POST["newCategoryName"];

                    if (!empty($newCategoryName)) {
                        $insertCategory = $pdo->prepare("INSERT INTO tbl_category (category) VALUES (:category)");
                        $insertCategory->bindParam(":category", $newCategoryName);

                        if ($insertCategory->execute()) {
                            $category = $newCategoryName; // Set the category to the new one
                        } else {
                            displayErrorMessage("Failed to add the new category.");
                        }
                    } else {
                        displayErrorMessage("New Category Name cannot be empty.");
                    }
                }

                $insert = $pdo->prepare("INSERT INTO tbl_product (pname, pcategory, saleprice, pstock, image) 
                        VALUES (:pname, :pcategory, :saleprice, :pstock, :image)");
                $insert->bindParam(":pname", $productname);
                $insert->bindParam(":pcategory", $category);
                $insert->bindParam(":saleprice", $purchaseprice);
                $insert->bindParam(":pstock", $_POST["txtstock"]);
                $insert->bindParam(":image", $imagePath);


                if ($insert->execute()) {
                    displaySuccessMessage("Product added successfully.");
                } else {
                    displayErrorMessage("Unable to add product.");
                }
            } else {
                displayErrorMessage("Failed to upload the image.");
            }
        } else {
            displayErrorMessage("Error uploading the image.");
        }
    }
}

function displaySuccessMessage($message)
{
    echo "<script type='text/javascript'>
        Swal.fire({
            position: 'center',
            icon: 'success',
            title: 'Saved',
            text: '$message',
            showConfirmButton: false,
            timer: 3000
        });
    </script>";
}

function displayErrorMessage($message)
{
    echo "<script type='text/javascript'>
        Swal.fire({
            position: 'center',
            icon: 'error',
            title: 'Failed',
            text: '$message',
            showConfirmButton: false,
            timer: 3000
        });
    </script>";
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
                        <li class="breadcrumb-item">Products</li>
                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
        <div class="row">
            <!-- left column -->
            <div class="col-md-4">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            Add Products
                        </h3>
                    </div>
                    <form action="addproducts.php" method="post" enctype="multipart/form-data">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Items Name</label>
                                <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Enter Name" name="txtpname">
                            </div>

                            <div class="form-group">
                                <label>Category</label>
                                <select class="form-control" name="txtselect_option" id="categorySelect" required>
                                    <option value="" disabled selected>Select Category</option>
                                    <?php
                                    $select = $pdo->prepare("select * from tbl_category order by catid ASC");
                                    $select->execute();
                                    while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
                                        extract($row);
                                    ?>
                                        <option value="<?php echo $row['category']; ?>">
                                            <?php echo $row['category']; ?>
                                        </option>
                                    <?php
                                    }
                                    ?>
                                    <option value="new_category">Add New Category</option>
                                </select>
                            </div>

                            <!-- Input field for entering a new category name -->
                            <div class="form-group" id="newCategoryInput" style="display: none;">
                                <label for="newCategoryName">New Category Name</label>
                                <input type="text" class="form-control" name="newCategoryName" id="newCategoryName" placeholder="Enter New Category Name">
                            </div>

                            <div class="form-group">
                                <label for="exampleInputPassword1">Price</label>
                                <input type="text" class="form-control" placeholder="Enter Product Price" name="txtprice" required>
                            </div>

                            <div class="form-group">
                                <label for="image">Image</label>
                                <input type="file" class="form-control-file" name="image" accept="image/*" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Stock</label>
                                <input type="text" class="form-control" placeholder="Enter Quantity" name="txtstock" required>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success" name="btnaddproduct">Add Item</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Right column -->
            <div class="col-md-8">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Product Data</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST">
                        <table id="example1" class="table table-bordered table-hover">
                            <thead>
    <tr class="bg-lightblue">
        <td class="justify-center" style="text-align: center;">No.</td>
        <td class="justify-center" style="text-align: center;">Product</td>
        <td class="justify-center" style="text-align: center;">Image</td>
        <td class="justify-center" style="text-align: center;">Stock</td>
        <td class="justify-center" style="text-align: center;">Action</td>
    </tr>
</thead>
<tbody>
<?php
    $select = $pdo->prepare("SELECT * FROM tbl_product ORDER BY pid DESC");
    $select->execute();

    $rowNumber = 1; // Initialize the row number counter

    while ($row = $select->fetch(PDO::FETCH_OBJ)) {
        echo "<tr>
            <td class='justify-center' style='text-align: center;'>$rowNumber</td>
            <td class='justify-center' style='text-align: center;'>$row->pname</td>
            <td class='justify-center' style='text-align: center;'>";

        // Check if $row->image is set before displaying the image
        if (isset($row->image) && !empty($row->image)) {
            echo "<img src='$row->image' width='100' height='60'>";
        } else {
            echo "No Image Available";
        }

        echo "</td>
            <td class='justify-center' style='text-align: center;'>$row->pstock</td>
            <td class='justify-center' style='text-align: center;'>
                <button id=" . $row->pid . " class=\"btn btn-danger dltBttn\" type=\"button\">
                    <span class=\"fas fa-trash\" style=\"color:#ffffff\" data-toggle=\"tooltip\" title=\"DELETE Order\"></span>
                </button>
                <a href=\"Editproduct.php?id=" . $row->pid . "\" class=\"btn btn-info\" role=\"button\">
                    <span class=\"fas fa-edit\" style=\"color:#ffffff\" data-toggle=\"tooltip\" title=\"EDIT\"></span>
                </a>
            </td>
        </tr>";

        $rowNumber++; // Increment the row number counter
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

<script>
    $(document).ready(function () {
        $('#categorySelect').change(function () {
            if ($(this).val() === 'new_category') {
                $('#newCategoryInput').show();
            } else {
                $('#newCategoryInput').hide();
            }
        });

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

        $(".dltBttn").click(function () {
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
                        url: "productdelete.php",
                        type: "post",
                        data: {
                            pidd: id
                        },
                        success: function (data) {
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
    });
</script>

<?php
include_once "footer.php";
?>
