<?php
require("../auth/check_auth.php");
require("../requires/db.php");
include_once(__DIR__ . '/layouts/header.php');

// Filters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$status_filter = isset($_GET['payment_status']) ? $_GET['payment_status'] : 'all';

// Members (filtered)
$member_count_query = "SELECT COUNT(*) AS total FROM members WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date'";
$member_result = $mysqli->query($member_count_query);
$member_count = $member_result->fetch_assoc()['total'];

// Trainers (all)
$trainer_count_query = "SELECT COUNT(*) AS total FROM trainers";
$trainer_result = $mysqli->query($trainer_count_query);
$trainer_count = $trainer_result->fetch_assoc()['total'];

// Classes (filtered)
$class_count_query = "SELECT COUNT(*) AS total FROM classes WHERE start_date BETWEEN '$start_date' AND '$end_date'";
$class_result = $mysqli->query($class_count_query);
$class_count = $class_result->fetch_assoc()['total'];

// Equipment (all)
$equipment_count_query = "SELECT COUNT(*) AS total FROM equipments";
$equipment_result = $mysqli->query($equipment_count_query);
$equipment_count = $equipment_result->fetch_assoc()['total'];

// Class Payments (filtered by date and status)
$class_payment_total_query = "SELECT SUM(CAST(total_amount AS DECIMAL(10,2))) AS total FROM class_payment WHERE DATE(order_date) BETWEEN '$start_date' AND '$end_date'";
if ($status_filter == 'paid') {
  $class_payment_total_query .= " AND status = 'paid'";
} elseif ($status_filter == 'pending') {
  $class_payment_total_query .= " AND status = 'pending'";
}
$class_payment_total_result = $mysqli->query($class_payment_total_query);
$class_payment_total = $class_payment_total_result->fetch_assoc()['total'] ?? 0;

// E_sale Orders (filtered by date)
$e_sale_order_total_query = "SELECT SUM(CAST(total_amount AS DECIMAL(10,2))) AS total FROM esale_order WHERE DATE(order_date) BETWEEN '$start_date' AND '$end_date'";
$e_sale_order_total_result = $mysqli->query($e_sale_order_total_query);
$e_sale_order_total = $e_sale_order_total_result->fetch_assoc()['total'] ?? 0;

// Recent Class Payments (filtered)
$status_sql = '';
if ($status_filter == 'paid') {
  $status_sql = " AND cp.status = 'paid'";
} elseif ($status_filter == 'pending') {
  $status_sql = " AND cp.status = 'pending'";
}
$recent_payments_query = "SELECT 
    cp.id,
    cp.total_amount,
    cp.order_date,
    cp.status,
    m.name as member_name,
    c.batch_name,
    (SELECT COUNT(*) 
     FROM class_payment cp2 
     WHERE cp2.class_member_id = cp.class_member_id 
     AND DATE(cp2.order_date) <= DATE(cp.order_date)) as payment_number
FROM class_payment cp 
JOIN class_members cm ON cp.class_member_id = cm.id 
JOIN members m ON cm.member_id = m.id 
JOIN classes c ON cm.class_id = c.id 
WHERE DATE(cp.order_date) BETWEEN '$start_date' AND '$end_date' $status_sql
ORDER BY cp.order_date DESC 
LIMIT 10";
$recent_payments_result = $mysqli->query($recent_payments_query);

// Recent E_sale Orders (filtered)
$recent_orders_query = "SELECT eo.*, m.name as member_name, e.price, e.quantity as eq_qty FROM esale_order eo JOIN members m ON eo.member_id = m.id JOIN equipments e ON eo.equipment_id = e.id WHERE DATE(eo.order_date) BETWEEN '$start_date' AND '$end_date' ORDER BY eo.order_date DESC LIMIT 10";
$recent_orders_result = $mysqli->query($recent_orders_query);

// Recent Members (filtered)
$recent_members_query = "SELECT m.*, w.curr_weight FROM members m LEFT JOIN member_weight w ON m.id = w.member_id WHERE DATE(m.created_at) BETWEEN '$start_date' AND '$end_date' ORDER BY m.created_at DESC LIMIT 5";
$recent_members_result = $mysqli->query($recent_members_query);

