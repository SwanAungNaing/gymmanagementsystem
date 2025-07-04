<?php
require("./layouts/sidebar.php");
require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$error = false;
$certificate_name = '';
$certificate_name_error = '';
$trainer_id = '';
$trainer_id_error = '';
$img_path = ''; // This will store the uploaded file path
$img_path_error = '';

// Fetch all trainers to populate the dropdown
$trainers = [];
$trainer_res = selectData('trainers', $mysqli, 'id, name', '', 'ORDER BY name ASC');
if ($trainer_res && $trainer_res->num_rows > 0) {
    while ($row = $trainer_res->fetch_assoc()) {
        $trainers[] = $row;
    }
}

if (isset($_POST['form_sub']) && $_POST['form_sub'] == 1) {

    // Certificate Name Validation
    $certificate_name = $mysqli->real_escape_string($_POST['certificate_name']);
    if (empty($certificate_name)) {
        $error = true;
        $certificate_name_error = "Please fill Certificate Name.";
    } else if (strlen($certificate_name) < 3) {
        $error = true;
        $certificate_name_error = "Certificate Name must be greater than 3 characters.";
    } else if (strlen($certificate_name) > 100) {
        $error = true;
        $certificate_name_error = "Certificate Name must be less than 100 characters.";
    }

    // Trainer ID Validation
    $trainer_id = $mysqli->real_escape_string($_POST['trainer_id']);
    if (empty($trainer_id)) {
        $error = true;
        $trainer_id_error = "Please select a Trainer.";
    } else if (!is_numeric($trainer_id) || $trainer_id <= 0) {
        $error = true;
        $trainer_id_error = "Invalid Trainer selection.";
    }

    // Image Upload Handling
    if (isset($_FILES['certificate_image']) && $_FILES['certificate_image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "../uploads/certificates/"; // Directory to save uploaded images
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Create directory if it doesn't exist
        }

        $file_name = basename($_FILES["certificate_image"]["name"]);
        $target_file = $target_dir . time() . '_' . $file_name; // Unique file name
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Allow certain file formats
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");
        if (!in_array($imageFileType, $allowed_extensions)) {
            $error = true;
            $img_path_error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }

        // Check file size (e.g., 5MB limit)
        if ($_FILES["certificate_image"]["size"] > 5000000) {
            $error = true;
            $img_path_error = "Sorry, your file is too large (max 5MB).";
        }

        // Check if $error is still false before moving the file
        if (!$error) {
            if (move_uploaded_file($_FILES["certificate_image"]["tmp_name"], $target_file)) {
                $img_path = str_replace('../', '', $target_file); // Store relative path for database
            } else {
                $error = true;
                $img_path_error = "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        $error = true;
        $img_path_error = "Please upload a certificate image.";
    }

    if (!$error) { // If no validation or upload errors
        $data = [
            'name'       => $certificate_name,
            'trainer_id' => $trainer_id,
            'img_path'   => $img_path // Store the relative path
        ];

        if (insertData('certificates', $mysqli, $data)) {
            $url = $admin_base_url . "certificate_list.php?success=Certificate Created Successfully";
            header("Location: $url");
            exit;
        } else {
            $error = true;
            echo "Error inserting data: " . $mysqli->error; // For debugging
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
    <title>Create Certificate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between">
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Certificate/</span>Create</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "certificate_list.php") ?>" class="btn btn-dark">Back</a>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class="form-group mb-4">
                                    <label for="certificate_name">Certificate Name</label>
                                    <input type="text" name="certificate_name" id="certificate_name" class="form-control" value="<?= htmlspecialchars($certificate_name) ?>" />
                                    <?php if ($certificate_name_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($certificate_name_error) ?></span>
                                    <?php } ?>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="trainer_id">Select Trainer</label>
                                    <select name="trainer_id" id="trainer_id" class="form-control">
                                        <option value="">-- Select Trainer --</option>
                                        <?php foreach ($trainers as $trainer) { ?>
                                            <option value="<?= htmlspecialchars($trainer['id']) ?>" <?= ($trainer_id == $trainer['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($trainer['name']) ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <?php if ($trainer_id_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($trainer_id_error) ?></span>
                                    <?php } ?>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="certificate_image">Certificate Image</label>
                                    <input type="file" name="certificate_image" id="certificate_image" class="form-control" />
                                    <?php if ($img_path_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($img_path_error) ?></span>
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