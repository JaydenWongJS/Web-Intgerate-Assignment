<?php

$title="DisplayProduct";
require('../_base.php');
include('_headerAdmin.php');
include('_sideBar.php');

// Fetch products with their attributes and options
$query = "
  
    SELECT p.product_id, product_name, p.status AS product_status, 
           p.attributes_id AS product_attributes_id, a.attributes_type, o.option_id, 
           o.option_value, pa.price, pa.status AS attribute_status
    FROM products p
    LEFT JOIN product_attributes pa ON p.product_id = pa.product_id AND pa.status = 'available'
    LEFT JOIN attributes a ON p.attributes_id = a.attributes_id
    LEFT JOIN options o ON pa.option_id = o.option_id
    ORDER BY p.product_id, p.attributes_id, o.option_id

";

$stmt = $_db->prepare($query);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group data by product
$products = [];
foreach ($results as $row) {
    $product_id = $row['product_id'];
    $attribute_id = $row['product_attributes_id'];

    if (!isset($products[$product_id])) {
        $products[$product_id] = [
            'product_name' => $row['product_name'],
            'product_status' => $row['product_status'],
            'attributes' => []
        ];
    }

    if ($attribute_id) {
        if (!isset($products[$product_id]['attributes'][$attribute_id])) {
            $products[$product_id]['attributes'][$attribute_id] = [
                'attributes_type' => $row['attributes_type'] ?: '-',
                'options' => []
            ];
        }
        $products[$product_id]['attributes'][$attribute_id]['options'][] = [
            'option_value' => $row['option_value'] ?: '-',
            'price' => $row['price'] ?: '-'
        ];
    }
}
?>

<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Products Attributes</title>
    <link rel="stylesheet" href="path/to/your/css/styles.css">  -->
    <style>
        /* body {
            font-family: Arial, sans-serif;
            margin: 20px;
        } */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        td ul {
    padding-left: 10px;    /* Adds some padding to align the text within the cell */
    margin: 0;             /* Ensures no margin affects the layout */
}
        th {
            background-color: #f2f2f2;
        }
    </style>
<!-- </head>
<body> --> 
<div class="container">
    <h1>Products and Their Attributes</h1>
    <div class="divline"></div>
    <a href="adminProductPage.php" class="btn">Back to Main Page</a><br><br>
    <?php if (!empty($products)): ?>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Status</th>
                    <th>Attribute Type</th>
                    <th>Options</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product_id => $product): ?>
                    <?php if (!empty($product['attributes'])): ?>
                        <?php foreach ($product['attributes'] as $attribute_id => $attribute): ?>
                            <tr>
                                <?php if ($attribute === reset($product['attributes'])): ?>
                                    <td rowspan="<?= count($product['attributes']) ?>">
                                        <?= htmlspecialchars($product['product_name']) ?>
                                    </td>
                                    <td rowspan="<?= count($product['attributes']) ?>">
                                        <?= htmlspecialchars($product['product_status']) ?>
                                    </td>
                                <?php endif; ?>
                                <td><?= htmlspecialchars($attribute['attributes_type']) ?></td>
                                <td>
                                    <ul>
                                        <?php foreach ($attribute['options'] as $option): ?>
                                            <li><?= htmlspecialchars($option['option_value']) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        <?php foreach ($attribute['options'] as $option): ?>
                                            <li><?= htmlspecialchars($option['price']) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </td>
                                <td>
                                    <a href="editProduct.php?productId=<?= $product_id ?>">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td><?= htmlspecialchars($product['product_name']) ?></td>
                            <td><?= htmlspecialchars($product['product_status']) ?></td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td><a href="add_product_attribute.php">Please add attribute type</a> </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="column">
            <a href="addProduct.php" class="btn">Add Products</a>
            <a href="addCategory.php" class="btn">Add Category</a>
            <a href="addAttributes.php" class="btn">Add Attribute Type</a>
            <a href="addOption.php" class="btn">Add Option</a>
        </div>
    <?php else: ?>
        <p>No products found.</p>
    <?php endif; ?>
</div>

<?php include('_footerAdmin.php') ?>
<!-- </body>
</html> -->
