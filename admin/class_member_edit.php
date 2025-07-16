<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$error = false;
$member_id = '';
$member_id_error = '';
$class_id = '';
$class_id_error = '';
$start_date = '';
$start_date_error = '';
$end_date = '';
$end_date_error = '';
$class_member_pk_id = '';

// Fetch members for dropdown
$members_res = selectData('members', $mysqli, 'id, name', '', 'ORDER BY name ASC');
$members = [];
if ($members_res && $members_res->num_rows > 0) {
    while ($row = $members_res->fetch_assoc()) {
        $members[] = $row;
    }
}

// Fetch classes for dropdown, including service name for clarity
$classes_sql = "
    SELECT
        c.id,
        c.batch_name,
        s.name AS service_name
    FROM
        classes c
    JOIN
        services s ON c.service_id = s.id
    ORDER BY
        c.batch_name ASC
";
$classes_res = $mysqli->query($classes_sql);
$classes = [];
if ($classes_res && $classes_res->num_rows > 0) {
    while ($row = $classes_res->fetch_assoc()) {
        $classes[] = $row;
    }
}

// Check if an ID is provided in the URL for editing
if (isset($_GET['id'])) {
    $class_member_pk_id = $mysqli->real_escape_string($_GET['id']);
    $class_member_res = selectData('class_members', $mysqli, '*', "WHERE `id`='$class_member_pk_id'");

    if ($class_member_res && $class_member_res->num_rows > 0) {
        $class_member_data = $class_member_res->fetch_assoc();
        $member_id = $class_member_data['member_id'];
        $class_id = $class_member_data['class_id'];
        $start_date = $class_member_data['start_date'];
        $end_date = $class_member_data['end_date'];
    } else {
        $url = $admin_base_url . "class_member_list.php?error=Class Member Not Found";
        header("Location: $url");
        exit;
    }
} else {
    $url = $admin_base_url . "class_member_list.php?error=No Class Member ID Provided";
    header("Location: $url");
    exit;
}

if (isset($_POST['form_sub']) && $_POST['form_sub'] == 1) {

    // Safely retrieve POST variables
    $member_id = $_POST['member_id'] ?? '';
    $class_id = $_POST['class_id'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $class_member_pk_id = $_POST['class_member_pk_id'] ?? '';

    // Validation for Member
    if (empty($member_id)) {
        $error = true;
        $member_id_error = "Please select a Member.";
    }

    // Validation for Class
    if (empty($class_id)) {
        $error = true;
        $class_id_error = "Please select a Class.";
    }

    // Validation for Start Date
    if (empty($start_date)) {
        $error = true;
        $start_date_error = "Please fill Start Date.";
    }

    // Validation for End Date
    if (empty($end_date)) {
        $error = true;
        $end_date_error = "Please fill End Date.";
    } elseif (!empty($start_date) && strtotime($end_date) <= strtotime($start_date)) {
        $error = true;
        $end_date_error = "End Date must be after Start Date.";
    }

    if (!$error) {
        $data = [
            'member_id'   => $mysqli->real_escape_string($member_id),
            'class_id'    => $mysqli->real_escape_string($class_id),
            'start_date'  => $mysqli->real_escape_string($start_date),
            'end_date'    => $mysqli->real_escape_string($end_date),
            'updated_at'  => date('Y-m-d H:i:s')
        ];

        // Update data in the 'class_members' table
        if (updateData('class_members', $mysqli, $data, "`id`='$class_member_pk_id'")) {
            $url = $admin_base_url . "class_member_list.php?success=Class Member Updated Successfully";
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
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Class Member/</span>Edit</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "class_member_list.php") ?>" class="btn btn-dark">Back</a>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="POST">
                                <input type="hidden" name="class_member_pk_id" value="<?= htmlspecialchars($class_member_pk_id) ?>">
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
                                                <?= htmlspecialchars($class['batch_name'] . ' (' . $class['service_name'] . ')') ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <?php if ($class_id_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($class_id_error) ?></span>
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