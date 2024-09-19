<?php $title = "History Order" ?>
<?php
require_once('_base.php');
include('_header.php');
include('nav_bar.php');

$error = ''; // Initialize the error message

// Fetch parameters from the search and filter form
$searchOrder = isset($_GET['searchOrder']) ? $_GET['searchOrder'] : '';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : 'All';
$sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'DESC';  // Default sort is DESC (newest first)

$orders = []; // Initialize orders array

// Validate that if one date is provided, both are required
if ((!empty($startDate) && empty($endDate)) || (empty($startDate) && !empty($endDate))) {
    $error = 'Please provide both "From" and "To" dates for filtering.';
} else {
    // Base query to get orders for the user
    $query = "SELECT * FROM orders WHERE member_id = :member_id AND order_status IN ('Completed', 'Cancelled')";

    // Add status filter if it's not set to 'All'
    if ($status != 'All') {
        $query .= " AND order_status = :status ";
    }

    // Add date filters if both are provided
    if (!empty($startDate) && !empty($endDate)) {
        $query .= " AND DATE(order_created_time) BETWEEN :startDate AND :endDate ";
    }

    // Add search by order ID if provided
    if (!empty($searchOrder)) {
        $query .= " AND order_id LIKE :searchOrder ";
    }

    // Final sorting by date
    $query .= " ORDER BY order_created_time $sortOrder";

    // Prepare the query and bind the parameters
    $stmt = $_db->prepare($query);
    $stmt->bindParam(':member_id', $_user->member_id, PDO::PARAM_INT);

    if ($status != 'All') {
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    }

    if (!empty($startDate) && !empty($endDate)) {
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
    }

    if (!empty($searchOrder)) {
        $stmt->bindValue(':searchOrder', "%$searchOrder%");
    }

    // Execute the query
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_OBJ);
}
?>

<div class="history_bar">
    <h1><i class="fas fa-history"></i> MY HISTORY ORDERS</h1>
    <div class="summary_qty">
        <p style="color:green;font-weight: bold;">Total Completed Order : <span><?= countCompletedOrder($_user->member_id) ?></span></p>
        <p style="color:red;font-weight: bold">Total Cancelled Order : <span><?= countCancelledOrder($_user->member_id) ?></span></p>
    </div>
</div>

<div class="order-search-container">
    <form action="" method="GET">
        <label for="searchOrder">Search</label>
        <input type="search" id="searchOrder" name="searchOrder" value="<?= htmlspecialchars($searchOrder) ?>" placeholder="Enter a search order"/>

        <label for="start_date">From:</label>
        <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($startDate) ?>">

        <label for="end_date">To:</label>
        <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($endDate) ?>">

        <label for="status">Status:</label>
        <select id="status" name="status">
            <option value="All" <?= $status == 'All' ? 'selected' : '' ?>>All</option>
            <option value="Completed" <?= $status == 'Completed' ? 'selected' : '' ?>>Completed</option>
            <option value="Cancelled" <?= $status == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>

        <label for="sortOrder">Sort By Date:</label>
        <select id="sortOrder" name="sortOrder">
            <option value="DESC" <?= $sortOrder == 'DESC' ? 'selected' : '' ?>>Latest First</option>
            <option value="ASC" <?= $sortOrder == 'ASC' ? 'selected' : '' ?>>Oldest First</option>
        </select>

        <button type="submit" class="search-btn">Search</button>
    </form>
</div>

<!-- Error message displayed here -->
<?php if (!empty($error)): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<div class="order-summary-container">
    <?php foreach ($orders as $order): ?>
        <div class="order-summary">
            <h1>Order ID : <?= htmlspecialchars($order->order_id) ?></h1>
            <table>
                <tr>
                    <th><i class="fas fa-calendar-alt" style="color: #007bff;font-weight:bold;"></i> Date Created</th>
                    <th>:</th>
                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($order->order_created_time))) ?></td>
                </tr>

                <?php if ($order->order_completed_date): ?>
                    <tr>
                        <th><i class="fas fa-check-circle" style="color: green;font-weight:bold;"></i> Completed Date</th>
                        <th>:</th>
                        <td style="color: green;font-weight:bold">
                            <?= htmlspecialchars(date('d/m/Y', strtotime($order->order_completed_date))) ?>
                        </td>
                    </tr>
                <?php elseif ($order->order_cancelled_date && $order->order_status == 'Cancelled'): ?>
                    <tr>
                        <th><i class="fas fa-times-circle" style="color: red;"></i> Cancelled Date</th>
                        <th>:</th>
                        <td style="color: red;font-weight:bold;">
                            <?= htmlspecialchars(date('d/m/Y', strtotime($order->order_cancelled_date))) ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <th><i class="fas fa-exclamation-circle" style="color: orange;font-weight:bold;"></i> Status Date</th>
                        <th>:</th>
                        <td>-</td>
                    </tr>
                <?php endif; ?>

                <tr>
                    <th><i class="fas fa-info-circle" style="color: #007bff;"></i> Status</th>
                    <th>:</th>
                    <td class="status" 
                        <?php
                        if ($order->order_status == 'Completed') {
                            echo 'style="color: green;"';
                        } elseif ($order->order_status == 'Cancelled') {
                            echo 'style="color: red;"';
                        }
                        ?>
                    >
                        <?= htmlspecialchars($order->order_status) ?>
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

<?php include('_footer.php'); ?>
