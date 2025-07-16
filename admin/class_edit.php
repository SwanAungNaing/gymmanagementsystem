<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

// Initialize variables to prevent undefined variable errors
$error = false;
$batch_name = '';
$batch_name_error = '';
$trainer_id = '';
$trainer_id_error = '';
$service_id = '';
$service_id_error = '';
$start_date = '';
$start_date_error = '';
$end_date = '';
$end_date_error = '';
$price = '';
$price_error = '';

// Fetch services for dropdown
$services_res = selectData('services', $mysqli, 'id, name', '', 'ORDER BY name ASC');
$services = [];
if ($services_res && $services_res->num_rows > 0) {
    while ($row = $services_res->fetch_assoc()) {
        $services[] = $row;
    }
}

// Fetch trainers for dropdown
$trainers_res = selectData('trainers', $mysqli, 'id, name', '', 'ORDER BY name ASC');
$trainers = [];
if ($trainers_res && $trainers_res->num_rows > 0) {
    while ($row = $trainers_res->fetch_assoc()) {
        $trainers[] = $row;
    }
}

if (isset($_GET['id'])) {
    $class_id = $mysqli->real_escape_string($_GET['id']);
    $class_res = selectData('classes', $mysqli, 'id, batch_name, service_id, trainer_id, start_date, end_date, price', "WHERE `id`='$class_id'");

    // Check if the query returned results and if a class with that ID exists
    if ($class_res && $class_res->num_rows > 0) {
        $class_data = $class_res->fetch_assoc();
        $batch_name = $class_data['batch_name'];
        $service_id = $class_data['service_id'];
        $trainer_id = $class_data['trainer_id'];
        $start_date = $class_data['start_date'];
        $end_date = $class_data['end_date'];
        $price = $class_data['price'];
    } else {
        $url = $admin_base_url . "class_list.php?error=Class Not Found.";
        header("Location: $url");
        exit;
    }
} else {
    $url = $admin_base_url . "class_list.php?error=No Class ID Provided for Editing.";
    header("Location: $url");
    exit;
}

