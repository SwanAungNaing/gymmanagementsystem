<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

// Handle delete operation
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $res = deleteData('member_weight', $mysqli, "`id`='$delete_id'");
    if ($res) {
        $url = $admin_base_url . "member_weight_list.php?success=Member Weight Record Deleted Successfully";
        header("Location: $url");
        exit;
    } else {
        $url = $admin_base_url . "member_weight_list.php?error=Failed to Delete Member Weight Record";
        header("Location: $url");
        exit;
    }
}

$success_msg = "";
$error_msg = "";
if (isset($_GET['success'])) {
    $success_msg = $_GET['success'];
}
if (isset($_GET['error'])) {
    $error_msg = $_GET['error'];
}

// Fetch member weight records with member names
$member_weights_sql = "
    SELECT
        mw.id,
        mw.date,
        mw.curr_weight,
        mw.created_at,
        mw.updated_at,
        m.name AS member_name
    FROM
        member_weight mw
    JOIN
        members m ON mw.member_id = m.id
    ORDER BY
        mw.date DESC, mw.id DESC
";
$member_weights_res = $mysqli->query($member_weights_sql);
$member_weights = [];
if ($member_weights_res && $member_weights_res->num_rows > 0) {
    while ($row = $member_weights_res->fetch_assoc()) {
        $member_weights[] = $row;
    }
}
require "./layouts/header.php";
?>

<div style="overflow-y: auto; height:80vh;">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between">
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Member Weight/</span>List</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "member_weight_create.php") ?>" class="btn btn-primary">Record New Daily Weight</a>
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
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error_msg) ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Member Name</th>
                                    <th>Date</th>
                                    <th>Current Weight (kg)</th>
                                    <th>Recorded At</th>
                                    <th>Last Updated At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($member_weights)) {
                                    foreach ($member_weights as $row) { ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['id']) ?></td>
                                            <td><?= htmlspecialchars($row['member_name']) ?></td>
                                            <td><?= htmlspecialchars(date("Y/F/d", strtotime($row['date']))) ?></td>
                                            <td><?= htmlspecialchars($row['curr_weight']) ?></td>
                                            <td><?= htmlspecialchars(date("Y/F/d h:i:s A", strtotime($row['created_at']))) ?></td>
                                            <td><?= htmlspecialchars(date("Y/m/d h:i:s A", strtotime($row['updated_at']))) ?></td>
                                            <td>
                                                <a href="<?= $admin_base_url . "member_weight_edit.php?id=" . $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                                <button type="button" class="btn btn-sm btn-danger delete_btn" data-id="<?= htmlspecialchars($row['id']) ?>">Delete</button>
                                            </td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No member weight records found.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-backdrop fade"></div>
    </div>
</div>
<?php
require "./layouts/footer.php";
?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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
                    window.location.href = "member_weight_list.php?delete_id=" + id;
                }
            });
        });
    });
</script>