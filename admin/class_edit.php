<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$error = false;
$batch_name = '';
$batch_name_error = '';
$service_id = '';
$service_id_error = '';
$trainer_id = '';
$trainer_id_error = '';
$schedule = '';
$schedule_error = '';
$class_id = '';

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

// Check if an ID is provided in the URL for editing
if (isset($_GET['id'])) {
    $class_id = $mysqli->real_escape_string($_GET['id']);
    $class_res = selectData('classes', $mysqli, '*', "WHERE `id`='$class_id'");

    if ($class_res && $class_res->num_rows > 0) {
        $class_data = $class_res->fetch_assoc();
        $batch_name = $class_data['batch_name'];
        $service_id = $class_data['service_id'];
        $trainer_id = $class_data['trainer_id'];
        $schedule = $class_data['schedule'];
    } else {
        $url = $admin_base_url . "class_list.php?error=Class Not Found";
        header("Location: $url");
        exit;
    }
} else {
    $url = $admin_base_url . "class_list.php?error=No Class ID Provided";
    header("Location: $url");
    exit;
}

if (isset($_POST['form_sub']) && $_POST['form_sub'] == 1) {

    // Safely retrieve POST variables
    $batch_name = $_POST['batch_name'] ?? '';
    $service_id = $_POST['service_id'] ?? '';
    $trainer_id = $_POST['trainer_id'] ?? '';
    $schedule = $_POST['schedule'] ?? '';
    $class_id = $_POST['class_id'] ?? '';

    // Validation for Batch Name
    if (empty($batch_name)) {
        $error = true;
        $batch_name_error = "Please fill Batch Name.";
    } elseif (strlen($batch_name) < 3) {
        $error = true;
        $batch_name_error = "Batch Name must be greater than 3 characters.";
    } elseif (strlen($batch_name) > 100) {
        $error = true;
        $batch_name_error = "Batch Name must be less than 100 characters.";
    }

    // Validation for Service
    if (empty($service_id)) {
        $error = true;
        $service_id_error = "Please select a Service.";
    }

    // Validation for Trainer
    if (empty($trainer_id)) {
        $error = true;
        $trainer_id_error = "Please select a Trainer.";
    }

    // Validation for Schedule
    if (empty($schedule)) {
        $error = true;
        $schedule_error = "Please fill Schedule.";
    } elseif (strlen($schedule) < 5) {
        $error = true;
        $schedule_error = "Schedule must be greater than 5 characters.";
    }

    if (!$error) {
        $data = [
            'batch_name' => $mysqli->real_escape_string($batch_name),
            'service_id' => $mysqli->real_escape_string($service_id),
            'trainer_id' => $mysqli->real_escape_string($trainer_id),
            'schedule'   => $mysqli->real_escape_string($schedule),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Update data in the 'classes' table
        if (updateData('classes', $mysqli, $data, "`id`='$class_id'")) {
            $url = $admin_base_url . "class_list.php?success=Class Updated Successfully";
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
                                    <label for="schedule">Schedule (e.g., Mon-Wed-Fri, 9:00 AM - 10:00 AM)</label>
                                    <input type="text" name="schedule" id="schedule" class="form-control" value="<?= htmlspecialchars($schedule) ?>" />
                                    <?php if ($schedule_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($schedule_error) ?></span>
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