<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$error = false;
$name = '';
$name_error = '';
$phone = '';
$phone_error = '';
$address = '';
$address_error = '';
$gender = '';
$gender_error = '';
$original_weight = '';
$original_weight_error = '';
$member_id = '';

// Check if an ID is provided in the URL for editing
if (isset($_GET['id'])) {
    $member_id = $mysqli->real_escape_string($_GET['id']);
    $member_res = selectData('members', $mysqli, '*', "WHERE `id`='$member_id'");

    if ($member_res && $member_res->num_rows > 0) {
        $member_data = $member_res->fetch_assoc();
        $name = $member_data['name'];
        $phone = $member_data['phone'];
        $address = $member_data['address'];
        $gender = $member_data['gender'];
        $original_weight = $member_data['original_weight'];
    } else {
        $url = $admin_base_url . "member_list.php?error=Member Not Found";
        header("Location: $url");
        exit;
    }
} else {
    $url = $admin_base_url . "member_list.php?error=No Member ID Provided";
    header("Location: $url");
    exit;
}

if (isset($_POST['form_sub']) && $_POST['form_sub'] == 1) {

    // Safely retrieve POST variables
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $original_weight = $_POST['original_weight'] ?? '';
    $member_id = $_POST['member_id'] ?? '';

    // Validation for Name
    if (empty($name)) {
        $error = true;
        $name_error = "Please fill Member Name.";
    } elseif (strlen($name) < 3) {
        $error = true;
        $name_error = "Member Name must be greater than 3 characters.";
    } elseif (strlen($name) > 100) {
        $error = true;
        $name_error = "Member Name must be less than 100 characters.";
    }

    // Validation for Phone
    if (empty($phone)) {
        $error = true;
        $phone_error = "Please fill Phone Number.";
    } elseif (strlen($phone) < 9) {
        $error = true;
        $phone_error = "Phone Number must be greater than 9 characters.";
    } elseif (strlen($phone) > 20) {
        $error = true;
        $phone_error = "Phone Number must be less than 20 characters.";
    }

    // Validation for Address (optional, adjust as needed)
    if (empty($address)) {
        $error = true;
        $address_error = "Please fill Address.";
    }

    // Validation for Gender
    if (empty($gender) || !in_array($gender, ['male', 'female', 'others'])) {
        $error = true;
        $gender_error = "Please select a Gender.";
    }

    // Validation for Original Weight
    if (empty($original_weight)) {
        $error = true;
        $original_weight_error = "Please fill Original Weight.";
    } elseif (!is_numeric($original_weight)) {
        $error = true;
        $original_weight_error = "Original Weight must be a number.";
    } elseif ($original_weight <= 0) {
        $error = true;
        $original_weight_error = "Original Weight must be a positive number.";
    }

    if (!$error) {
        $data = [
            'name'            => $mysqli->real_escape_string($name),
            'phone'           => $mysqli->real_escape_string($phone),
            'address'         => $mysqli->real_escape_string($address),
            'gender'          => $mysqli->real_escape_string($gender),
            'original_weight' => $mysqli->real_escape_string($original_weight),
            'updated_at'      => date('Y-m-d H:i:s')
        ];

        // Update data in the 'members' table
        if (updateData('members', $mysqli, $data, "`id`='$member_id'")) {
            $url = $admin_base_url . "member_list.php?success=Member Updated Successfully";
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
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Member/</span>Edit</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "member_list.php") ?>" class="btn btn-dark">Back</a>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="POST">
                                <input type="hidden" name="member_id" value="<?= htmlspecialchars($member_id) ?>">
                                <div class="form-group mb-4">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($name) ?>" />
                                    <?php if ($name_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($name_error) ?></span>
                                    <?php } ?>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="phone">Phone</label>
                                    <input type="text" name="phone" id="phone" class="form-control" value="<?= htmlspecialchars($phone) ?>" />
                                    <?php if ($phone_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($phone_error) ?></span>
                                    <?php } ?>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="address">Address</label>
                                    <input type="text" name="address" id="address" class="form-control" value="<?= htmlspecialchars($address) ?>" />
                                    <?php if ($address_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($address_error) ?></span>
                                    <?php } ?>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="">Gender</label>
                                    <div class="mb-4">
                                        <label class="form-check-label me-2">
                                            Male <input type="radio" name="gender" class="form-check-input" value="male" <?= ($gender === 'male') ? 'checked' : '' ?> />
                                        </label>
                                        <label class="form-check-label me-2">
                                            Female <input type="radio" name="gender" class="form-check-input" value="female" <?= ($gender === 'female') ? 'checked' : '' ?> />
                                        </label>
                                        <label class="form-check-label me-2">
                                            Others <input type="radio" name="gender" class="form-check-input" value="others" <?= ($gender === 'others') ? 'checked' : '' ?> />
                                        </label>
                                    </div>
                                    <?php if ($gender_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($gender_error) ?></span>
                                    <?php } ?>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="original_weight">Original Weight (kg)</label>
                                    <input type="number" step="0.01" name="original_weight" id="original_weight" class="form-control" value="<?= htmlspecialchars($original_weight) ?>" />
                                    <?php if ($original_weight_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($original_weight_error) ?></span>
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