if (isset($_POST['form_sub']) && $_POST['form_sub'] == 1) {
    $batch_name = $_POST['batch_name'] ?? '';
    $service_id = $_POST['service_id'] ?? '';
    $trainer_id = $_POST['trainer_id'] ?? '';
    $start_time = $_POST['start_date'] ?? '';
    $end_time = $_POST['end_date'] ?? '';
    $price = $_POST['price'] ?? '';
    $class_id = $_POST['class_id'] ?? '';

    // --- Validation for Batch Name ---
    if (empty($batch_name)) {
        $error = true;
        $batch_name_error = "Please fill Batch Name.";
    } elseif (strlen($batch_name) < 3) {
        $error = true;
        $batch_name_error = "Batch Name must be at least 3 characters.";
    } elseif (strlen($batch_name) > 100) {
        $error = true;
        $batch_name_error = "Batch Name cannot exceed 100 characters.";
    } else {
        $check_name_sql = "SELECT id FROM classes WHERE batch_name = '" . $mysqli->real_escape_string($batch_name) . "' AND id != '$class_id'";
        $check_name_res = $mysqli->query($check_name_sql);
        if ($check_name_res && $check_name_res->num_rows > 0) {
            $error = true;
            $batch_name_error = "Batch Name already exists for another class.";
        }
    }

    // --- Validation for Service ---
    if (empty($service_id)) {
        $error = true;
        $service_id_error = "Please select a Service.";
    }

    // --- Validation for Trainer ---
    if (empty($trainer_id)) {
        $error = true;
        $trainer_id_error = "Please select a Trainer.";
    }

    // Start Date Validation
    $start_date = $_POST['start_date'];
    if (empty($start_date)) {
        $error = true;
        $start_date_error = "Please fill Start Date.";
    }

    // End Date Validation
    $end_date = $_POST['end_date'];
    if (empty($end_date)) {
        $error = true;
        $end_date_error = "Please fill End Date.";
    } else if ($start_date && $end_date && strtotime($end_date) < strtotime($start_date)) {
        $error = true;
        $end_date_error = "End Date must be after Start Date.";
    }

    // Price Validation
    $price = $mysqli->real_escape_string($_POST['price']);
    if (empty($price)) {
        $error = true;
        $price_error = "Please fill Price.";
    } else if (!is_numeric($price) || $price < 0) {
        $error = true;
        $price_error = "Price must be a positive number.";
    } else if (strlen($price) > 50) {
        $error = true;
        $price_error = "Price must be less than 50 characters.";
    }

    // --- If no validation errors, proceed with database update ---
    if (!$error) {
        $data = [
            'batch_name' => $mysqli->real_escape_string($batch_name),
            'service_id' => $mysqli->real_escape_string($service_id),
            'trainer_id' => $mysqli->real_escape_string($trainer_id),
            'start_date'   => $mysqli->real_escape_string($start_date),
            'end_date'   => $mysqli->real_escape_string($end_date),
            'price'   => $mysqli->real_escape_string($price),
            'updated_at' => date('Y-m-d H:i:s')
        ];


        if (updateData('classes', $mysqli, $data, "`id`='$class_id'")) {
            $url = $admin_base_url . "class_list.php?success=Class Updated Successfully!";
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
    <div style="overflow-y: auto; height:80vh;">
        <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
                <div class="d-flex justify-content-between">
                    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Class/</span>Edit</h4>
                    <div class="">
                        <a href="<?= htmlspecialchars($admin_base_url . "class_list.php") ?>" class="btn btn-dark">Back</a>
                    </div>
                </div>
                <div class="d-flex justify-content-center">
                    <div class="col-md-8 col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <form action="" method="POST">
                                    <input type="hidden" name="class_id" value="<?= htmlspecialchars($class_id) ?>">

                                    <div class="form-group mb-4">
                                        <label for="batch_name">Batch Name</label>
                                        <input type="text" name="batch_name" id="batch_name" class="form-control" value="<?= htmlspecialchars($batch_name) ?>" />
                                        <?php if ($batch_name_error) { ?>
                                            <span class="text-danger"><?= htmlspecialchars($batch_name_error) ?></span>
                                        <?php } ?>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label for="service_id">Service</label>
                                        <select name="service_id" id="service_id" class="form-control">
                                            <option value="">Select Service</option>
                                            <?php foreach ($services as $service) { ?>
                                                <option value="<?= htmlspecialchars($service['id']) ?>" <?= ($service_id == $service['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($service['name']) ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <?php if ($service_id_error) { ?>
                                            <span class="text-danger"><?= htmlspecialchars($service_id_error) ?></span>
                                        <?php } ?>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label for="trainer_id">Trainer</label>
                                        <select name="trainer_id" id="trainer_id" class="form-control">
                                            <option value="">Select Trainer</option>
                                            <?php foreach ($trainers as $trainer) { ?>
                                                <option value="<?= htmlspecialchars($trainer['id']) ?>" <?= ($trainer_id == $trainer['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($trainer['name']) ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <?php if ($trainer_id_error) { ?>
                                            <span class="text-danger"><?= htmlspecialchars($trainer_id_error) ?></span>
                                        <?php } ?>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label for="start_date">Start Date</label>
                                        <input type="date" name="start_date" id="start_date" class="form-control" value="<?= htmlspecialchars($start_date) ?>" />
                                        <?php if ($start_date_error) { ?>
                                            <span class="text-danger"><?= htmlspecialchars($start_date_error) ?></span>
                                        <?php } ?>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label for="end_date">End Date</label>
                                        <input type="date" name="end_date" id="end_date" class="form-control" value="<?= htmlspecialchars($end_date) ?>" />
                                        <?php if ($end_date_error) { ?>
                                            <span class="text-danger"><?= htmlspecialchars($end_date_error) ?></span>
                                        <?php } ?>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label for="price">Price</label>
                                        <input type="text" name="price" id="price" class="form-control" value="<?= htmlspecialchars($price) ?>" />
                                        <?php if ($price_error) { ?>
                                            <span class="text-danger"><?= htmlspecialchars($price_error) ?></span>
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
</div>
<?php
require "./layouts/footer.php";
?>