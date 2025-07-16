<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$success_msg = "";
$error_msg = "";

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $res_delete = deleteData('certificates', $mysqli, "`id`='" . $mysqli->real_escape_string($delete_id) . "'");
    if ($res_delete) {
        $url = $admin_base_url . "certificate_list.php?success=Certificate Delete Success";
        header("Location: $url");
        exit;
    } else {
        $url = $admin_base_url . "certificate_list.php?error=Certificate Delete Failed";
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

// Fetch certificates along with trainer names using a JOIN
$sql = "SELECT c.*, t.name AS trainer_name FROM `certificates` c JOIN `trainers` t ON c.trainer_id = t.id ORDER BY c.id DESC";
$res = $mysqli->query($sql);

require "./layouts/header.php";
?>

<div style="overflow-y: auto; height:80vh;">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between">
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Certificate/</span>List</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "certificate_create.php") ?>" class="btn btn-primary">Add Certificate</a>
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
                                <th>Certificate Name</th>
                                <th>Trainer Name</th>
                                <th>Image</th>
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
                                        <td><?= htmlspecialchars($row['name']) ?></td>
                                        <td><?= htmlspecialchars($row['trainer_name']) ?></td>
                                        <td>
                                            <?php if ($row['img_path']) { ?>
                                                <img src="../<?= htmlspecialchars($row['img_path']) ?>" alt="Certificate Image" style="width: 100px; height: auto;" onerror="this.onerror=null;this.src='path/to/default/image.png';" />
                                            <?php } else { ?>
                                                No Image
                                            <?php } ?>
                                        </td>
                                        <td><?= date("Y/F/d h:i:s A", strtotime($row['created_at'])) ?></td>
                                        <td><?= date("Y/m/d h:i:s A", strtotime($row['updated_at'])) ?></td>
                                        <td>
                                            <a href="<?= $admin_base_url . "certificate_edit.php?id=" . $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                            <button type="button" class="btn btn-sm btn-danger delete_btn" data-id="<?= htmlspecialchars($row['id']) ?>">Delete</button>
                                        </td>
                                    </tr>
                                <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="7" class="text-center">No certificates found.</td>
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
                    window.location.href = "certificate_list.php?delete_id=" + id;
                }
            });
        });
    });
</script>