<?php
require('_base.php');


$title = "Check Out Page";

//only authorized members can access this page
auth("member");

// Check if there are any cart items; if not, redirect to the index page
// if (get_cart_item() === null || empty(get_cart_item())) {
//     redirect("../index.php");
// }
$member_id = $_user->member_id;

// Retrieve the checkout items from the session
$checkoutItems = get_cart_item();

//get all member details
$memberDetails = retrieveAllValue($member_id, "member", "member_id");
extract((array)$memberDetails);

// Assume $member_points is a value retrieved from the member details
$member_points = $memberDetails->member_points ?? 0; // Retrieve member points
$showAddressForm = false;


$checkPendingOrdersQuery = "
    SELECT SUM(voucher_points_used) as total_used_points 
    FROM orders 
    WHERE member_id = :member_id 
    AND order_status = 'Pending'
    AND voucher_points_used > 0
";
$stmt = $_db->prepare($checkPendingOrdersQuery);
$stmt->execute([':member_id' => $member_id]);
$pendingOrderData = $stmt->fetch(PDO::FETCH_ASSOC);
$pendingUsedPoints = $pendingOrderData['total_used_points'] ?? 0;

$availablePointsForRedemption = $member_points - $pendingUsedPoints;

$member_voucher_points_category = [
    "500" => "Member Points 500 (RM50)",
    "1000" => "Member Points 1000 (RM100)",
    "2000" => "Member Points 2000 (RM200)",
    "5000" => "Member Points 5000 (RM500)"
];

// Filter voucher options based on member points
$availableVouchers = array_filter($member_voucher_points_category, function ($pointsRequired) use ($availablePointsForRedemption) {
    return $pointsRequired <= $availablePointsForRedemption;
}, ARRAY_FILTER_USE_KEY);


