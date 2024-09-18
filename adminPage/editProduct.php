<?php
$title ="Edit Product";
require('../_base.php');
include('_headerAdmin.php');
include('_sideBar.php');?>
<div class="container">
<?php
$product_id = req("productId");

// Fetch product details with product_attribute_id
$query = "
    SELECT p.product_id, p.product_name, p.category_id, c.category_name, 
           p.status as product_status, a.attributes_id, a.attributes_type, o.option_id, 
           o.option_value, pa.price, pa.product_attribute_id, pa.status
    FROM products p
    LEFT JOIN category c ON p.category_id = c.category_id
    LEFT JOIN product_attributes pa ON p.product_id = pa.product_id
    LEFT JOIN attributes a ON pa.attributes_id = a.attributes_id
    LEFT JOIN options o ON pa.option_id = o.option_id
    WHERE p.product_id = ? AND pa.status='available'
    ORDER BY a.attributes_id, o.option_id
";

$stmt = $_db->prepare($query);
$stmt->execute([$product_id]);
$rows = $stmt->fetchAll(PDO::FETCH_OBJ);

// Check if the product was found
if (empty($rows)) {
    // Fetch product name directly if no attributes were found
    $product_query = "SELECT  product_name, status as product_status FROM products WHERE product_id = ?";
    $product_stmt = $_db->prepare($product_query);
    $product_stmt->execute([$product_id]);
    $product = $product_stmt->fetch(PDO::FETCH_OBJ);

    if (!$product) {
        echo "Product not found.";
        exit;
    }

    // Prepare a placeholder for display
    $rows = [
        (object)[
            'product_id' => $product_id,
            'product_name' => $product->product_name,
            'category_id' => null,
            'category_name' => null,
            'product_status' => $product->product_status,
            'attributes_id' => null,
            'attributes_type' => null,
            'option_id' => null,
            'option_value' => null,
            'price' => null,
            'product_attribute_id' => null,
            'status' => null
        ]
    ];
}

// Initialize attributes array
$attributes = [];
$attribute_id = "";

// Process fetched data
foreach ($rows as $row) {
    $attribute_id = $row->attributes_id;
    if ($attribute_id) {
        if (!isset($attributes[$attribute_id])) {
            $attributes[$attribute_id] = (object)[
                'attributes_type' => $row->attributes_type,
                'options' => []
            ];
        }
        $attributes[$attribute_id]->options[] = (object)[
            'product_attribute_id' => $row->product_attribute_id,
            'option_id' => $row->option_id,
            'option_value' => $row->option_value,
            'price' => $row->price,
            'status' => $row->status // Add status to options
        ];
    }
}

// Fetch categories for the dropdown
$category_query = "SELECT category_id, category_name FROM category";
$category_stmt = $_db->prepare($category_query);
$category_stmt->execute();
$categories = $category_stmt->fetchAll(PDO::FETCH_OBJ);

// Retrieve attribute_id from the product table
$product_attribute_query = "SELECT attributes_id FROM products WHERE product_id = ?";
$product_attribute_stmt = $_db->prepare($product_attribute_query);
$product_attribute_stmt->execute([$product_id]);
$product_attribute = $product_attribute_stmt->fetch(PDO::FETCH_OBJ);
$attribute_id = $product_attribute->attributes_id;

// Prepare the SQL query to fetch options based on the attribute ID from the product table
$query = "
    SELECT option_id, option_value
    FROM options
    WHERE attributes_id = ? AND option_id NOT IN (
        SELECT option_id
        FROM product_attributes
        WHERE product_id = ? AND status = 'available'
    )
";

$stmt = $_db->prepare($query);
$stmt->execute([$attribute_id, $product_id]);
$options = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if no options are left to add
$no_options_left = empty($options);

// Handle form submission for product update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_name'])) {
    $product_name = $_POST['product_name'];
    $category_id = $_POST['category_id'];
    $product_status = $_POST['product_status'];

    // Check if attempting to set status to 'active' when there are no attributes or attributes_id is null
    if ($product_status === 'active' && (empty($attributes) || !$attribute_id)) {
        echo "<p class='errorMsg';>Error: Cannot change product status to active. No attributes available or attributes_id is null.</p>";
    } else {
        // Update product name, category, and status
        $update_product_query = "UPDATE products SET product_name = ?, category_id = ?, status = ? WHERE product_id = ?";
        $update_product_stmt = $_db->prepare($update_product_query);
        $update_product_stmt->execute([$product_name, $category_id, $product_status, $product_id]);
       

        // Check if 'options' array is set before processing
        if (isset($_POST['options'])) {
            $options_old = $_POST['options'];
            // Update options and prices based on product_attribute_id
            foreach ($options_old as $product_attribute_id => $option_data) {
                $update_price_query = "UPDATE product_attributes SET price = ?, status = ? WHERE product_attribute_id = ? AND product_id = ?";
                $update_price_stmt = $_db->prepare($update_price_query);
                $update_price_stmt->execute([$option_data['price'], $option_data['status'], $product_attribute_id, $product_id]);
               
            }
        }

        echo "<p class='successfulMsg';>Product details updated successfully.</p>";
        temp("success_edit_message","$product_id has been succesed edited");
        header("Location: editProduct.php?productId=$product_id");
         exit;
    }
}

