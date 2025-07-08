<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$error = false;
$name = '';
$name_error = '';

if (isset($_POST['form_sub']) && $_POST['form_sub'] == 1) {

    // Name Validation
    $name = $mysqli->real_escape_string($_POST['name']);
    if (empty($name)) {
        $error = true;
        $name_error = "Please fill Brand Name.";
    } else if (strlen($name) < 3) {
        $error = true;
        $name_error = "Brand Name must be greater than 3 characters.";
    } else if (strlen($name) > 100) {
        $error = true;
        $name_error = "Brand Name must be less than 100 characters.";
    }

    if (!$error) {
        $data = [
            'name' => $mysqli->real_escape_string($name)
        ];

        if (insertData('brand_name', $mysqli, $data)) {
            $url = $admin_base_url . "brand_name_list.php?success=Brand Name Created Successfully";
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
    <title>Create Brand Name</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between">
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Equipment/Brand Name/</span>Create</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "brand_name_list.php") ?>" class="btn btn-dark">Back</a>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="form-group mb-4">
                                    <label for="name">Brand Name</label>
                                    <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($name) ?>" />
                                    <?php if ($name_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($name_error) ?></span>
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