<?php
require_once("../_base.php");

if (is_post()) {
    $attribute_id = req('attribute_id');
    // Prepare the SQL query to fetch options based on the attribute ID
    $query = "SELECT option_id, option_value FROM options WHERE attributes_id = ?";
    $stmt = $_db->prepare($query);
    $stmt->execute([$attribute_id]);
    $options = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Loop through each option and generate HTML
    foreach ($options as $option) {
        echo "<div class='option-item'>
                <input type='hidden' value=\"{$option['option_id']}\" name='options[]'>
                <span class='option-value'>{$option['option_value']}</span>
                <input type='text' name='price[]'/>
                <button type='button' onclick='removeOptionField(this)'>Remove</button>
              </div>";
    }
}
?>
