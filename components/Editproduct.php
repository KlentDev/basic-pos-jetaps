<?php
// Database Connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pos_db", "root", "");
} catch (PDOException $e) {
    echo $e->getMessage();
}

session_start();
if (empty($_SESSION["useremail"])) {
    header("location:index.php");
}elseif ( $_SESSION["role"] !== "Admin") {
    header("location:order_user.php");
}

include_once "header.php";

$id = isset($_GET['id']) ? $_GET['id'] : '';

if (!empty($id)) {
    $select = $pdo->prepare("SELECT * FROM tbl_product WHERE pid = :id");
    $select->bindParam(':id', $id, PDO::PARAM_INT);
    $select->execute();
    $row = $select->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        $id_db = $row["pid"];
        $productname_db = $row["pname"];
        $category_db = $row["pcategory"];
        $purchaseprice_db = $row["saleprice"];
        $stock_db = $row["pstock"];
        // $description_db = $row["pdescription"];
    } else {
        // Handle invalid product ID
        echo "Product not found";
        exit;
    }
}

if (isset($_POST["btnupdateproduct"])) {
    $product_id = $_GET['id']; // Get the product ID from the URL
    $productname_txt = $_POST["txtpname"];
    $category_txt = $_POST["txtselect_option"];
    $purchaseprice_txt = $_POST["txtprice"];
    $stock_txt = $_POST["txtstock"];
    // $description_txt = $_POST["txtdesc"];

    // Check if a new image was uploaded
    if (!empty($_FILES["new_image"]["name"])) {
        // Handle the new image upload
        $new_image = $_FILES["new_image"];
        if ($new_image["error"] === UPLOAD_ERR_OK) {
            $imagePath = "image/" . $new_image["name"]; // Define the path where the new image will be saved

            if (move_uploaded_file($new_image["tmp_name"], $imagePath)) {
                // Update the product information, including the new image path
                $update = $pdo->prepare("UPDATE tbl_product SET pname=:pname, pcategory=:pcategory, saleprice=:saleprice, pstock=:pstock, image=:image WHERE pid = :id");
                $update->bindParam(":pname", $productname_txt);
                $update->bindParam(":pcategory", $category_txt);
                $update->bindParam(":saleprice", $purchaseprice_txt);
                $update->bindParam(":pstock", $stock_txt);
                // $update->bindParam(":pdescription", $description_txt);
                $update->bindParam(":image", $imagePath);
                $update->bindParam(":id", $product_id, PDO::PARAM_INT);

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
            } else {
                echo "<script type='text/javascript'>
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'Failed',
                        text: 'Failed to upload the new image',
                        showConfirmButton: false,
                        timer: 3000
                    });
                </script>";
            }
        } else {
            echo "<script type='text/javascript'>
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'Failed',
                    text: 'Error uploading the new image',
                    showConfirmButton: false,
                    timer: 3000
                });
            </script>";
        }
    } else {
        // No new image was uploaded, update the product information without changing the image
        $update = $pdo->prepare("UPDATE tbl_product SET pname=:pname, pcategory=:pcategory, saleprice=:saleprice, pstock=:pstock WHERE pid = :id");
        $update->bindParam(":pname", $productname_txt);
        $update->bindParam(":pcategory", $category_txt);
        $update->bindParam(":saleprice", $purchaseprice_txt);
        $update->bindParam(":pstock", $stock_txt);
        // $update->bindParam(":pdescription", $description_txt);
        $update->bindParam(":id", $product_id, PDO::PARAM_INT);

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
}
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="addproducts.php">Back</a></li>
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
                                 Edit Products
                            </div>
               

                    <!-- /.card-header -->
                    <!-- form start -->
                    <form action="" method="post">

                        <div class="card-body">
                            <div class="form-group">
                                <label>Items Name</label>
                                <input type="text" class="form-control" id="exampleInputEmail1" name="txtpname" value="<?php echo $productname_db; ?>"
                                    required>
                            </div>
                            <div class="form-group">
                                <label>Category</label>
                                <select class="form-control" name="txtselect_option" required>
                                    <option value="" disabled selected>Select Category</option>
                                    <?php
                                    $select = $pdo->prepare("SELECT * FROM tbl_category ORDER BY catid DESC");
                                    $select->execute();
                                    while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
                                        $selected = ($row['category'] == $category_db) ? 'selected="selected"' : '';
                                        echo "<option $selected>{$row['category']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Price</label>
                                <input type="text" class="form-control" value="<?php echo number_format($purchaseprice_db, 2, '.', ','); ?>" name="txtprice" required>

                            </div>

                            <div class="form-group">
                                    <label for="image">Image</label>
                                    <input type="file" class="form-control-file" name="new_image" accept="image/*">
                                </div>

                            <div class="form-group">
                                <label for="exampleInputPassword1">Stock</label>
                                <input type="text" class="form-control" value="<?php echo $stock_db; ?>" name="txtstock" required>
                            </div>
                           <div class="form-group">
                                <!-- <label>Description</label>
                                <textarea type="text" class="form-control" name="txtdesc" rows="4">
                                    <?php echo $description_db; ?>
                                </textarea>
                            </div>
                        </div> -->
                        
                        <div class="card-footer">
                        <button type="submit" align-items="center" class="btn btn-primary" name="btnupdateproduct">Update</button>

                                </div>
                    </form>
                </div>
                              
                <!-- /.card -->
            </div>
        </div>
    </section>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    $(document).ready(function () {
    $('form').submit(function (e) {
        e.preventDefault();

        var formData = new FormData($(this)[0]);
        formData.append('id', <?php echo $id; ?>); // Add the product ID to the form data

        $.ajax({
            url: 'update_product.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Saved',
                    text: 'Item updated successfully',
                    showConfirmButton: false,
                    timer: 3000
                });

                // Reload notification buttons after updating the product
                updateNotificationButtons();
            },
            error: function (error) {
                console.error('Error updating product: ', error);
            }
        });
    });

    function updateNotificationButtons() {
        // Add logic to update your notification buttons here
        // You can use another AJAX request to get the updated counts from the server
        // and then update the badge counts in the notification buttons
    }
});

</script>

<?php
include_once "footer.php";
?>
