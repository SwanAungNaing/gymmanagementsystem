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

if (isset($_POST['form_sub']) && $_POST['form_sub'] == 1) {

    // Name Validation
    $name = $mysqli->real_escape_string($_POST['name']);
    if (empty($name)) {
        $error = true;
        $name_error = "Please fill Member Name.";
    } else if (strlen($name) < 3) {
        $error = true;
        $name_error = "Member Name must be greater than 3 characters.";
    } else if (strlen($name) > 100) {
        $error = true;
        $name_error = "Member Name must be less than 100 characters.";
    }

    // Phone Validation
    $phone = $mysqli->real_escape_string($_POST['phone']);
    if (empty($phone)) {
        $error = true;
        $phone_error = "Please fill Member Phone.";
    } else if (strlen($phone) < 9) {
        $error = true;
        $phone_error = "Member Phone must be greater than 9 characters.";
    } else if (strlen($phone) > 20) {
        $error = true;
        $phone_error = "Member Phone must be less than 20 characters.";
    }

    // Address Validation
    $address = $mysqli->real_escape_string($_POST['address']);
    if (empty($address)) {
        $error = true;
        $address_error = "Please fill Member Address.";
    } else if (strlen($address) < 5) {
        $error = true;
        $address_error = "Member Address must be greater than 5 characters.";
    } else if (strlen($address) > 255) {
        $error = true;
        $address_error = "Member Address must be less than 255 characters.";
    }

    // Gender Validation
    $gender = isset($_POST['gender']) ? $mysqli->real_escape_string($_POST['gender']) : '';
    if (empty($gender) || !in_array($gender, ['male', 'female', 'others'])) {
        $error = true;
        $gender_error = "Please select a valid gender.";
    }

    // Original Weight Validation - MODIFIED LOGIC HERE
    $original_weight_input = trim($_POST['original_weight']);
    $original_weight = $mysqli->real_escape_string($original_weight_input);

    // Extract numeric part using a regular expression
    preg_match('/^\d+(\.\d+)?/', $original_weight_input, $matches);
    $numeric_weight = isset($matches[0]) ? (float)$matches[0] : 0;

    if (empty($original_weight_input)) {
        $error = true;
        $original_weight_error = "Please fill Original Weight.";
    } else if (!preg_match('/^\d+(\.\d+)?\s*(kg|lbs)?$/i', $original_weight_input)) {
        $error = true;
        $original_weight_error = "Invalid weight format. Use numbers, optionally followed by 'kg' or 'lbs'.";
    } else if ($numeric_weight <= 0) {
        $error = true;
        $original_weight_error = "Original Weight must be a positive number.";
    } else if (strlen($original_weight) > 20) {
        $error = true;
        $original_weight_error = "Original Weight must be less than 20 characters.";
    }


    if (!$error) {
        $data = [
            'name'            => $name,
            'phone'           => $phone,
            'address'         => $address,
            'gender'          => $gender,
            'original_weight' => $original_weight
        ];

        if (insertData('members', $mysqli, $data)) {
            $url = $admin_base_url . "member_list.php?success=Member Created Successfully";
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Member</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between">
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Member/</span>Create</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "member_list.php") ?>" class="btn btn-dark">Back</a>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="POST">
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
                                <div class="from-group mb-3">
                                    <label for="">Gender</label>
                                    <div class="mb-4">
                                        <label class="form-check-label me-2">
                                            Male <input type="radio" name="gender" class="form-check-input" value="male" <?= ($gender == 'male') ? 'checked' : '' ?> />
                                        </label>
                                        <label class="form-check-label me-2">
                                            Female <input type="radio" name="gender" class="form-check-input" value="female" <?= ($gender == 'female') ? 'checked' : '' ?> />
                                        </label>
                                        <label class="form-check-label me-2">
                                            Others <input type="radio" name="gender" class="form-check-input" value="others" <?= ($gender == 'others') ? 'checked' : '' ?> />
                                        </label>
                                    </div>
                                    <?php if ($gender_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($gender_error) ?></span>
                                    <?php } ?>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="original_weight">Original Weight</label>
                                    <input type="text" name="original_weight" id="original_weight" class="form-control" value="<?= htmlspecialchars($original_weight) ?>" placeholder="e.g., 70 kg or 70.5 lbs" />
                                    <?php if ($original_weight_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($original_weight_error) ?></span>
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