// Handle form submission for adding a new option
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_option'])) {
    $option_id = $_POST['option_id'];
    $new_option_price = $_POST['new_option_price'];

    // Check if the option already exists but is marked as unavailable
    $check_query = "SELECT product_attribute_id FROM product_attributes WHERE product_id = ? AND option_id = ? AND status = 'unavailable'";
    $check_stmt = $_db->prepare($check_query);
    $check_stmt->execute([$product_id, $option_id]);
    $existing_attribute = $check_stmt->fetch(PDO::FETCH_OBJ);

    if ($existing_attribute) {
        // Update the status to available and update the price
        $update_query = "UPDATE product_attributes SET status = 'available', price = ? WHERE product_attribute_id = ?";
        $update_stmt = $_db->prepare($update_query);
        $update_stmt->execute([$new_option_price, $existing_attribute->product_attribute_id]);
    } else {
        // Insert new option as a product attribute
        $product_attribute_id = getNextIdWithPrefix("product_attributes", "product_attribute_id", "PRA", 2);
        $insert_product_attribute_query = "INSERT INTO product_attributes (product_attribute_id, product_id, attributes_id, option_id, price, status) VALUES (?, ?, ?, ?, ?, 'available')";
        $insert_product_attribute_stmt = $_db->prepare($insert_product_attribute_query);
        $insert_product_attribute_stmt->execute([$product_attribute_id, $product_id, $attribute_id, $option_id, $new_option_price]);
    }

    echo "<p class='successfulMsg';>New option added or restored successfully.</p>";
     header("Location: editProduct.php?productId=$product_id");
     exit;
}
?>




    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        h2{
            margin-top: 5%;
        }
        
        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        
        th {
            background-color: #f2f2f2;
        }
    </style>




<?=temp("success_edit_message")?>

    <h1>Edit Product: <?= htmlspecialchars($rows[0]->product_name) ?></h1>
    <div class="divline"></div>
    <a href="displayProductsAttributes.php" class="btn">Go Back</a><br><br>
    <form method="POST" action="editProduct.php?productId=<?= htmlspecialchars($product_id) ?>">
        <label for="product_name">Product ID:</label>
        <input type="text" id="product_id" name="product_id" value="<?= htmlspecialchars($product_id) ?>" readonly><br><br>

        <label for="product_name">Product Name:</label>
        <input type="text" id="product_name" name="product_name" value="<?= htmlspecialchars($rows[0]->product_name) ?>"><br><br>

        <label for="category_id">Category:</label>
        <select id="category_id" name="category_id">
            <?php foreach ($categories as $category) : ?>
                <option value="<?= $category->category_id ?>" <?= $category->category_id == $rows[0]->category_id ? 'selected' : '' ?>><?= htmlspecialchars($category->category_name) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <!-- New Status Dropdown -->
        <label for="product_status">Product Status:</label>
        <select id="product_status" name="product_status">
            <option value="active" <?= $rows[0]->product_status == 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= $rows[0]->product_status == 'inactive' ? 'selected' : '' ?>>Inactive</option>
        </select><br><br>

        <table>
            <thead>
                <tr>
                    <th>Attribute Type</th>
                    <th>Product Attribute ID</th>
                    <th>Option ID</th>
                    <th>Option Value</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($attributes)) : ?>
                    <tr>
                        <td colspan="6">No attributes available for this product.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($attributes as $attribute_id => $attribute) : ?>
                        <?php foreach ($attribute->options as $option) : ?>
                            <tr>
                                <td><?= htmlspecialchars($attribute->attributes_type) ?></td>
                                <td><?= htmlspecialchars($option->product_attribute_id) ?></td>
                                <td><?= htmlspecialchars($option->option_id) ?></td>
                                <td><?= htmlspecialchars($option->option_value) ?></td>
                                <td>
                                    <input type="text" name="options[<?= $option->product_attribute_id ?>][price]" value="<?= htmlspecialchars($option->price) ?>">
                                </td>
                                <td>
                                    <select name="options[<?= $option->product_attribute_id ?>][status]">
                                        <option value="available" <?= $option->status === 'available' ? 'selected' : '' ?>>Available</option>
                                        <option value="unavailable" <?= $option->status === 'unavailable' ? 'selected' : '' ?>>Unavailable</option>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <input type="submit" class="btn" value="Update Product">
    </form>

    <h2>Add New Option</h2>
    <div class="divline"></div>
    <form method="POST" action="editProduct.php?productId=<?= htmlspecialchars($product_id) ?>">
        <input type="hidden" name="add_option" value="1">
        <?php if ($no_options_left): ?>
            <p>No options can be added. All available options have been added.</p>
        <?php else: ?>
            <label for="option_id">Option:</label>
            <select id="option_id" name="option_id">
                <?php foreach ($options as $option) : ?>
                    <option value="<?= htmlspecialchars($option['option_id']) ?>"><?= htmlspecialchars($option['option_value']) ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="new_option_price">Price:</label><br>
            <input type="text" id="new_option_price" name="new_option_price"><br>

            <input type="submit" class="btn" value="Add Option">
        <?php endif; ?>
       
    </form>
    </div>
    



