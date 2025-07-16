<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

// Initialize variables to prevent undefined variable errors
$error = false;
$name = '';
$name_error = '';
$start_time = '';
$start_time_error = '';
$end_time = '';
$end_time_error = '';
$service_id = '';


if (isset($_GET['id'])) {
    $service_id = $mysqli->real_escape_string($_GET['id']);


    $service_res = selectData('services', $mysqli, 'id, name, start_time, end_time', "WHERE `id`='$service_id'");

    if ($service_res && $service_res->num_rows > 0) {
        $service_data = $service_res->fetch_assoc();
        $name = $service_data['name'];
        $start_time = $service_data['start_time'];
        $end_time = $service_data['end_time'];
    } else {
        $url = $admin_base_url . "service_list.php?error=Service Not Found.";
        header("Location: $url");
        exit;
    }
} else {
    $url = $admin_base_url . "service_list.php?error=No Service ID Provided for Editing.";
    header("Location: $url");
    exit;
}

if (isset($_POST['form_sub']) && $_POST['form_sub'] == 1) {
    $name = $_POST['name'] ?? '';
    $start_time = $_POST['start_time'] ?? '';
    $end_time = $_POST['end_time'] ?? '';
    $service_id = $_POST['service_id'] ?? '';

    // --- Validation for Name ---
    if (empty($name)) {
        $error = true;
        $name_error = "Please fill Service Name.";
    } elseif (strlen($name) < 3) {
        $error = true;
        $name_error = "Service Name must be at least 3 characters.";
    } elseif (strlen($name) > 100) {
        $error = true;
        $name_error = "Service Name cannot exceed 100 characters.";
    } else {
        $check_name_sql = "SELECT id FROM services WHERE name = '" . $mysqli->real_escape_string($name) . "' AND id != '$service_id'";
        $check_name_res = $mysqli->query($check_name_sql);
        if ($check_name_res && $check_name_res->num_rows > 0) {
            $error = true;
            $name_error = "Service Name already exists for another service.";
        }
    }

    // --- Validation for Start Time ---
    if (empty($start_time)) {
        $error = true;
        $start_time_error = "Please fill Start Time.";
    }

    // --- Validation for End Time ---
    if (empty($end_time)) {
        $error = true;
        $end_time_error = "Please fill End Time.";
    } elseif (!empty($start_time) && strtotime($end_time) <= strtotime($start_time)) {
        $error = true;
        $end_time_error = "End Time must be after Start Time.";
    }

    // --- If no validation errors, proceed with database update ---
    if (!$error) {
        $data = [
            'name'        => $mysqli->real_escape_string($name),
            'start_time'  => $mysqli->real_escape_string($start_time),
            'end_time'    => $mysqli->real_escape_string($end_time),
            'updated_at'  => date('Y-m-d H:i:s') // Update timestamp for the record
        ];

        if (updateData('services', $mysqli, $data, "`id`='$service_id'")) {
            $url = $admin_base_url . "service_list.php?success=Service Updated Successfully!";
            header("Location: $url");
            exit;
        } else {
            $error = true;
            echo "<div class='alert alert-danger'>Error updating data: " . $mysqli->error . "</div>";
        }
    }
}

// Include header layout (assuming it contains HTML head and opening body tags)
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
                                    <label for="start_time">Start Time (HH:MM - 24hr format)</label>
                                    <input type="time" name="start_time" id="start_time" class="form-control" value="<?= htmlspecialchars($start_time) ?>" />
                                    <?php if ($start_time_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($start_time_error) ?></span>
                                    <?php } ?>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="end_time">End Time (HH:MM - 24hr format)</label>
                                    <input type="time" name="end_time" id="end_time" class="form-control" value="<?= htmlspecialchars($end_time) ?>" />
                                    <?php if ($end_time_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($end_time_error) ?></span>
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