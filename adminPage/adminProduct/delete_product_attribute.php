<?php
require('../../_base.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $delete_option_id = $_POST['delete_option_id'];
    $product_id = $_POST['product_id'];

    // Delete the option from the database
    $delete_option_query = "DELETE FROM options WHERE option_id = ?";
    $delete_option_stmt = $_db->prepare($delete_option_query);
    $delete_option_stmt->execute([$delete_option_id]);

    // Delete the product attribute associated with this option
    $delete_product_attribute_query = "DELETE FROM product_attributes WHERE option_id = ? AND product_id = ?";
    $delete_product_attribute_stmt = $_db->prepare($delete_product_attribute_query);
    $delete_product_attribute_stmt->execute([$delete_option_id, $product_id]);

    echo "Option deleted successfully.";
    // Redirect to the edit page
    // header("Location: editProduct.php?productId=$product_id");
    exit;
}
?>