// If form is submitted
if (is_post()) {
    $firstname = req("firstname");
    $lastname = req("lastname");
    $email = req("email");
    $phone = req("phone");

    $useAnotherAddress = req("useAnotherAddress");
    $otherAddress1 = req("otherAddress1");
    $otherAddress2 = req("otherAddress2");
    $otherCity = req("otherCity");
    $otherState = req("otherState");
    $otherPostcode = req("otherPostcode");



    // Validate personal information
    if (empty($firstname)) {
        $_err['firstname'] = 'First Name is required';
    }
    if (empty($lastname)) {
        $_err['lastname'] = 'Last Name is required';
    }

    // Validate email
    if (empty($email)) {
        $_err['email'] = 'Email is required';
    } else if (!is_email($email)) {
        $_err['email'] = 'Invalid Email Format';
    }

    // Validate phone
    if (empty($phone)) {
        $_err['phone'] = 'Phone Number is required';
    } else if (!is_malaysia_phone($phone)) {
        $_err['phone'] = 'Phone Number Must Be in Malaysia Format';
    }

    // Validate address if "Ship To Another Address" is checked
    if ($useAnotherAddress) {
        if (empty($otherAddress1)) {
            $_err["otherAddress1"] = "Address 1 is required";
        }
        if (empty($otherAddress2)) {
            $_err["otherAddress2"] = "Address 2 is required";
        }
        if (empty($otherCity)) {
            $_err["otherCity"] = "City is required";
        }
        if (empty($otherState)) {
            $_err["otherState"] = "State is required";
        }
        if (empty($otherPostcode)) {
            $_err["otherPostcode"] = "Postcode is required";
        } else if (!is_postcode($otherPostcode)) {
            $_err["otherPostcode"] = "Postcode must contain only numeric characters";
        }
        $showAddressForm = true;
    }

    if (!$_err) {
        // Determine which address to use
        if (isset($_POST['useAnotherAddress'])) {
            // User selected to use another address
            $address1 = $_POST['otherAddress1'];
            $address2 = $_POST['otherAddress2'];
            $city = $_POST['otherCity'];
            $state = $_POST['otherState'];
            $postcode = $_POST['otherPostcode'];
        } else {
            // Use the default address fetched from the database
            $address1 = $memberDetails->address1;
            $address2 = $memberDetails->address2;
            $city = $memberDetails->city;
            $state = $memberDetails->state;
            $postcode = $memberDetails->postcode;
        }

        // Calculate subtotal
        $subtotal = array_sum(array_column($checkoutItems, 'total_price'));

        // Get voucher points used
        $voucher_points_used = req("voucher_points_used") ? intval(req("voucher_points_used")) : 0;

        // Define voucher values based on points
        $voucher_values = [
            "500" => 50,
            "1000" => 100,
            "2000" => 200,
            "5000" => 500
        ];

        // Calculate discount amount if a voucher is selected
        $discount = 0;
        if ($voucher_points_used && isset($voucher_values[$voucher_points_used])) {
            $discount = $voucher_values[$voucher_points_used];
            $subtotal -= $discount; // Apply discount to subtotal
        }

        // Generate unique IDs for order and payment
        $order_id = getNextIdWithPrefix("orders", "order_id", "O");
        $payment_id = getNextIdWithPrefix("payment", "payment_id", "PAY");

        // Step 1: Insert data into the payment table first
        $payment_status = 'Pending'; // Initial payment status

        $paymentInsertQuery = "INSERT INTO payment (payment_id, method, payment_status) VALUES (:payment_id, :method, :payment_status)";
        $paymentStmt = $_db->prepare($paymentInsertQuery);
        $paymentStmt->execute([
            ':payment_id' => $payment_id,
            ':method' => "-",
            ':payment_status' => $payment_status
        ]);

        // Step 2: Now insert data into the orders table
        $orderStmt = $_db->prepare("INSERT INTO orders (order_id, member_id, total_qty, subtotal, voucher_points_used, payment_id, order_status, address1, address2, city, state, postcode) VALUES (:order_id, :member_id, :total_qty, :subtotal, :voucher_points_used, :payment_id, :order_status, :address1, :address2, :city, :state, :postcode)");

        $orderStmt->execute([
            ':order_id' => $order_id,
            ':member_id' => $member_id,
            ':total_qty' => null, // Assuming total_qty will be updated later or calculated
            ':subtotal' => $subtotal,
            ':voucher_points_used' => $voucher_points_used,
            ':payment_id' => $payment_id,  // Correctly including payment_id here
            ':order_status' => 'Pending', // Default status for unpaid orders
            ':address1' => $address1,
            ':address2' => $address2,
            ':city' => $city,
            ':state' => $state,
            ':postcode' => $postcode
        ]);

        // Step 3: Insert data into the order_details table
        $orderDetailStmt = $_db->prepare("INSERT INTO order_details (order_detail_id, order_id, product_attribute_id, qty, order_product_price, total) VALUES (:order_detail_id, :order_id, :product_attribute_id, :qty, :order_product_price, :total)");

        foreach ($checkoutItems as $item) {
            // Generate new order detail ID
            $order_detail_id = getNextIdWithPrefix("order_details", "order_detail_id", "OD");

            $orderDetailStmt->execute([
                ':order_detail_id' => $order_detail_id,
                ':order_id' => $order_id,
                ':product_attribute_id' => $item['product_attribute_id'], // Ensure this key exists in $checkoutItems
                ':qty' => $item['qty'],
                ':order_product_price' => $item['price'],
                ':total' => $item['price'] * $item['qty']
            ]);
        }

        // Step 4: Update total quantity in the order
        $updateTotalQtyOrderQuery = "UPDATE orders SET total_qty = (SELECT SUM(qty) FROM order_details WHERE order_id = ?) WHERE order_id = ?";
        $stm = $_db->prepare($updateTotalQtyOrderQuery);
        $stm->execute([$order_id, $order_id]);

        // Step 5: Clear the cart
        foreach ($checkoutItems as $item) {
            $clearCartStmt = $_db->prepare("DELETE FROM Cart WHERE product_attributes_id = ? AND member_id = ?");
            $clearCartStmt->execute([$item['product_attribute_id'], $member_id]);
        }


        // Clear session items related to checkout
        unset($_SESSION['checkout_items']);

        // Generate a unique token
        $payment_duration_token = sha1(uniqid() . rand());

        $insertPaymentTokenQuery = '
                 INSERT INTO token (token_id, expire, member_id) 
            VALUES (?, ADDTIME(NOW(), "00:05"), ?);
          ';
        $stmt = $_db->prepare($insertPaymentTokenQuery);
        $stmt->execute([$payment_duration_token, $member_id]);

        if ($stmt->rowCount()) {
            // Redirect to success page
            redirect("paymentGateway.php?order_id=$order_id&payment_id=$payment_id&payment_duration_token=$payment_duration_token");
        } else {
            echo "Unable to create payment";
        }

        exit;
    }
}
?>

<?php
include('_header.php');
?>

