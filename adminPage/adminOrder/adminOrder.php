<?php
$title = "Admin Order";

// Include base.php for database connection and functions
include('../_base.php');
include('_headerAdmin.php');
include('_sideBar.php');

auth("admin");

// Initialize search term and sorting criteria
$searchTerm = isset($_POST['searchOrder']) ? $_POST['searchOrder'] : '';
$sorting = isset($_POST['sorting']) ? $_POST['sorting'] : 'none';

// Prepare the base SQL query to retrieve data from the `orders` table
$sql = "SELECT `order_id`, `member_id`, `order_date`, `order_status`, `subtotal` 
        FROM `orders` 
        WHERE `member_id` LIKE :searchTerm";

// Define sorting (filtering) options
$sortingOptions = [
    'completed' => 'order_status = "Completed"',
    'delivering' => 'order_status = "Delivering"',
    'delivered' => 'order_status = "Delivered"',  // New Sorting Option for Delivered Orders
    'cancelled' => 'order_status = "Cancelled"',
    'packing' => 'order_status = "Packing"',
    'pending' => 'order_status = "Pending"'
];

// Add sorting criteria to the SQL query if it's not 'none'
if ($sorting && $sorting !== 'none' && isset($sortingOptions[$sorting])) {
    $sql .= " AND " . $sortingOptions[$sorting];
}

$sql .= " ORDER BY `order_date` DESC"; // Default sorting by date

// Prepare and execute the SQL query
$stm = $_db->prepare($sql);
$stm->execute([':searchTerm' => '%' . $searchTerm . '%']);
$orders = $stm->fetchAll(PDO::FETCH_OBJ);
?>

<!-- Include CSS for styling -->
<link rel="stylesheet" href="adminOrder.css">

<div class="dashboard_container">
    <div class="title">
        <h2>Manage Orders</h2>
    </div>

    <div class="filter">
        <!-- Combined form for search and sorting -->
        <form action="" method="post" class="search-sorting-form">
            <input 
                type="search" 
                name="searchOrder" 
                id="searchOrder" 
                class="search-order-input" 
                placeholder="Search by member ID" 
                value="<?= htmlspecialchars($searchTerm) ?>"
            />
            <button type="submit" class="search-button"><i class="fas fa-search"></i></button>
            
            <select class="form-select" name="sorting" onchange="this.form.submit()">
                <option value="none" <?= $sorting === 'none' ? 'selected' : '' ?>>None</option>
                <option value="completed" <?= $sorting === 'completed' ? 'selected' : '' ?>>Completed</option>
                <option value="delivering" <?= $sorting === 'delivering' ? 'selected' : '' ?>>Delivering</option>
                <option value="delivered" <?= $sorting === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                <option value="packing" <?= $sorting === 'packing' ? 'selected' : '' ?>>Packing</option>
                <option value="cancelled" <?= $sorting === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                <option value="pending" <?= $sorting === 'pending' ? 'selected' : '' ?>>Pending</option>
            </select>
        </form>
    </div>

    <div class="order-count">
        <p>Total Orders: <?= count($orders) ?></p>
        <h4>(You can only view Completed and Cancelled Order.)</h4>
    </div>

    <table class="order-table">
        <thead>
            <tr>
                <th>ORDER</th>
                <th>MEMBER ID</th>
                <th>DATE</th>
                <th>STATUS</th>
                <th>TOTAL</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?= htmlspecialchars($order->order_id) ?></td>
                    <td><?= htmlspecialchars($order->member_id) ?></td>
                    <td><?= htmlspecialchars(date('F j, Y', strtotime($order->order_date))) ?></td>
                    <td><?= htmlspecialchars(ucfirst($order->order_status)) ?></td>
                    <td>RM <?= htmlspecialchars(number_format($order->subtotal, 2)) ?></td>
                    <td>
                        <div class="button">
                            <!-- Pass orderId and orderStatus via query string -->
                            <form action="adminOrderDetails.php" method="get">
                                <input type="hidden" name="orderId" value="<?= htmlspecialchars($order->order_id) ?>">
                                <input type="hidden" name="orderStatus" value="<?= htmlspecialchars($order->order_status) ?>">
                                <button type="submit"
                                    style="<?= ($order->order_status === 'Completed' || $order->order_status === 'Cancelled') ? 'border: 3px solid green; background-color: green; color: white;' : '' ?>">
                                    <?= ($order->order_status === 'Completed' || $order->order_status === 'Cancelled') ? 'View' : 'Edit' ?>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No orders found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include('_footerAdmin.php') ?>

