<?php

require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

// Handle delete operation
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $res = deleteData('esale_order', $mysqli, "`id`='$delete_id'");
    if ($res) {
        $url = $admin_base_url . "e_sale_order_list.php?success=Equipment Sale Order Deleted Successfully"; //
        header("Location: $url");
        exit;
    } else {
        $url = $admin_base_url . "e_sale_order_list.php?error=Failed to Delete Equipment Sale Order"; //
        header("Location: $url");
        exit;
    }
}

$success_msg = "";
$error_msg = "";
if (isset($_GET['success'])) {
    $success_msg = $_GET['success'];
}
if (isset($_GET['error'])) {
    $error_msg = $_GET['error'];
}

// Fetch equipment sale orders with related details
$esale_orders_sql = "
    SELECT
        eso.id,
        eso.quantity,
        eso.total_amount,
        eso.order_date,
        eso.created_at,
        eso.updated_at,
        e.price AS equipment_price,
        bn.name AS brand_name,
        et.type_name AS equipment_type_name,
        m.name AS member_name
    FROM
        esale_order eso
    JOIN
        equipments e ON eso.equipment_id = e.id
    JOIN
        brand_name bn ON e.brand_name_id = bn.id
    JOIN
        equipment_type et ON e.equipment_type_id = et.id
    JOIN
        members m ON eso.member_id = m.id
    ORDER BY
        eso.id DESC
";
$esale_orders_res = $mysqli->query($esale_orders_sql);
$esale_orders = [];
if ($esale_orders_res && $esale_orders_res->num_rows > 0) {
    while ($row = $esale_orders_res->fetch_assoc()) {
        $esale_orders[] = $row;
    }
}

require "./layouts/header.php";
?>

<div style="overflow-y: auto; height:80vh;">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between">
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Equipment Sales/</span>List</h4>
                <div class="">
                    <a href="<?= htmlspecialchars($admin_base_url . "e_sale_order_create.php") ?>" class="btn btn-primary">Create New Sale Order</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-7 offset-md-5 col-12">
                    <?php if ($success_msg) { ?>
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <?= htmlspecialchars($success_msg) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php } ?>
                    <?php if ($error_msg) { ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error_msg) ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Equipment</th>
                                    <th>Brand</th>
                                    <th>Type</th>
                                    <th>Member</th>
                                    <th>Quantity</th>
                                    <th>Total Amount</th>
                                    <th>Order Date</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($esale_orders)) {
                                    foreach ($esale_orders as $row) { ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['id']) ?></td>
                                            <td><?= htmlspecialchars($row['equipment_type_name']) ?></td>
                                            <td><?= htmlspecialchars($row['brand_name']) ?></td>
                                            <td><?= htmlspecialchars($row['equipment_type_name']) ?></td>
                                            <td><?= htmlspecialchars($row['member_name']) ?></td>
                                            <td><?= htmlspecialchars($row['quantity']) ?></td>
                                            <td><?= htmlspecialchars($row['total_amount']) ?></td>
                                            <td><?= htmlspecialchars(date("Y/F/d", strtotime($row['order_date']))) ?></td>
                                            <td><?= htmlspecialchars(date("Y/F/d h:i:s A", strtotime($row['created_at']))) ?></td>
                                            <td><?= htmlspecialchars(date("Y/m/d h:i:s A", strtotime($row['updated_at']))) ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger delete_btn" data-id="<?= htmlspecialchars($row['id']) ?>">Delete</button>
                                            </td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="11" class="text-center">No equipment sale orders found.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-backdrop fade"></div>
    </div>
</div>
<?php
require "./layouts/footer.php";
?>
<script>
    $(document).ready(function() {
        $('.delete_btn').click(function() {
            const id = $(this).data('id');

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "e_sale_order_list.php?delete_id=" + id;
                }
            });
        });
    });
</script>