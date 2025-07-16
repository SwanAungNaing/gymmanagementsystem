<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$error = false;
$type_name = '';
$type_name_error = '';
$equipment_type_id = '';

// Check if an ID is provided in the URL for editing
if (isset($_GET['id'])) {
    $equipment_type_id = $mysqli->real_escape_string($_GET['id']);
    $type_res = selectData('equipment_type', $mysqli, '*', "WHERE `id`='$equipment_type_id'");

    if ($type_res && $type_res->num_rows > 0) {
        $type_data = $type_res->fetch_assoc();
        $type_name = $type_data['type_name'];
    } else {
        $url = $admin_base_url . "equipment_type_list.php?error=Equipment Type Not Found";
        header("Location: $url");
        exit;
    }
} else {
    $url = $admin_base_url . "equipment_type_list.php?error=No Equipment Type ID Provided";
    header("Location: $url");
    exit;
}

if (isset($_POST['form_sub']) && $_POST['form_sub'] == 1) {

    // Safely retrieve POST variables
    $type_name = $_POST['type_name'] ?? '';
    $equipment_type_id = $_POST['equipment_type_id'] ?? '';

    // Validation for Type Name
    if (empty($type_name)) {
        $error = true;
        $type_name_error = "Please fill Equipment Type Name.";
    } elseif (strlen($type_name) < 3) {
        $error = true;
        $type_name_error = "Equipment Type Name must be greater than 3 characters.";
    } elseif (strlen($type_name) > 100) {
        $error = true;
        $type_name_error = "Equipment Type Name must be less than 100 characters.";
    } else {
        $check_name_sql = "SELECT id FROM equipment_type WHERE type_name = '$type_name' AND id != '$equipment_type_id'";
        $check_name_res = $mysqli->query($check_name_sql);
        if ($check_name_res && $check_name_res->num_rows > 0) {
            $error = true;
            $type_name_error = "Equipment Type Name already exists.";
        }
    }

    if (!$error) {
        $data = [
            'type_name'  => $mysqli->real_escape_string($type_name),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Update data in the 'equipment_type' table
        if (updateData('equipment_type', $mysqli, $data, "`id`='$equipment_type_id'")) {
            $url = $admin_base_url . "equipment_type_list.php?success=Equipment Type Updated Successfully";
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
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Equipment Type/</span>Edit</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "equipment_type_list.php") ?>" class="btn btn-dark">Back</a>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="POST">
                                <input type="hidden" name="equipment_type_id" value="<?= htmlspecialchars($equipment_type_id) ?>">
                                <div class="form-group mb-4">
                                    <label for="type_name">Equipment Type Name</label>
                                    <input type="text" name="type_name" id="type_name" class="form-control" value="<?= htmlspecialchars($type_name) ?>" />
                                    <?php if ($type_name_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($type_name_error) ?></span>
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