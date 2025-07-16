<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$error = false;
$class_member_id = '';
$class_member_id_error = '';
$date = '';
$date_error = '';
$status = '';
$status_error = '';
$attendance_id = '';
$class_members_sql = "
    SELECT
        cm.id,
        m.name AS member_name,
        c.batch_name AS class_batch_name,
        s.name AS service_name
    FROM
        class_members cm
    JOIN
        members m ON cm.member_id = m.id
    JOIN
        classes c ON cm.class_id = c.id
    JOIN
        services s ON c.service_id = s.id
    ORDER BY
        m.name, c.batch_name
";
$class_members_res = $mysqli->query($class_members_sql);
$class_members = [];
if ($class_members_res && $class_members_res->num_rows > 0) {
    while ($row = $class_members_res->fetch_assoc()) {
        $class_members[] = $row;
    }
}

// Check if an ID is provided in the URL for editing
if (isset($_GET['id'])) {
    $attendance_id = $mysqli->real_escape_string($_GET['id']);
    $attendance_res = selectData('attendance', $mysqli, '*', "WHERE `id`='$attendance_id'");

    if ($attendance_res && $attendance_res->num_rows > 0) {
        $attendance_data = $attendance_res->fetch_assoc();
        $class_member_id = $attendance_data['class_member_id'];
        $date = $attendance_data['date'];
        $status = $attendance_data['status'];
    } else {
        $url = $admin_base_url . "attendance_list.php?error=Attendance Record Not Found";
        header("Location: $url");
        exit;
    }
} else {
    $url = $admin_base_url . "attendance_list.php?error=No Attendance Record ID Provided";
    header("Location: $url");
    exit;
}

if (isset($_POST['form_sub']) && $_POST['form_sub'] == 1) {

    // Safely retrieve POST variables
    $class_member_id = $_POST['class_member_id'] ?? '';
    $date = $_POST['date'] ?? '';
    $status = $_POST['status'] ?? '';
    $attendance_id = $_POST['attendance_id'] ?? '';

    // Validation for Class Member
    if (empty($class_member_id)) {
        $error = true;
        $class_member_id_error = "Please select a Class Member.";
    }

    // Validation for Date
    if (empty($date)) {
        $error = true;
        $date_error = "Please select a Date.";
    }

    // Validation for Status
    if (empty($status) || !in_array($status, ['present', 'absent'])) {
        $error = true;
        $status_error = "Please select a valid Status (Present/Absent).";
    }

    if (!$error) {
        $data = [
            'class_member_id' => $mysqli->real_escape_string($class_member_id),
            'date'            => $mysqli->real_escape_string($date),
            'status'          => $mysqli->real_escape_string($status),
            'updated_at'      => date('Y-m-d H:i:s')
        ];

        // Update data in the 'attendance' table
        if (updateData('attendance', $mysqli, $data, "`id`='$attendance_id'")) {
            $url = $admin_base_url . "attendance_list.php?success=Attendance Record Updated Successfully";
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
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Attendance/</span>Edit</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "attendance_list.php") ?>" class="btn btn-dark">Back</a>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="POST">
                                <input type="hidden" name="attendance_id" value="<?= htmlspecialchars($attendance_id) ?>">
                                <div class="form-group mb-4">
                                    <label for="class_member_id">Class Member</label>
                                    <select name="class_member_id" id="class_member_id" class="form-control">
                                        <option value="">Select Class Member</option>
                                        <?php foreach ($class_members as $cm) { ?>
                                            <option value="<?= htmlspecialchars($cm['id']) ?>" <?= ($class_member_id == $cm['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cm['member_name'] . ' (' . $cm['class_batch_name'] . ' - ' . $cm['service_name'] . ')') ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <?php if ($class_member_id_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($class_member_id_error) ?></span>
                                    <?php } ?>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="date">Date</label>
                                    <input type="date" name="date" id="date" class="form-control" value="<?= htmlspecialchars($date) ?>" />
                                    <?php if ($date_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($date_error) ?></span>
                                    <?php } ?>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="">Status</label>
                                    <div class="mb-4">
                                        <label class="form-check-label me-2">
                                            Present <input type="radio" name="status" class="form-check-input" value="present" <?= ($status === 'present') ? 'checked' : '' ?> />
                                        </label>
                                        <label class="form-check-label me-2">
                                            Absent <input type="radio" name="status" class="form-check-input" value="absent" <?= ($status === 'absent') ? 'checked' : '' ?> />
                                        </label>
                                    </div>
                                    <?php if ($status_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($status_error) ?></span>
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