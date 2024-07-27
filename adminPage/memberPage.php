<?php
$title="Members";
include('_headerAdmin.php');
include('_sideBar.php');

?>

</div>
<div class="dashboard_container">
    <div class="title">
        <h2>Manage Members</h2>
      </div>

      <div class="filter">
        <form action="" method="post" class="search-form">
            <input type="search" name="searchOrder" id="searchOrder" class="search-order-input" placeholder="Search Here ....."/>
            <button type="submit" class="search-button"><i class="fas fa-search"></i></button>
        </form>
        <form id="sortingForm" class="sorting-form">
            <select class="form-select" onchange="submitSortingSize()" name="sorting">
                <option selected disabled>Sort User Type</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="suspended">Suspended</option>
            </select>
        </form>
    </div>

    <table class="order-table">
        <thead>
            <tr>
                <th>MEMBER ID</th>
                <th>DATE</th>
                <th>STATUS</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody>

            <tr>
                <td><a href="">A001</a></td>
                <td>20/3/203</td>
                <td class="status">ACTIVE</td>
                <td class="flex_active_button">
                    <form>
                        <input type="hidden" name="userId" value=""/>
                        <input class="active-button" type="submit" name="submitUserActiveType" value="ACTIVE"/>
                    </form>
                    <form>
                        <input type="hidden" name="userId" value=""/>
                        <input class="inactive-button" type="submit" name="submitUserActiveType" value="SUSPEND"/>
                    </form>
                </td>
            </tr>

            <tr>
                <td>A002</td>
                <td>20/3/203</td>
                <td class="status">ACTIVE</td>
                <td class="flex_active_button">
                    <form>
                        <input type="hidden" name="userId" value=""/>
                        <input class="active-button" type="submit" name="submitUserActiveType" value="ACTIVE"/>
                    </form>
                    <form>
                        <input type="hidden" name="userId" value=""/>
                        <input class="inactive-button" type="submit" name="submitUserActiveType" value="SUSPEND"/>
                    </form>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?php include('_footerAdmin.php')?>