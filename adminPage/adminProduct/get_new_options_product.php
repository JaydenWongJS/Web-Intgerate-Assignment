<?php
require_once("../../_base.php");

if (is_post()) {
    $attribute_id = req('attribute_id');
    $product_id = req('product_id');

    // Debugging output
    error_log("Attribute ID: " . $attribute_id);
    error_log("Product ID: " . $product_id);

    // Prepare the SQL query to fetch options based on the attribute ID
    $query = "
        SELECT option_id, option_value
        FROM options
        WHERE attributes_id = ? AND option_id NOT IN (
            SELECT option_id
            FROM product_attributes
            WHERE product_id = ?
        )
    ";

    $stmt = $_db->prepare($query);
    $stmt->execute([$attribute_id, $product_id]);
    $options = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debugging output
    error_log("Options: " . print_r($options, true));

    if (empty($options)) {
        echo "<p>No options available for this attribute.</p>";
    } else {
        // Generate dropdown
        echo "<label for='option_select'>Select Option:</label>
              <select id='option_select' name='option_id'>
                  <option value=''>--Select an option--</option>";
        foreach ($options as $option) {
            echo "<option value='{$option['option_id']}'>{$option['option_value']}</option>";
        }
        echo "</select><br><br>";
        echo '
              <label for="new_option_price">Price:</label>
    <input type="text" id="new_option_price" name="new_option_price" ><br><br>

    <button type="submit" name="add_option">Add Option</button>
        ';
    }
}
