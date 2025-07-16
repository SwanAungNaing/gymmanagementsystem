<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$error = false;
$member_id = '';
$member_id_error = '';
$weight = '';
$weight_error = '';
$date = '';
$date_error = '';
$member_weight_id = '';

// Fetch members for dropdown
$members_res = selectData('members', $mysqli, 'id, name', '', 'ORDER BY name ASC');
$members = [];
if ($members_res && $members_res->num_rows > 0) {
    while ($row = $members_res->fetch_assoc()) {
        $members[] = $row;
    }
}

// Check if an ID is provided in the URL for editing
if (isset($_GET['id'])) {
    $member_weight_id = $mysqli->real_escape_string($_GET['id']);
    $member_weight_res = selectData('member_weight', $mysqli, '*', "WHERE `id`='$member_weight_id'");

    if ($member_weight_res && $member_weight_res->num_rows > 0) {
        $member_weight_data = $member_weight_res->fetch_assoc();
        $member_id = $member_weight_data['member_id'];
        $weight = $member_weight_data['weight'];
        $date = $member_weight_data['date'];
    } else {
        $url = $admin_base_url . "member_weight_list.php?error=Member Weight Record Not Found";
        header("Location: $url");
        exit;
    }
} else {
    // No ID provided, redirect to list page with error
    $url = $admin_base_url . "member_weight_list.php?error=No Member Weight ID Provided";
    header("Location: $url");
    exit;
}

if (isset($_POST['form_sub']) && $_POST['form_sub'] == 1) {

    // Safely retrieve POST variables
    $member_id = $_POST['member_id'] ?? '';
    $weight = $_POST['weight'] ?? '';
    $date = $_POST['date'] ?? '';
    $member_weight_id = $_POST['member_weight_id'] ?? '';

    // Validation for Member
    if (empty($member_id)) {
        $error = true;
        $member_id_error = "Please select a Member.";
    }

    // Validation for Weight
    if (empty($weight)) {
        $error = true;
        $weight_error = "Please fill Weight.";
    } elseif (!is_numeric($weight) || $weight <= 0) {
        $error = true;
        $weight_error = "Weight must be a positive number.";
    }

    // Validation for Date
    if (empty($date)) {
        $error = true;
        $date_error = "Please select a Date.";
    }

    if (!$error) {
        $data = [
            'member_id'  => $mysqli->real_escape_string($member_id),
            'weight'     => $mysqli->real_escape_string($weight),
            'date'       => $mysqli->real_escape_string($date),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Update data in the 'member_weight' table
        if (updateData('member_weight', $mysqli, $data, "`id`='$member_weight_id'")) {
            $url = $admin_base_url . "member_weight_list.php?success=Member Weight Updated Successfully";
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
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Member Weight/</span>Edit</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "member_weight_list.php") ?>" class="btn btn-dark">Back</a>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="POST">
                                <input type="hidden" name="member_weight_id" value="<?= htmlspecialchars($member_weight_id) ?>">
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
                                    <label for="weight">Weight (kg)</label>
                                    <input type="number" step="0.01" name="weight" id="weight" class="form-control" value="<?= htmlspecialchars($weight) ?>" />
                                    <?php if ($weight_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($weight_error) ?></span>
                                    <?php } ?>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="date">Date</label>
                                    <input type="date" name="date" id="date" class="form-control" value="<?= htmlspecialchars($date) ?>" />
                                    <?php if ($date_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($date_error) ?></span>
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