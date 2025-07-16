<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$error = false;
$member_id = '';
$member_id_error = '';
$issue_date = '';
$issue_date_error = '';
$expiry_date = '';
$expiry_date_error = '';
$certificate_id = '';

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
    $certificate_id = $mysqli->real_escape_string($_GET['id']);
    $certificate_res = selectData('certificates', $mysqli, '*', "WHERE `id`='$certificate_id'");

    if ($certificate_res && $certificate_res->num_rows > 0) {
        $certificate_data = $certificate_res->fetch_assoc();
        $member_id = $certificate_data['member_id'];
        $issue_date = $certificate_data['issue_date'];
        $expiry_date = $certificate_data['expiry_date'];
    } else {
        $url = $admin_base_url . "certificate_list.php?error=Certificate Not Found";
        header("Location: $url");
        exit;
    }
} else {
    $url = $admin_base_url . "certificate_list.php?error=No Certificate ID Provided";
    header("Location: $url");
    exit;
}

if (isset($_POST['form_sub']) && $_POST['form_sub'] == 1) {

    // Safely retrieve POST variables
    $member_id = $_POST['member_id'] ?? '';
    $issue_date = $_POST['issue_date'] ?? '';
    $expiry_date = $_POST['expiry_date'] ?? '';
    $certificate_id = $_POST['certificate_id'] ?? '';

    // Validation for Member
    if (empty($member_id)) {
        $error = true;
        $member_id_error = "Please select a Member.";
    }

    // Validation for Issue Date
    if (empty($issue_date)) {
        $error = true;
        $issue_date_error = "Please fill Issue Date.";
    }

    // Validation for Expiry Date
    if (empty($expiry_date)) {
        $error = true;
        $expiry_date_error = "Please fill Expiry Date.";
    } elseif (!empty($issue_date) && strtotime($expiry_date) <= strtotime($issue_date)) {
        $error = true;
        $expiry_date_error = "Expiry Date must be after Issue Date.";
    }

    if (!$error) {
        $data = [
            'member_id'   => $mysqli->real_escape_string($member_id),
            'issue_date'  => $mysqli->real_escape_string($issue_date),
            'expiry_date' => $mysqli->real_escape_string($expiry_date),
            'updated_at'  => date('Y-m-d H:i:s')
        ];

        // Update data in the 'certificates' table
        if (updateData('certificates', $mysqli, $data, "`id`='$certificate_id'")) {
            $url = $admin_base_url . "certificate_list.php?success=Certificate Updated Successfully";
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
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Certificate/</span>Edit</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "certificate_list.php") ?>" class="btn btn-dark">Back</a>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="POST">
                                <input type="hidden" name="certificate_id" value="<?= htmlspecialchars($certificate_id) ?>">
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
                                    <label for="issue_date">Issue Date</label>
                                    <input type="date" name="issue_date" id="issue_date" class="form-control" value="<?= htmlspecialchars($issue_date) ?>" />
                                    <?php if ($issue_date_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($issue_date_error) ?></span>
                                    <?php } ?>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="expiry_date">Expiry Date</label>
                                    <input type="date" name="expiry_date" id="expiry_date" class="form-control" value="<?= htmlspecialchars($expiry_date) ?>" />
                                    <?php if ($expiry_date_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($expiry_date_error) ?></span>
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