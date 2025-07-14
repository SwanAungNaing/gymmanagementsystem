<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

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

// Fetch all trainers for the dropdown
$trainers_res = selectData('trainers', $mysqli, 'id, name');
$trainers = [];
if ($trainers_res && $trainers_res->num_rows > 0) {
    while ($row = $trainers_res->fetch_assoc()) {
        $trainers[] = $row;
    }
}

// Fetch all services for the dropdown
$services_res = selectData('services', $mysqli, 'id, name');
$services = [];
if ($services_res && $services_res->num_rows > 0) {
    while ($row = $services_res->fetch_assoc()) {
        $services[] = $row;
    }
}

if (isset($_POST['form_sub']) && $_POST['form_sub'] == 1) {

    // Batch Name Validation
    $batch_name = $mysqli->real_escape_string($_POST['batch_name']);
    if (empty($batch_name)) {
        $error = true;
        $batch_name_error = "Please fill Batch Name.";
    } else if (strlen($batch_name) < 3) {
        $error = true;
        $batch_name_error = "Batch Name must be greater than 3 characters.";
    } else if (strlen($batch_name) > 50) {
        $error = true;
        $batch_name_error = "Batch Name must be less than 50 characters.";
    }

    // Trainer ID Validation
    $trainer_id = $_POST['trainer_id'];
    if (empty($trainer_id)) {
        $error = true;
        $trainer_id_error = "Please select a Trainer.";
    }

    // Service ID Validation
    $service_id = $_POST['service_id'];
    if (empty($service_id)) {
        $error = true;
        $service_id_error = "Please select a Service.";
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


    if (!$error) {
        $data = [
            'batch_name' => $mysqli->real_escape_string($batch_name),
            'trainer_id' => $mysqli->real_escape_string($trainer_id),
            'service_id' => $mysqli->real_escape_string($service_id),
            'start_date' => $mysqli->real_escape_string($start_date),
            'end_date'   => $mysqli->real_escape_string($end_date),
            'price'      => $mysqli->real_escape_string($price)
        ];

        if (insertData('classes', $mysqli, $data)) {
            $url = $admin_base_url . "class_list.php?success=Class Created Successfully";
            header("Location: $url");
            exit;
        } else {
            $error = true;
            echo "Error inserting data: " . $mysqli->error;
        }
    }
}
require "./layouts/header.php";
?>

<div style="overflow-y: auto; height:80vh;">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between">
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Class/</span>Create</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "class_list.php") ?>" class="btn btn-dark">Back</a>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="form-group mb-4">
                                    <label for="batch_name">Batch Name</label>
                                    <input type="text" name="batch_name" id="batch_name" class="form-control" value="<?= htmlspecialchars($batch_name) ?>" />
                                    <?php if ($batch_name_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($batch_name_error) ?></span>
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
                                    <button class="btn btn-primary w-100">Submit</button>
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