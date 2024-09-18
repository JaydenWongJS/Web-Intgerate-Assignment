<?php

// ============================================================================
// PHP Setups
// ============================================================================

date_default_timezone_set('Asia/Kuala_Lumpur');
session_start();
// TODO

// ============================================================================
// General Page Functions
// ============================================================================

// Is GET request?
function is_get()
{
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

// Is POST request?
function is_post()
{
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

// Obtain GET parameter
function get($key, $value = null)
{
    $value = $_GET[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain POST parameter
function post($key, $value = null)
{
    $value = $_POST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain REQUEST (GET and POST) parameter
function req($key, $value = null)
{
    $value = $_REQUEST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Redirect to URL
function redirect($url = null)
{
    $url ??= $_SERVER['REQUEST_URI'];
    header("Location: $url");
    exit();
}


// Set or get temporary session variable
function temp($key, $value = null)
{
    if ($value !== null) {
        $_SESSION["temp_$key"] = $value;
    } else {
        $value = $_SESSION["temp_$key"] ?? null;
        unset($_SESSION["temp_$key"]);
        return $value;
    }
}

// Obtain uploaded file --> cast to object
function get_file($key)
{
    $f = $_FILES[$key] ?? null;

    if ($f && $f['error'] == 0) {
        return (object)$f;
    }

    return null;
}

// Crop, resize and save photo
function save_photo($f, $folder, $width = 200, $height = 200)
{
    $photo = uniqid() . '.jpg';

    require_once 'lib/SimpleImage.php';
    $img = new SimpleImage();
    $img->fromFile($f->tmp_name)
        ->thumbnail($width, $height)
        ->toFile("$folder/$photo", 'image/jpeg');

    return $photo;
}

// Is money?
function is_money($value)
{
    return preg_match('/^\-?\d+(\.\d{1,2})?$/', $value);
}

// Is email?
function is_email($value)
{
    return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
}

function is_malaysia_phone($value)
{
    $phonePattern = "/^01[0-46-9]\d{7,8}$|^0\d{1,2}\d{7,8}$/";
    return preg_match($phonePattern, $value) === 1;
}

function is_character_string($value)
{
    $namePattern = "/^[A-Za-z\s]+$/";
    return preg_match($namePattern, $value) === 1;
}

function is_postcode($value)
{
    $postcodePattern = "/^\d{5}$/"; // Malaysian postcodes are typically 5 digits
    return preg_match($postcodePattern, $value) === 1;
}


// ============================================================================
// HTML Helpers
// ============================================================================

// Encode HTML special characters
function encode($value)
{
    return htmlentities($value);
}


// Generate <input type='text'>
function html_text_type($type, $key, $class_style, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? "");
    echo "<input type='$type' class='$class_style' id='$key' name='$key' value='$value' $attr/>";
}


function html_text($key, $attr = '', $value = '', $class = '', $style = '')
{
    $value = encode($value ?: ($GLOBALS[$key] ?? ''));
    $class_attr = $class ? "class='$class'" : '';
    $style_attr = $style ? "style='$style'" : '';
    echo "<input type='text' id='$key' name='$key' value='$value' $attr $class_attr $style_attr>";
}



// Generate <input type='text'>
function html_textarea($key, $rows, $cols, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? "");
    echo "<textarea id='$key' name='$key' rows='$rows' cols='$cols' value='$value' $attr></textarea>";
}

// Generate <input type='number'>
function html_number($key, $min = '', $max = '', $step = '', $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='number' id='$key' name='$key' value='$value'
                 min='$min' max='$max' step='$step' $attr>";
}

// Generate <input type='radio'> list
function html_radios($key, $items, $br = false)
{
    $value = encode($GLOBALS[$key] ?? '');
    echo '<div>';
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'checked' : '';
        echo "<label><input type='radio' id='{$key}_$id' name='$key' value='$id' $state>$text</label>";
        if ($br) {
            echo '<br>';
        }
    }
    echo '</div>';
}

function html_radios_attr($key, $attributes, $br = false)
{
    $value = encode($GLOBALS[$key] ?? ''); // Retrieve and encode the global key value
    foreach ($attributes as $attribute) {
        $state = htmlspecialchars($attribute->option_id) == $value ? 'checked' : ''; // Check if this option is selected
        echo "<input type='radio' class='input-radio' id='" . htmlspecialchars($attribute->option_id) . "' name='" . htmlspecialchars($key) . "' value='" . htmlspecialchars($attribute->option_id) . "' $state>";
        echo "<label class='option' for='" . htmlspecialchars($attribute->option_id) . "'>" . htmlspecialchars($attribute->option_value) . "</label>";
        if ($br) {
            echo '<br>';
        }
    }
}

function html_radio_payment($key, $options)
{
    // Get the current value from POST or global if available
    $currentValue = isset($_POST[$key]) ? $_POST[$key] : ($GLOBALS[$key] ?? '');

    // Loop through each option to create radio inputs
    foreach ($options as $option) {
        $value = htmlspecialchars($option['value']);
        $label = htmlspecialchars($option['label']);
        $checked = $value == $currentValue ? 'checked' : ''; // Check if this is the selected value
        $activeClass = $checked ? 'active' : ''; // Add 'active' class if checked

        // Output the label and input with the appropriate classes and attributes
        echo "<input class='e-wallet-radio' type='radio' name='" . htmlspecialchars($key) . "' id='$value' value='$value' $checked>";
        echo "<label class='e-wallet-pic $activeClass' for='$value'><img class='wallet-pic' src='image/$value.png' alt='$label'></label>";
    }
}






// Generate <select>
function html_select($key, $items, $default = '- Select One -', $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<select id='$key' name='$key' $attr>";
    if ($default !== null) {
        echo "<option value=''>$default</option>";
    }
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'selected' : '';
        echo "<option value='$id' $state>$text</option>";
    }
    echo '</select>';
}

function html_select_ron($key, $items, $default = '- Select One -', $attr = '', $class = '', $style = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    $class_attr = $class ? "class='$class'" : '';
    $style_attr = $style ? "style='$style'" : '';
    echo "<select id='$key' name='$key' $attr $class_attr $style_attr>";
    if ($default !== null) {
        echo "<option value=''>$default</option>";
    }
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'selected' : '';
        echo "<option value='$id' $state>$text</option>";
    }
    echo '</select>';
}

// Generate <input type='file'>
function html_file($key, $accept = '', $attr = '')
{
    echo "<input type='file' id='$key' name='$key' accept='$accept' $attr>";
}

function html_checkBox($key, $attr = '')
{
    $checked = isset($GLOBALS[$key]) && $GLOBALS[$key] ? 'checked' : '';

    echo "<input type='checkbox' id='$key' name='$key' value='1' $checked $attr>";
}

/*-----------------------------------------------------*/
// Generate table headers <th>
function table_headers($fields, $sort, $dir, $href = '')
{
    foreach ($fields as $k => $v) {
        $d = 'asc'; // Default direction
        $c = 'sortable'; // Default class for unsorted columns
        $arrow = "↕"; // Default arrow for unsorted columns

        // Check if this is the currently sorted column
        if ($k == $sort) {
            $d = $dir == 'asc' ? 'desc' : 'asc';
            $c = $dir; // Class will be 'asc' or 'desc'
            $arrow = $dir == 'asc' ? "▲" : "▼"; // Set the arrow for sorted columns
        }

        // Echo the table header with the appropriate class and arrow
        echo "<th><a href='?sort=$k&dir=$d&$href' class='$c'>$v $arrow</a></th>";
    }
}
function count_cart()
{
    global $_db, $_user; // Access the global database connection and session user variable

    $query = "SELECT * FROM cart WHERE member_id = ?";
    $stm = $_db->prepare($query);

    // Execute the query with the user session variable
    $stm->execute([$_user->member_id]);

    // Fetch all results to count the items
    $cartItems = $stm->fetchAll();

    return count($cartItems);
}

// ============================================================================
// Error Handlings
// ============================================================================


// Global error array
$_err = [];


// Generate <span class='err'>
function err($key, $id_style)
{
    global $_err;

    if ($_err[$key] ?? false) {
        echo "
        <div class='invalid' id='{$id_style}'  style='display:block;'>
            <i class='fa fa-exclamation-circle'></i>
            {$_err[$key]}
        </div>
        ";
    } else {
        echo "
        <div class='invalid' id='{$id_style}'>
        </div>
        ";
    }
}

// helpers.php

function getProductRangePrice($product)
{
    $min_price = number_format($product['price_range']['min'], 2);
    $max_price = number_format($product['price_range']['max'], 2);

    if ($min_price == $max_price) {
        return "RM{$min_price}";
    } else {
        return "RM{$min_price} - RM{$max_price}";
    }
}

function countPendingOrder($member_id = null)
{
    global $_db;

    // Base SQL query to count pending orders
    $sql = "SELECT COUNT(*) as count FROM orders WHERE order_status = 'Pending'";

    // If member_id is provided, add it to the query
    if ($member_id) {
        $sql .= " AND member_id = ?";
        $stmt = $_db->prepare($sql);
        $stmt->execute([$member_id]);
    } else {
        $stmt = $_db->prepare($sql);
        $stmt->execute();
    }

    // Fetch the result and return the count
    $orderCount = $stmt->fetch(PDO::FETCH_OBJ);

    return $orderCount ? $orderCount->count : 0; // Return the count or 0 if not found
}
function countCurrentOrder($member_id = null)
{
    global $_db;

    // Base SQL query to count current orders (excluding Pending, Completed, and Cancelled)
    $sql = "SELECT COUNT(*) as count FROM orders WHERE order_status NOT IN ('Pending', 'Completed', 'Cancelled')";

    // If member_id is provided, add it to the query
    if ($member_id) {
        $sql .= " AND member_id = ?";
        $stmt = $_db->prepare($sql);
        $stmt->execute([$member_id]);
    } else {
        // If no member_id, just execute the query for all current orders
        $stmt = $_db->prepare($sql);
        $stmt->execute();
    }

    // Fetch the result and return the count
    $orderCount = $stmt->fetch(PDO::FETCH_OBJ);

    return $orderCount ? $orderCount->count : 0; // Return the count or 0 if not found
}

function countHistoryOrder($member_id)
{
    global $_db;
    $stmt = $_db->prepare("SELECT COUNT(*) as count FROM orders WHERE (order_status = 'Completed' OR order_status = 'Cancelled') AND member_id = ?");
    $stmt->execute([$member_id]);
    $orderCount = $stmt->fetch(PDO::FETCH_OBJ);
    return $orderCount ? $orderCount->count : 0; // Return the count or 0 if not found
}

function countCompletedOrder($member_id = null)
{
    global $_db;

    $sql = "SELECT COUNT(*) as count FROM orders WHERE order_status = 'Completed'";


    if ($member_id) {
        $sql .= "AND member_id = ?";
        $stmt = $_db->prepare($sql);
        $stmt->execute([$member_id]);
    } else {
        $stmt = $_db->prepare($sql);
        $stmt->execute();
    }

    $orderCount = $stmt->fetch(PDO::FETCH_OBJ);
    return $orderCount ? $orderCount->count : 0; // Return the count or 0 if not found

}

function countCancelledOrder($member_id = null)
{
    global $_db;

    $sql = "SELECT COUNT(*) as count FROM orders WHERE order_status = 'Cancelled'";


    if ($member_id) {
        $sql .= "AND member_id = ?";
        $stmt = $_db->prepare($sql);
        $stmt->execute([$member_id]);
    } else {
        $stmt = $_db->prepare($sql);
        $stmt->execute();
    }

    $orderCount = $stmt->fetch(PDO::FETCH_OBJ);
    return $orderCount ? $orderCount->count : 0; // Return the count or 0 if not found
}


function countAllUser($type = "")
{
    global $_db;
    $sql = "SELECT COUNT(*) as count FROM member where role =?";
    $stm = $_db->prepare($sql);
    $stm->execute([$type]);

    $userCount = $stm->fetchColumn();

    return $userCount;
}
function updateOrderOnCancellationReturnMemberPoints($order_id, $member_id)
{
    global $_db;

    $updateQuery = "UPDATE orders SET order_status = 'Cancelled', order_cancelled_date = NOW() WHERE order_id = ?";
    $updateStmt = $_db->prepare($updateQuery);
    $updateSuccess = $updateStmt->execute([$order_id]);



    // Step 2: Retrieve voucher points used and subtotal of the canceled order
    $getOrderDetailsQuery = "
        SELECT voucher_points_used, subtotal 
        FROM orders 
        WHERE order_id = ? AND order_status !='Pending'
    ";
    $stmt = $_db->prepare($getOrderDetailsQuery);
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_OBJ);  // Fetch as object

    // Debug: Check if order details are retrieved
    if ($order) {
        // echo "Order details retrieved: <br>";
        // var_dump($order);  // Print order details for debugging

        $voucher_points_used = $order->voucher_points_used;
        $subtotal = $order->subtotal;

        // Step 3: Retrieve the member's current points
        $getMemberPointsQuery = "
            SELECT member_points 
            FROM member 
            WHERE member_id = ?
        ";
        $memberStmt = $_db->prepare($getMemberPointsQuery);
        $memberStmt->execute([$member_id]);
        $member = $memberStmt->fetch(PDO::FETCH_OBJ);  // Fetch as object

        // Debug: Check if member details are retrieved
        if ($member) {
            // echo "Member details retrieved: <br>";
            // var_dump($member);  // Print member details for debugging

            $current_member_points = $member->member_points;

            // Step 4: Add the voucher points back to the member's points
            $new_member_points = $current_member_points + $voucher_points_used;

            // Debug: Show points calculation
            // echo "Voucher points used: {$voucher_points_used} <br>";
            // echo "Current member points: {$current_member_points} <br>";
            // echo "New member points after adding voucher points: {$new_member_points} <br>";

            // Step 5: Calculate the points earned from the canceled order and subtract them
            $points_earned = floor($subtotal / 10); // Assuming 1 point for every RM10 spent
            $new_member_points -= $points_earned;

            // // Debug: Show final points after deduction
            // echo "Points earned from order: {$points_earned} <br>";
            // echo "Final member points after subtracting earned points: {$new_member_points} <br>";

            // Step 6: Update the member's points in the database
            $updateMemberPointsQuery = "
                UPDATE member 
                SET member_points = :new_member_points 
                WHERE member_id = :member_id
            ";
            $updateStmt = $_db->prepare($updateMemberPointsQuery);
            $updateSuccess = $updateStmt->execute([
                ':new_member_points' => $new_member_points,
                ':member_id' => $member_id
            ]);

            // Debug: Check if the member's points were successfully updated
            if ($updateSuccess && $updateStmt->rowCount() > 0) {
                temp("order_status", "$order_id - <b>Cancelled</b> ,Member points after deduct $new_member_points");
            } else {
                echo "Failed to update member points.<br>";
            }
        } else {
            echo "Member not found.<br>";
        }
    } else {
        echo "No canceled order found or no voucher points used.<br>";
    }
}



