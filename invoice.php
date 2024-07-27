<?php
function generatePDF($name, $address, $city, $postcode, $id, $orderDate, $orderId, $orderItems = [],)
{
    class PDF extends FPDF
    {
        // Table header
        function BasicTable($header, $orderItems)
        {
            // Set font for header
            $this->SetFont('Arial', 'B', 12);

            // Set background color for header
            $this->SetFillColor(0, 0, 0); // Black background

            // Set text color for header
            $this->SetTextColor(255, 255, 255); // White text

            // Header
            foreach ($header as $col) {

                if ($col == "Description") {
                    $this->Cell(50, 7, $col, 1, 0, 'C', true); // Center align text, fill with background color
                } else {
                    $this->Cell(35, 7, $col, 1, 0, 'C', true); // Center align text, fill with background color
                }
            }
            $this->Ln();

            // Reset text color for data
            $this->SetTextColor(0, 0, 0); // Black text

            // Reset font for data
            $this->SetFont('Arial', '', 12);

            $subtotal = 0;
            // Data
            foreach ($orderItems as $item) {
                $this->Cell(35, 7, $item->itemID, 1, 0, 'C');
                $this->Cell(50, 7, $item->description, 1, 0, 'C');
                $this->Cell(35, 7, $item->quantity, 1, 0, 'C');
                $this->Cell(35, 7, number_format($item->unitPrice, 2), 1, 0, 'C');
                $this->Cell(35, 7, number_format($item->total, 2), 1, 0, 'C');
                $this->Ln();
                $subtotal += $item->total;
            }

            return $subtotal;
        }


        function Footer()
        {
            // Go to 1.5 cm from bottom
            $this->SetY(-15);
            // Set font
            $this->SetFont('Arial', 'I', 8);
            // Add footer text with top border and center alignment
            $this->Cell(0, 10, '@Copyright 2024@ SMART SDN', 'T', 0, 'C');
            // Add page number with no border, center aligned
            $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
        }
    }

    // Column headings
    $header = array('No', 'Description', 'Qty', 'Unit Price', 'Total');
    // Data loading
    $data = array();

    //call database and put the data inside

    for ($i = 0; $i < 10; $i++) {
        $data[] = array($i + 1, 'HP Laptop', '1', '15000.00', '15100.00');
    }

    $pdf = new PDF();
    $pdf->AddPage();

    // Set font for the title
    $pdf->SetFont('Arial', 'B', 23);

    // Center the title
    $pdf->Cell(50, 10, 'Invoice', 0, 0, 'C');
    $pdf->Image("image/logo.png", 165, 5, 18, 18);

    // Add space before the next section
    $pdf->Ln(20); // Adjust the value as needed



    // Bill To section
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(100, 10, 'Bill To:');
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(100, 10, $name);
    $pdf->Ln();
    $pdf->Cell(100, 10, $address);
    $pdf->Ln();
    $pdf->Cell(100, 10, $city . ',' . $postcode);
    $pdf->Ln(20);

    // Details section title
    $pdf->SetXY(120, 30); // Move to the right
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(50, 10, 'Details:');
    $pdf->Ln();
    $pdf->SetXY(120, 40); // Move to the right and down for content
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(50, 10, 'Customer ID: ');
    $pdf->Cell(50, 10, $id, 0, 1);
    $pdf->SetXY(120, 50); // Move to the right and down for content
    $pdf->Cell(50, 10, 'Invoice Date: ');
    $pdf->Cell(50, 10, $orderDate, 0, 1);
    $pdf->SetXY(120, 60); // Move to the right and down for content
    $pdf->Cell(50, 10, 'Invoice No: ');
    $pdf->Cell(50, 10, $orderId, 0, 1);
    $pdf->Ln(20);

    // Move back to the main left margin
    $pdf->SetXY(10, 80);

    // Table
    $subtotal = $pdf->BasicTable($header, $orderItems);
    $pdf->Ln(10);

    // Subtotal
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(120, 10, '');
    $pdf->Cell(27, 10, 'Subtotal', 1, 0, 'C');
    $pdf->Cell(27, 10,  number_format($subtotal,2), 1, 0, 'C');

    $pdf->Output();
}
