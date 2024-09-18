<?php $title = "My Order" ?>
<?php
require_once('_base.php');
include('_header.php');
include('nav_bar.php');

// Handle cancel or complete button click
if (is_post() && req('order_id')) {
    $orderId = req('order_id'); // Get the order ID from the form
    $action = req('action'); // Get the action from the form ('complete' or 'cancel')

    if ($action === 'cancel') {
        // Update order status to Cancelled
        updateOrderOnCancellationReturnMemberPoints($orderId, $_user->member_id);
        temp("order_status", "$orderId" . "," . " Has Been Cancelled");
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
    } else {
        echo "<script>alert('Unknown action for order $orderId');</script>";
    }
}

// Initialize variables for search and sorting
$searchOrder = req('searchOrder'); // For search functionality
$sorting = req('sorting'); // For sorting functionality

// Base SQL query to fetch orders
$sql = "SELECT order_id, order_date, order_status, subtotal FROM orders WHERE member_id = ? ";

// Modify the query if searching by Order ID
if (!empty($searchOrder)) {
    $sql .= " AND order_id = ? ";
} elseif (!empty($sorting)) {
    // Modify the query for sorting based on order_status
    if ($sorting === 'delivering') {
        $sql .= " AND order_status = 'Delivering' ";
    } elseif ($sorting === 'delivered') {
        $sql .= " AND order_status = 'Delivered' ";
    } elseif ($sorting === 'packing') {
        $sql .= " AND order_status = 'Packing' ";
    }
} else {
    // Default query to exclude 'Completed', 'Cancelled', and 'Pending' statuses
    $sql .= " AND order_status != 'Completed' 
              AND order_status != 'Cancelled'
              AND order_status != 'Pending' ";
}

// Prepare and execute the SQL statement
$stm = $_db->prepare($sql);

// Bind values based on whether it's a search or not
if (!empty($searchOrder)) {
    $stm->execute([$_user->member_id, $searchOrder]); // For search
} else {
    $stm->execute([$_user->member_id]); // For sorting or default
}

$orders = $stm->fetchAll();
$totalOrderIds = count($orders);

?>

<div id="info"><?= temp("order_status"); ?></div>
<div class="myOrderContainer">
    <div class="filter_order">
        <div class="my_order_title">
            <h2><i class="fas fa-receipt"></i> MY ORDERS</h2>
        </div>

        <!-- Search Form -->
        <form action="" method="post" class="search-form">
            <input type="search" name="searchOrder" id="searchOrder" class="search-order-input" placeholder="Search by Order ID" />
            <button type="submit" class="search-button"><i class="fas fa-search"></i></button>
        </form>

        <!-- Sorting Form -->
        <form id="sortingForm" class="sorting-form" method="post">
            <select class="form-select" name="sorting" onchange="this.form.submit()">
                <option value="" disabled selected>Sort Order</option>
                <option value="delivering">Delivering</option>
                <option value="delivered">Delivered</option>
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

        <div class="total-order-count">
            <h2>Total Processing Order: <span id="totalOrderCount"><?= $totalOrderIds ?></span></h2>
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
                            <td>
                                <!-- Form for the Complete button -->
                                <form action="" method="post" id="completeForm_<?= htmlspecialchars($order->order_id) ?>" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($order->order_id) ?>">
                                    <!-- Add a hidden input to indicate the action is 'complete' -->
                                    <input type="hidden" name="action" value="complete">
                                    <!-- Complete Button -->
                                    <button class="btn complete-btn" type="submit" onclick="confirmComplete(event, 'completeForm_<?= htmlspecialchars($order->order_id) ?>')">Complete</button>
                                </form>

                                <!-- Form for the Cancel button (only shows if the status is not 'Delivered') -->
                                <form action="" method="post" id="cancelForm_<?= htmlspecialchars($order->order_id) ?>" style="display:inline;">
                                    <?php if ($order->order_status !== 'Delivered'): ?>
                                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($order->order_id) ?>">
                                        <!-- Add a hidden input to indicate the action is 'cancel' -->
                                        <input type="hidden" name="action" value="cancel">
                                        <!-- Cancel Button -->
                                        <button class="btn cancel-btn" type="submit" onclick="confirmCancel(event, 'cancelForm_<?= htmlspecialchars($order->order_id) ?>')">Cancel</button>
                                    <?php endif; ?>
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
        </div>
    <?php endif; ?>

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
</div>

<?php include('_footer.php'); ?>
