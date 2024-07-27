<?php $title="My Order" ?>
<?php include('_header.php'); ?>

<div class="myOrderContainer">
    <div class="order_title">
        <h1><i class="fas fa-receipt"></i> MY ORDERS</h1>
    </div>

    <div class="filter">
        <div style="text-align: center;">
            <a href="historyOrder.php" class="history">  <i class="fa fa-history"></i> History</a>
        </div>
        <form action="" method="post" class="searchForm">
            <input type="search" name="searchOrder" id="searchOrder" class="searchOrderInput" placeholder="Search Here ....."/>
            <button class="searchButton"><i class="fas fa-search"></i></button>
        </form>
        <form id="sortingForm">
            <select class="form-select" onchange="submitSortingSize()" name="sorting">
                <option selected disabled>Sort Order</option>
                <option value="sales">Completed</option>
                <option value="exp">Delivering</option>
                <option value="inexp">In Progress</option>
                <option value="rating">Pending</option>
            </select>
        </form>
    </div>
</div>

<div class="tableOrder">
    <table class="styled-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Order Date</th>
                <th>Status</th>
                <th>Amount</th>
                <th>Action</th>
                <th>View</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="orderId">A01</td>
                <td>1/7/2024</td>
                <td>Processing</td>
                <td>RM 109</td>
                <td>
                    <button class="btn complete-btn" type="button">Complete</button>
                    <button class="btn cancel-btn" type="button">Cancel</button>
                </td>
                <td><a href="orderDetails.php"><i class="fas fa-eye"></i></a></td>
            </tr>
            <tr>
                <td class="orderId">A01</td>
                <td>1/7/2024</td>
                <td>Processing</td>
                <td>RM 109</td>
                <td>
                    <button class="btn complete-btn" type="button">Complete</button>
                    <button class="btn cancel-btn" type="button">Cancel</button>
                </td>
                <td><a href="orderDetails.php"><i class="fas fa-eye"></i></a></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th>ID</th>
                <th>Order Date</th>
                <th>Status</th>
                <th>Amount</th>
                <th>Action</th>
                <th>View</th>
            </tr>
        </tfoot>
    </table>
</div>


<?php include('_footer.php'); ?>