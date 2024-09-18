<?php
$title = "Order Details";
require_once('../_base.php');
include('../_header.php');
include('../nav_bar.php');

clear_cart();

auth("member");
// Retrieve order_id from query parameters
$orderId = req("order_id");
if (!$orderId) {
    echo "Order ID not provided!";
    redirect("myOrder.php");
    exit;
}

// Retrieve order details
$sqlOrder = "SELECT *
              FROM orders o 
              JOIN member m ON o.member_id = m.member_id
              WHERE o.order_id = ? AND m.member_id = ?";

$stmOrder = $_db->prepare($sqlOrder);
$stmOrder->execute([$orderId, $_user->member_id]);
$order = $stmOrder->fetch(PDO::FETCH_ASSOC);

if ($stmOrder->rowCount() == 0) {
    echo "Order not found!";
    redirect("myOrder.php");
    exit;
}

$order_member_name = $order["firstname"] . " " . $order["lastname"];
$total_qty = $order["total_qty"];
$subtotal = $order["subtotal"];
$discount = $order["voucher_points_used"] / 10;

// Retrieve order products
$sqlOrderDetails = "SELECT qty, order_product_price, total, product_attribute_id 
                     FROM order_details 
                     WHERE order_id = ?";
$stmOrderDetails = $_db->prepare($sqlOrderDetails);
$stmOrderDetails->execute([$orderId]);
$orderDetails = $stmOrderDetails->fetchAll(PDO::FETCH_ASSOC);

// Initialize arrays to store attributes and options
$productDetails = [];

// Retrieve product attributes and options
foreach ($orderDetails as $detail) {
    $productAttributeId = $detail['product_attribute_id'];

    $sqlProductAttributes = "SELECT option_id, attributes_id, product_id 
                             FROM product_attributes 
                             WHERE product_attribute_id = ?";
    $stmProductAttributes = $_db->prepare($sqlProductAttributes);
    $stmProductAttributes->execute([$productAttributeId]);
    $attributes = $stmProductAttributes->fetch(PDO::FETCH_ASSOC);

    $optionId = $attributes['option_id'];
    $attributesId = $attributes['attributes_id'];
    $productId = $attributes['product_id'];

    // Retrieve product information
    $sqlProduct = "SELECT product_name, product_image 
                   FROM products 
                   WHERE product_id = ?";
    $stmProduct = $_db->prepare($sqlProduct);
    $stmProduct->execute([$productId]);
    $product = $stmProduct->fetch(PDO::FETCH_ASSOC);

    $productName = $product['product_name'];
    $productImage = $product['product_image'];

    // Retrieve option value
    $sqlOption = "SELECT option_value 
                  FROM options 
                  WHERE option_id = ?";
    $stmOption = $_db->prepare($sqlOption);
    $stmOption->execute([$optionId]);
    $option = $stmOption->fetch(PDO::FETCH_ASSOC);
    $optionValue = $option['option_value'];

    // Retrieve attribute type
    $sqlAttributes = "SELECT attributes_type 
                      FROM attributes 
                      WHERE attributes_id = ?";
    $stmAttributes = $_db->prepare($sqlAttributes);
    $stmAttributes->execute([$attributesId]);
    $attribute = $stmAttributes->fetch(PDO::FETCH_ASSOC);
    $attributesType = $attribute['attributes_type'];

    // Store the information
    $productDetails[] = [
        'productName' => $productName,
        'productImage' => $productImage,
        'optionValue' => $optionValue,
        'attributesType' => $attributesType,
        'qty' => $detail['qty'],
        'price' => $detail['order_product_price'],
        'total' => $detail['total']
    ];
}

// Retrieve payment details
$sqlPayment = "SELECT method, payment_status 
               FROM payment 
               WHERE payment_id = ?";
$stmPayment = $_db->prepare($sqlPayment);
$stmPayment->execute([$order['payment_id']]);
$payment = $stmPayment->fetch(PDO::FETCH_ASSOC);

$subtotal_all_products = 0;
?>


