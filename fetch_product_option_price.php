<?php
require '_base.php';

if (is_post()) {
    $option_id = req('option_id');
    $product_id = req('product_id');

    // Prepare SQL query to fetch price based on the selected option
    if ($option_id&&$product_id) {
        $query = "
        SELECT price
        FROM product_attributes
        WHERE option_id = ? AND product_id=?
    ";
        $stmt = $_db->prepare($query);
        $stmt->execute([$option_id,$product_id]);
        $price = $stmt->fetchColumn();

        if ($price !== false) {
            // Format price and return
            echo "RM " . number_format($price, 2);
        } else {
            echo"$option_id " . ": Option ". "$product_id"." is not available";
            echo "Price not found";
        }
    }else{
        echo "Invalid option ID";
    }

} else {
    echo "Invalid request";
}
