<?php
$title = "Admin Order";

// Include base.php for database connection and functions
include('../../_base.php');
include('../_headerAdmin.php');
include('../_sideBar.php');

// Initialize search term and sorting criteria
$searchTerm = isset($_POST['searchOrder']) ? $_POST['searchOrder'] : '';
$sorting = isset($_POST['sorting']) ? $_POST['sorting'] : '';

// Prepare the base SQL query to retrieve data from the `orders` table
$sql = "SELECT `order_id`, `member_id`, `order_date`, `order_status`, `subtotal` 
        FROM `orders` 
        WHERE `member_id` LIKE :searchTerm";

// Add sorting criteria to the SQL query
$sortingOptions = [
    'completed' => 'order_status = "Completed"',
    'delivering' => 'order_status = "Delivering"',
    'cancelled' => 'order_status = "Cancelled"',
    'pending' => 'order_status = "Pending"'
];

if ($sorting && isset($sortingOptions[$sorting])) {
    $sql .= " AND " . $sortingOptions[$sorting];
}

$sql .= " ORDER BY `order_date` DESC"; // Default sorting by date, adjust if needed

// Prepare and execute the SQL query
$stm = $_db->prepare($sql);
$stm->execute([':searchTerm' => '%' . $searchTerm . '%']);
$orders = $stm->fetchAll(PDO::FETCH_OBJ);
?>

<link rel="stylesheet" href="adminOrder.css">
<script>
    function confirmEdit(orderStatus) {
        let message;
        if (orderStatus === 'Cancelled') {
            message = 'This order has been cancelled. Are you sure you want to edit it?';
        } else if (orderStatus === 'Completed') {
            message = 'This order has been completed. Editing it may change its status. Are you sure you want to proceed?';
        }
        return !message || confirm(message);
    }
</script>

<div class="dashboard_container">
    <div class="title">
        <h2>Manage Orders</h2>
    </div>

    <div class="filter">
        <form action="" method="post" class="search-form">
            <input type="search" name="searchOrder" id="searchOrder" class="search-order-input" placeholder="Search by member ID" value="<?= htmlspecialchars($searchTerm) ?>"/>
            <button type="submit" class="search-button"><i class="fas fa-search"></i></button>
        </form>
        <form id="sortingForm" class="sorting-form" method="post">
            <select class="form-select" name="sorting" onchange="this.form.submit()">
                <option value="" disabled <?= !$sorting ? 'selected' : '' ?>>Sort Order</option>
                <option value="completed" <?= $sorting === 'completed' ? 'selected' : '' ?>>Completed</option>
                <option value="delivering" <?= $sorting === 'delivering' ? 'selected' : '' ?>>Delivering</option>
                <option value="cancelled" <?= $sorting === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                <option value="pending" <?= $sorting === 'pending' ? 'selected' : '' ?>>Pending</option>
            </select>
        </form>
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
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?= htmlspecialchars($order->order_id) ?></td>
                    <td><?= htmlspecialchars($order->member_id) ?></td>
                    <td><?= htmlspecialchars(date('F j, Y', strtotime($order->order_date))) ?></td>
                    <td><?= htmlspecialchars(ucfirst($order->order_status)) ?></td>
                    <td>RM <?= htmlspecialchars(number_format($order->subtotal, 2)) ?></td>
                    <td>
                        <div class="button">
                            <form action="adminOrderDetails.php" method="get" onsubmit="return confirmEdit('<?= htmlspecialchars($order->order_status) ?>')">
                                <input type="hidden" name="orderId" value="<?= htmlspecialchars($order->order_id) ?>">
                                <button type="submit">Edit</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include('../_footerAdmin.php') ?>
