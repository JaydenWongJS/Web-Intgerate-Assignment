<?php $title = "Order Details" ?>
<?php include('_header.php'); ?>




<div class="container_order_details">
    <a class="goBack" style="top:-15px;left:-30px;" href="#" id="goBack"> <i class="fa fa-arrow-circle-left"></i> Back</a>
    <div class="order_details_info">
        <h1># Order A01</h1>
        <p>Status : <span class="status">Completed</span></p>
        <p>Order Date : 10/7/2023</p>
        <p>Complete Date : 17/7/2023</p>
        <p>Duration Taken : 7 days</p>
    </div>

    <div class="addressDetails">

        <div class="senderAddress">
            <h3>From : Smart Sdn Bhd</h3>
            <p>Lot 01,Ground Floor,</p>
            <p>IoI Mall Puchong,</p>
            <p>47100 Puchong</p>
            <p>Selangor.</p>
        </div>

        <div class="receiverAddress">
            <h3>To : Yong CF</h3>
            <p>A-02,Sri Pangsa</p>
            <p>Laman Puteri Alam Setar</p>
            <p>51110 KL</p>
            <p>Wilayah Persekuatuan.</p>
        </div>
    </div>


</div>

<table class="styled-table-orderDetails">
    <thead>
        <tr>
            <th>#</th>
            <th>Thumbnail</th>
            <th>Product ID</th>
            <th>Name</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Total</th>
        </tr>

    </thead>

    <tbody>
        <tr>
            <td>1</td>
            <td><img src="image/maydayConcert.jpg" alt=""></td>
            <td>W110</td>
            <td>MayDay Concert</td>
            <td>x 2</td>
            <td>RM 100</td>
            <td>RM 200</td>
        </tr>
        <tr>
            <td>2</td>
            <td><img src="image/maydayConcert.jpg" alt=""></td>
            <td>W110</td>
            <td>MayDay Concert</td>
            <td>x 2</td>
            <td>RM 100</td>
            <td>RM 200</td>
        </tr>
    </tbody>

    <tfoot>
        <tr class="row_subtotal">
            <th style="text-align: end;" colspan="6">Subtotal</th>
            <th>RM 400</th>
        </tr>
    </tfoot>
</table>

<div class="extra_charge">
    <table class="extra_charge_table">
        <tr>
            <th>Processing Fee (10%)</th>
            <th>:</th>
            <td>RM 40</td>
        </tr>
        <tr>
            <th>Tax (6%)</th>
            <th>:</th>
            <td>RM 10</td>
        </tr>
        <tr class="totalAmount">
            <th>Total Amount</th>
            <th>:</th>
            <td>RM 450</td>
        </tr>
    </table>
</div>


<?php include('_footer.php'); ?>