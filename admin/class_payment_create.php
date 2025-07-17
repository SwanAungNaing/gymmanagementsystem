<?php

use BcMath\Number;

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$error = false;
$class_member_id = '';
$class_member_id_error = '';
$total_amount = '';
$total_amount_error = '';
$order_date = '';
$order_date_error = '';
$status = '';
$status_error = '';

// Fetch class_members for the dropdown, joining with members and classes for display names
$class_members_sql = "SELECT cm.id, m.name AS member_name, c.batch_name AS class_batch_name
                     FROM class_members cm
                     LEFT JOIN members m ON cm.member_id = m.id
                     LEFT JOIN classes c ON cm.class_id = c.id
                     ORDER BY m.name, c.batch_name";
$class_members_res = $mysqli->query($class_members_sql);
$class_members = [];
if ($class_members_res && $class_members_res->num_rows > 0) {
    while ($row = $class_members_res->fetch_assoc()) {
        $class_members[] = $row;
    }
}

$payment_statuses = ['paid', 'pending'];

if (isset($_POST['form_sub']) && $_POST['form_sub'] == 1) {

    // Safely retrieve POST variables
    $class_member_id = $_POST['class_member_id'] ?? '';
    $total_amount =  intval($_POST['total_amount']) ?? '';
    $order_date = $_POST['order_date'] ?? '';
    $status = "pending";

    $oldPayment_sql = "SELECT * from class_payment 
    INNER JOIN class_members on class_members.id = class_payment.class_member_id 
    INNER JOIN classes on classes.id = class_members.class_id
    WHERE class_member_id='$class_member_id'";
    $oldPayment = $mysqli->query($oldPayment_sql);
    $total = 0;
    $class_price = 0;
    if ($oldPayment->num_rows > 0) {
        while ($row = $oldPayment->fetch_assoc()) {
            $total += $row['total_amount'];
            $class_price = $row['price'];
        }
        if ($total + $total_amount > $class_price) {
            $error = true;
            $class_member_id_error = "It's paid.";
        } elseif ($total + $total_amount == $class_price) {
            $status = "paid";
        }
    }


    // Validation
    if (empty($class_member_id)) {
        $error = true;
        $class_member_id_error = "Please select a Class Member.";
    }
    if (empty($total_amount)) {
        $error = true;
        $total_amount_error = "Please enter Total Amount.";
    } elseif (!is_numeric($total_amount) || $total_amount < 0) {
        $error = true;
        $total_amount_error = "Total Amount must be a positive number.";
    }
    if (empty($order_date)) {
        $error = true;
        $order_date_error = "Please select Order Date.";
    }

    if (!$error) {
        $data = [
            'class_member_id' => $mysqli->real_escape_string($class_member_id),
            'total_amount'    => $mysqli->real_escape_string($total_amount),
            'order_date'      => $mysqli->real_escape_string($order_date),
            'status'          => $mysqli->real_escape_string($status)
        ];

        if (insertData('class_payment', $mysqli, $data)) {
            $url = $admin_base_url . "class_payment_list.php?success=Class Payment Recorded Successfully";
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
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Class Payments/</span>Record</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "class_payment_list.php") ?>" class="btn btn-dark">Back</a>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="form-group mb-4">
                                    <label for="class_member_id">Class Member (Member Name - Class Batch)</label>
                                    <select name="class_member_id" id="class_member_id" class="form-control">
                                        <option value="">Select Class Member</option>
                                        <?php foreach ($class_members as $cm) { ?>
                                            <option value="<?= htmlspecialchars($cm['id']) ?>" <?= ($class_member_id == $cm['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cm['member_name'] . ' - ' . $cm['class_batch_name']) ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <?php if ($class_member_id_error) { ?>
                                        <span class="text-danger"><?= htmlspecialchars($class_member_id_error) ?></span>
                                    <?php } ?>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="total_amount">Total Amount</label>
                                    <input type="text" name="total_amount" id="total_amount" class="form-control" value="<?= htmlspecialchars($total_amount) ?>" />
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
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>