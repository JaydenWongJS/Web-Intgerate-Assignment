<?php
$title = "Dashboard";
require('../../_base.php');
include('../_headerAdmin.php');
include('../_sideBar.php');

?>
<div class="container">
    <h1>Add New Category</h1>
    <div class="divline"></div>
    <a href="displayProductsAttributes.php" class="btn">Go Back</a>
    <form action="" method="post">

        <div class="column">
            <div class="form-group">
                <label for="category_name" class="form-label">Category Name</label>
                <?= html_text_type("text", "category_name", "class","data-upper"); ?>
                <?= err("category_name", "invalidCategory"); ?>
                <input type="submit" class='btn' value="Add Category">
            </div>

            <!-- <div class="form-group">
                <label for="inputCategoryName" class="form-label">Category Name</label>
                <input type="text" id="inputCategoryName" name="category_name" placeholder="Enter category name">
            </div> -->



        </div>



        

        <!-- The Modal
        <div id="myModal" class="modal">
            <div class="modal-content">
                <div class="close-container">
                    <span class="close">&times;</span>
                </div>
                <p>Are you sure you want to add this category?</p>
                <div class="modal-buttons">
                    <button id="yesBtn" type="button">Yes</button>
                    <button id="noBtn" type="button">No</button>
                </div>
            </div>
        </div> -->

    </form>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // Get the category name from the form
        $category_name = req("category_name");

        if(empty($category_name)){
            $_err["category_name"]="<p>Category name cannot be empty.</p>";
        }
      
        if (!$_err) {
            // Prepare an SQL statement to insert the new category
            $category_id = getNextIdWithPrefix('category', 'category_id', 'c', 2);

            $query = "INSERT INTO category (category_id, category_name) VALUES (?, ?)";
            $stmt = $_db->prepare($query);

            // Execute the statement
            if ($stmt->execute([$category_id, $category_name])) {
                echo "<p>Category added successfully!</p>";
            } else {
                echo "<p>Error adding category: " . $stmt->errorInfo()[2] . "</p>";
            }
        } else {
            echo "<p>Category name cannot be empty.</p>";
        }
    }
    ?>
</div>


<?php include('../_footerAdmin.php') ?>