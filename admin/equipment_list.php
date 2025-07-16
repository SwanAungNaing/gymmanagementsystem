<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$success_msg = "";
$error_msg = "";

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $res_delete = deleteData('equipments', $mysqli, "`id`='" . $mysqli->real_escape_string($delete_id) . "'");
    if ($res_delete) {
        $url = $admin_base_url . "equipment_list.php?success=Equipment Deleted Successfully";
        header("Location: $url");
        exit;
    } else {
        $url = $admin_base_url . "equipment_list.php?error=Equipment Delete Failed";
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

// Fetch equipment with joined brand name and equipment type
$sql = "SELECT e.*, bn.name AS brand_name, et.type_name AS equipment_type
        FROM equipments e
        LEFT JOIN brand_name bn ON e.brand_name_id = bn.id
        LEFT JOIN equipment_type et ON e.equipment_type_id = et.id
        ORDER BY e.id DESC";
$res = $mysqli->query($sql);

require "./layouts/header.php";
?>

<div style="overflow-y: auto; height:80vh;">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between">
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Equipment/</span>List</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "equipment_create.php") ?>" class="btn btn-primary">Add Equipment</a>
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
                                <th>Brand Name</th>
                                <th>Equipment Type</th>
                                <th>Price</th>
                                <th>Quantity</th>
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
                                        <td><?= htmlspecialchars($row['brand_name']) ?></td>
                                        <td><?= htmlspecialchars($row['equipment_type']) ?></td>
                                        <td><?= htmlspecialchars($row['price']) ?></td>
                                        <td><?= htmlspecialchars($row['quantity']) ?></td>
                                        <td><?= date("Y/F/d h:i:s A", strtotime($row['created_at'])) ?></td>
                                        <td><?= date("Y/m/d h:i:s A", strtotime($row['updated_at'])) ?></td>
                                        <td>
                                            <a href="<?= $admin_base_url . "equipment_edit.php?id=" . $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                            <button type="button" class="btn btn-sm btn-danger delete_btn" data-id="<?= htmlspecialchars($row['id']) ?>">Delete</button>
                                        </td>
                                    </tr>
                                <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="8" class="text-center">No equipment found.</td>
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
                    window.location.href = "equipment_list.php?delete_id=" + id;
                }
            });
        });
    });
</script>