// ============================================================================
// Security
// ============================================================================

// Global user object
$_user = $_SESSION['user'] ?? null;

// Login user
function login($user, $url = '/')
{
    $_SESSION['user'] = $user;
    redirect($url);
}

// Logout user
function logout($url = '/')
{
    unset($_SESSION['user']);
    redirect($url);
}

// Authorization
function auth(...$roles)
{
    global $_user;
    if ($_user) {
        if ($roles) {
            if (in_array($_user->role, $roles)) {
                return; // OK
            }
        } else {
            return; // OK
        }
    }

    redirect('/login_register/login.php');
}
// Check if the user is logged in
function isLoggedIn()
{
    global $_user;
    return isset($_user);
}



function set_cart_item($cartItem)
{
    // Initialize the cart if it doesn't exist
    if (!isset($_SESSION['checkout_items'])) {
        $_SESSION['checkout_items'] = [];  // Initialize as an empty array if not set
    }

    // Append the new cart item to the session cart array
    $_SESSION['checkout_items'][] = $cartItem;
    echo '<pre>';
    print_r($_SESSION['checkout_items']);
    echo '</pre>';
}

function clear_cart()
{
    if (isset($_SESSION['checkout_items'])) {
        unset($_SESSION['checkout_items']);
    }
}
function get_cart_item()
{
    return $_SESSION['checkout_items'] ?? null;
}
// ============================================================================
// Database Setups and Functions
// ============================================================================