<form action="" method="post" class="check_out_container">
    <a class="goBack" style="top:0;" href="#"> <i class="fa fa-arrow-circle-left"></i> Back</a>
    <div class="check_out_info">
        <h2 style="margin-top:10px;">Personal Information</h2>
        <p class="note">*Please note that personal information cannot be changed during checkout.
            If you wish to make changes, please go to your profile to edit.</p>
        <?= html_text_type("hidden", "member_id", ""); ?>
        <div class="check_out_input_form" id="personal_info_input_form">
            <div class="check_out_input">
                <label for="firstName">First Name:</label>
                <?= html_text_type("text", "firstname", "disabled", 'placeholder="First Name" readonly'); ?>
                <?= err("firstname", ""); ?>
            </div>
            <div class="check_out_input">
                <label for="lastName">Last Name:</label>
                <?= html_text_type("text", "lastname", "disabled", 'placeholder="Last Name" readonly'); ?>
                <?= err("lastname", ""); ?>
            </div>
            <div class="check_out_input">
                <label for="email">Email:</label>
                <?= html_text_type("email", "email", "disabled", 'placeholder="Email" readonly'); ?>
                <?= err("email", ""); ?>
            </div>
            <div class="check_out_input">
                <label for="tel">Contact No:</label>
                <?= html_text_type("tel", "phone", "disabled", 'placeholder="Phone" readonly'); ?>
                <?= err("phone", ""); ?>
            </div>
        </div>

        <h2 style="margin:10px 0px 0px 0px;">Address Details</h2>

        <div class="order-summary-address" id="default_address" style="display: <?= $showAddressForm ? 'none' : 'block'; ?>;">
            <p><?= $address1 ?></p>
            <p><?= $address2 ?></p>
            <p><?= $city ?></p>
            <p><?= $state ?></p>
            <p><?= $postcode ?></p>
        </div>

        <div id="addressForm" style="display: <?= $showAddressForm ? 'block' : 'none'; ?>;">
            <div class="check_out_input">
                <label for="address1">Address 1:</label>
                <?= html_text_type("text", "otherAddress1", "", 'placeholder="Address 1" data-upper'); ?>
                <?= err("otherAddress1", ""); ?>
            </div>
            <div class="check_out_input">
                <label for="address2">Address 2:</label>
                <?= html_text_type("text", "otherAddress2", "", 'placeholder="Address 2" data-upper'); ?>
                <?= err("otherAddress2", ""); ?>
            </div>
            <div class="check_out_input">
                <label for="city">City:</label>
                <?= html_text_type("text", "otherCity", "", 'placeholder="City" data-upper '); ?>
                <?= err("otherCity", ""); ?>
            </div>
            <div class="check_out_input">
                <label for="state">State:</label>
                <?= html_text_type("text", "otherState", "", 'placeholder="State" data-upper '); ?>
                <?= err("otherState", ""); ?>
            </div>
            <div class="check_out_input">
                <label for="postcode">Postcode:</label>
                <?= html_text_type("text", "otherPostcode", "", 'placeholder="Postcode" data-upper '); ?>
                <?= err("otherPostcode", ""); ?>
            </div>
        </div>

        <div style="margin:10px 0px 15px 0px;">
            <?= html_checkBox("useAnotherAddress", "onclick='toggleAddressForm()'" . ($showAddressForm ? " checked" : "")); ?>
            <label for="useAnotherAddress">Ship To Another Address</label>
        </div>


    </div>

    <div class="order_info">
        <h1 style="text-align: center;">
            <i class="fas fa-receipt"></i>
            SUMMARY ORDER
        </h1>

        <table class="styled-table-orderSummary">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Thumbnail</th>
                    <th>Product ID</th>
                    <th>Name</th>
                    <th>Specification</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($checkoutItems)): ?>
                    <?php foreach ($checkoutItems as $index => $item): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><img class="img_summary" src="../uploadsImage/productImage/<?= htmlspecialchars($item['image']) ?>" alt="Product Image"></td>
                            <td><?= htmlspecialchars($item['product_id']) ?></td>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td>
                                <b><?= htmlspecialchars($item['attributes_type']) ?></b> - <?= htmlspecialchars($item['option_value']) ?>
                            </td>
                            <td>x <?= htmlspecialchars($item['qty']) ?></td>
                            <td>RM <?= number_format($item['price'], 2) ?></td>
                            <td>RM <?= number_format($item['total_price'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No items in your cart.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="row_total row_subtotal">
                    <th colspan="6">Subtotal</th>
                    <th colspan="2" id="subtotal">RM <?= number_format(array_sum(array_column($checkoutItems, 'total_price')), 2) ?></th>
                </tr>
            </tfoot>
        </table>

        <div class="member_voucher_points_container">
            <?php if (!empty($availableVouchers)): ?>
                <h3 style="margin-bottom: 10px;">Select Voucher:</h3>
                <?= html_select("voucher_points_used", $availableVouchers, "Select Voucher 🧧", "class='styled-select'") ?>
            <?php else: ?>
                <h3 style="margin-bottom: 10px;">No voucher available</h3>
            <?php endif; ?>
        </div>



        <div style="text-align: end;">
            <button type="button" class="checkOutButton" id="checkOutButton" name="change" onclick="openModal('checkOutModal')">Check Out</button>
        </div>

        <div class="overlay_all" id="checkOutModal" style="display: none;">
            <div class="modal">
                <h3 style="text-align: left;margin-left:10px;"><i class="fas fa-cart-arrow-down"></i> Order Will Be Placed</h3>
                <h2 class="comfirmMessage">Are You Sure Want To Place Order?</h2>
                <button type="button" class="notConfirmUpdate" onclick="closeModal('checkOutModal')">NO</button>
                <input type="submit" name="comfirmCheckOut" class="comfirmUpdate" value="Yes" />
            </div>
        </div>
    </div>
</form>

<?php include('_footer.php'); ?>

<!-- JavaScript to handle form interactions -->
<script>
    function toggleAddressForm() {
        var useAnotherAddress = document.getElementById('useAnotherAddress').checked;
        var addressForm = document.getElementById('addressForm');
        if (useAnotherAddress) {
            addressForm.style.display = 'block';
        } else {
            addressForm.style.display = 'none';
        }
    }

    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'block';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }
</script>