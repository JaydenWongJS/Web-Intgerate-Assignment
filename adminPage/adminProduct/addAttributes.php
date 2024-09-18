<?php
require('../../_base.php');
include('../_headerAdmin.php');
include('../_sideBar.php');

?>

<div class="container">
    <h1>Add New Attributes</h1>
    <div class="divline"></div>
    <a href="adminProductPage.php" class="btn">Back to Main Page</a>
    <form action="" method="post">
    <div class="column">
        <label for="attributes_name">Attributes Name:</label>
        <?= html_text_type("text","attributes_name","class","data-upper");?>
        <?= err("attributes_name","invalidAttributes"); ?>
        <input type="submit" value="Add Attributes">
    </div>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // Get the category name from the form
        $attributes_name = req("attributes_name");

        if(empty($attributes_name)){
            $_err["attributes_name"]="<p>attributes name cannot be empty.</p>";
        }
      
        if (!$_err) {
            // Prepare an SQL statement to insert the new category
            $attributes_id = getNextIdWithPrefix('attributes', 'attributes_id', 'at', 2);

            $query = "INSERT INTO attributes (attributes_id, attributes_type) VALUES (?, ?)";
            $stmt = $_db->prepare($query);

            // Execute the statement
            if ($stmt->execute([$attributes_id, $attributes_name])) {
                echo "<p>atributes added successfully!</p>";
            } else {
                echo "<p>Error adding attributes: " . $stmt->errorInfo()[2] . "</p>";
            }
        } else {
            echo "<p>atributes name cannot be empty.</p>";
        }
    }
    ?></div>
 <?php include('../_footerAdmin.php') ?>

