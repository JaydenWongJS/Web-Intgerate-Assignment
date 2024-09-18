<?php $title = "History Order" ?>
<?php
require('../_base.php');
include('../_header.php');
include('../nav_bar.php');
?>

<?php
$orders = $_db->query("SELECT * FROM orders WHERE order_status = 'Completed' OR order_status = 'Cancelled' AND member_id = '$_user->member_id' ");
?>

<div class="history_bar">
    <h1><i class="fas fa-history"></i> MY HISTORY ORDERS</h1>
    <div class="summary_qty">
    <p style="color:green;font-weight: bold;">Total Completed Order : <span><?= countCompletedOrder($_user->member_id) ?></span></p>
    <p style="color:red;font-weight: bold">Total Cancelled Order : <span><?= countCancelledOrder($_user->member_id) ?></span></p>
</div>
</div>

<div class="order-search-container">
    <form action="yourSearchHandler.php" method="GET">
        <label for="searchOrder">Search</label>
        <input type="search" id="searchOrder" name="searchOrder" placeholder="Enter a search order"/>

        <label for="start_date">From:</label>
        <input type="date" id="start_date" name="start_date">

        <label for="end_date">To:</label>
        <input type="date" id="end_date" name="end_date">

        <label for="status">Status:</label>
        <select id="status" name="status">
            <option value="All">All</option>
            <option value="Completed">Completed</option>
            <option value="Cancelled">Cancelled</option>
        </select>

        <button type="submit" class="search-btn">Search</button>
    </form>
</div>


<div class="order-summary-container">
    <?php foreach ($orders as $order): ?>
        <div class="order-summary">
            <h1>Order ID : <?= $order->order_id ?></h1>
            <table>
                <tr>
                    <th><i class="fas fa-calendar-alt" style="color: #007bff;font-weight:bold;"></i> Date Created</th>
                    <th>:</th>
                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($order->order_created_time))) ?></td>
                </tr>

                <?php if ($order->order_completed_date): ?>
                    <!-- If order_completed_date exists, show Completed Date -->
                    <tr>
                        <th><i class="fas fa-check-circle" style="color: green;font-weight:bold;"></i> Completed Date</th>
                        <th>:</th>
                        <td style="color: green;font-weight:bold">
                            <?= htmlspecialchars(date('d/m/Y', strtotime($order->order_completed_date))) ?>
                        </td>
                    </tr>
                <?php elseif ($order->order_cancelled_date && $order->order_status == 'Cancelled'): ?>
                    <!-- If order_cancelled_date exists and status is Cancelled, show Cancelled Date -->
                    <tr>
                        <th><i class="fas fa-times-circle" style="color: red;"></i> Cancelled Date</th>
                        <th>:</th>
                        <td style="color: red;font-weight:bold;">
                            <?= htmlspecialchars(date('d/m/Y', strtotime($order->order_cancelled_date))) ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <!-- Default case if neither completed nor cancelled date exists -->
                    <tr>
                        <th><i class="fas fa-exclamation-circle" style="color: orange;font-weight:bold;"></i> Status Date</th>
                        <th>:</th>
                        <td>-</td>
                    </tr>
                <?php endif; ?>

                <tr>
                    <th><i class="fas fa-info-circle" style="color: #007bff;"></i> Status</th>
                    <th>:</th>
                    <td class="status" style="
        <?php
        if ($order->order_status == 'Completed') {
            echo 'color: green;';
        } elseif ($order->order_status == 'Cancelled') {
            echo 'color: red;';
        }
        ?>">
                        <?= htmlspecialchars($order->order_status) ?>
                    </td>
                </tr>

                </td>
                </tr>

                <tr>
                    <th><i class="fas fa-money-bill-wave" style="color: #28a745;"></i> Amount</th>
                    <th>:</th>
                    <td>RM <?= htmlspecialchars($order->subtotal) ?></td>
                </tr>
            </table>

            <!-- View Order Details -->
            <a href="orderDetails.php?order_id=<?= urlencode($order->order_id) ?>" class="view"> View</a>

        </div>
    <?php endforeach; ?>
</div>


<?php include('../_footer.php'); ?>