// Global PDO object

$_db = new PDO('mysql:dbname=smart', 'root', '', [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
]);

// Function to fetch the next ID value with a prefix
function getNextIdWithPrefix($table, $idField, $prefix, $length = 2)
{
    global $_db;
    // Prepare the query to get the maximum ID with the given prefix
    $stm = $_db->prepare("SELECT MAX($idField) FROM $table WHERE $idField LIKE ?");
    $stm->execute([$prefix . '%']);
    $maxId = $stm->fetchColumn();

    // Extract the numeric part and increment it
    if ($maxId) {
        // Remove the prefix and zero-pad the number to the specified length
        $numericPart = substr($maxId, strlen($prefix));
        $nextNumeric = str_pad((int)$numericPart + 1, $length, '0', STR_PAD_LEFT);
    } else {
        // Start with the initial numeric part if no records found
        $nextNumeric = str_pad(1, $length, '0', STR_PAD_LEFT);
    }

    return $prefix . $nextNumeric;
}

function checkSuspended()
{
    global $_user, $_db;

    // Check if the user is logged in
    if (!isLoggedIn()) {
        // Optionally log this or handle the scenario where the user is not logged in
        error_log("User is not logged in.");
        return;  // Return immediately if the user is not logged in
    }

    // Now safe to use $_user as we have already confirmed the user is logged in
    $member_id = $_user->member_id;

    // Prepare and execute the query to check if the user is suspended
    $query = "SELECT status FROM member WHERE member_id = ? AND status IN('suspend', 'inactive')";
    $stm = $_db->prepare($query);
    $stm->execute([$member_id]);

    if ($stm->rowCount() > 0) {
        // If the member is suspended, redirect to the login page
        unset($_SESSION['user']);
        temp("loginChecking", "You have been <b>Blocked<b/> from Administrator");
        redirect("/login_register/login.php");
        return;
    } else {
        // OK - Member is not suspended, continue normally
        return;
    }
}

