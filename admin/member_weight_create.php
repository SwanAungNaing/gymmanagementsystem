<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$error = false;
$member_id = '';
$member_id_error = '';
$date = date('Y-m-d');
$date_error = '';
$curr_weight = '';
$curr_weight_error = '';

// Fetch members for dropdown
$members_res = selectData('members', $mysqli, 'id, name'); //
$members = [];
if ($members_res && $members_res->num_rows > 0) {
    while ($row = $members_res->fetch_assoc()) {
        $members[] = $row;
    }
}

if (isset($_POST['form_sub']) && $_POST['form_sub'] == 1) {

    // Safely retrieve POST variables
    $member_id = $_POST['member_id'] ?? '';
    $date = $_POST['date'] ?? '';
    $curr_weight = $_POST['curr_weight'] ?? '';

    // Validation
    if (empty($member_id)) {
        $error = true;
        $member_id_error = "Please select a Member.";
    }
    if (empty($date)) {
        $error = true;
        $date_error = "Please select a Date.";
    }
    if (empty($curr_weight)) {
        $error = true;
        $curr_weight_error = "Please enter Current Weight.";
    } elseif (!is_numeric($curr_weight)) {
        $error = true;
        $curr_weight_error = "Current Weight must be a number.";
    } elseif ($curr_weight <= 0) {
        $error = true;
        $curr_weight_error = "Current Weight must be a positive number.";
    }

    if (!$error) {
        $data = [
            'member_id'   => $mysqli->real_escape_string($member_id),
            'date'        => $mysqli->real_escape_string($date),
            'curr_weight' => $mysqli->real_escape_string($curr_weight)
        ];

        if (insertData('member_weight', $mysqli, $data)) {
            $url = $admin_base_url . "member_weight_list.php?success=Member Weight Recorded Successfully";
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
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Member Weight/</span>Record</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "member_weight_list.php") ?>" class="btn btn-dark">Back</a>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body">
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
                                    <label for="date">Date</label>
                                    <input type="date" name="date" id="date" class="form-control" value="<?= htmlspecialchars($date) ?>" />
                                    <?php if ($date_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($date_error) ?></span>
                                    <?php } ?>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="curr_weight">Current Weight (kg)</label>
                                    <input type="number" step="0.01" name="curr_weight" id="curr_weight" class="form-control" value="<?= htmlspecialchars($curr_weight) ?>" />
                                    <?php if ($curr_weight_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($curr_weight_error) ?></span>
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