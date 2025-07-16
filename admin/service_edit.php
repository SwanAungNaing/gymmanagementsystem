<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$error = false;
$name = '';
$name_error = '';
$description = '';
$description_error = '';
$service_id = '';

// Check if an ID is provided in the URL for editing
if (isset($_GET['id'])) {
    $service_id = $mysqli->real_escape_string($_GET['id']);
    $service_res = selectData('services', $mysqli, '*', "WHERE `id`='$service_id'");

    if ($service_res && $service_res->num_rows > 0) {
        $service_data = $service_res->fetch_assoc();
        $name = $service_data['name'];
        $description = $service_data['description'];
    } else {
        $url = $admin_base_url . "service_list.php?error=Service Not Found";
        header("Location: $url");
        exit;
    }
} else {
    $url = $admin_base_url . "service_list.php?error=No Service ID Provided";
    header("Location: $url");
    exit;
}

if (isset($_POST['form_sub']) && $_POST['form_sub'] == 1) {

    // Safely retrieve POST variables
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $service_id = $_POST['service_id'] ?? '';

    // Validation for Name
    if (empty($name)) {
        $error = true;
        $name_error = "Please fill Service Name.";
    } elseif (strlen($name) < 3) {
        $error = true;
        $name_error = "Service Name must be greater than 3 characters.";
    } elseif (strlen($name) > 100) {
        $error = true;
        $name_error = "Service Name must be less than 100 characters.";
    } else {
        $check_name_sql = "SELECT id FROM services WHERE name = '$name' AND id != '$service_id'";
        $check_name_res = $mysqli->query($check_name_sql);
        if ($check_name_res && $check_name_res->num_rows > 0) {
            $error = true;
            $name_error = "Service Name already exists.";
        }
    }

    // Validation for Description
    if (empty($description)) {
        $error = true;
        $description_error = "Please fill Description.";
    } elseif (strlen($description) < 5) {
        $error = true;
        $description_error = "Description must be greater than 5 characters.";
    }

    if (!$error) {
        $data = [
            'name'        => $mysqli->real_escape_string($name),
            'description' => $mysqli->real_escape_string($description),
            'updated_at'  => date('Y-m-d H:i:s')
        ];

        // Update data in the 'services' table
        if (updateData('services', $mysqli, $data, "`id`='$service_id'")) {
            $url = $admin_base_url . "service_list.php?success=Service Updated Successfully";
            header("Location: $url");
            exit;
        } else {
            $error = true;
            echo "Error updating data: " . $mysqli->error;
        }
    }
}
require "./layouts/header.php";
?>

<div style="overflow-y: auto; height:80vh;">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between">
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Service/</span>Edit</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "service_list.php") ?>" class="btn btn-dark">Back</a>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="POST">
                                <input type="hidden" name="service_id" value="<?= htmlspecialchars($service_id) ?>">
                                <div class="form-group mb-4">
                                    <label for="name">Service Name</label>
                                    <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($name) ?>" />
                                    <?php if ($name_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($name_error) ?></span>
                                    <?php } ?>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" class="form-control" rows="5"><?= htmlspecialchars($description) ?></textarea>
                                    <?php if ($description_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($description_error) ?></span>
                                    <?php } ?>
                                </div>

                                <input type="hidden" name="form_sub" value="1">
                                <div class="form-group">
                                    <button class="btn btn-primary w-100">Update</button>
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