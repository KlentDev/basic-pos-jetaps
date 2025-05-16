<?php
try {
    // Establishing a connection to the database
    $pdo = new PDO("mysql:host=localhost;dbname=pos_db", "root", "");
    // Set the timezone for the MySQL connection to UTC
    $pdo->exec("SET time_zone = '+00:00'");
} catch (PDOException $e) {
    echo $e->getMessage(); // Displaying any connection error
}

// Check if the user is logged in
session_start();
if (empty($_SESSION["useremail"])) {
    header("location:index.php"); // Redirecting to index.php if the user is not logged in
}

// Include the appropriate header based on the user's role
include_once ($_SESSION["role"] === "Admin") ? "header.php" : "headeruser.php";




// Check if the user is an admin or cashier before displaying the login history section
if ($_SESSION["role"] === "Admin" || $_SESSION["role"] === "Cashier") {
    // Insert login history when a user logs in
    $user_id = $_SESSION["userid"];
    $user_role = $_SESSION["role"];
    $login_date = date("Y-m-d");
    $login_time = date("H:i:s"); // Use 24-hour format for consistency

    $pdo->beginTransaction();

    try {
        // Check if a record already exists
        $existing_login_check = $pdo->prepare("SELECT * FROM tbl_login_history WHERE user_id = :user_id AND login_date = :login_date");
        $existing_login_check->bindParam(":user_id", $user_id);
        $existing_login_check->bindParam(":login_date", $login_date);
        $existing_login_check->execute();

        if (!$existing_login_check->fetch(PDO::FETCH_ASSOC)) {
            // Insert login history only if a record doesn't already exist
            $insert_login_history = $pdo->prepare("INSERT INTO tbl_login_history (user_id, user_role, login_date, login_time) VALUES (:user_id, :user_role, :login_date, :login_time)");
            $insert_login_history->bindParam(":user_id", $user_id);
            $insert_login_history->bindParam(":user_role", $user_role);
            $insert_login_history->bindParam(":login_date", $login_date);
            $insert_login_history->bindParam(":login_time", $login_time);
            $insert_login_history->execute();
        }

        $pdo->commit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }

    // Fetching login history from the tbl_login_history table
    $select = $pdo->prepare("SELECT * FROM tbl_login_history INNER JOIN tbl_user ON tbl_login_history.user_id = tbl_user.userid ORDER BY login_id DESC");
    $select->execute();
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <!-- You can add content header elements here if needed -->
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Login History</h3>
                        </div>
                        <div class="card-body">
                            <!-- Displaying a table for login history -->
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                    <tr class="bg-lightblue">
                                        <td class='justify-center' style='text-align: center;'>Username</td>
                                        <td class='justify-center' style='text-align: center;'>Role</td>
                                        <td class='justify-center' style='text-align: center;'>Date</td>
                                        <td class='justify-center' style='text-align: center;'>Time in</td>
                                        <td class='justify-center' style='text-align: center;'>Timeout</td>
                                        <td class='justify-center' style='text-align: center;'>Action</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = $select->fetch(PDO::FETCH_OBJ)) {

                                        $user_timezone = new DateTimeZone('America/New_York');
                                        $login_time_utc = new DateTime($row->login_time, new DateTimeZone('UTC'));
                                        $login_time_utc->setTimezone($user_timezone);
                                        $login_time_utc->modify('+12 hours'); 
                                        $formatted_login_time = $login_time_utc->format('h:i:s A');
                                    
                                        // Convert logout_time to user's timezone if it exists and add 12 hours
                                        $formatted_logout_time = '';
                                        if ($row->logout_time) {
                                            $logout_time_utc = new DateTime($row->logout_time, new DateTimeZone('UTC'));
                                            $logout_time_utc->setTimezone($user_timezone);
                                        
                                            $formatted_logout_time = $logout_time_utc->format('h:i:s A');
                                        }
                                    
                                        echo "
                                        <tr>
                                            <td class='justify-center' style='text-align: center;'>$row->username</td>
                                            <td class='justify-center' style='text-align: center;'>$row->role</td>
                                            <td class='justify-center' style='text-align: center;'>" . date("F j, Y", strtotime($row->login_date)) . "</td>
                                            <td class='justify-center' style='text-align: center;'>$formatted_login_time</td>
                                            <td class='justify-center' style='text-align: center;'>$formatted_logout_time</td>
                                            <td class='justify-center' style='text-align: center;'>
                                            <button id='$row->user_id' class='btn btn-danger dltBttn' type='button'>
                                            <span class='fas fa-trash' style='color:#ffffff' data-toggle='tooltip' title='Delete User'></span>
                                            </button>
                                            </td>
                                        </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>

    <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>

    <script>

        
        $(function () {
            $("#example2").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "order": [[0, "asc"]],
                "buttons": [
            {
                extend: "excel",
                text: '<i class="fas fa-file-excel" style="color: orange;"></i> Excel',
                title: 'Login Record',
                exportOptions: {
                    columns: ':visible',
                    modifier: {
                        footer: 'true'
                    }
                }
            },
            {
                extend: "pdf",
                text: '<i class="fas fa-file-pdf" style="color: red;"></i> PDF',
                title: 'Login Record',
                exportOptions: {
                    columns: ':visible',
                    modifier: {
                        footer: 'true'
                    }
                }
            },
            "colvis",
        ],
            }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
            $("[data-toggle='tooltip']").tooltip();
        });
       
    </script>

<script>
    $(document).ready(function () {
        $(".dltBttn").click(function () {
            var id = $(this).attr("id");
            var buttonElement = $(this);

            Swal.fire({
                title: 'Are you sure?',
                text: "Once Deleted You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "logindelete.php",
                        type: "post",
                        data: {
                            user_id: id
                        },
                        success: function (data) {
                            buttonElement.closest("tr").remove();
                        }
                    });
                    Swal.fire('Deleted!', 'Login History. Deleted', 'success');
                }
            });
        });
    });
</script>





    <?php
} // end of if ($_SESSION["role"] === "Admin" || $_SESSION["role"] === "Cashier")

include_once "footer.php";
?>
