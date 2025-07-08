<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$error = false;
$brand_name_id = '';
$brand_name_id_error = '';
$equipment_type_id = '';
$equipment_type_id_error = '';
$price = '';
$price_error = '';
$quantity = '';
$quantity_error = '';

// Fetch all brand names for the dropdown
$brands_res = selectData('brand_name', $mysqli, 'id, name');
$brand_names = [];
if ($brands_res && $brands_res->num_rows > 0) {
    while ($row = $brands_res->fetch_assoc()) {
        $brand_names[] = $row;
    }
}

// Fetch all equipment types for the dropdown
$types_res = selectData('equipment_type', $mysqli, 'id, type_name');
$equipment_types = [];
if ($types_res && $types_res->num_rows > 0) {
    while ($row = $types_res->fetch_assoc()) {
        $equipment_types[] = $row;
    }
}

if (isset($_POST['form_sub']) && $_POST['form_sub'] == 1) {

    // Brand Name ID Validation
    $brand_name_id = $_POST['brand_name_id'];
    if (empty($brand_name_id)) {
        $error = true;
        $brand_name_id_error = "Please select a Brand Name.";
    }

    // Equipment Type ID Validation
    $equipment_type_id = $_POST['equipment_type_id'];
    if (empty($equipment_type_id)) {
        $error = true;
        $equipment_type_id_error = "Please select an Equipment Type.";
    }

    // Price Validation
    $price = $mysqli->real_escape_string($_POST['price']);
    if (empty($price)) {
        $error = true;
        $price_error = "Please fill Price.";
    } else if (!is_numeric($price) || $price < 0) {
        $error = true;
        $price_error = "Price must be a positive number.";
    } else if (strlen($price) > 50) {
        $error = true;
        $price_error = "Price must be less than 50 characters.";
    }

    // Quantity Validation
    $quantity = $_POST['quantity'];
    if (empty($quantity)) {
        $error = true;
        $quantity_error = "Please fill Quantity.";
    } else if (!filter_var($quantity, FILTER_VALIDATE_INT) || $quantity < 0) {
        $error = true;
        $quantity_error = "Quantity must be a non-negative integer.";
    }

    if (!$error) {
        $data = [
            'brand_name_id'     => $mysqli->real_escape_string($brand_name_id),
            'equipment_type_id' => $mysqli->real_escape_string($equipment_type_id),
            'price'             => $mysqli->real_escape_string($price),
            'quantity'          => $mysqli->real_escape_string($quantity)
        ];

        if (insertData('equipments', $mysqli, $data)) {
            $url = $admin_base_url . "equipment_list.php?success=Equipment Created Successfully";
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
    <title>Create Equipment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between">
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Equipment/</span>Create</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "equipment_list.php") ?>" class="btn btn-dark">Back</a>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="form-group mb-4">
                                    <label for="brand_name_id">Brand Name</label>
                                    <select name="brand_name_id" id="brand_name_id" class="form-control">
                                        <option value="">Select Brand</option>
                                        <?php foreach ($brand_names as $brand) { ?>
                                            <option value="<?= htmlspecialchars($brand['id']) ?>" <?= ($brand_name_id == $brand['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($brand['name']) ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <?php if ($brand_name_id_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($brand_name_id_error) ?></span>
                                    <?php } ?>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="equipment_type_id">Equipment Type</label>
                                    <select name="equipment_type_id" id="equipment_type_id" class="form-control">
                                        <option value="">Select Type</option>
                                        <?php foreach ($equipment_types as $type) { ?>
                                            <option value="<?= htmlspecialchars($type['id']) ?>" <?= ($equipment_type_id == $type['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($type['type_name']) ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <?php if ($equipment_type_id_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($equipment_type_id_error) ?></span>
                                    <?php } ?>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="price">Price</label>
                                    <input type="text" name="price" id="price" class="form-control" value="<?= htmlspecialchars($price) ?>" />
                                    <?php if ($price_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($price_error) ?></span>
                                    <?php } ?>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="quantity">Quantity</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control" value="<?= htmlspecialchars($quantity) ?>" />
                                    <?php if ($quantity_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($quantity_error) ?></span>
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