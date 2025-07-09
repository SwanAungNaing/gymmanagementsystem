<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$success_msg = "";
$error_msg = "";

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $res_delete = deleteData('equipment_type', $mysqli, "`id`='" . $mysqli->real_escape_string($delete_id) . "'");
    if ($res_delete) {
        $url = $admin_base_url . "equipment_type_list.php?success=Equipment Type Deleted Successfully";
        header("Location: $url");
        exit;
    } else {
        $url = $admin_base_url . "equipment_type_list.php?error=Equipment Type Delete Failed";
        header("Location: $url");
        exit;
    }
}

if (isset($_GET['success'])) {
    $success_msg = $_GET['success'];
}
if (isset($_GET['error'])) {
    $error_msg = $_GET['error'];
}

// Fetch data from the 'equipment_type' table
$res = selectData('equipment_type', $mysqli, $column = "*", $where = "", $order = "ORDER BY id DESC");

require "./layouts/header.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Type List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<body>
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between">
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Equipment_Type/</span>List</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "equipment_type_create.php") ?>" class="btn btn-primary">Add Equipment Type</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5 offset-md-7 col-12">
                    <?php if ($success_msg) { ?>
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <?= htmlspecialchars($success_msg) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php } ?>
                    <?php if ($error_msg) { ?>
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <?= htmlspecialchars($error_msg) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type Name</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($res && $res->num_rows > 0) {
                                while ($row = $res->fetch_assoc()) {
                            ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['id']) ?></td>
                                        <td><?= htmlspecialchars($row['type_name']) ?></td>
                                        <td><?= date("Y/F/d h:i:s A", strtotime($row['created_at'])) ?></td>
                                        <td><?= date("Y/m/d h:i:s A", strtotime($row['updated_at'])) ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger delete_btn" data-id="<?= htmlspecialchars($row['id']) ?>">Delete</button>
                                        </td>
                                    </tr>
                                <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="5" class="text-center">No equipment types found.</td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php
    require "./layouts/footer.php";
    ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('.delete_btn').click(function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel!",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "equipment_type_list.php?delete_id=" + id;
                    }
                });
            });
        });
    </script>
</body>

</html>