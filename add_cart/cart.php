<?php $title = "Cart" ?>
<?php
require('../_base.php');
include('../_header.php');
include('../nav_bar.php');

auth("member");
clear_cart();

$member_id = $_user->member_id;
$query = "SELECT 
            c.qty,
            pa.product_attribute_id,
            p.product_id,
            p.product_name,
            p.product_image,
            pa.price,
            (c.qty * pa.price) AS total_price,
            a.attributes_type,
            o.option_value,
            p.status as product_status,
            pa.status as product_attribute_status
          FROM 
            cart c
          JOIN 
            product_attributes pa ON c.product_attributes_id = pa.product_attribute_id
          JOIN 
            products p ON pa.product_id = p.product_id
          JOIN 
            attributes a ON pa.attributes_id = a.attributes_id
          JOIN 
            options o ON pa.option_id = o.option_id
          WHERE 
            c.member_id = :member_id";

$stmt = $_db->prepare($query);
$stmt->execute(['member_id' => $member_id]);
$cartItems = $stmt->fetchAll();
ksort($cartItems);
?>

<div id="empty_selection_info"><?= temp("empty_selection_info") ?></div>

<div id="empty_selection_info"><?= temp("check_out") ?></div>

<div id="info"><?php
    $messages = temp("update_cart");
    if ($messages && is_array($messages)) {
        foreach ($messages as $index => $message) {
            echo "<p>" . ($index + 1) . ". " . $message . "</p>";
        }
    }
    ?></div>

<div class="order_title">
    <h1>
        <i class="fas fa-shopping-cart"></i>
        MY CART
    </h1>
</div>

<form action="update_cart.php" method="post" novalidate>

    <table class="styled-table-orderDetails">
        <thead>
            <tr>
                <th><input type="checkbox" name="selectAllItems" value="1" id="selectAllItems" class="selectAllItems" /></th>
                <th>#</th>
                <th>Thumbnail</th>
                <th>Product ID</th>
                <th>Name</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!$cartItems) {
                echo "<tr><td colspan='8'>Cart is empty <a href='../shop.php'>Go To Shop</a></td></tr>";
                exit;
            }
            ?>
            <?php foreach ($cartItems as $index => $item) : ?>
                <tr <?php
                    if ($item->product_status != 'active' || $item->product_attribute_status != 'available') {
                        echo "class='notAvailableProductInCart'";
                    }
                    ?>>
                    <td>
                            <input type="checkbox" name="selected_items[]" value="<?= htmlspecialchars($item->product_attribute_id) ?>" class="checkBoxProduct">
                    </td>
                    <td><?= $index + 1 ?></td>
                    <td><img src="../uploadsImage/productImage/<?= htmlspecialchars($item->product_image) ?>" alt=""></td>
                    <td><?= htmlspecialchars($item->product_attribute_id) ?></td>
                    <td>
                        <p class="product_cart"><?= htmlspecialchars($item->product_name) ?></p>
                        <div class="spec">
                            <small><b><?= htmlspecialchars($item->attributes_type) ?></b> - <?= htmlspecialchars($item->option_value) ?></small>
                        </div>
                        <?php if ($item->product_status != 'active' || $item->product_attribute_status != 'available') : ?>
                            <div class="unavailable-message" style="margin:10px 0px;color: red;font-weight:bold; font-size: 1em;">
                                This product or option is currently unavailable.
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                            <input type="number" name="quantities[<?= htmlspecialchars($item->product_attribute_id) ?>]" value="<?= htmlspecialchars($item->qty) ?>" min="1" style="width: 60px;">
    
                    </td>
                    <td>RM <?= number_format($item->price, 2) ?></td>
                    <td>RM <?= number_format($item->total_price, 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="row_subtotal">
                <th style="text-align: end;" colspan="7">Subtotal</th>
                <th>RM <?= number_format(array_sum(array_map(function ($item) {
                                return $item->total_price;
                            }, $cartItems)), 2) ?></th>
            </tr>
        </tfoot>
    </table>

    <div class="updateCartbox">
        <button class="updateCartButton" type="submit" name="action" value="update_cart">Update Cart</button>
        <button class="deleteCartButton" type="submit" name="action" value="delete_cart_items">Delete</button>
        <button class="proceed_to_checkout_button" type="button" onclick="openModal('checkOutModal')">Proceed To Check Out</button>
    </div>

    <div class="overlay_all" id="checkOutModal" style="display: none;">
        <div class="modal">
            <h3 style="text-align: left;margin-left:10px;color:green;">
                <i class="fas fa-shopping-cart"></i>
                Proceed To Check Out
            </h3>
            <h2 class="comfirmMessage">
                <i style="color: red;" class="fas fa-exclamation-circle"></i>Are You Sure Want To Check Out?
            </h2>
            <button type="button" class="notConfirmUpdate" onclick="closeModal('checkOutModal')">NO</button>
            <button type="button" class="comfirmUpdate" onclick="confirmCheckOut()">Yes</button>
        </div>
    </div>
</form>

<script>
    function confirmCheckOut() {
        // Change the form's action value to 'check_out' before submitting
        const form = document.querySelector('form');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'action';
        input.value = 'check_out';
        form.appendChild(input);

        // Now submit the form
        form.submit();
    }
</script>
<?php include('../_footer.php'); ?>
