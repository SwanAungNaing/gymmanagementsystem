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
$equipment_id = '';

// Fetch brand names for dropdown
$brands_res = selectData('brand_name', $mysqli, 'id, name', '', 'ORDER BY name ASC');
$brands = [];
if ($brands_res && $brands_res->num_rows > 0) {
    while ($row = $brands_res->fetch_assoc()) {
        $brands[] = $row;
    }
}

// Fetch equipment types for dropdown
$types_res = selectData('equipment_type', $mysqli, 'id, type_name', '', 'ORDER BY type_name ASC');
$types = [];
if ($types_res && $types_res->num_rows > 0) {
    while ($row = $types_res->fetch_assoc()) {
        $types[] = $row;
    }
}

// Check if an ID is provided in the URL for editing
if (isset($_GET['id'])) {
    $equipment_id = $mysqli->real_escape_string($_GET['id']);
    $equipment_res = selectData('equipments', $mysqli, '*', "WHERE `id`='$equipment_id'");

    if ($equipment_res && $equipment_res->num_rows > 0) {
        $equipment_data = $equipment_res->fetch_assoc();
        $brand_name_id = $equipment_data['brand_name_id'];
        $equipment_type_id = $equipment_data['equipment_type_id'];
        $price = $equipment_data['price'];
        $quantity = $equipment_data['quantity'];
    } else {
        $url = $admin_base_url . "equipment_list.php?error=Equipment Not Found";
        header("Location: $url");
        exit;
    }
} else {
    $url = $admin_base_url . "equipment_list.php?error=No Equipment ID Provided";
    header("Location: $url");
    exit;
}

if (isset($_POST['form_sub']) && $_POST['form_sub'] == 1) {

    // Safely retrieve POST variables
    $brand_name_id = $_POST['brand_name_id'] ?? '';
    $equipment_type_id = $_POST['equipment_type_id'] ?? '';
    $price = $_POST['price'] ?? '';
    $quantity = $_POST['quantity'] ?? '';
    $equipment_id = $_POST['equipment_id'] ?? '';

    // Validation for Brand Name
    if (empty($brand_name_id)) {
        $error = true;
        $brand_name_id_error = "Please select a Brand Name.";
    }

    // Validation for Equipment Type
    if (empty($equipment_type_id)) {
        $error = true;
        $equipment_type_id_error = "Please select an Equipment Type.";
    }

    // Validation for Price
    if (empty($price)) {
        $error = true;
        $price_error = "Please fill Price.";
    } elseif (!is_numeric($price) || $price < 0) {
        $error = true;
        $price_error = "Price must be a non-negative number.";
    }

    // Validation for Quantity
    if (empty($quantity) && $quantity !== '0') {
        $error = true;
        $quantity_error = "Please fill Quantity.";
    } elseif (!is_numeric($quantity) || $quantity < 0 || strpos($quantity, '.') !== false) {
        $error = true;
        $quantity_error = "Quantity must be a non-negative integer.";
    }

    if (!$error) {
        $data = [
            'brand_name_id'   => $mysqli->real_escape_string($brand_name_id),
            'equipment_type_id' => $mysqli->real_escape_string($equipment_type_id),
            'price'           => $mysqli->real_escape_string($price),
            'quantity'        => $mysqli->real_escape_string($quantity),
            'updated_at'      => date('Y-m-d H:i:s')
        ];

        // Update data in the 'equipments' table
        if (updateData('equipments', $mysqli, $data, "`id`='$equipment_id'")) {
            $url = $admin_base_url . "equipment_list.php?success=Equipment Updated Successfully";
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
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Equipment/</span>Edit</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "equipment_list.php") ?>" class="btn btn-dark">Back</a>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="POST">
                                <input type="hidden" name="equipment_id" value="<?= htmlspecialchars($equipment_id) ?>">
                                <div class="form-group mb-4">
                                    <label for="brand_name_id">Brand Name</label>
                                    <select name="brand_name_id" id="brand_name_id" class="form-control">
                                        <option value="">Select Brand</option>
                                        <?php foreach ($brands as $brand) { ?>
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
                                        <?php foreach ($types as $type) { ?>
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
                                    <input type="number" step="0.01" name="price" id="price" class="form-control" value="<?= htmlspecialchars($price) ?>" />
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