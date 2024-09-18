<?php
$title = "Order Details";
include('_headerAdmin.php');
include('../_base.php'); // Include base.php for database connection
include('_sideBar.php');

// Get the order ID from the GET parameter
$orderId = isset($_GET['orderId']) ? $_GET['orderId'] : '';

// Validate orderId
if (!$orderId) {
    die('Invalid Order ID');
}

// Handle form submissions for status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['updateStatus'])) {
        $status = $_POST['status']; // Get the status from the form submission

        // Validate status
        $validStatuses = ['Cancelled', 'Delivering', 'Delivered'];
        if (in_array($status, $validStatuses)) {
            // Update order status in the database
            $sqlUpdate = "UPDATE `orders` SET `order_status` = ? WHERE `order_id` = ?";
            $stmUpdate = $_db->prepare($sqlUpdate);
            $stmUpdate->execute([$status, $orderId]);

            // Redirect to adminOrder.php
            header('Location: adminOrder.php');
            exit;
        }
    }
}

// Retrieve order details
$sqlOrder = "SELECT `member_id`, `order_date`, `order_created_time`, `order_delivered_time`, `order_status`, `subtotal`, 
 `payment_id`, `address1`, `address2`, `city`, `state`, `postcode` FROM `orders` WHERE order_id=?";
$stmOrder = $_db->prepare($sqlOrder);
$stmOrder->execute([$orderId]);
$order = $stmOrder->fetch(PDO::FETCH_OBJ);

// Check current status
$currentStatus = $order->order_status;

// Retrieve order details items
$sqlOrderDetails = "SELECT product_attribute_id, qty, order_product_price FROM order_details WHERE order_id = ?";
$stmOrderDetails = $_db->prepare($sqlOrderDetails);
$stmOrderDetails->execute([$orderId]);
$orderDetails = $stmOrderDetails->fetchAll(PDO::FETCH_OBJ);

// Retrieve product attributes, options, and attributes
$productDetails = [];
foreach ($orderDetails as $detail) {
    $productAttributeId = $detail->product_attribute_id;

    // Get product_id from product_attributes
    $sqlProductAttribute = "SELECT product_id, attributes_id, option_id FROM product_attributes WHERE product_attribute_id = ?";
    $stmProductAttribute = $_db->prepare($sqlProductAttribute);
    $stmProductAttribute->execute([$productAttributeId]);
    $productAttribute = $stmProductAttribute->fetch(PDO::FETCH_OBJ);

    if ($productAttribute) {
        $productId = $productAttribute->product_id;
        $attributesId = $productAttribute->attributes_id;
        $optionId = $productAttribute->option_id;

        // Get option_value from options
        $sqlOption = "SELECT option_value FROM options WHERE option_id = ?";
        $stmOption = $_db->prepare($sqlOption);
        $stmOption->execute([$optionId]);
        $option = $stmOption->fetch(PDO::FETCH_OBJ);

        // Get attributes_type from attributes
        $sqlAttribute = "SELECT attributes_type FROM attributes WHERE attributes_id = ?";
        $stmAttribute = $_db->prepare($sqlAttribute);
        $stmAttribute->execute([$attributesId]);
        $attribute = $stmAttribute->fetch(PDO::FETCH_OBJ);

        // Get product_name and description from products
        $sqlProduct = "SELECT product_name, description FROM products WHERE product_id = ?";
        $stmProduct = $_db->prepare($sqlProduct);
        $stmProduct->execute([$productId]);
        $product = $stmProduct->fetch(PDO::FETCH_OBJ);

        if ($product) {
            $productDetails[] = [
                'name' => $product->product_name,
                'description' => $product->description,
                'qty' => $detail->qty,
                'price' => $detail->order_product_price, // Price per unit
                'type' => ($option ? $option->option_value : '') . ' ' . ($attribute ? $attribute->attributes_type : '') // Concatenate option_value and attributes_type
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="adminOrderDetails.css">
    <script>
        function confirmAction(action) {
            let message;
            switch (action) {
                case 'cancelOrder':
                    message = 'Are you sure you want to cancel the order?';
                    break;
                case 'readyToShip':
                    message = 'Confirm ready for delivery?';
                    break;
                case 'parcelArrived':
                    message = 'Confirm that the parcel has arrived?';
                    break;
                default:
                    return false;
            }
            return confirm(message);
        }
    </script>
</head>
<body>
    <div class="container">
        <main>
            <div class="order-header">
                <h1>Order #<?= htmlspecialchars($orderId) ?></h1>
                <span class="order-status"><?= htmlspecialchars(ucfirst($currentStatus)) ?></span>
                <div class="order-dates">
                    <p>Created on: <?= htmlspecialchars(date('F j, Y, g:i a', strtotime($order->order_created_time))) ?></p>
                    <p>Paid on: <?= htmlspecialchars(date('F j, Y, g:i a', strtotime($order->order_date))) ?></p>
                </div>
                <div class="order-actions">
                    <form method="post" onsubmit="return confirmAction('cancelOrder');">
                        <input type="hidden" name="status" value="Cancelled">
                        <button type="submit" name="updateStatus" class="cancel-order" <?= $currentStatus === 'Cancelled' ? 'disabled' : '' ?>>Cancel Order</button>
                    </form>
                    <form method="post" onsubmit="return confirmAction('readyToShip');">
                        <input type="hidden" name="status" value="Delivering">
                        <button type="submit" name="updateStatus" class="ready-to-ship" <?= $currentStatus === 'Cancelled' ? 'disabled' : '' ?>>Ready To Ship</button>
                    </form>
                    <form method="post" onsubmit="return confirmAction('parcelArrived');">
                        <input type="hidden" name="status" value="Delivered">
                        <button type="submit" name="updateStatus" class="parcel-arrived" <?= in_array($currentStatus, ['Cancelled', 'Delivered']) ? 'disabled' : '' ?>>Parcel Arrived</button>
                    </form>
                </div>
            </div>

            <div class="order-details">
                <div class="order-info">
                    <h2>Customer & Order</h2>
                    <p>Member ID: <?= htmlspecialchars($order->member_id) ?></p>
                    <p>Payment Method: <?= htmlspecialchars($order->payment_id) ?></p>
                    <p>Shipping Address: <?= htmlspecialchars($order->address1) ?>, <?= htmlspecialchars($order->address2) ?>, <?= htmlspecialchars($order->city) ?>, <?= htmlspecialchars($order->state) ?> <?= htmlspecialchars($order->postcode) ?></p>
                    <p>Subtotal: RM <?= htmlspecialchars(number_format($order->subtotal, 2)) ?></p> <!-- Moved subtotal here -->
                </div>
            </div>

            <div class="order-items">
                <div class="detailsTitle">
                    <h2>Items Ordered</h2>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Type</th> <!-- Column for type -->
                            <th>Description</th>
                            <th>Quantity</th>
                            <th>Total Price</th> <!-- Renamed subtotal to Total Price -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productDetails as $product): ?>
                            <tr>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td><?= htmlspecialchars($product['type']) ?></td> <!-- Display type here -->
                                <td><?= htmlspecialchars($product['description']) ?></td>
                                <td><?= htmlspecialchars($product['qty']) ?></td>
                                <td>RM <?= htmlspecialchars(number_format($product['qty'] * $product['price'], 2)) ?></td> <!-- Calculate total price -->
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
<?php include('_footerAdmin.php') ?>