checkSuspended();


// Is unique?
function is_unique($value, $table, $field)
{
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() == 0;
}

function getOrderDeliverd()
{
    global $_db;
    // Fetch all orders with status 'Delivered'
    $stm = $_db->prepare("SELECT * FROM orders WHERE order_status = ?");
    $stm->execute(["Delivered"]); // 'Delivered' was misspelled
    return $stm->fetchAll(); // Fetch all results as an array of objects
}

function changeOrderDeliverdWithin7Days()
{
    global $_db;

    $data = getOrderDeliverd(); // Get all delivered orders

    if ($data) {
        foreach ($data as $order) {
            // Calculate the due date as 7 days after the order was delivered
            $due_date = strtotime($order->order_delivered_date . ' + 7 days');
            $current_date = time(); // Get current time

            // If the current date is past or equal to the due date
            if ($current_date >= $due_date) {
                // Update the order status to 'Completed'
                $updateOrderQuery = "
                UPDATE orders 
                SET order_status = 'Completed', 
                    order_completed_date = NOW() 
                WHERE order_id = ?
            ";
                $updateStmt = $_db->prepare($updateOrderQuery);
                $updateStmt->execute([$order->order_id]);
            }
        }
    }
}

//this will update the order auto in each page
changeOrderDeliverdWithin7Days();

