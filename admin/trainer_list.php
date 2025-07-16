<?php
require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$success_msg = "";
$error_msg = "";

if (isset($_GET['delete_id'])) {
    $delete_id = $mysqli->real_escape_string($_GET['delete_id']);
    $cert_query = $mysqli->query("SELECT img_path FROM certificates WHERE trainer_id = '$delete_id'");
    while ($cert = $cert_query->fetch_assoc()) {
        $file_path = '../' . $cert['img_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    $mysqli->query("DELETE FROM certificates WHERE trainer_id = '$delete_id'");

    $mysqli->query("DELETE FROM trainers WHERE id = '$delete_id'");

    header("Location: trainer_list.php?deleted=1");
    die();
}

if (isset($_GET['success'])) {
    $success_msg = $_GET['success'];
}
if (isset($_GET['error'])) {
    $error_msg = $_GET['error'];
}

$res = selectData('trainers', $mysqli, "*", "", "ORDER BY id DESC");

require "./layouts/header.php";
?>

<div style="overflow-y: auto; height:80vh;">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between">
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Trainer/</span>List</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "trainer_create.php") ?>" class="btn btn-primary">Add New Trainer</a>
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
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Certificates</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($res && $res->num_rows > 0) {
                                while ($row = $res->fetch_assoc()) {
                                    $cert_count = selectData('certificates', $mysqli, "COUNT(*) as count", "WHERE trainer_id = '" . $row['id'] . "'");
                                    $count = $cert_count->fetch_assoc()['count'] ?? 0;
                            ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['id']) ?></td>
                                        <td><?= htmlspecialchars($row['name']) ?></td>
                                        <td><?= htmlspecialchars($row['email']) ?></td>
                                        <td><?= htmlspecialchars($row['phone']) ?></td>
                                        <td>
                                            <span class="badge bg-primary"><?= $count ?> Certificates</span>
                                            <a href="<?= $admin_base_url ?>trainer_certificates.php?trainer_id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Manage</a>
                                        </td>
                                        <td><?= date("Y/m/d h:i A", strtotime($row['created_at'])) ?></td>
                                        <td>
                                            <a href="<?= $admin_base_url ?>trainer_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                            <button type="button" class="btn btn-sm btn-danger delete_btn" data-id="<?= htmlspecialchars($row['id']) ?>">Delete</button>
                                        </td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="7" class="text-center">No trainers found.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="content-backdrop fade"></div>
    </div>
</div>

<?php
require "./layouts/footer.php";
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('.delete_btn').click(function() {
            const id = $(this).data('id')
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
                    window.location.href = "trainer_list.php?delete_id=" + id
                }
            });
        })
    })
</script>