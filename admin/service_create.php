<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$error = false;
$name = '';
$name_error = '';
$start_time = '';
$start_time_error = '';
$end_time = '';
$end_time_error = '';

if (isset($_POST['form_sub']) && $_POST['form_sub'] == 1) {

    // Name Validation
    $name = $mysqli->real_escape_string($_POST['name']);
    if (empty($name)) {
        $error = true;
        $name_error = "Please fill Service Name.";
    } else if (strlen($name) < 3) {
        $error = true;
        $name_error = "Service Name must be greater than 3 characters.";
    } else if (strlen($name) > 100) {
        $error = true;
        $name_error = "Service Name must be less than 100 characters.";
    }

    // Start Time Validation
    $start_time = $_POST['start_time'];
    if (empty($start_time)) {
        $error = true;
        $start_time_error = "Please fill Start Time.";
    } else if (!preg_match("/^([01]\d|2[0-3]):([0-5]\d)$/", $start_time)) { // HH:MM format (24-hour)
        $error = true;
        $start_time_error = "Invalid Start Time format (HH:MM).";
    }

    // End Time Validation
    $end_time = $_POST['end_time'];
    if (empty($end_time)) {
        $error = true;
        $end_time_error = "Please fill End Time.";
    } else if (!preg_match("/^([01]\d|2[0-3]):([0-5]\d)$/", $end_time)) { // HH:MM format (24-hour)
        $error = true;
        $end_time_error = "Invalid End Time format (HH:MM).";
    } else if ($start_time && $end_time && $end_time <= $start_time) {
        $error = true;
        $end_time_error = "End Time must be after Start Time.";
    }

    if (!$error) {
        $data = [
            'name'          => $mysqli->real_escape_string($name),
            'start_time'    => $mysqli->real_escape_string($start_time),
            'end_time'      => $mysqli->real_escape_string($end_time)
        ];

        if (insertData('services', $mysqli, $data)) {
            $url = $admin_base_url . "service_list.php?success=Service Created Successfully";
            header("Location: $url");
            exit;
        } else {
            $error = true;
            echo "Error inserting data: " . $mysqli->error; // For debugging
        }
    }
}
require "./layouts/header.php";
?>

<div style="overflow-y: auto; height:80vh;">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between">
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Service/</span>Create</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "service_list.php") ?>" class="btn btn-dark">Back</a>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="POST">
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