<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$error = false;
$name = '';
$name_error = '';
$brand_id = '';

// Check if an ID is provided in the URL for editing
if (isset($_GET['id'])) {
    $brand_id = $mysqli->real_escape_string($_GET['id']);
    $brand_res = selectData('brand_name', $mysqli, '*', "WHERE `id`='$brand_id'");

    if ($brand_res && $brand_res->num_rows > 0) {
        $brand_data = $brand_res->fetch_assoc();
        $name = $brand_data['name'];
    } else {
        $url = $admin_base_url . "brand_name_list.php?error=Brand Name Not Found";
        header("Location: $url");
        exit;
    }
} else {
    $url = $admin_base_url . "brand_name_list.php?error=No Brand Name ID Provided";
    header("Location: $url");
    exit;
}

if (isset($_POST['form_sub']) && $_POST['form_sub'] == 1) {

    // Safely retrieve POST variables
    $name = $_POST['name'] ?? '';
    $brand_id = $_POST['brand_id'] ?? '';

    // Validation for Name
    if (empty($name)) {
        $error = true;
        $name_error = "Please fill Brand Name.";
    } elseif (strlen($name) < 3) {
        $error = true;
        $name_error = "Brand Name must be greater than 3 characters.";
    } elseif (strlen($name) > 100) {
        $error = true;
        $name_error = "Brand Name must be less than 100 characters.";
    } else {
        $check_name_sql = "SELECT id FROM brand_name WHERE name = '$name' AND id != '$brand_id'";
        $check_name_res = $mysqli->query($check_name_sql);
        if ($check_name_res && $check_name_res->num_rows > 0) {
            $error = true;
            $name_error = "Brand Name already exists.";
        }
    }

    if (!$error) {
        $data = [
            'name'       => $mysqli->real_escape_string($name),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Update data in the 'brand_name' table
        if (updateData('brand_name', $mysqli, $data, "`id`='$brand_id'")) {
            $url = $admin_base_url . "brand_name_list.php?success=Brand Name Updated Successfully";
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
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Brand Name/</span>Edit</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "brand_name_list.php") ?>" class="btn btn-dark">Back</a>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="POST">
                                <input type="hidden" name="brand_id" value="<?= htmlspecialchars($brand_id) ?>">
                                <div class="form-group mb-4">
                                    <label for="name">Brand Name</label>
                                    <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($name) ?>" />
                                    <?php if ($name_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($name_error) ?></span>
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