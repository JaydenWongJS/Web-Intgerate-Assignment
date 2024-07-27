<?php
$title="Dashboard";
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
          <i class="fas fa-receipt"></i>
        </div>
        <div class="statistics_details">
          <h3>Pending Orders</h3>
          <p>12</p>
        </div>
      </div>

      <div class="statistics_box">
        <div class="icon_box">
          <i class="fas fa-star"></i>
        </div>
        <div class="statistics_details">
          <h3>Pending Reviews</h3>
          <p>20</p>
        </div>
      </div>

      <div class="statistics_box">
        <div class="icon_box">
          <i class="fas fa-check-circle"></i>
        </div>
        <div class="statistics_details">
          <h3>Order Complete</h3>
          <p>12</p>
        </div>
      </div>

      <div class="statistics_box">
        <div class="icon_box">
          <i class="fas fa-users"></i>
        </div>
        <div class="statistics_details">
          <h3>Members Have Join</h3>
          <p>12</p>
        </div>
      </div>
    </div>

    <div class="sum_percentage_box">
      <div class="card">
        <div class="card-header">
          <div class="profit">
            <p>Total Sales Overview</p>
            <h2>RM 500</h2>
          </div>
        </div>
        <div class="card-body">
          <div class="order">
            <i class="fas fa-shopping-cart"></i>
            <p>Order Completed</p>
            <h3 class="orderCompletedPercentage">62.2%</h3>
            <p class="number">RM 6,450</p>
          </div>
          <div class="vs">
            <p>VS</p>
          </div>
          <div class="orderCancel">
            <i class="fas fa-ban"></i>
            <p>Order Cancelled</p>
            <h3 class="orderUnCompletedPercentage">25.5%</h3>
            <p class="number">RM 230</p>
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

    </div>

    <?php include('_footerAdmin.php')?>