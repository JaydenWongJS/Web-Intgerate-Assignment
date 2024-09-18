<?php
$title = "AddProduct";
require('../_base.php');
include('_headerAdmin.php');
include('_sideBar.php');

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = trim(req('product_name'));
    $description = trim(req('description'));
    $category_id = req('category_id');

    // Validation
    if (empty($product_name)) {
        $errors[] = "Product name is required.";
    }

    if (empty($description)) {
        $errors[] = "Description is required.";
    }

    if (empty($category_id)) {
        $errors[] = "Category is required.";
    }

    if (empty($errors)) {
        try {
            // Insert product data with status set to 'inactive'
            $product_id = getNextIdWithPrefix("products", "product_id", "PR", 2);
            $product_query = "INSERT INTO products (product_id, product_name, description, category_id, status) VALUES (?, ?, ?, ?, 'inactive')";
            $product_stmt = $_db->prepare($product_query);
            $product_stmt->execute([$product_id, $product_name, $description, $category_id]);
            $success = true;

        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

    // Output errors or success message
    if ($success) {
        echo "Product added successfully!";
    } else {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
}

// Fetch categories from the database for the form
$query = "SELECT category_id, category_name FROM category";
$stmt = $_db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    body {
        font-family: Arial, sans-serif;
    }

    form {
        width: 300px;
        margin: 0 auto;
    } */

   label,
    input,
    select,
    textarea,
    button {
        display: block;
        width: 100%;
        margin-bottom: 10px;
    } 

    .attribute {
        border: 1px solid #ccc;
        padding: 10px;
        margin-bottom: 10px;
    }

    .options-container {
        display: flex;
        flex-direction: column;
    }

    .option-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>
<div class="container">
    <h1>Add New Product</h1>
    <div class="divline"></div>
    <a href="adminProductPage.php" class="btn">Back to Main Page</a>
    <form id="addProductForm" action="addProduct.php" method="post">
    <?php 
                 if ($success) {
                    echo "Product added successfully!";
                } else {
                    foreach ($errors as $error) {
                        echo "<p style='color:red;'>$error</p>";
                    }
                }
                ?>
        <div class="column">
            <div class="form-group">
                <label for="product_name">Product Name</label>
                <?=html_text_type("text", "product_name","class","data-upper")?>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <?=html_textarea("description", 4,50,"data-upper");?>
            </div>

            <div class="form-group">
                <label for="category_id">Category:</label>
                <select id="category_id" name="category_id">
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?= $category['category_id'] ?>"><?= $category['category_name'] ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" class='btn' name="addProductSubmit" value="Add Products">
                <a href="add_product_attribute.php" class="btn">Add Attributes</a>
            </div>
    </form>
</div>

<?php include('_footerAdmin.php') ?>