// --- Chart Data Queries ---
// Class Payments daily totals
$class_payments_daily = [];
$class_payments_labels = [];
$cp_chart_status_sql = '';
if ($status_filter == 'paid') {
  $cp_chart_status_sql = " AND status = 'paid'";
} elseif ($status_filter == 'pending') {
  $cp_chart_status_sql = " AND status = 'pending'";
}
$cp_chart_query = "SELECT DATE(order_date) as day, SUM(CAST(total_amount AS DECIMAL(10,2))) as total FROM class_payment WHERE DATE(order_date) BETWEEN '$start_date' AND '$end_date' $cp_chart_status_sql GROUP BY day ORDER BY day ASC";
$cp_chart_result = $mysqli->query($cp_chart_query);
while ($row = $cp_chart_result->fetch_assoc()) {
  $class_payments_labels[] = $row['day'];
  $class_payments_daily[] = (float)$row['total'];
}
// E_sale Orders daily totals
$esale_orders_daily = [];
$esale_orders_labels = [];
$es_chart_query = "SELECT DATE(order_date) as day, SUM(CAST(total_amount AS DECIMAL(10,2))) as total FROM esale_order WHERE DATE(order_date) BETWEEN '$start_date' AND '$end_date' GROUP BY day ORDER BY day ASC";
$es_chart_result = $mysqli->query($es_chart_query);
while ($row = $es_chart_result->fetch_assoc()) {
  $esale_orders_labels[] = $row['day'];
  $esale_orders_daily[] = (float)$row['total'];
}

?>
<div style="overflow-y: auto; height:80vh;" class="bg-light">
  <div class="container-fluid">
    <div class="page-inner">
      <div class="row mb-3 bg-white pt-3 pb-2">
        <div class="col-12">
          <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
              <label for="start_date" class="form-label">Start Date</label>
              <input type="date" class="form-control" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
            </div>
            <div class="col-md-3">
              <label for="end_date" class="form-label">End Date</label>
              <input type="date" class="form-control" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
            </div>
            <div class="col-md-3">
              <label for="payment_status" class="form-label">Class Payment Status</label>
              <select class="form-select" id="payment_status" name="payment_status">
                <option value="all" <?= $status_filter == 'all' ? ' selected' : '' ?>>All</option>
                <option value="paid" <?= $status_filter == 'paid' ? ' selected' : '' ?>>Paid</option>
                <option value="pending" <?= $status_filter == 'pending' ? ' selected' : '' ?>>Pending</option>
              </select>
            </div>
            <div class="col-md-3">
              <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
            </div>
          </form>
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-lg-2 col-md-3 col-sm-4 col-12">
          <div class="card text-center shadow-sm">
            <div class="card-body">
              <div class="mb-2"><i class="fas fa-users fa-2x text-primary"></i></div>
              <h6 class="card-title">Members</h6>
              <h3><?= $member_count ?></h3>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-4 col-12">
          <div class="card text-center shadow-sm">
            <div class="card-body">
              <div class="mb-2"><i class="fas fa-user-tie fa-2x text-info"></i></div>
              <h6 class="card-title">Trainers</h6>
              <h3><?= $trainer_count ?></h3>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-4 col-12">
          <div class="card text-center shadow-sm">
            <div class="card-body">
              <div class="mb-2"><i class="fas fa-calendar-check fa-2x text-warning"></i></div>
              <h6 class="card-title">Classes</h6>
              <h3><?= $class_count ?></h3>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-4 col-12">
          <div class="card text-center shadow-sm">
            <div class="card-body">
              <div class="mb-2"><i class="fas fa-dumbbell fa-2x text-success"></i></div>
              <h6 class="card-title">Equipment</h6>
              <h3><?= $equipment_count ?></h3>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-4 col-12">
          <div class="card text-center shadow-sm">
            <div class="card-body">
              <div class="mb-2"><i class="fas fa-money-bill-wave fa-2x text-success"></i></div>
              <h6 class="card-title">Class Payments</h6>
              <h3 class="text-success"><?= number_format($class_payment_total, 0) ?></h3>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-4 col-12">
          <div class="card text-center shadow-sm">
            <div class="card-body">
              <div class="mb-2"><i class="fas fa-shopping-cart fa-2x text-secondary"></i></div>
              <h6 class="card-title">E_sale Orders</h6>
              <h3 class="text-secondary"><?= number_format($e_sale_order_total, 0) ?></h3>
            </div>
          </div>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col-lg-6 mb-3">
          <div class="card h-100">
            <div class="card-header bg-light"><strong>Class Payments (Daily)</strong></div>
            <div class="card-body">
              <div id="classPaymentsChart" style="height:320px;"></div>
            </div>
          </div>
        </div>
        <div class="col-lg-6 mb-3">
          <div class="card h-100">
            <div class="card-header bg-light"><strong>E_sale Orders (Daily)</strong></div>
            <div class="card-body">
              <div id="esaleOrdersChart" style="height:320px;"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="row g-3">
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header bg-light">
              <strong>Recent Class Payments</strong>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-sm mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Member</th>
                      <th>Class</th>
                      <th>Payment #</th>
                      <th>Amount</th>
                      <th>Date</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while ($payment = $recent_payments_result->fetch_assoc()): ?>
                      <tr>
                        <td><?= htmlspecialchars($payment['member_name']) ?></td>
                        <td><?= htmlspecialchars($payment['batch_name']) ?></td>
                        <td><?= $payment['payment_number'] ?></td>
                        <td>$<?= number_format($payment['total_amount'], 2) ?></td>
                        <td><?= date('Y-m-d', strtotime($payment['order_date'])) ?></td>
                        <td><span class="badge bg-<?= $payment['status'] == 'paid' ? 'success' : 'warning' ?>"><?= ucfirst($payment['status']) ?></span></td>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header bg-light">
              <strong>Recent E_sale Orders</strong>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-sm mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Member</th>
                      <th>Equipment</th>
                      <th>Qty</th>
                      <th>Amount</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while ($order = $recent_orders_result->fetch_assoc()): ?>
                      <tr>
                        <td><?= htmlspecialchars($order['member_name']) ?></td>
                        <td><?= htmlspecialchars($order['equipment_id']) ?></td>
                        <td><?= $order['quantity'] ?></td>
                        <td>$<?= number_format($order['total_amount'], 2) ?></td>
                        <td><?= date('Y-m-d', strtotime($order['order_date'])) ?></td>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row g-3 mt-3">
        <div class="col-lg-4">
          <div class="card h-100">
            <div class="card-header bg-light">
              <strong>Recent Members</strong>
            </div>
            <div class="card-body p-0">
              <ul class="list-group list-group-flush">
                <?php while ($member = $recent_members_result->fetch_assoc()): ?>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                      <strong><?= htmlspecialchars($member['name']) ?></strong><br>
                      <small>Joined: <?= date('Y-m-d', strtotime($member['created_at'])) ?></small><br>
                      <small>Original Weight: <?= $member['original_weight'] ?> kg</small>
                      <?php if ($member['curr_weight']): ?>
                        <br><small>Current Weight: <?= $member['curr_weight'] ?> kg</small>
                      <?php endif; ?>
                    </div>
                  </li>
                <?php endwhile; ?>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
