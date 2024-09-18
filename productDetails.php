<?php

require('_base.php');

// Get the product ID from the URL

if (isset($_user)) {
    $member_id = $_user->member_id;
}

if (is_get()) {
    //for refresh and insert into cart puroses
    $product_id = req('product_id');

    $product = fetchProductDetails($product_id);

    if (!$product) {
        redirect("shop.php");
    }

    $attr_result = fetchProductAttributes($product_id);

    // Ensure $attr_result is not empty and the property exists
    if (!empty($attr_result) && isset($attr_result[0]->attributes_id)) {
        $attribute_id = $attr_result[0]->attributes_id;
    } else {
        echo "<p>Attribute ID not found!</p>";
    }


    // Prepare the price range data
    $product->price_range = [
        'min' => min(array_column($attr_result, 'price')),
        'max' => max(array_column($attr_result, 'price'))
    ];

    // Display errors if any
    if (isset($_SESSION['errors'])) {
        $_err = $_SESSION['errors'];
        unset($_SESSION['errors']); // Clear the errors after displaying them
    }

    // Display errors if any
    if (isset($_SESSION['option'])) {
        $option = $_SESSION['option'];
        unset($_SESSION['option']); // Clear the errors after displaying them
    }
} else {
    echo "Not get method";
}

if (is_post()) {
    $product_id = req('product_id');
    $attribute_id = req('attribute_id');
    $option = req('option');
    $qty = req("qty");

    if ($_user == null) {
        temp("login_required_info", "Login are required to continue add to cart !");
        redirect("productDetails.php?product_id=" . $product_id);
    } else if ($_user->role == 'admin') {
        temp("login_required_info", "<b class='fail'>Admin are not able to purchase products ! </b>");
        redirect("productDetails.php?product_id=" . $product_id);
    }
    // Output the values for debugging
    echo "<p>Product ID: " . htmlspecialchars($product_id) . "</p>";
    echo "<p>Attribute ID: " . htmlspecialchars($attribute_id) . "</p>";
    echo "<p>Option: " . htmlspecialchars($option) . "</p>";
    echo "<p>Quantity: " . htmlspecialchars($qty) . "</p>";

    if (empty($option)) {
        $_err["option"] = "No option Selected";
    } else {
        $_SESSION['option'] = $option;
    }

    if (empty($qty)) {
        $_err["qty"] = "Quantity is required.";
    } else if ($qty < 1 || $qty > 10) {
        $_err["qty"] = "Invalid quantity. Please enter a number between 1 and 10.";
    }

    if (empty($_err)) {
        echo "No error occurred";

        $query = "SELECT product_attribute_id FROM product_attributes WHERE product_id = ? AND attributes_id = ? AND option_id = ?";
        $getProductAttributeIdStmt = $_db->prepare($query);
        $getProductAttributeIdStmt->execute([$product_id, $attribute_id, $option]);


        if ($getProductAttributeIdStmt->rowCount() > 0) {
            $productAttributeId = $getProductAttributeIdStmt->fetchColumn();
            echo "Product attribute id is: " . $productAttributeId;

            // Check if the product is already in the cart for the current user
            $query = "SELECT qty FROM cart WHERE product_attributes_id = ? AND member_id = ?";
            $stmtCheckExistsCartUser = $_db->prepare($query);
            $stmtCheckExistsCartUser->execute([$productAttributeId, $member_id]);

            if ($stmtCheckExistsCartUser->rowCount() > 0) {
                // Product already exists in the cart, update the quantity
                $currentQtyInCart = $stmtCheckExistsCartUser->fetchColumn();
                $currentQtyInCart += $qty;
                $updateCartQuery = "UPDATE cart SET qty = ? WHERE product_attributes_id = ? AND member_id = ?";
                $stmtUpdateCartQty = $_db->prepare($updateCartQuery);
                $stmtUpdateCartQty->execute([$currentQtyInCart, $productAttributeId, $member_id]);
                temp("add_cart_info", "Exists product : $productAttributeId qty has been increase in cart. <a style='color:white;' href='add_cart/cart.php'>View Cart</a> .");
                redirect("/shop.php");
            } else {
                // Product does not exist in the cart, insert a new row
                $query = "INSERT INTO cart (product_attributes_id, member_id, qty) VALUES (?, ?, ?)";
                $stmt = $_db->prepare($query);
                $stmt->execute([$productAttributeId, $member_id, $qty]);
                if ($stmt->rowCount() > 0) {
                    temp("add_cart_info", "$productAttributeId has been added to cart. <a style='color:white;' href='add_cart/cart.php'>View Cart</a> .");
                    redirect("/shop.php");
                } else {
                    echo "Error adding product to cart: " . $stmt->errorInfo()[2];
                }
            }

            unset($_SESSION['option']);
        } else {
            echo "No matching product attribute found.";
        }
    } else {
        $_SESSION['errors'] = $_err;
        redirect("productDetails.php?product_id=" . $product_id);
    }
}
?>

