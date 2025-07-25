<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$admin_id = 1;

$admin_name = 'N/A';
$admin_email = 'N/A';
$error_message = '';

// Fetch admin data
$admin_res = selectData('admin', $mysqli, 'name, email', "WHERE `id`='$admin_id'");

if ($admin_res && $admin_res->num_rows > 0) {
    $admin_data = $admin_res->fetch_assoc();
    $admin_name = htmlspecialchars($admin_data['name']);
    $admin_email = htmlspecialchars($admin_data['email']);
} else {
    $error_message = "Admin profile not found or database error.";
}

require "./layouts/header.php";
?>

<div style="overflow-y: auto; height:80vh;">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Admin/</span>My Profile</h4>
                <a href="<?= htmlspecialchars($admin_base_url . "profile_edit.php?id=" . $admin_id) ?>" class="btn btn-primary">Edit Profile</a>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <?php if ($error_message) { ?>
                                <div class="alert alert-danger" role="alert">
                                    <?= $error_message ?>
                                </div>
                            <?php } else { ?>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Name:</label>
                                    <p class="form-control-static"><?= $admin_name ?></p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Email:</label>
                                    <p class="form-control-static"><?= $admin_email ?></p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
require "./layouts/footer.php";
?>