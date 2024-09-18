<?php
require('../_base.php');  // Load your database connection and session

auth("member");

// Get the order ID from the query string
$order_id =req("order_id")?? null;

if ($order_id) {
    // Query the database to verify if the current user owns this order
    $stmt = $_db->prepare("SELECT * FROM orders WHERE order_id = ? AND member_id = ?");
    $stmt->execute([$order_id, $_user->member_id]);
    $order = $stmt->fetch();

    if ($order) {
        // If the order belongs to the current user, allow access to the invoice
        $invoice_file = "../invoice_file/Invoice_" . htmlspecialchars($order_id) . ".pdf";

        // Check if the file exists
        if (file_exists($invoice_file)) {
            // Serve the file securely (download or view)
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . basename($invoice_file) . '"');
            readfile($invoice_file);
            exit;
        } else {
            echo "Invoice file not found.";
        }
    } else {
        echo "You are not authorized to view this invoice.";
        echo"<a href='index.php'>Back To Home</a>";
    }
} else {
    echo "Invalid order ID.";
    echo"<a href='index.php'>Back To Home</a>";
}
?>