<div class="container_order_details">
    <a class="goBack" style="top:-15px;left:-30px;" href="#" id="goBack"> <i class="fa fa-arrow-circle-left"></i> Back</a>
    <div class="order_details_info">
        <h1># Order <?= htmlspecialchars($orderId) ?></h1>

        <!-- Status with icons based on the status -->
        <p>Status :
            <span class="status" style="
        <?php
        if ($order['order_status'] == 'Pending') {
            echo 'color: #ff8c00;'; // Dark orange
        } elseif ($order['order_status'] == 'Packing') {
            echo 'color: #ffd700;'; // Gold
        } elseif ($order['order_status'] == 'Delivered') {
            echo 'color: green;';
        } elseif ($order['order_status'] == 'Delivering') {
            echo 'color: blue;';
        } elseif ($order['order_status'] == 'Cancelled') {
            echo 'color: red;';
        }
        ?>">
                <i class="fas fa-info-circle"></i>
                <?= htmlspecialchars($order['order_status']) ?>
            </span>
        </p>

        <!-- Order Details with icons -->
        <div style="padding: 15px; border: 1px solid #ccc; border-radius: 10px; ">
            <p style="font-size: 18px; font-weight: bold;">
                <i class="fas fa-calendar-day" style="color: #007bff;"></i> Ordered Date:
                <span style="color: #000;"><?= htmlspecialchars($order['order_date']) ?></span>
            </p>

            <p style="font-size: 18px; font-weight: bold;">
                <i class="fas fa-calendar-day" style="color: #007bff;"></i> Created Date:
                <span style="color: #000;"><?= htmlspecialchars($order['order_created_time']) ?></span>
            </p>

            <p style="font-size: 18px; font-weight: bold;">
                <i class="fas fa-truck" style="color: #f0ad4e;"></i> Delivered Date:
                <span style="color: <?= $order['order_delivered_time'] ? '#000' : '#d9534f' ?>;">
                    <?= htmlspecialchars($order['order_delivered_time'] ?? "-") ?>
                </span>
            </p>

            <?php if ($order['order_completed_date']): ?>
                <!-- If order_completed_date exists, show Completed Date -->
                <p style="font-size: 18px; font-weight: bold; color: green;">
                    <i class="fas fa-check-circle" style="color: #5cb85c;"></i> Completed Date:
                    <span style="color: #5cb85c;">
                        <?= htmlspecialchars($order['order_completed_date']) ?>
                    </span>
                </p>
            <?php elseif ($order['order_cancelled_date'] && $order['order_status'] == 'Cancelled'): ?>
                <!-- If order_cancelled_date exists and status is Cancelled, show Cancelled Date -->
                <p style="font-size: 18px; font-weight: bold; color: red;">
                    <i class="fas fa-times-circle" style="color: #d9534f;"></i> Cancelled Date:
                    <span style="color: #d9534f;">
                        <?= htmlspecialchars($order['order_cancelled_date']) ?>
                    </span>
                </p>
            <?php else: ?>
                <!-- Default case if neither completed nor cancelled date exists -->
                <p style="font-size: 18px; font-weight: bold; ">
                    <i class="fas fa-exclamation-circle"></i> Completed Date:
                    <span style="color: #d9534f;">-</span>
                </p>
            <?php endif; ?>


            <?php
            // Ensure both times are valid and complete timestamps before calculating the difference
            $orderCreatedTime = strtotime($order['order_created_time']);
            $orderCompletedDate = strtotime($order['order_completed_date']);

            if ($orderCreatedTime && $orderCompletedDate) {
                // Calculate the difference in seconds and then convert to days
                $durationInSeconds = $orderCompletedDate - $orderCreatedTime;
                $durationInDays = $durationInSeconds / (60 * 60 * 24);

                // Round the result to avoid fractional days
                $durationInDays = round($durationInDays);
            } else {
                // Default to 0 if any of the dates are invalid
                $durationInDays = 0;
            }
            ?>
            <p style="font-size: 18px; font-weight: bold;">
                <i class="fas fa-hourglass-half" style="color: #17a2b8;"></i> Duration Taken:
                <span style="color: #17a2b8;">
                    <?= htmlspecialchars($durationInDays) ?> days
                </span>
            </p>

        </div>

        <!-- Payment Method with icon -->
        <p style="font-size: 18px;">
            <i class="fas fa-credit-card" style="color: #007bff;"></i> Payment Method:
            <?= htmlspecialchars($payment['method']) ?>
        </p>

        <!-- Payment Status with icon based on status -->
        <?php
        $paymentStatus = htmlspecialchars($payment['payment_status']);
        $statusStyle = '';
        $statusIcon = '';

        if ($paymentStatus === 'Pending') {
            $statusStyle = 'color: orange;';
            $statusIcon = '<i class="fas fa-hourglass-start" style="color: orange;"></i>';
        } elseif ($paymentStatus === 'Completed') {
            $statusStyle = 'color: green;';
            $statusIcon = '<i class="fas fa-check-circle" style="color: green;"></i>';
        } elseif ($paymentStatus === 'Cancelled') {
            $statusStyle = 'color: red;';
            $statusIcon = '<i class="fas fa-times-circle" style="color: red;"></i>';
        }
        ?>

        <p style="font-size: 18px;">Payment Status :
            <span style="font-weight:bold;<?= $statusStyle ?>">
                <?= $statusIcon ?> <?= $paymentStatus ?>
            </span>
        </p>

        <!-- Voucher Points Used with icon -->
        <p style="font-size: 18px;">
            <i class="fas fa-gift" style="color: #ff8c00;"></i> Member Points Used:
            <?= htmlspecialchars($order['voucher_points_used']) ?>
        </p>
    </div>

    <div class="addressDetails">

        <div class="receiverAddress">
            <h2>To : <?= $order_member_name ?></h2>
            <p><?= htmlspecialchars($order['address1'] . " ," . $order['address2']) ?></p>
            <p><?= htmlspecialchars($order['city']) ?></p>
            <p><?= htmlspecialchars($order['state']) ?></p>
            <p><?= htmlspecialchars($order['postcode']) ?></p>
        </div>
        <?php
        if ($order['order_status'] != "Pending" && $payment['payment_status'] === "Completed") {
        ?>
            <div style="display: flex;">
                <a class="pending_order" href="view_invoice.php?order_id=<?= urlencode($orderId) ?>" target="_blank">
                    View Invoice
                </a>
                 <!--RON ADD HERE-->
     <div class="review-header">
            <?php if ($order['order_status'] === 'Completed'): ?>
                <?php
                // check the date whether over 14 days after "completed"
                $orderCompletedDate = new DateTime($order['order_completed_date']);
                $currentDate = new DateTime();
                $interval = $currentDate->diff($orderCompletedDate);
                $daysSinceCompletion = $interval->days;
                ?>
                <?php if ($daysSinceCompletion > 14): ?>
                    <p style="color: red; font-weight: bold;">You cannot write a review after 14 days from order completion.</p>
                <?php else: ?>
                    <a class="writeReview" href="addReview.php?order_id=<?= htmlspecialchars($orderId) ?>" id="write-review">WRITE A REVIEW</a>
                <?php endif; ?>
            <?php else: ?>
                <p style="color: red; font-weight: bold;">You can only write a review after the order is completed.</p>
            <?php endif; ?>
        </div>
            </div>

            
        <?php
        }
        ?>


    </div>


