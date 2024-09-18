
<div class="sidebar">
    <div class="menu">
        <div>
            <a href="../" title="Go To Shop"><img class="logo" src="../image/logo.png" alt=""></a>
        </div>
        <a class="<?= ($title == 'Dashboard') ? 'active ' : '' ?>navDashboardUrl" href="adminDashboard.php"><i class="fa fa-tachometer-alt"></i> Dashboard</a>
        <a class="<?= ($title == 'Admin Order') ? 'active ' : '' ?>navDashboardUrl" id="management" href="#"><i class="fa fa-cogs"></i> Management &nbsp;<i class="fa fa-angle-down"></i></a>
        <div class="manageDropdown" id="dropdownMenu">
            <a class="<?= ($title == 'Admin Order') ? 'active ' : '' ?>navDashboardUrl" href="adminOrder.php"><i class="fa fa-box"></i> Orders</a>
            <a class="<?= ($title == 'Members') ? 'active ' : '' ?> navDashboardUrl" href="memberPage.php">
                <i class="fas fa-users"></i> Members
            </a>
            <a class="navDashboardUrl<?= ($title == 'Reviews') ? 'active ' : '' ?> " href="reviewPage.php">
                <i class="fas fa-comments"></i> Reviews
            </a>
            <a class="navDashboardUrl <?= ($title == 'Products') ? 'active ' : '' ?>" href="adminProductPage.php">
                <i class="fas fa-comments"></i> Products
            </a>
        </div>
        <a class="navDashboardUrl <?= ($title == 'Admin Profile') ? 'active ' : '' ?>" href="adminProfile.php">
            <i class="fas fa-user"></i> Profile
        </a>
        <a class="navDashboardUrl" href="#">
            <i class="fas fa-question-circle"></i> Get Help
        </a>
        <a class="navDashboardUrl" href="../log_out.php">
            <i class="fas fa-sign-out-alt"></i> Log Out
        </a>
    </div>

    <div class="side_bar_login_info">
        <p>Admin <i style="font-size: 17px;" class='far fa-id-card'></i></p>
        <p><?= $_user->member_id ?></p>
    </div>

</div>