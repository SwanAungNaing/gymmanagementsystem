<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$error = false;
$member_id = '';
$member_id_error = '';
$class_id = '';
$class_id_error = '';

// Fetch all member for the dropdown
$classes_res = selectData('classes', $mysqli, 'id, batch_name');
$classes = [];
if ($classes_res && $classes_res->num_rows > 0) {
    while ($row = $classes_res->fetch_assoc()) {
        $classes[] = $row;
    }
}

// Fetch all class for the dropdown
$members_res = selectData('members', $mysqli, 'id, name');
$members = [];
if ($members_res && $members_res->num_rows > 0) {
    while ($row = $members_res->fetch_assoc()) {
        $members[] = $row;
    }
}

if (isset($_POST['form_sub']) && $_POST['form_sub'] == 1) {

    // Class ID Validation
    $class_id = $_POST['class_id'];
    if (empty($class_id)) {
        $error = true;
        $class_id_error = "Please select a Class.";
    }

    // Member ID Validation
    $member_id = $_POST['member_id'];
    if (empty($member_id)) {
        $error = true;
        $member_id = "Please select a Member.";
    }

    if (!$error) {
        $data = [
            'class_id'     => $mysqli->real_escape_string($class_id),
            'member_id'    => $mysqli->real_escape_string($member_id)
        ];

        if (insertData('class_members', $mysqli, $data)) {
            $url = $admin_base_url . "class_member_list.php?success=Class Member Created Successfully";
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

<div style="overflow-y: auto; height:80vh;">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between">
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Class Member/</span>Create</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "class_member_list.php") ?>" class="btn btn-dark">Back</a>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="form-group mb-4">
                                    <label for="member_id">Member</label>
                                    <select name="member_id" id="member_id" class="form-control">
                                        <option value="">Select Member</option>
                                        <?php foreach ($members as $member) { ?>
                                            <option value="<?= htmlspecialchars($member['id']) ?>" <?= ($member_id == $member['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($member['name']) ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <?php if ($member_id_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($member_id_error) ?></span>
                                    <?php } ?>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="brand_name_id">Class</label>
                                    <select name="class_id" id="class_id" class="form-control">
                                        <option value="">Select Class</option>
                                        <?php foreach ($classes as $class) { ?>
                                            <option value="<?= htmlspecialchars($class['id']) ?>" <?= ($class_id == $class['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($class['batch_name']) ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <?php if ($class_id_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($class_id_error) ?></span>
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
</div>
<?php
require "./layouts/footer.php";
?>