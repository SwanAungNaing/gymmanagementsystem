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
$trainer_id = '';

// Check if an ID is provided in the URL for editing
if (isset($_GET['id'])) {
    $trainer_id = $mysqli->real_escape_string($_GET['id']);
    $trainer_res = selectData('trainers', $mysqli, '*', "WHERE `id`='$trainer_id'");

    if ($trainer_res && $trainer_res->num_rows > 0) {
        $trainer_data = $trainer_res->fetch_assoc();
        $name = $trainer_data['name'];
        $email = $trainer_data['email'];
        $phone = $trainer_data['phone'];
        $address = $trainer_data['address'];
        $gender = $trainer_data['gender'];
    } else {
        // Trainer not found, redirect to list page with error
        $url = $admin_base_url . "trainer_list.php?error=Trainer Not Found";
        header("Location: $url");
        exit;
    }
} else {
    // No ID provided, redirect to list page with error
    $url = $admin_base_url . "trainer_list.php?error=No Trainer ID Provided";
    header("Location: $url");
    exit;
}

if (isset($_POST['form_sub']) && $_POST['form_sub'] == 1) {

    // Safely retrieve POST variables
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $trainer_id = $_POST['trainer_id'] ?? '';

    // Validation for Name
    if (empty($name)) {
        $error = true;
        $name_error = "Please fill Trainer Name.";
    } elseif (strlen($name) < 3) {
        $error = true;
        $name_error = "Trainer Name must be greater than 3 characters.";
    } elseif (strlen($name) > 100) {
        $error = true;
        $name_error = "Trainer Name must be less than 100 characters.";
    }

    // Validation for Email
    if (empty($email)) {
        $error = true;
        $email_error = "Please fill Email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = true;
        $email_error = "Invalid Email format.";
    } else {
        // Check for duplicate email, excluding the current trainer's email
        $check_email_sql = "SELECT id FROM trainers WHERE email = '$email' AND id != '$trainer_id'";
        $check_email_res = $mysqli->query($check_email_sql);
        if ($check_email_res && $check_email_res->num_rows > 0) {
            $error = true;
            $email_error = "Email already exists.";
        }
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


    if (!$error) {
        $data = [
            'name'    => $mysqli->real_escape_string($name),
            'email'   => $mysqli->real_escape_string($email),
            'phone'   => $mysqli->real_escape_string($phone),
            'address' => $mysqli->real_escape_string($address),
            'gender'  => $mysqli->real_escape_string($gender),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Assuming 'trainers' is the correct table name from db.php
        if (updateData('trainers', $mysqli, $data, "`id`='$trainer_id'")) {
            $url = $admin_base_url . "trainer_list.php?success=Trainer Updated Successfully";
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
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Trainer/</span>Edit</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "trainer_list.php") ?>" class="btn btn-dark">Back</a>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="POST">
                                <input type="hidden" name="trainer_id" value="<?= htmlspecialchars($trainer_id) ?>">
                                <div class="form-group mb-4">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($name) ?>" />
                                    <?php if ($name_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($name_error) ?></span>
                                    <?php } ?>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($email) ?>" />
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