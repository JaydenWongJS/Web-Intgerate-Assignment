<?php $title = "MyPending Order" ?>
<?php
require_once('../_base.php');
include('../_header.php');
include('../nav_bar.php');
?>

<?php

if (is_post() && req('order_id') && req('payment_id')) {
    $orderId = req('order_id'); // Get the order ID from the form
    $payment_id = req('payment_id'); // Get the order ID from the form
    $action = req('action'); // Get the action from the form ('complete' or 'cancel')

    if ($action === 'cancel') {
        // Update order status to Cancelled
        $updateSql = "UPDATE orders SET order_status = 'Cancelled', order_cancelled_date=NOW() WHERE order_id =? AND member_id =?";
        $stm = $_db->prepare($updateSql);
        $stm->execute([$orderId, $_user->member_id]);

        $updatePaymentSql = "UPDATE payment SET payment_status = 'Cancelled' WHERE payment_id =?";
        $updatePaymentStmt = $_db->prepare($updatePaymentSql);
        $updatePaymentStmt->execute([$payment_id]);

        temp("order_status", "$orderId" . "," . " Has Been Cancelled");
    } elseif ($action === 'paid') {
        // Generate a unique token
        $payment_duration_token = sha1(uniqid() . rand());

        $insertPaymentTokenQuery = '
        
                  INSERT INTO token (token_id, expire, member_id) 
             VALUES (?, ADDTIME(NOW(), "00:05"), ?);
           ';
        $stmt = $_db->prepare($insertPaymentTokenQuery);
        $stmt->execute([$payment_duration_token, $_user->member_id]);

        redirect("paymentGateway.php?order_id=$orderId&payment_id=$payment_id&payment_duration_token=$payment_duration_token");
    } else {
        echo "<script>alert('Unknown action for order $orderId');</script>";
    }
}

// SQL query to fetch orders where member_id matches
$sql = "SELECT *
FROM orders o JOIN payment p ON o.payment_id = p.payment_id
WHERE o.member_id = ? 
    AND o.order_status = 'Pending'
    ";
$stm = $_db->prepare($sql);
$stm->execute([$_user->member_id]);
$orders = $stm->fetchAll();

$totalOrderIds = count($orders);

?>

<div id="info"><?= temp("order_status"); ?></div>
<div class="myOrderContainer">
    <div class="filter_order">
        <div class="my_order_title">
            <h2><i class="fas fa-receipt"></i> PENDING ORDERS</h2>
        </div>
        <form action="myOrderSearch.php" method="post" class="search-form">
            <input type="search" name="searchOrder" id="searchOrder" class="search-order-input" placeholder="Search by Order ID" />
            <button type="submit" class="search-button"><i class="fas fa-search"></i></button>
        </form>
        <form id="sortingForm" class="sorting-form" method="post">
            <select class="form-select" name="sorting" onchange="this.form.submit()">
                <option value="" disabled>Sort Order</option>
                <option value="delivering">Delivering</option>
                <option value="cancelled">Cancelled</option>
                <option value="packing">Packing</option>
            </select>
        </form>
    </div>
    <hr />

    <div class="order_container_center">
        <a class="pending_order" href="myOrder.php">
            <i class="fas fa-list" style="color: #007bff;"></i>
            <p>All</p>
            <span class="pending_order_rounded"><?= countCurrentOrder($_user->member_id) ?></span>
        </a>
        <div class="total-order-count">
            <h2>Total Pending Order: <span id="totalOrderCount"><?= $totalOrderIds ?></span></h2>
        </div>
    </div>

    <?php if ($orders): ?>
        <div class="tableOrder">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Subtotal (RM)</th>
                        <th>Payment Status</th>
                        <th>Action</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr data-order-id="<?= htmlspecialchars($order->order_id) ?>" data-status="<?= htmlspecialchars($order->order_status) ?>">
                            <td><?= htmlspecialchars($order->order_id) ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y', strtotime($order->order_date))) ?></td>
                            <td><?= htmlspecialchars(ucfirst($order->order_status)) ?></td>
                            <td><?= htmlspecialchars(number_format($order->subtotal, 2)) ?></td>
                            <td><?= $order->payment_status ?></td>
                            <td>
                                <!-- For Paid Now -->
                                <form action="" method="post" id="completeForm_<?= htmlspecialchars($order->order_id) ?>" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($order->order_id) ?>">
                                    <input type="hidden" name="payment_id" value="<?= htmlspecialchars($order->payment_id) ?>"> <!-- Changed name -->
                                    <input type="hidden" name="action" value="paid">
                                    <button class="btn complete-btn" type="submit" onclick="comfirmPaid(event, 'completeForm_<?= htmlspecialchars($order->order_id) ?>')">Paid Now</button>
                                </form>

                                <!-- For Cancel -->
                                <form action="" method="post" id="cancelForm_<?= htmlspecialchars($order->order_id) ?>" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($order->order_id) ?>">
                                    <input type="hidden" name="payment_id" value="<?= htmlspecialchars($order->payment_id) ?>"> <!-- Changed name -->
                                    <input type="hidden" name="action" value="cancel">
                                    <button class="btn cancel-btn" type="submit" onclick="confirmCancel(event, 'cancelForm_<?= htmlspecialchars($order->order_id) ?>')">Cancel</button>
                                </form>

                            </td>

                            <td>
                                <a href="orderDetails.php?order_id=<?= urlencode($order->order_id) ?>">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="tableOrder">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Subtotal (RM)</th>
                        <th>Action</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6">No any orders found.</td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
        </div>

        <script>
            // Function to handle order cancellation with a warning
            function confirmCancel(event, formId) {
                if (confirm("⚠️ Warning: Are you sure you want to cancel this order? This action cannot be undone.")) {
                    document.getElementById(formId).submit(); // Submit the form with the specific ID if the user confirms
                } else {
                    event.preventDefault(); // Cancel the default form submission if user does not confirm
                }
            }

            // Function to confirm order completion with a reminder to double-check
            function comfirmPaid(event, formId) {
                if (confirm("Do you want to paid for this order ?")) {
                    document.getElementById(formId).submit(); // Submit the form with the specific ID if the user confirms
                } else {
                    event.preventDefault(); // Cancel the default form submission if user does not confirm
                }
            }
        </script>

        <?php include('../_footer.php'); ?>