<?php
$title = "$product->product_name";
include('_header.php');
include('nav_bar.php');
?>
<div id="info"><?= temp("login_required_info"); ?></div>
<div class="product-container">
    <div class="product-image">
        <img src="uploadsImage/productImage/<?= htmlspecialchars($product->product_image); ?>" alt="Product Image">
    </div>
    <form class="product-details" action="productDetails.php" method="post" novalidate>
        <?= html_text_type("hidden", "product_id", " "); ?>

        <h1><?= htmlspecialchars($product->product_name); ?></h1>
        <div>
            <div class="rating">
                <?php for ($i = 0; $i < floor($product->total_rating); $i++): ?>
                    <i class="fas fa-star"></i>
                <?php endfor; ?>
            </div>
            <p class="category"><?= htmlspecialchars($product->category_name); ?></p>
            <p class="price" id="display_price"><?= getProductRangePrice((array)$product); ?></p>
        </div>
        <div class="options">
            <?= html_text_type("hidden", "attribute_id", " "); ?>
            <label for="" class="spec"><?= htmlspecialchars($attr_result[0]->attributes_type); ?></label>
            <div class="option_box">
                <!-- <?php foreach ($attr_result as $attribute): ?>
                    <input type="radio" class="input-radio" id="<?= htmlspecialchars($attribute->option_id); ?>" name="option" value="<?= htmlspecialchars($attribute->option_id); ?>">
                    <label class="option" for="<?= htmlspecialchars($attribute->option_id); ?>"><?= htmlspecialchars($attribute->option_value); ?></label>
                <?php endforeach; ?> -->
                <?= html_radios_attr("option", $attr_result); ?>
                <?= err("option", " "); ?>
            </div>
        </div>
        <div class="clearAndQty_box">
            <div>
                <small class="clear-btn">Clear</small>
            </div>
            <div class="qty_input">
                <label for="qty">QTY:</label>
                <?= html_number("qty", '1', '10', '1', ''); ?>
                <?= err("qty", " "); ?>
            </div>
        </div>
        <div style="text-align: center;">
            <button type="submit" id="add-to-cart">ADD TO CART</button>
        </div>
    </form>
</div>

<div class="product-container">
    <div class="tab-container">
        <div class="tabs">
            <button class="tab-link active_tab" data-tab="short">Description</button>
            <button class="tab-link" data-tab="medium">Use Case</button>
            <button class="tab-link" data-tab="long">Spec</button>
        </div>
        <div class="tab-content active_tab" id="short">
            <h2>Description</h2>
            <p><?= htmlspecialchars($product->description); ?></p>
        </div>
        <div class="tab-content" id="medium">
            <h2>Usage Scenarios and Case Studies</h2>
            <p><?= htmlspecialchars($product->use_case); ?></p>
        </div>
        <div class="tab-content" id="long">
            <h2>Spec</h2>
            <p><?= htmlspecialchars($product->spec); ?></p>
        </div>
    </div>



</div>
<div class="review-container">
    <div class="review-header">
        <a class="writeReview" href="addReview.php" id="write-review">WRITE A REVIEW</a>
    </div>

    <div class="review_comment_container">

        <div class="reviews_comment">
            <div class="review-item">
                <div class="review-user">
                    <img src="https://via.placeholder.com/50x50" alt="User Image" height="50" width="50">
                    <span class="user-name">User Name</span>
                </div>
                <div class="review-content">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed posuere, ipsum eget fermentum luctus, justo eros viverra lectus, sed consectetur ligula nunc ac risus. Donec tristique, mauris at imperdiet dignissim, justo erat pulvinar lectus, ac faucibus felis velit nec velit.</p>
                    <div class="review-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i> <!-- Assuming a 5-star rating system -->
                    </div>
                </div>

            </div>
            <div class="seller_reply">
                <p>jsjdssjdosjd</p>
            </div>

        </div>

        <div class="reviews_comment">
            <div class="review-item">
                <div class="review-user">
                    <img src="https://via.placeholder.com/50x50" alt="User Image" height="50" width="50">
                    <span class="user-name">User Name</span>
                </div>
                <div class="review-content">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed posuere, ipsum eget fermentum luctus, justo eros viverra lectus, sed consectetur ligula nunc ac risus. Donec tristique, mauris at imperdiet dignissim, justo erat pulvinar lectus, ac faucibus felis velit nec velit.</p>
                    <div class="review-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i> <!-- Assuming a 5-star rating system -->
                    </div>
                </div>
            </div>
        </div>



    </div>
</div>



<script>
    $(document).ready(function() {
        var initialPriceRange = $('#display_price').html();

        function updatePrice() {
            var selectedOptionId = $('input[type="radio"][name="option"]:checked').val();
            var productId = <?= json_encode($product_id); ?>;

            if (selectedOptionId) {
                $.ajax({
                    url: 'fetch_product_option_price.php',
                    method: 'POST',
                    data: {
                        option_id: selectedOptionId,
                        product_id: productId
                    },
                    success: function(response) {
                        $('#display_price').html(response);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX error:', textStatus, errorThrown);
                    }
                });
            }
        }

        function resetPrice() {
            $('.input-radio:checked').prop('checked', false);
            $('#display_price').html(initialPriceRange);
        }

        function activateTab(tabId) {
            $('.tab-link').removeClass('active_tab');
            $('[data-tab="' + tabId + '"]').addClass('active_tab');
            $('.tab-content').removeClass('active_tab');
            $('#' + tabId).addClass('active_tab');
        }

        // Bind change event to update price when an option is selected
        $('input[type="radio"][name="option"]').on('change', updatePrice);

        // Bind click event to reset price when 'Clear' button is clicked
        $('.clear-btn').on('click', resetPrice);

        // Bind click event to activate tabs
        $('.tab-link').on('click', function() {
            activateTab($(this).data('tab'));
        });

        // Ensure the price is updated based on the selected option when the page loads
        updatePrice();
    });
</script>


<?php include('_footer.php'); ?>