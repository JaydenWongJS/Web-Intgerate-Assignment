<?php
require_once '../_base.php';

auth("admin");
$member_id = $_user->member_id;
// Query to get total sales from paid statuses (Packing, Delivering, Delivered, Completed)
$stmt = $_db->prepare("
    SELECT SUM(subtotal) AS total_sales
    FROM orders
    WHERE order_status IN ('Packing', 'Delivering', 'Delivered', 'Completed')
");
$stmt->execute();
$total_sales = $stmt->fetch(PDO::FETCH_OBJ)->total_sales ?? 0;

// Query to get completed sales
$stmt = $_db->prepare("
    SELECT SUM(subtotal) AS completed_sales
    FROM orders
    WHERE order_status = 'Completed'
");
$stmt->execute();
$completed_sales = $stmt->fetch(PDO::FETCH_OBJ)->completed_sales ?? 0;

// Query to get cancelled sales
$stmt = $_db->prepare("
    SELECT SUM(subtotal) AS cancelled_sales
    FROM orders
    WHERE order_status = 'Cancelled'
");
$stmt->execute();
$cancelled_sales = $stmt->fetch(PDO::FETCH_OBJ)->cancelled_sales ?? 0;

// Calculate the percentages
$orderCompletedPercentage = $total_sales > 0 ? ($completed_sales / $total_sales) * 100 : 0;
$orderUnCompletedPercentage = $total_sales > 0 ? ($cancelled_sales / $total_sales) * 100 : 0;

$stmt = $_db->prepare("
  SELECT 
    m.member_id, 
    CONCAT(m.firstname, ' ', m.lastname) AS full_name, 
    m.email,
    COUNT(o.order_id) AS total_orders
FROM 
    orders o
JOIN 
    member m ON o.member_id = m.member_id
WHERE 
    o.order_status IN ('Completed', 'Delivered')
GROUP BY 
    m.member_id, m.firstname, m.lastname, m.email
ORDER BY 
    total_orders DESC
LIMIT 10;

");

$stmt->execute();
$order = $stmt->fetch();;

?>

?>

<?php
$title = "Dashboard";
include('_headerAdmin.php');
include('_sideBar.php');
?>


<div class="dashboard_container">
  <div class="title">
    <h2>Dashboard</h2>
  </div>

  <div class="dashboard-header">
    <p><i class="fas fa-chart-bar" style="font-size:18px"></i> Statistics</p>
  </div>

  <div class="statistics_container">
    <div class="statistics_box">
      <div class="icon_box">
        <i class="fas fa-clock"></i>
      </div>
      <div class="statistics_details">
        <h3>Pending Orders</h3>
        <p><?= countPendingOrder() ?></p>
      </div>
    </div>

    <div class="statistics_box">
      <div class="icon_box">
        <i class="fas fa-receipt"></i>
      </div>
      <div class="statistics_details">
        <h3>Processing Orders</h3>
        <p><?= countCurrentOrder() ?></p>
      </div>
    </div>

    <div class="statistics_box">
      <div class="icon_box">
        <i class="fas fa-check-circle"></i>
      </div>
      <div class="statistics_details">
        <h3>Orders Completed</h3>
        <p><?= countCompletedOrder() ?></p>
      </div>
    </div>


    <div class="statistics_box">
      <div class="icon_box">
        <i class="fas fa-ban"></i>
      </div>
      <div class="statistics_details">
        <h3>Orders Cancelled</h3>
        <p><?= countCancelledOrder() ?></p>
      </div>
    </div>

    <div class="statistics_box">
      <div class="icon_box">
        <i class="fas fa-star"></i>
      </div>
      <div class="statistics_details">
        <h3>Pending Reviews</h3>
        <p><?= 0  ?></p>
      </div>
    </div>



    <div class="statistics_box">
      <div class="icon_box">
        <i class="fas fa-users"></i>
      </div>
      <div class="statistics_details">
        <h3>Members Have Join</h3>
        <p><?= countAllUser("member") ?></p>
      </div>
    </div>
  </div>

  <div class="sum_percentage_box">
    <div class="card">
      <div class="card-header">
        <div class="profit">
          <p>Total Sales Overview</p>
          <h2>RM <?= number_format($total_sales, 2) ?></h2>
        </div>
      </div>
      <div class="card-body">
        <div class="order">
          <i class="fas fa-shopping-cart"></i>
          <p>Order Completed</p>
          <h3 class="orderCompletedPercentage"><?= number_format($orderCompletedPercentage, 2) ?>%</h3>
          <p class="number">RM <?= number_format($completed_sales, 2) ?></p>
        </div>
        <div class="vs">
          <p>VS</p>
        </div>
        <div class="orderCancel">
          <i class="fas fa-ban"></i>
          <p>Order Cancelled</p>
          <h3 class="orderUnCompletedPercentage"><?= number_format($orderUnCompletedPercentage, 2) ?>%</h3>
          <p class="number">RM <?= number_format($cancelled_sales, 2) ?></p>
        </div>
      </div>
    </div>
    <?php
    $positivePercentage = 39.23;
    $neutralPercentage = 27.26;
    $negativePercentage = 23.51;
    ?>

    <div class="review_card">
      <div class="card-header">
        <h2>Review Overview</h2>
      </div>
      <div class="card-body">
        <div class="circle-container">
          <div class="circle" data-positive="<?php echo $positivePercentage; ?>" data-neutral="<?php echo $neutralPercentage; ?>" data-negative="<?php echo $negativePercentage; ?>">
            <div class="inside-circle"><?php echo round($positivePercentage + $neutralPercentage + $negativePercentage, 2); ?>%</div>
          </div>
          <ul>
            <li><span class="dot positive"></span> Positive Reviews (4-5 stars) (140) <span class="percent"><?php echo round($positivePercentage, 2); ?>%</span></li>
            <li><span class="dot neutral"></span> Neutral Reviews (3 stars) (30) <span class="percent"><?php echo round($neutralPercentage, 2); ?>%</span></li>
            <li><span class="dot negative"></span> Negative Reviews (1-2 stars) (60) <span class="percent"><?php echo round($negativePercentage, 2); ?>%</span></li>
          </ul>
        </div>
      </div>
    </div>

    <div>

    </div>




  </div><!-- container -->
  <div class="customer-container">
    <h2 class="customer-title">Top 10 Potential Customers</h2>
    <table class="customer-table">
      <thead>
        <tr class="customer-table-header">
          <th class="customer-table-header-cell">Member Id</th>
          <th class="customer-table-header-cell">Name</th>
          <th class="customer-table-header-cell">Email</th>
          <th class="customer-table-header-cell">Total Orders</th>
        </tr>
      </thead>
      <tbody>
        <tr class="customer-table-row" onclick="window.location.href='memberDetail.php?member_id=<?= $order->member_id ?>'">
          <td class="customer-table-cell"><?= $order->member_id ?></td>
          <td class="customer-table-cell"><?= $order->full_name ?></td>
          <td class="customer-table-cell"><?= $order->email ?></td>
          <td class="customer-table-cell"><?= $order->total_orders ?></td>
        </tr>
      </tbody>
    </table>
  </div>

  <?php include('_footerAdmin.php') ?>