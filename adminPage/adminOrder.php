<?php
$title="Admin Order";
include('_headerAdmin.php');
include('_sideBar.php');

?>

    <div class="dashboard_container">
        <div class="title">
            <h2>Manage Orders</h2>
          </div>

          <div class="filter">
            <form action="" method="post" class="search-form">
                <input type="search" name="searchOrder" id="searchOrder" class="search-order-input" placeholder="Search Here ....."/>
                <button type="submit" class="search-button"><i class="fas fa-search"></i></button>
            </form>
            <form id="sortingForm" class="sorting-form">
                <select class="form-select" onchange="submitSortingSize()" name="sorting">
                    <option selected disabled>Sort Order</option>
                    <option value="completed">Completed</option>
                    <option value="delivering">Delivering</option>
                    <option value="in_progress">In Progress</option>
                    <option value="pending">Pending</option>
                </select>
            </form>
        </div>

        <table class="order-table">
            <thead>
                <tr>
                    <th>ORDER</th>
                    <th>DATE</th>
                    <th>STATUS</th>
                    <th>TOTAL</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#A01</td>
                    <td>FEBRUARY 22, 2023</td>
                    <td>COMPLETED</td>
                    <td>RM0.00 FOR 1 ITEM</td>
                    <td>
                        <button class="view-button">VIEW</button>
                        <a target="_blank" href="../testGeneratePdf.php?orderId=A01" class="invoice-button">INVOICE</a>
                    </td>
                </tr>
                <tr>
                    <td>#A02</td>
                    <td>FEBRUARY 21, 2023</td>
                    <td>COMPLETED</td>
                    <td>RM0.00 FOR 1 ITEM</td>
                    <td>
                        <button class="view-button">VIEW</button>
                        <a href="../testGeneratePdf.php?orderId=A02" class="invoice-button">INVOICE</a>
                    </td>
                </tr>
                <tr>
                    <td>#2178</td>
                    <td>FEBRUARY 28, 2018</td>
                    <td>COMPLETED</td>
                    <td>RM1.00 FOR 1 ITEM</td>
                    <td>
                        <button class="view-button">VIEW</button>
                        <a href="../testGeneratePdf.php?orderId=A02" class="invoice-button">INVOICE</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <?php include('_footerAdmin.php')?>