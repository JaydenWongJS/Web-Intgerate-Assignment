<?php
require('fpdf/fpdf.php'); // Make sure to include FPDF library

function generatePDF($name, $address, $city, $postcode, $state,$member_id, $orderDate, $payment_method, $orderId, $orderItems = [], $discount, $subtotal)
{
    class PDF extends FPDF
    {
        // Class-level variable to hold the subtotal of items
        var $subtotal_items = 0.00;

        function Header()
        {
            // Company Logo and Information
            $logoPath = 'image/logo.png';
            if (file_exists($logoPath)) {
                $this->Image($logoPath, 10, 10, 40); // Adjust path if needed
            } else {
                $this->SetFont('Arial', 'B', 12);
                $this->Cell(0, 10, 'Logo not found', 0, 1, 'C');
            }

            $this->SetFont('Arial', 'B', 14); // Changed to Arial (a built-in font)
            $this->Cell(0, 5, 'SMART - Official Website', 0, 1, 'R');
            $this->SetFont('Arial', '', 12); // Changed to Arial
            $this->Cell(0, 5, 'SMASTER SUIT SDN. BHD. (571193-A)', 0, 1, 'R');
            $this->Cell(0, 5, 'GST ID: 123456789', 0, 1, 'R');
            $this->Cell(0, 5, 'No 3, Third Floor, Jalan OP 1/2,', 0, 1, 'R');
            $this->Cell(0, 5, 'Pusat Perdagangan One Puchong,', 0, 1, 'R');
            $this->Cell(0, 5, '47160, Puchong, Selangor Darul Ehsan', 0, 1, 'R');
            $this->Ln(10);
        }

        function Footer()
        {
            // Position at 1.5 cm from bottom
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8); // Changed to Arial
            $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
        }

        function CustomerDetails($member_id, $name, $address, $city, $postcode,$state)
        {
            $this->SetFont('Arial', 'B', 14); // Changed to Arial
            $this->Cell(0, 10, 'INVOICE', 0, 1);
            $this->SetFont('Arial', '', 12); // Changed to Arial
            $this->Cell(0, 5, "Member Id : $member_id ", 0, 1);
            $this->Cell(0, 5, $name, 0, 1);
            $this->Cell(0, 5, $address, 0, 1);
            $this->Cell(0, 5, $city . ', ' . $postcode, 0, 1);
            $this->Cell(0, 5, $state, 0, 1);
            $this->Ln(10);
        }

        function OrderDetails($orderId, $orderDate, $payment_method)
        {
            $this->SetFont('Arial', 'B', 12); // Changed to Arial
            $this->Cell(0, 5, 'Order Number: ' . $orderId, 0, 1, 'R');
            $this->Cell(0, 5, 'Order Date: ' . $orderDate, 0, 1, 'R');
            $this->Cell(0, 5, 'Paid By: ' . $payment_method, 0, 1, 'R');
            $this->Ln(10);
        }

        function ProductTable($header, $orderItems)
        {
            // Set font for header
            $this->SetFont('Arial', 'B', 12); // Changed to Arial

            // Set background color for header
            $this->SetFillColor(0, 0, 0); // Black background

            // Set text color for header
            $this->SetTextColor(255, 255, 255); // White text

            // Column widths
            $w = array(130, 30, 30); // Adjusted widths for full page

            // Header
            foreach ($header as $i => $col) {
                $this->Cell($w[$i], 7, $col, 1, 0, 'C', true); // Adjusted widths for all columns
            }
            $this->Ln();

            // Reset text color for data
            $this->SetTextColor(0, 0, 0); // Black text

            // Reset font for data
            $this->SetFont('Arial', '', 12); // Changed to Arial

            // Data
            foreach ($orderItems as $item) {
                $this->Cell($w[0], 7, $item['product_name'] . ' - ' . $item['attributes_type'] . ': ' . $item['option_value'], 1, 0, 'L');
                $this->Cell($w[1], 7, $item['qty'], 1, 0, 'C');
                $this->Cell($w[2], 7, 'RM ' . number_format($item['total'], 2), 1, 0, 'R');
                $this->Ln();

                // Add item total to subtotal
                $this->subtotal_items += $item['total'];
            }
            $this->Ln(5);
        }

        function SummaryTable($discount, $shipping = "Free shipping")
        {
            $this->SetFont('Arial', '', 12); // Changed to Arial
            $this->Cell(140, 7, 'Subtotal', 0, 0, 'R');
            $this->Cell(50, 7, 'RM ' . number_format($this->subtotal_items, 2), 0, 1, 'R');
            $this->Cell(140, 7, 'Discount', 0, 0, 'R');
            $this->Cell(50, 7, '-RM ' . number_format($discount,2), 0, 1, 'R');
            $this->Cell(140, 7, 'Shipping', 0, 0, 'R');
            $this->Cell(50, 7, $shipping, 0, 1, 'R');
            $this->SetFont('Arial', 'B', 14); // Changed to Arial
            $this->Cell(140, 10, 'Total', 0, 0, 'R');
            $total = $this->subtotal_items - $discount;
            $this->Cell(50, 10, 'RM ' . number_format($total, 2), 0, 1, 'R');
        }
    }

    $pdf = new PDF();
    $pdf->AddPage();

    // Customer and Order Details
    $pdf->CustomerDetails($member_id, $name, $address, $city, $postcode,$state);
    $pdf->OrderDetails($orderId, $orderDate, $payment_method);

    // Table header
    $header = array('Product', 'Quantity', 'Price');

    // Product Table
    $pdf->ProductTable($header, $orderItems);

    // Summary Table
    $pdf->SummaryTable($discount);

    return $pdf;
}
?>
