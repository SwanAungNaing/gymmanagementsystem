<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$error = false;
$equipment_id = '';
$equipment_id_error = '';
$member_id = '';
$member_id_error = '';
$quantity = '';
$quantity_error = '';
$total_amount = '';
$total_amount_error = '';
$order_date = '';
$order_date_error = '';

$equipments_sql = "SELECT e.id, e.price, bn.name AS brand_name, et.type_name AS equipment_type_name
                     FROM equipments e
                     LEFT JOIN brand_name bn ON e.brand_name_id = bn.id
                     LEFT JOIN equipment_type et ON e.equipment_type_id = et.id
                     WHERE e.quantity > 0
                     ORDER BY bn.name, et.type_name";
$equipments_res = $mysqli->query($equipments_sql);
$equipments = [];
if ($equipments_res && $equipments_res->num_rows > 0) {
    while ($row = $equipments_res->fetch_assoc()) {
        $equipments[] = $row;
    }
}

// Fetch all members for the dropdown
$members_res = selectData('members', $mysqli, 'id, name');
$members = [];
if ($members_res && $members_res->num_rows > 0) {
    while ($row = $members_res->fetch_assoc()) {
        $members[] = $row;
    }
}

if (isset($_POST['form_sub']) && $_POST['form_sub'] == 1) {

    // Safely retrieve POST variables
    $equipment_id = $_POST['equipment_id'] ?? '';
    $member_id = $_POST['member_id'] ?? '';
    $quantity = $_POST['quantity'] ?? '';
    $order_date = $_POST['order_date'] ?? '';
    $total_amount = $_POST['total_amount'] ?? '';

    $equipment_data = selectData('equipments', $mysqli, '*', 'where id =' . $equipment_id);
    $data = $equipment_data->fetch_assoc();
    $instock = $data['quantity'];
    // var_dump($instock);
    // die();
    // Validation
    if (empty($equipment_id)) {
        $error = true;
        $equipment_id_error = "Please select an Equipment.";
    }
    if (empty($member_id)) {
        $error = true;
        $member_id_error = "Please select a Member.";
    }

    if ($quantity > $instock) {
        $error = true;
        $quantity_error = "Please enter less than of equal of instock $instock";
    }

    if (empty($quantity)) {
        $error = true;
        $quantity_error = "Please enter Quantity.";
    } elseif (!is_numeric($quantity) || $quantity <= 0) {
        $error = true;
        $quantity_error = "Quantity must be a positive number.";
    }
    if (empty($order_date)) {
        $error = true;
        $order_date_error = "Please select Order Date.";
    }
    // Validate total_amount from hidden field (calculated client-side, but still good to check if it's numeric)
    if (empty($total_amount) || !is_numeric($total_amount) || $total_amount < 0) {
        $error = true;
        $total_amount_error = "Invalid Total Amount. Please ensure equipment and quantity are selected.";
    }

    if (!$error) {
        $data = [
            'equipment_id' => $mysqli->real_escape_string($equipment_id),
            'member_id'    => $mysqli->real_escape_string($member_id),
            'quantity'     => $mysqli->real_escape_string($quantity),
            'total_amount' => $mysqli->real_escape_string($total_amount),
            'order_date'   => $mysqli->real_escape_string($order_date)
        ];

        if (insertData('esale_order', $mysqli, $data)) {
            $current_stock = $instock - $quantity;
            $dataUpdate = [
                'quantity' => $mysqli->real_escape_string($current_stock),
            ];
            $update_instock = updateData("equipments", $mysqli, $dataUpdate, "`id`='$equipment_id'");
            if ($update_instock) {
                $url = $admin_base_url . "e_sale_order_list.php?success=Equipment Sale Order Recorded Successfully";
                header("Location: $url");
                exit;
            }
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
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Equipment Sales/</span>Record</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "e_sale_order_list.php") ?>" class="btn btn-dark">Back</a>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="form-group mb-4">
                                    <label for="equipment_id">Equipment</label>
                                    <select name="equipment_id" id="equipment_id" class="form-control">
                                        <option value="">Select Equipment</option>
                                        <?php foreach ($equipments as $equipment) { ?>
                                            <option value="<?= htmlspecialchars($equipment['id']) ?>"
                                                data-price="<?= htmlspecialchars($equipment['price']) ?>"
                                                <?= ($equipment_id == $equipment['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($equipment['brand_name'] . ' - ' . $equipment['equipment_type_name'] . ' (Price: ' . $equipment['price'] . ')') ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <?php if ($equipment_id_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($equipment_id_error) ?></span>
                                    <?php } ?>
                                </div>
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
                                    <label for="quantity">Quantity</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control" value="<?= htmlspecialchars($quantity) ?>" min="1" />
                                    <?php if ($quantity_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($quantity_error) ?></span>
                                    <?php } ?>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="total_amount_display">Total Amount</label>
                                    <input type="text" id="total_amount_display" class="form-control" value="<?= htmlspecialchars($total_amount) ?>" readonly />
                                    <input type="hidden" name="total_amount" id="total_amount_hidden" value="<?= htmlspecialchars($total_amount) ?>" />
                                    <?php if ($total_amount_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($total_amount_error) ?></span>
                                    <?php } ?>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="order_date">Order Date</label>
                                    <input type="date" name="order_date" id="order_date" class="form-control" value="<?= htmlspecialchars($order_date) ?>" />
                                    <?php if ($order_date_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($order_date_error) ?></span>
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
<!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> -->
<script>
    $(document).ready(function() {
        function calculateTotalAmount() {
            const selectedOption = $('#equipment_id option:selected');
            const price = parseFloat(selectedOption.data('price')) || 0;
            const quantity = parseInt($('#quantity').val()) || 0;

            const total = price * quantity;

            $('#total_amount_display').val(total.toFixed(2));
            $('#total_amount_hidden').val(total.toFixed(2));
        }

        // Attach change and keyup listeners
        $('#equipment_id').change(calculateTotalAmount);
        $('#quantity').on('input', calculateTotalAmount);
        calculateTotalAmount();
    });
</script>