</div>

<table class="styled-table-orderDetails">
    <thead>
        <tr>
            <th>#</th>
            <th>Thumbnail</th>
            <th>Product Name</th>
            <th>Attributes</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Total</th>
        </tr>

    </thead>
    <tbody>
        <?php foreach ($productDetails as $index => $detail): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><img src="../uploadsImage/productImage/<?= htmlspecialchars($detail['productImage']) ?>" alt="<?= $detail['productImage'] ?>"></td>
                <td><?= htmlspecialchars($detail['productName']) ?></td>
                <td style="text-transform: capitalize;"><?= htmlspecialchars($detail['attributesType'] . ' - ' . $detail['optionValue']) ?></td>
                <td>x <?= htmlspecialchars($detail['qty']) ?></td>
                <td>RM <?= htmlspecialchars(number_format($detail['price'], 2)) ?></td>
                <td>RM <?= htmlspecialchars(number_format($detail['total'], 2)) ?></td>
                <?php
                $subtotal_all_products += $detail['total'];
                ?>
            </tr>
        <?php endforeach; ?>
    </tbody>

    <tfoot>
        <tr class="row_subtotal">
            <th style="text-align: end;" colspan="5">Qty : <?= $total_qty ?></th>
            <th style="text-align: end;">Subtotal</th>
            <th>RM <?= number_format($subtotal_all_products, 2); ?></th>
        </tr>
    </tfoot>
</table>

<div class="extra_charge">
    <table class="extra_charge_table">
        <tr>
            <th>Discount</th>
            <th>:</th>
            <td>RM <?= number_format($discount, 2) ?></td>
        </tr>

        <tr class="totalAmount">
            <th>Total Amount</th>
            <th>:</th>
            <td>RM <?= number_format($subtotal, 2) ?></td>
        </tr>
    </table>
</div>


<?php include('../_footer.php'); ?>