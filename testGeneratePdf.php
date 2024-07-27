<?php
require('fpdf/fpdf.php');
require('invoice.php');
require('_base.php')
?>

<?php
if (is_get()) {
    $orderId = req("orderId");
}

$orderItems = [
    (object)[
        'itemID' => '1001',
        'description' => 'Laptop',
        'quantity' => 2,
        'unitPrice' => 12.50,
        'total' => 2 * 12.50
    ],
    (object)[
        'itemID' => '1002',
        'description' => 'Mouse',
        'quantity' => 5,
        'unitPrice' => 2.00,
        'total' => 5 * 2.00
    ],
    (object)[
        'itemID' => '1003',
        'description' => 'Keyboard',
        'quantity' => 3,
        'unitPrice' => 7.50,
        'total' => 3 * 7.50
    ]
];


?>
<?= generatePDF("Yong", "Sri cAssia", "Puchong", "47100", "A01", "20/5/2024", "ORD1", $orderItems) ?>