<script>
  const classPaymentsLabels = <?= json_encode($class_payments_labels) ?>;
  const classPaymentsData = <?= json_encode($class_payments_daily) ?>;
  const esaleOrdersLabels = <?= json_encode($esale_orders_labels) ?>;
  const esaleOrdersData = <?= json_encode($esale_orders_daily) ?>;
  // Class Payments Chart
  var cpChart = echarts.init(document.getElementById('classPaymentsChart'));
  cpChart.setOption({
    tooltip: {
      trigger: 'axis'
    },
    xAxis: {
      type: 'category',
      data: classPaymentsLabels
    },
    yAxis: {
      type: 'value',
      name: 'Amount(MMK)',
      axisLabel: {
        formatter: function(value) {
          return value.toLocaleString('en-US');
        }
      }
    },
    series: [{
      data: classPaymentsData,
      type: 'line',
      smooth: true,
      color: '#28a745',
      name: 'Class Payments',
      areaStyle: {}
    }],
    grid: {
      left: 70,
      right: 20,
      bottom: 40,
      top: 40
    }
  });
  // E_sale Orders Chart
  var esChart = echarts.init(document.getElementById('esaleOrdersChart'));
  esChart.setOption({
    tooltip: {
      trigger: 'axis'
    },
    xAxis: {
      type: 'category',
      data: esaleOrdersLabels
    },
    yAxis: {
      type: 'value',
      name: 'Amount(MMK)',
      axisLabel: {
        formatter: function(value) {
          return value.toLocaleString('en-US');
        }
      }
    },
    series: [{
      data: esaleOrdersData,
      type: 'line',
      smooth: true,
      color: '#007bff',
      name: 'E_sale Orders',
      areaStyle: {}
    }],
    grid: {
      left: 70,
      right: 20,
      bottom: 40,
      top: 40
    }
  });
  window.addEventListener('resize', function() {
    cpChart.resize();
    esChart.resize();
  });
</script>

<?php include_once(__DIR__ . '/layouts/footer.php'); ?>