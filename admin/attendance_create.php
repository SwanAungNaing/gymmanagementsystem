<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$error = false;
$member_id = '';
$member_id_error = '';
$class_id = '';
$class_id_error = '';
$date = '';
$date_error = '';
$status = '';
$status_error = '';
$general_error = '';

// Fetch all members for the dropdown
$members_res = selectData('members', $mysqli, 'id, name');
$members = [];
if ($members_res && $members_res->num_rows > 0) {
    while ($row = $members_res->fetch_assoc()) {
        $members[] = $row;
    }
}

// Fetch all classes for the dropdown
$classes_res = selectData('classes', $mysqli, 'id, batch_name');
$classes = [];
if ($classes_res && $classes_res->num_rows > 0) {
    while ($row = $classes_res->fetch_assoc()) {
        $classes[] = $row;
    }
}

$attendance_statuses = ['present', 'absent'];

if (isset($_POST['form_sub']) && $_POST['form_sub'] == 1) {

    // Safely retrieve POST variables using null coalescing operator
    $member_id = $_POST['member_id'] ?? '';
    $class_id = $_POST['class_id'] ?? '';
    $date = $_POST['date'] ?? '';
    $status = $_POST['status'] ?? '';

    // Member ID Validation
    if (empty($member_id)) {
        $error = true;
        $member_id_error = "Please select a Member.";
    }

    // Class ID Validation
    if (empty($class_id)) {
        $error = true;
        $class_id_error = "Please select a Class.";
    }

    // Date Validation
    if (empty($date)) {
        $error = true;
        $date_error = "Please fill Date.";
    }

    // Status Validation
    if (empty($status) || !in_array($status, $attendance_statuses)) {
        $error = true;
        $status_error = "Please select a valid Status (Present or Absent).";
    }

    if (!$error) {

        // Find the class_member_id from the class_members table
        $class_member_where = "WHERE `class_id` = '" . $mysqli->real_escape_string($class_id) . "' AND `member_id` = '" . $mysqli->real_escape_string($member_id) . "'";
        $class_member_res = selectData('class_members', $mysqli, 'id', $class_member_where);

        if ($class_member_res && $class_member_res->num_rows > 0) {
            $class_member_row = $class_member_res->fetch_assoc();
            $class_member_id = $class_member_row['id'];

            $data = [
                'class_member_id' => $mysqli->real_escape_string($class_member_id),
                'date'            => $mysqli->real_escape_string($date),
                'status'          => $mysqli->real_escape_string($status)
            ];

            if (insertData('attendance', $mysqli, $data)) {
                $url = $admin_base_url . "attendance_list.php?success=Attendance Recorded Successfully";
                header("Location: $url");
                exit;
            } else {
                $error = true;
                $general_error = "Error inserting data: " . $mysqli->error;
            }
        } else {
            $error = true;
            $general_error = "No existing Class-Member assignment found for the selected Class and Member. Please assign the member to the class first.";
        }
    }
}
require "./layouts/header.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between">
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Attendance/</span>Record</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "attendance_list.php") ?>" class="btn btn-dark">Back</a>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <?php if ($general_error) { ?>
                                <div class="alert alert-danger" role="alert">
                                    <?= htmlspecialchars($general_error) ?>
                                </div>
                            <?php } ?>
                            <form action="" method="POST">
                                <div class="form-group mb-4">
                                    <label for="member_id">Member</label>
                                    <select name="member_id" id="member_id" class="form-control">
                                        <option value="">Select Member</option>
                                        <?php foreach ($members as $member) { ?>
                                            <option value="<?= htmlspecialchars($member['id']) ?>" <?= ($member_id == $member['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($member['name']) ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <?php if ($member_id_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($member_id_error) ?></span>
                                    <?php } ?>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="class_id">Class</label>
                                    <select name="class_id" id="class_id" class="form-control">
                                        <option value="">Select Class</option>
                                        <?php foreach ($classes as $class) { ?>
                                            <option value="<?= htmlspecialchars($class['id']) ?>" <?= ($class_id == $class['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($class['batch_name']) ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <?php if ($class_id_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($class_id_error) ?></span>
                                    <?php } ?>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="date">Date</label>
                                    <input type="date" name="date" id="date" class="form-control" value="<?= htmlspecialchars($date) ?>" />
                                    <?php if ($date_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($date_error) ?></span>
                                    <?php } ?>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Select Status</option>
                                        <?php foreach ($attendance_statuses as $s) { ?>
                                            <option value="<?= htmlspecialchars($s) ?>" <?= ($status == $s) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($s) ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <?php if ($status_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($status_error) ?></span>
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
    <?php
    require "./layouts/footer.php";
    ?>
</body>

</html>