Zayar Maung, [7/8/2025 3:38 PM]
<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$success_msg = "";
$error_msg = "";

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $res_delete = deleteData('class_members', $mysqli, "id='" . $mysqli->real_escape_string($delete_id) . "'");
    if ($res_delete) {
        $url = $admin_base_url . "class_member_list.php?success=Class Member Deleted Successfully";
        header("Location: $url");
        exit;
    } else {
        $url = $admin_base_url . "class_member_list.php?error=Class Member Delete Failed";
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

// Fetch class members with joined class batch_name and member name
$sql = "SELECT cm.*, c.batch_name AS class_batch_name, m.name AS member_name
        FROM class_members cm
        LEFT JOIN classes c ON cm.class_id = c.id
        LEFT JOIN members m ON cm.member_id = m.id
        ORDER BY cm.id DESC";
$res = $mysqli->query($sql);

require "./layouts/header.php";
?>

<div style="overflow-y: auto; height:80vh;">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between">
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Class Member/</span>List</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "class_member_create.php") ?>" class="btn btn-primary">Assign Class Member</a>
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
                                <th>Class Batch Name</th>
                                <th>Member Name</th>
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
                                        <td><?= htmlspecialchars($row['class_batch_name']) ?></td>
                                        <td><?= htmlspecialchars($row['member_name']) ?></td>
                                        <td><?= date("Y/F/d h:i:s A", strtotime($row['created_at'])) ?></td>
                                        <td><?= date("Y/m/d h:i:s A", strtotime($row['updated_at'])) ?></td>
                                        <td>
                                            <a href="<?= $admin_base_url . "class_member_edit.php?id=" . $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                            <button type="button" class="btn btn-sm btn-danger delete_btn" data-id="<?= htmlspecialchars($row['id']) ?>">Delete</button>
                                        </td>
                                    </tr>
                                <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="7" class="text-center">No class members found.</td>
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
                    window.location.href = "class_member_list.php?delete_id=" + id;
                }
            });
        });
    });
</script>