<?php
require_once('../_base.php');

$member_id = $_user->member_id;

if (is_post()) {
    $selected_items = req('selected_items') ?? [];
    $quantities = req('quantities') ?? [];
    $action = req('action'); // Capture which button was pressed

    if (empty($selected_items)) {
        temp("empty_selection_info", "Please tick your product !!");
        redirect("cart.php");
        exit();
    }

    switch ($action) {
        case 'update_cart':
            $updated_items = [];
            $failed_items = [];
            $zero_qty_items = [];
            $no_change_items = [];
            // Update the cart
            foreach ($selected_items as $product_attribute_id) {
                if (isset($quantities[$product_attribute_id])) {
                    $new_qty = (int)$quantities[$product_attribute_id];

                    // Fetch the current quantity and product name from the database
                    $query = "SELECT c.qty, p.product_name 
                              FROM cart c
                              JOIN product_attributes pa ON c.product_attributes_id = pa.product_attribute_id
                              JOIN products p ON pa.product_id = p.product_id
                              WHERE c.product_attributes_id = ? AND c.member_id = ?";
                    $stmt = $_db->prepare($query);
                    $stmt->execute([$product_attribute_id, $member_id]);
                    $result = $stmt->fetch(PDO::FETCH_OBJ);

                    $current_qty = $result->qty;
                    $product_name = $result->product_name;

                    $product_descrip = $product_attribute_id . " - " . $product_name;

                    if ($new_qty > 0) {
                        if ($new_qty == $current_qty) {
                            // Quantities are the same, no need to update
                            $no_change_items[] = htmlspecialchars($product_descrip);
                        } else {
                            // Quantities are different, perform the update
                            $update_query = "UPDATE cart SET qty = ? WHERE product_attributes_id = ? AND member_id = ?";
                            $update_stmt = $_db->prepare($update_query);
                            $update_stmt->execute([$new_qty, $product_attribute_id, $member_id]);
                            if ($update_stmt->rowCount()) {

                                $updated_items[] = htmlspecialchars($product_descrip);
                            } else {
                                $failed_items[] = htmlspecialchars($product_descrip);
                            }
                        }
                    } else {
                        $zero_qty_items[] = htmlspecialchars($product_descrip);
                    }
                }
            }

            // Prepare the summary messages
            $update_cart_info = [];
            if (!empty($updated_items)) {
                $update_cart_info[] = "<b class='successInfo'> Update item qty: " . implode(", ", $updated_items) . "</b>";
            }
            if (!empty($no_change_items)) {
                $update_cart_info[] = "No changes made for item(s): <b>" . implode(", ", $no_change_items) . "</b>";
            }
            if (!empty($zero_qty_items)) {
                $update_cart_info[] = "<b class='fail'> Quantity Zero not allowed: " . implode(", ", $zero_qty_items) . "</b>";
            }
            if (!empty($failed_items)) {
                $update_cart_info[] = "<b class='fail'>Failed to update Cart for item(s):" . implode(", ", $failed_items) . "</b>";
            }

            // Store the messages in session
            temp("update_cart", $update_cart_info);

            break;



        case 'delete_cart_items':
            // Delete selected items from the cart
            $deleted_cart_items = [];

            foreach ($selected_items as $product_attribute_id) {
                // First, fetch the product name associated with this attribute
                $fetch_query = "SELECT p.product_name FROM products p 
                                    JOIN product_attributes pa ON p.product_id = pa.product_id 
                                    WHERE pa.product_attribute_id = ?";
                $fetch_stmt = $_db->prepare($fetch_query);
                $fetch_stmt->execute([$product_attribute_id]);
                $product_name = $fetch_stmt->fetchColumn();

                // Now, delete the item from the cart
                $delete_query = "DELETE FROM cart WHERE product_attributes_id = ? AND member_id = ?";
                $delete_stmt = $_db->prepare($delete_query);
                $delete_stmt->execute([$product_attribute_id, $member_id]);

                $product_descrip = $product_attribute_id . " - " . $product_name;

                if ($delete_stmt->rowCount()) {
                    $deleted_cart_items[] = htmlspecialchars($product_descrip);
                }
            }

            // If there are deleted items, prepare the success message
            if (!empty($deleted_cart_items)) {
                $delete_cart_info[] = "<b class='successInfo'>Deleted items: " . implode(", ", $deleted_cart_items) . "</b>";
            }

            // Store the message in session
            temp("update_cart", $delete_cart_info);
            break;

        case 'check_out':
            // Clear previous checkout session data
            $_SESSION['checkout_items'] = [];

            // Initialize a flag to check if all items are available
            $all_items_available = true;
            $unavailable_items = [];
            $zero_quantity_items = [];

            // Store selected items and their quantities in session
            foreach ($selected_items as $product_attribute_id) {
                
                if (isset($quantities[$product_attribute_id])) {
                    $new_qty = (int)$quantities[$product_attribute_id];

                    // Check if quantity is zero
                    if ($new_qty <= 0) {
                        $all_items_available = false;
                        $zero_quantity_items[] = $product_attribute_id;
                        continue; // Skip processing this item further
                    }

                    // Fetch product details including attribute specifications and status
                    $query = "SELECT 
                                    pa.product_attribute_id, 
                                    p.product_id, 
                                    p.product_name, 
                                    p.product_image, 
                                    pa.price,
                                    a.attributes_type,
                                    o.option_value,
                                    p.status as product_status,
                                    pa.status as product_attribute_status
                                  FROM product_attributes pa
                                  JOIN products p ON pa.product_id = p.product_id
                                  JOIN attributes a ON pa.attributes_id = a.attributes_id
                                  JOIN options o ON pa.option_id = o.option_id
                                  WHERE pa.product_attribute_id = ?";

                    $stmt = $_db->prepare($query);
                    $stmt->execute([$product_attribute_id]);
                    $product = $stmt->fetch();

                    // Debug output for fetched product details
                    // echo "<pre>";
                    // echo "Product Details for product_attribute_id {$product_attribute_id}: ";
                    // print_r($product);
                    // echo "</pre>";

                    if ($product) {
                        $product_name=$product->product_name;
                        $product_descrip = $product_attribute_id . " - " . $product_name;

                        // Check product and attribute statuses
                        if ($product->product_status != 'active' || $product->product_attribute_status != 'available') {
                            $all_items_available = false;
                            $unavailable_items[] = htmlspecialchars($product_descrip);
                        } else {
                            // Sanitize input and store item in session if available
                            $cartItem = [
                                'product_attribute_id' => htmlspecialchars($product->product_attribute_id),
                                'product_id' => htmlspecialchars($product->product_id),
                                'product_name' => htmlspecialchars($product->product_name),
                                'qty' => $new_qty,
                                'price' => htmlspecialchars($product->price),
                                'total_price' => $new_qty * $product->price,
                                'image' => htmlspecialchars($product->product_image),
                                'attributes_type' => htmlspecialchars($product->attributes_type),
                                'option_value' => htmlspecialchars($product->option_value)
                            ];

                            // Debug output for cart item data
                            // echo "<pre>";
                            // echo "Cart Item Data: ";
                            // print_r($cartItem);
                            // echo "</pre>";

                            set_cart_item($cartItem);
                        }
                    }
                }
            }

            // Debug output for unavailable and zero quantity items
            // echo "<pre>";
            // echo "Unavailable Items: ";
            // print_r($unavailable_items);
            // echo "</pre>";

            // echo "<pre>";
            // echo "Zero Quantity Items: ";
            // print_r($zero_quantity_items);
            // echo "</pre>";

            // echo "<pre>";
            // echo "All Items Available: ";
            // var_dump($all_items_available);
            // echo "</pre>";

            // Commenting out the redirection for debugging purposes
            if (!$all_items_available || !empty($zero_quantity_items)) {
                $error_message = "<b class='fail'>Unable to proceed with checkout.";

                if (!empty($unavailable_items)) {
                    $error_message .= " The following items are either inactive or unavailable: " . implode(", ", $unavailable_items) . ".<br>";
                }

                if (!empty($zero_quantity_items)) {
                    $error_message .= " Items with zero quantity are not allowed: " . implode(", ", $zero_quantity_items) . ".";
                }

                $error_message .= "</b>";

                temp("check_out", $error_message);

                // Commented out for debugging
                redirect('cart.php');
            }

            // If all items are available, proceed to the checkout page
            // Commented out for debugging
            redirect('../checkout/checkout.php');


            break;

        default:
            temp("update_cart", "Invalid action.");
            break;
    }

    // Redirect back to the cart page after the action
    redirect('cart.php');
    exit();
}
