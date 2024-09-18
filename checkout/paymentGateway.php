<?php
require('../_base.php');
require('../lib/generateInvoicePdf.php');

// (1) Clean up expired tokens from the database
$_db->query('DELETE FROM token WHERE expire < NOW()'); // Clean expired tokens

if (is_get()) {
    $order_id = req('order_id');
    $payment_id = req('payment_id');
    $payment_duration_token = req('payment_duration_token');


    // (2) Check if the token exists and if it's still valid (not expired)
    $tokenQuery = $_db->prepare('SELECT * FROM token WHERE token_id = ? AND expire > NOW() AND member_id = ?');
    $tokenQuery->execute([$payment_duration_token, $_user->member_id]);

    if ($tokenQuery->rowCount() == 0) {
        // Token is either invalid or expired
        temp('payment_gateway_message', 'Invalid or Expired token. Please try again.');
        redirect("../add_cart/cart.php");
    }
}

// Validate payment method
$paymentMethod = $_POST['paymentMethod'] ?? ''; // Get selected payment method

if (is_post()) {
    $order_id = req('order_id');
    $payment_id = req('payment_id');
    $payment_duration_token = req('payment_duration_token');

    // // (3) Validate token again on form submission (to prevent form resubmission after expiration)
    $tokenQuery = $_db->prepare('SELECT * FROM token WHERE token_id = ? AND expire > NOW()');
    $tokenQuery->execute([$payment_duration_token]);

    if ($tokenQuery->rowCount() == 0) {
        // If token is invalid or expired, redirect the user back to the cart
        temp('payment_gateway_message', 'Your session has expired. Please try again.');
        redirect("../add_cart/cart.php");
        exit;
    }

    // Validate payment method
    $paymentMethod = req("paymentMethod");
    if (!$paymentMethod) {
        $_err["payment"] = "Please select a payment method";
    } else {
        switch ($paymentMethod) {
            case "tng":
            case "grab":
            case "boost":
                $phoneNoEwallet = req("phoneNoEwallet");
                $passwordEwallet = req("passwordEwallet");

                if (empty($phoneNoEwallet)) {
                    $_err['phoneNoEwallet'] = 'E-Wallet Phone Number is required';
                }

                if (empty($passwordEwallet)) {
                    $_err['passwordEwallet'] = 'E-Wallet Password is required';
                }
                break;

            case "maybank":
            case "publicBank":
            case "hongLeongBank":
                $cardName = req("cardName");
                $cardNo = req("cardNo");
                $expiredDate = req("expiredDate");
                $cvc = req("cvc");

                if (empty($cardName)) {
                    $_err['cardName'] = 'Card Name is required';
                }

                if (empty($cardNo)) {
                    $_err['cardNo'] = 'Card Number is required';
                } else if (!preg_match('/^\d{4}\s?\d{4}\s?\d{4}\s?\d{4}$/', $cardNo)) {
                    $_err['cardNo'] = 'Card Number must be 16 digits';
                }

                if (empty($expiredDate)) {
                    $_err['expiredDate'] = 'Expired Date is required';
                } else if (!preg_match('/^(0[1-9]|1[0-2])\/?([0-9]{2})$/', $expiredDate)) {
                    $_err['expiredDate'] = 'Expired Date must be in MM/YY format';
                }

                if (empty($cvc)) {
                    $_err['cvc'] = 'CVC is required';
                } else if (!preg_match('/^\d{3}$/', $cvc)) {
                    $_err['cvc'] = 'CVC must be 3 digits';
                }
                break;
        }
    }

    // If no errors, process payment and update the database
    if (!$_err) {
        // Update payment method and status
        $updatePaymentQuery = "UPDATE payment SET method = :method, payment_status = :status WHERE payment_id = :payment_id";
        $updatePaymentStmt = $_db->prepare($updatePaymentQuery);
        $updatePaymentStmt->execute([
            ':method' => $paymentMethod,
            ':status' => 'Completed', // Simulate 'Completed' status after successful payment
            ':payment_id' => $payment_id
        ]);

        $updateOrderStatusQuery = "UPDATE orders SET order_status = ?, order_created_time = NOW() WHERE order_id = ?";

        $updateOrderStatusStmt = $_db->prepare($updateOrderStatusQuery);
        $updateOrderStatusStmt->execute(['Packing', $order_id]);

        // Fetch order and member details for PDF generation
        $orderAndUserDetails = "SELECT o.order_id, o.order_date, o.voucher_points_used, o.subtotal, m.firstname, m.lastname, m.email, o.address1, o.address2, o.city, o.state, o.postcode
                                FROM orders o
                                JOIN member m ON o.member_id = m.member_id
                                WHERE o.order_id = :order_id";

        $orderStmt = $_db->prepare($orderAndUserDetails);
        $orderStmt->execute([':order_id' => $order_id]);
        $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            // Get the voucher points used and member points
            $voucher_points_used = $order['voucher_points_used'];  // Voucher points used for the order
            $subtotal = $order['subtotal'];

            $getMemberPointsQuery = "SELECT member_points FROM member WHERE member_id = :member_id";
            $memberPointsStmt = $_db->prepare($getMemberPointsQuery);
            $memberPointsStmt->execute([':member_id' => $_user->member_id]);
            $memberPoints = $memberPointsStmt->fetchColumn();

            // Deduct the voucher points from the member's points if any voucher points were used
            if ($voucher_points_used != 0) {
                $memberPoints -= $voucher_points_used;  // Subtract only the used voucher points
            }

            // Calculate points earned (1 point for every $10 spent, for example)
            $pointsEarned = floor($subtotal / 10);  // Points earned based on subtotal

            echo "pointsEarned : $pointsEarned";
            // Add earned points to the member's current points
            $memberPoints += $pointsEarned;
            echo "After added points $memberPoints";

            // Update the member's points in the database
            $updateMemberPointsQuery = "UPDATE member SET member_points = :member_points WHERE member_id = :member_id";
            $updateMemberPointsStmt = $_db->prepare($updateMemberPointsQuery);
            $updateMemberPointsStmt->execute([
                ':member_points' => $memberPoints,
                ':member_id' => $_user->member_id
            ]);


            // Fetch order details
            $orderDetailsQuery = "
            SELECT od.order_id, od.qty, od.order_product_price, od.total,
                   p.product_name, a.attributes_type AS attributes_type, o.option_value
            FROM order_details od
            JOIN product_attributes pa ON od.product_attribute_id = pa.product_attribute_id
            JOIN products p ON pa.product_id = p.product_id
            JOIN attributes a ON pa.attributes_id = a.attributes_id
            JOIN options o ON pa.option_id = o.option_id
            WHERE od.order_id = :order_id";

            $orderDetailsStmt = $_db->prepare($orderDetailsQuery);
            $orderDetailsStmt->execute([':order_id' => $order_id]);
            $orderDetails = $orderDetailsStmt->fetchAll(PDO::FETCH_ASSOC);

            // Extract discount and subtotal
            $discount = $order['voucher_points_used'];
            $discount = (float)$discount / 10;
            echo "Discount: " . $discount . "In order";

            $subtotal = $order['subtotal'];
            $name = $order['firstname'] . " " . $order['lastname'];

            // Generate PDF
            $pdfFilename = 'Invoice_' . $order_id . '.pdf';
            $pdfPath = '../invoice_file/' . $pdfFilename; // Adjust the path

            // Call the function to generate the PDF
            $pdf = generatePDF($name, $order['address1'] . ' ' . $order['address2'], $order['city'], $order['postcode'],$order['state'], $_user->member_id, $order['order_date'], $paymentMethod, $order_id, $orderDetails, $discount, $subtotal);

            // Save PDF to file
            $pdf->Output($pdfPath, 'F'); // 'F' saves the PDF

            // Send Invoice via Email
            sendInvoiceEmail($_user->email, $pdfPath);

            $deletePaymentToken = " DELETE FROM token WHERE token_id = ?;";
            $deletePaymentTokenStmt = $_db->prepare($deletePaymentToken);
            $deletePaymentTokenStmt->execute([$payment_duration_token]);

            if ($deletePaymentTokenStmt->rowCount() == 1) {
                redirect("paymentSuccess.php");
            }

            exit;
        } else {
            echo "Error: Order details not found.";
        }
    } else {
        // If there are errors, reload the payment page with the errors
        redirect("paymentGateway.php?order_id=$order_id&payment_id=$payment_id&payment_duration_token=$payment_duration_token");
    }
}
?>

