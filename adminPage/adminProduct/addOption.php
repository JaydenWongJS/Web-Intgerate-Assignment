<?php
$title = "Add Option";
require('../../_base.php');
include('../_headerAdmin.php');
include('../_sideBar.php');
?>
<div class="container">
    <h1>Add Options to Attributes</h1>
    <div class="divline"></div>
    <a href="adminProductPage.php" class="btn">Back to Main Page</a>
    <form action="" method="post">
        <div class="column">
            <div class="form-group">
                <label for="attributes_id">Select Attribute:</label>
                <select id="attributes_id" name="attributes_id" required>
                    <?php
                    // Fetch attributes from the database
                    $query = "SELECT attributes_id, attributes_type FROM attributes";
                    $stmt = $_db->prepare($query);
                    $stmt->execute();
                    $attributes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($attributes as $attribute) {
                        echo "<option value=\"{$attribute['attributes_id']}\">{$attribute['attributes_type']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="options_name">Option Name:</label>
                <?= html_text_type("text", "options_name[]", "class","data-upper"); ?>
                <?= err("options_name", "invalidOption"); ?>
            </div>
            <!-- Add more options -->
            <div id="additional_options"></div>
            <button type="button" onclick="addOptionField()">Add Another Option</button>

            <input type="submit" value="Add Options">
        </div>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $attributes_id = req("attributes_id");
        $options_names = req("options_name");

        $errors = [];

        if (empty($attributes_id)) {
            $errors[] = "Attribute must be selected.";
        }

        $options_names = array_map('trim', $options_names); // Trim white spaces
        $unique_options_names = array_unique($options_names);

        if (count($options_names) !== count($unique_options_names)) {
            $errors[] = "Option values must be unique.";
        }

        foreach ($options_names as $index => $option_name) {
            if (empty($option_name)) {
                $errors[] = "Option name at position " . ($index + 1) . " cannot be empty.";
            }
        }

        if (empty($errors)) {

            //run the query
            $query = "INSERT INTO options (option_id, attributes_id, option_value) VALUES (?, ?, ?)";
            $stmt = $_db->prepare($query);

            foreach ($unique_options_names as $option_name) {
                $option_id = getNextIdWithPrefix("options", "option_id", "o", 2);
                if (!$stmt->execute([$option_id, $attributes_id, $option_name])) {
                    echo "<p>Error adding option: " . $stmt->errorInfo()[2] . "</p>";
                } else {
                    echo "<p>Option '{$option_name}' added successfully!</p>";
                }
            }
        } else {
            foreach ($errors as $error) {
                echo "<p>$error</p>";
            }
        }
    }
    ?>
    <script>
        function addOptionField() {
            const container = document.getElementById('additional_options');
            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'options_name[]';
            input.className = 'class';
            container.appendChild(input);
        }
    </script>
<?php include('../_footerAdmin.php') ?>