// Is exists?
function is_exists($value, $table, $field)
{
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() > 0;
}

function retrieveAllValue($value, $table, $field)
{
    global $_db;
    $stm = $_db->prepare("SELECT * FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetch();
}


function checkAdminRole($value)
{
    global $_db;
    $stm = $_db->prepare("SELECT * FROM member WHERE member_id = ?");
    $stm->execute([$value]);
    $result = $stm->fetch();

    if ($result->role == "admin") {
        return true;
    }
    return false;
}

// Functions to fetch product details and attributes
function fetchProductDetails($product_id)
{
    global $_db;
    $query = "SELECT * FROM products p 
              JOIN Category c ON p.category_id=c.category_id 
              WHERE product_id = ? AND p.status = 'active'";
    $stmt = $_db->prepare($query);
    $stmt->execute([$product_id]);
    return $stmt->fetch();
}

function fetchProductAttributes($product_id)
{
    global $_db;
    $query = "SELECT pa.price, a.attributes_id, a.attributes_type, o.option_id, o.option_value
              FROM product_attributes pa
              JOIN attributes a ON pa.attributes_id = a.attributes_id
              JOIN options o ON pa.option_id = o.option_id
              WHERE pa.product_id = ? AND pa.status='available';";
    $stmt = $_db->prepare($query);
    $stmt->execute([$product_id]);
    return $stmt->fetchAll();
}

// Return local root path
function root($path = '')
{
    return "$_SERVER[DOCUMENT_ROOT]/$path";
}

// Return base url (host + port)
function base($path = '')
{
    return "http://$_SERVER[SERVER_NAME]:$_SERVER[SERVER_PORT]/$path";
}



function get_mail()
{

    require_once 'lib/PHPMailer.php';
    require_once 'lib/SMTP.php';

    $m = new PHPMailer(true);
    $m->isSMTP();
    $m->Host = 'smtp.gmail.com';
    $m->SMTPAuth = true;
    $m->Port = 587;
    $m->Username = 'infosmart609@gmail.com'; // Your Gmail address
    $m->Password = 'txeiqgbjoklqkflq'; // Your Gmail app password
    $m->CharSet = 'utf-8';

    $m->setFrom('infosmart609@gmail.com', 'Smart'); // Your email address

    return $m;
}

// Function to send the PDF invoice email
function sendInvoiceEmail($recipientEmail, $pdfPath)
{

    $m = get_mail();

    $m->addAddress($recipientEmail); // Add a recipient

    // Attachments
    $m->addAttachment($pdfPath);    // Add attachments

    // Content
    $m->isHTML(true);                                  // Set email format to HTML
    $m->Subject = 'Your Invoice from [Smart]';
    $m->Body    = 'Dear Customer,<br><br>Thank you for your order. Please find your invoice attached.<br><br>Best regards,<br>SMaster SUIT SDN BHD';

    if ($m->send()) {
        echo 'Invoice has been sent via email.';
    } else {
        echo 'Message could not be sent.';
    }
}
// ============================================================================
// Global Constants and Variables
// ============================================================================

$_genders = [
    'F' => 'Female',
    'M' => 'Male',
];

$_programs = [
    'RDS' => 'Data Science',
    'REI' => 'Enterprise Information Systems',
    'RIS' => 'Information Security',
    'RSD' => 'Software Systems Development',
    'RST' => 'Interactive Software Technology',
    'RSW' => 'Software Engineering',
];

/*---------------------------------------------------------*/
$_member_status = [
    'inactive' => 'inactive',
    'active' => 'active',
    'suspend' => 'suspend',
];
