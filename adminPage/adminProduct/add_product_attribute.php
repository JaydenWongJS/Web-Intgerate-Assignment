<?php
$title = "Add or Update Product Attributes";
require('../../_base.php');
include('../_headerAdmin.php');
include('../_sideBar.php');?>
<div class="container"><?php
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = trim(req('product_id'));
    $attribute_id = trim(req('attributes'));  // Single attribute
    $options = req('options');
    $prices = req('price');

    // Validation
    if (empty($product_id)) {
        $errors[] = "<p class='errorMsg';>Product is required.</p>";
    }
    if (empty($attribute_id)) {
        $errors[] = "<p class='errorMsg';>Attribute is required.</p>";
    }
    if (empty($options) || !is_array($options) || count($options) === 0) {
        $errors[] = "<p class='errorMsg';>At least one option must be selected.</p>";
    }
    if (empty($prices) || !is_array($prices) || count($prices) !== count($options)) {
        $errors[] = "<p class='errorMsg';>Price is required for each option.</p>";
    } else {
        foreach ($prices as $price) {
            if (trim($price) === '') {
                $errors[] = "<p class='errorMsg';>All price fields must be filled.</p>";
                break;
            }
        }
    }

    if (empty($errors)) {
        try {
            // Check if the product already has an attribute assigned
            $check_product_query = "SELECT attributes_id FROM products WHERE product_id = ?";
            $check_product_stmt = $_db->prepare($check_product_query);
            $check_product_stmt->execute([$product_id]);
            $existing_attribute_id = $check_product_stmt->fetchColumn();

            if ($existing_attribute_id !== null && $existing_attribute_id != $attribute_id) {
                // If an attribute already exists and is different from the one being added, throw an error
                $errors[] = "<p class='errorMsg'; >This product already has a different attribute type assigned.</p>";
            } else {
                foreach ($options as $index => $option_id) {
                    $price = isset($prices[$index]) ? trim($prices[$index]) : '';

                    // Check if the product attribute already exists
                    $check_query = "SELECT product_attribute_id FROM product_attributes WHERE product_id = ? AND attributes_id = ? AND option_id = ?";
                    $check_stmt = $_db->prepare($check_query);
                    $check_stmt->execute([$product_id, $attribute_id, $option_id]);
                    $existing_product_attribute_id = $check_stmt->fetchColumn();

                    if ($existing_product_attribute_id) {
                        // Update the existing product attribute's price
                        $update_query = "UPDATE product_attributes SET price = ?, status = 'available' WHERE product_attribute_id = ?";
                        $update_stmt = $_db->prepare($update_query);
                        $update_stmt->execute([$price, $existing_product_attribute_id]);
                        // echo "Updated: [$existing_product_attribute_id, $product_id, $attribute_id, $option_id, $price]\n";
                        echo "<p class='successfulMsg';>Updated Price successfully</p>";
                    } else {
                        // Insert a new product attribute
                        $product_attribute_id = getNextIdWithPrefix("product_attributes", "product_attribute_id", "PRA", 2);
                        $insert_query = "INSERT INTO product_attributes (product_attribute_id, product_id, attributes_id, option_id, price, status) VALUES (?, ?, ?, ?, ?, 'available')";
                        $insert_stmt = $_db->prepare($insert_query);
                        $insert_stmt->execute([$product_attribute_id, $product_id, $attribute_id, $option_id, $price]);
                        // echo "Inserted: [$product_attribute_id, $product_id, $attribute_id, $option_id, $price, 'available']\n";
                        echo "<p class='successfulMsg'; >Successfully inserted new product attribute type</p>";
                    }
                }

                // If no attribute was previously assigned, update the products table to include this attribute
                if ($existing_attribute_id === null) {
                    $update_product_query = "UPDATE products SET attributes_id = ? WHERE product_id = ?";
                    $update_product_stmt = $_db->prepare($update_product_query);
                    $update_product_stmt->execute([$attribute_id, $product_id]);
                }

                $success = true;
            }

        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

    // Output errors or success message
    if ($success) {
         "<p class='successfulMsg'; >Product Attributes added/updated successfully!</p>";
    } else {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
}

// Fetch products and attributes from the database for the form
$query = "SELECT product_id, product_name FROM products";
$stmt = $_db->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$query = "SELECT attributes_id, attributes_type FROM attributes";
$stmt = $_db->prepare($query);
$stmt->execute();
$attributes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
    li{
        margin: 0 0 5px 20px ;
    }
    
</style>


    <a href="adminProductPage.php" class="btn">Back to Main Page</a>
    <form id="addProductAttributesForm" action="add_product_attribute.php" method="post">
        <div class="column">
            <label for="product_id">Product:</label>
            <select id="product_id" name="product_id">
                <?php foreach ($products as $product) : ?>
                    <option value="<?= $product['product_id'] ?>"><?= $product['product_name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="attributes-container">
            <h3>Attributes</h3>
            <div class="attribute-form">
                <div class="attribute" data-index="1">
                    <label for="attributes_id_1">Attribute Type:</label>
                    <select class="attributes_id" name="attributes" onchange="loadOptions(this, 1)">
                        <option value="">Select Attribute</option>
                        <?php foreach ($attributes as $attribute) : ?>
                            <option value="<?= $attribute['attributes_id'] ?>"><?= $attribute['attributes_type'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="options-container" id="options_container_1">
                        <!-- Options will be loaded dynamically based on the selected attribute type -->
                    </div>
                </div>
            </div>
            <input type="submit" class="btn" value="Add Product Attributes">
        </div>

        <div id="attributes-container">
            <div class="removed-options" id="removedOptionsContainer">
                <h4>Removed Options</h4>
                <ul id="removedOptionsList"></ul>
            </div>
        </div>
    </form>

    <script>
        let removedOptions = [];

        function removeOptionField(button) {
            const optionItem = button.closest('.option-item');
            const optionId = optionItem.querySelector('input[name="options[]"]').value;
            const optionValue = optionItem.querySelector('.option-value').textContent.trim();
            const optionHtml = optionItem.outerHTML;

            removedOptions.push({
                optionId,
                optionValue,
                html: optionHtml
            });
            optionItem.remove();

            displayRemovedOptions();
        }

        function restoreOption(optionId) {
            const option = removedOptions.find(opt => opt.optionId === optionId);
            if (option) {
                document.querySelector('.options-container').innerHTML += option.html;
                removedOptions = removedOptions.filter(opt => opt.optionId !== optionId);
                displayRemovedOptions();
            }
        }

        function displayRemovedOptions() {
            const list = document.getElementById('removedOptionsList');
            list.innerHTML = '';

            removedOptions.forEach(option => {
                const listItem = document.createElement('li');
                listItem.innerHTML = `
                    ${option.optionValue} 
                    <button type="button" onclick="restoreOption('${option.optionId}')" class="restore-btn">Restore</button>
                `;
                list.appendChild(listItem);
            });
        }

        function clearRemovedOptions() {
            removedOptions = [];
            displayRemovedOptions();
        }

        function loadOptions(selectElement, index) {
            const attribute_id = selectElement.value;
            const attributeContainer = document.getElementById(`options_container_${index}`);

            if (attribute_id) {
                $.ajax({
                    url: 'get-options.php',
                    method: 'POST',
                    data: {
                        attribute_id: attribute_id
                    },
                    success: function(data) {
                        attributeContainer.innerHTML = data;

                        // Add this line to add the 'remove-btn' class to the remove button
                        attributeContainer.querySelectorAll('.option-item button').forEach(button => {
                            button.classList.add('remove-btn');
                        });

                        clearRemovedOptions(); // Clear removed options when loading new options
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching data:", status, error);
                    }
                });
            } else {
                attributeContainer.innerHTML = '';
            }
        }

        document.querySelectorAll('.attributes_id').forEach((selectElement) => {
            selectElement.addEventListener('change', function() {
                const index = this.closest('.attribute').dataset.index;
                loadOptions(this, index);
            });
        });

        document.getElementById('product_id').addEventListener('change', function() {
            clearRemovedOptions(); // Clear removed options when product changes
        });

        function validateForm() {
            let isValid = true;
            const optionFields = document.querySelectorAll('.option-item');
            optionFields.forEach(field => {
                const priceInput = field.querySelector('input[name="price[]"]');
                if (!priceInput || priceInput.value.trim() === '') {
                    isValid = false;
                    priceInput.style.borderColor = 'red';
                } else {
                    priceInput.style.borderColor = '';
                }
            });

            if (!isValid) {
                alert('Please fill in the price for all options.');
            }
            return isValid;
        }

        document.getElementById('addProductAttributesForm').addEventListener('submit', function(event) {
            if (!validateForm()) {
                event.preventDefault();
            }
        });
    </script>
</div>

<?php include('/../_footerAdmin.php') ?>
