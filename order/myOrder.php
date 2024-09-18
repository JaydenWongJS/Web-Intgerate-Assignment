<?php
$title = "My Order";
require_once('../_base.php');
include('../_header.php');
include('../nav_bar.php');

// Handle cancel or complete button click
if (is_post() && req('order_id')) {
    $orderId = req('order_id'); // Get the order ID from the form
    $action = req('action'); // Get the action from the form ('complete' or 'cancel')

    if ($action === 'cancel') {
        // Update order status to Cancelled
        updateOrderOnCancellationReturnMemberPoints($orderId, $_user->member_id);
        temp("order_status", "$orderId" . "," . " Has Been Cancelled");
        redirect("myOrder.php");
    } elseif ($action === 'complete') {
        // Update order status to Completed
        $updateSql = "UPDATE orders SET order_status = 'Completed', order_completed_date = NOW() WHERE order_id = ? AND member_id = ?";
        $stm = $_db->prepare($updateSql);
        $stm->execute([$orderId, $_user->member_id]);

        if ($stm->rowCount()) {
            temp("order_status", "$orderId" . "," . " Successfully Completed");
        } else {
            echo "<script>alert('Failed to mark the order as complete.');</script>";
        }
        redirect("myOrder.php");
    } else {
        echo "<script>alert('Unknown action for order $orderId');</script>";
    }
}

// Retrieve search and filter input
$order_id = req('order_id', '');
$status = req('status', '');
$order_status_query = "order_status != 'Completed' AND order_status != 'Cancelled' AND order_status != 'Pending'";
// Define the base SQL query
$query = "SELECT order_id, order_date, order_status, subtotal FROM orders WHERE member_id = ? AND $order_status_query";
$params = [$_user->member_id]; // Filter orders by the logged-in member

// Apply search by Order ID if provided
if ($order_id !== '') {
    $query .= " AND order_id LIKE ?";
    $params[] = "%$order_id%";
}

// Apply filter by status if provided
if ($status !== '') {
    $query .= " AND order_status = ?";
    $params[] = $status;
}

// Define the fields for sorting
$fields = [
    'order_id'       => 'Order ID',
    'order_date'     => 'Order Date',
    'order_status'   => 'Status',
    'subtotal'       => 'Subtotal (RM)',
];

// Handle sorting and direction
$sort = req('sort');
key_exists($sort, $fields) || $sort = 'order_id'; // Default sorting by Order ID

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc'; // Default to ascending order


// Append sorting to the query
$query .= " ORDER BY $sort $dir";

// Paging
$page = req('page', 1);

// Implement SimplePager for pagination
require_once '../lib/SimplePager.php';
$p = new SimplePager($query, $params, 10, $page); // Fetch 10 orders per page
$orders = $p->result;

?>
<div id="info"><?= temp("order_status"); ?></div>
<div class="myOrderContainer">
    <div class="filter_order">
        <div class="my_order_title">
            <h2><i class="fas fa-receipt"></i> MY ORDERS</h2>
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
        <div>
            <!-- Pending Orders -->
            <a class="pending_order" href="pendingOrder.php">
                <p>
                    <i class="fas fa-clock" style="color: #ff8c00;"></i> Pending
                </p>
                <span class="pending_order_rounded"><?= countPendingOrder($_user->member_id) ?></span>
            </a>

            <!-- History Orders -->
            <a class="pending_order" href="historyOrder.php">
                <p>
                    <i class="fas fa-history" style="color: #007bff;"></i> History Order
                </p>
                <span class="pending_order_rounded"><?= countCompletedOrder($_user->member_id) + countCancelledOrder($_user->member_id) ?></span>
            </a>

        </div>
    </div>

    <?php if ($orders): ?>

        <!-- ORDER LIST TABLE -->
        <div class="tableOrder">
            <div style="width:70%;margin:10px 0px;">
                <p style="text-align: left;">
                    <?= $p->count ?> of <?= $p->item_count ?> order(s) | Page <b><?= $p->page ?></b> of <?= $p->page_count ?>
                </p>
            </div>

            <table class="styled-table">
                <thead>
                    <tr>
                        <?= table_headers($fields, $sort, $dir, "page=$page") ?>
                        <th>Action</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order->order_id) ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y', strtotime($order->order_date))) ?></td>
                            <td><?= htmlspecialchars(ucfirst($order->order_status)) ?></td>
                            <td><?= htmlspecialchars(number_format($order->subtotal, 2)) ?></td>
                            <td>
                                <!-- Form for the Complete button -->
                                <form action="" method="post" id="completeForm_<?= htmlspecialchars($order->order_id) ?>" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($order->order_id) ?>">
                                    <input type="hidden" name="action" value="complete">
                                    <button class="btn complete-btn" type="submit" onclick="confirmComplete(event, 'completeForm_<?= htmlspecialchars($order->order_id) ?>')">Complete</button>
                                </form>

                                <!-- Form for the Cancel button (only shows if the status is not 'Delivered') -->
                                <?php if ($order->order_status !== 'Delivered'): ?>
                                    <form action="" method="post" id="cancelForm_<?= htmlspecialchars($order->order_id) ?>" style="display:inline;">
                                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($order->order_id) ?>">
                                        <input type="hidden" name="action" value="cancel">
                                        <button class="btn cancel-btn" type="submit" onclick="confirmCancel(event, 'cancelForm_<?= htmlspecialchars($order->order_id) ?>')">Cancel</button>
                                    </form>
                                <?php endif; ?>
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
        <div class="tableOrder">
            <?= $p->html("sort=$sort&dir=$dir&order_id=$order_id&status=$status") ?>
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
            function confirmComplete(event, formId) {
                if (confirm("Please ensure the parcel has arrived before completing the order. Are you sure you want to mark this order as complete?")) {
                    document.getElementById(formId).submit(); // Submit the form with the specific ID if the user confirms
                } else {
                    event.preventDefault(); // Cancel the default form submission if user does not confirm
                }
            }
        </script>

        <?php include('../_footer.php'); ?>