<?php
$title="Payment Gateway";
include('../_header.php');
?>
<form method="post" action="" class="payment_form">
    <input type="hidden" name="order_id" value="<?= $order_id ?>">
    <input type="hidden" name="payment_id" value="<?= $payment_id ?>">
    <input type="hidden" name="payment_duration_token" value="<?= $payment_duration_token ?>">
   
    <h1>Order Id :<?= $order_id ?></h1>
    <div style="margin-top: 10px;">
        <h2>Payment Method</h2>

        <div>
            <h3>E-Wallet</h3>
        </div>
        <div class="e-wallet-container">
            <?php
            $paymentOptions = [
                ['value' => 'tng', 'label' => 'Touch \'n Go'],
                ['value' => 'grab', 'label' => 'GrabPay'],
                ['value' => 'boost', 'label' => 'Boost']
            ];

            html_radio_payment('paymentMethod', $paymentOptions);
            ?>
        </div>

        <div>
            <h3>Bank</h3>
        </div>

        <div class="bank-container">
            <?php
            $bankOptions = [
                ['value' => 'maybank', 'label' => 'Maybank'],
                ['value' => 'publicBank', 'label' => 'Public Bank'],
                ['value' => 'hongLeongBank', 'label' => 'Hong Leong Bank']
            ];

            html_radio_payment('paymentMethod', $bankOptions);
            ?>
        </div>

        <?= err("payment", ""); ?>

        <fieldset class="fieldset_payment">
            <legend id="payment_method_name"></legend>

            <!-- E-Wallet Fields (PHP checks initial value for page load) -->
            <div id="eWalletFields" style="display: <?= in_array($paymentMethod, ['tng', 'grab', 'boost']) ? 'block' : 'none'; ?>;">
                <div class="check_out_input">
                    <label for="phoneNoEwallet">Phone No:</label>
                    <input type="tel" name="phoneNoEwallet" id="phoneNoEwallet" placeholder="01X-XXXXXXXXX">
                    <?= err('phoneNoEwallet', ''); ?>
                </div>
                <div class="check_out_input">
                    <label for="passwordEwallet">Password:</label>
                    <input type="password" name="passwordEwallet" id="passwordEwallet" placeholder="*********">
                    <?= err('passwordEwallet', ''); ?>
                </div>
            </div>

            <!-- Bank Fields (PHP checks initial value for page load) -->
            <div id="bankFields" style="display: <?= in_array($paymentMethod, ['maybank', 'publicBank', 'hongLeongBank']) ? 'block' : 'none'; ?>;">
                <div class="check_out_input">
                    <label for="cardName">Name On Card:</label>
                    <input type="text" name="cardName" id="cardName" placeholder="XXXX XXXXX">
                    <?= err('cardName', ''); ?>
                </div>
                <div class="check_out_input">
                    <label for="cardNo">Card No:</label>
                    <input type="text" name="cardNo" id="cardNo" placeholder="XXXX XXXX XXXX">
                    <?= err('cardNo', ''); ?>
                </div>
                <div class="check_out_input">
                    <label for="expiredDate">Expired Date:</label>
                    <input type="text" name="expiredDate" id="expiredDate" placeholder="XX/XX">
                    <?= err('expiredDate', ''); ?>
                </div>
                <div class="check_out_input">
                    <label for="cvc">CVC:</label>
                    <input type="text" name="cvc" id="cvc" placeholder="XXX">
                    <?= err('cvc', ''); ?>
                </div>
            </div>

            <input type="submit" name="paid" id="paid" value="Submit">
        </fieldset>
    </div>
</form>


<?php include('../_footer.php'); ?>