<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$error = false;
$name = '';
$name_error = '';
$email = '';
$email_error = '';
$admin_id = '';
$admin_id = 1;
$admin_res = selectData('admin', $mysqli, 'id, name, email', "WHERE `id`='$admin_id'");

if ($admin_res && $admin_res->num_rows > 0) {
    $admin_data = $admin_res->fetch_assoc();
    $name = $admin_data['name'];
    $email = $admin_data['email'];
} else {
    $url = $admin_base_url . "profile_create.php?error=" . urlencode("Admin Profile Not Found.");
    header("Location: $url");
    exit;
}

// --- STEP 2: Handle form submission (POST request) for updating data ---
if (isset($_POST['form_sub']) && $_POST['form_sub'] == 1) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $admin_id = $_POST['admin_id'] ?? '';

    // Validation for Name
    if (empty($name)) {
        $error = true;
        $name_error = "Please fill your Name.";
    } elseif (strlen($name) < 3) {
        $error = true;
        $name_error = "Name must be at least 3 characters.";
    } elseif (strlen($name) > 100) {
        $error = true;
        $name_error = "Name cannot exceed 100 characters.";
    }

    // Validation for Email
    if (empty($email)) {
        $error = true;
        $email_error = "Please fill your Email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = true;
        $email_error = "Invalid Email format.";
    } else {
        $check_email_sql = "SELECT id FROM admin WHERE email = '" . $mysqli->real_escape_string($email) . "' AND id != '$admin_id'";
        $check_email_res = $mysqli->query($check_email_sql);
        if ($check_email_res && $check_email_res->num_rows > 0) {
            $error = true;
            $email_error = "This email is already registered to another admin.";
        }
    }

    // If no validation errors, proceed with database update
    if (!$error) {
        $data = [
            'name'       => $mysqli->real_escape_string($name),
            'email'      => $mysqli->real_escape_string($email),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Update data in the 'admin' table
        if (updateData('admin', $mysqli, $data, "`id`='$admin_id'")) {
            $url = $admin_base_url . "profile_create.php?success=" . urlencode("Profile Updated Successfully!");
            header("Location: $url");
            exit;
        } else {
            $error = true;
            echo "<div class='alert alert-danger'>Error updating profile: " . htmlspecialchars($mysqli->error) . "</div>";
        }
    }
}

require "./layouts/header.php";
?>

<div style="overflow-y: auto; height:80vh;">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold py-3 mb-0"><span class="text-muted fw-light">Admin/</span>Edit Profile</h4>
                <a href="<?= htmlspecialchars($admin_base_url . "profile_create.php") ?>" class="btn btn-dark">Back to Profile</a>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="POST">
                                <input type="hidden" name="admin_id" value="<?= htmlspecialchars($admin_id) ?>">

                                <div class="form-group mb-4">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($name) ?>" />
                                    <?php if ($name_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($name_error) ?></span>
                                    <?php } ?>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($email) ?>" />
                                    <?php if ($email_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($email_error) ?></span>
                                    <?php } ?>
                                </div>

                                <input type="hidden" name="form_sub" value="1">
                                <div class="form-group">
                                    <button class="btn btn-primary w-100">Update Profile</button>
                                </div>
                            </form>
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