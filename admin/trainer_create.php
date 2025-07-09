<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$error = false;
$name = '';
$name_error = '';
$email = '';
$email_error = '';
$phone = '';
$phone_error = '';
$address = '';
$address_error = '';
$gender = '';
$gender_error = '';

if (isset($_POST['form_sub']) && $_POST['form_sub'] == 1) {

    // Safely retrieve POST variables
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $gender = $_POST['gender'] ?? '';

    // Validation
    if (empty($name)) {
        $error = true;
        $name_error = "Please fill Trainer Name.";
    } else if (strlen($name) < 3) {
        $error = true;
        $name_error = "Trainer Name must be greater than 3 characters.";
    } else if (strlen($name) > 100) {
        $error = true;
        $name_error = "Trainer Name must be less than 100 characters.";
    }

    if (empty($email)) {
        $error = true;
        $email_error = "Please fill Trainer Email.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = true;
        $email_error = "Please enter a valid email address.";
    } else if (strlen($email) < 5) {
        $error = true;
        $email_error = "Trainer Email must be greater than 5 characters.";
    } else if (strlen($email) > 100) {
        $error = true;
        $email_error = "Trainer Email must be less than 100 characters.";
    }

    if (empty($phone)) {
        $error = true;
        $phone_error = "Please fill Trainer Phone.";
    } else if (strlen($phone) < 9) {
        $error = true;
        $phone_error = "Trainer Phone must be greater than 9 characters.";
    } else if (strlen($phone) > 20) {
        $error = true;
        $phone_error = "Trainer Phone must be less than 20 characters.";
    }

    if (empty($address)) {
        $error = true;
        $address_error = "Please fill Trainer Address.";
    } else if (strlen($address) < 5) {
        $error = true;
        $address_error = "Trainer Address must be greater than 5 characters.";
    } else if (strlen($address) > 255) {
        $error = true;
        $address_error = "Trainer Address must be less than 255 characters.";
    }

    if (empty($gender)) {
        $error = true;
        $gender_error = "Please select a Gender.";
    } elseif (!in_array($gender, ['male', 'female', 'others'])) { 
        $error = true;
        $gender_error = "Invalid gender selected.";
    }

    if (!$error) {
        $data = [
            'name'      => $mysqli->real_escape_string($name),
            'email'     => $mysqli->real_escape_string($email),
            'phone'     => $mysqli->real_escape_string($phone),
            'address'   => $mysqli->real_escape_string($address),
            'gender'    => $mysqli->real_escape_string($gender)
        ];

        if (insertData('trainers', $mysqli, $data)) {
            $url = $admin_base_url . "trainer_list.php?success=Trainer Created Successfully";
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
    <title>Create Trainer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between">
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Trainer/</span>Create</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "trainer_list.php") ?>" class="btn btn-dark">Back</a>
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
                                    <label for="email">Email</label>
                                    <input type="text" name="email" id="email" class="form-control" value="<?= htmlspecialchars($email) ?>" />
                                    <?php if ($email_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($email_error) ?></span>
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