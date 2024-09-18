<?php

$title = "Members";
require('../_base.php');
include('_headerAdmin.php');
include('_sideBar.php');

/*----------------------------------*/
// Retrieve search and filter input
$id = req('member_id');

$stm = $_db->prepare('SELECT * FROM member WHERE member_id = ?');
$stm->execute([$id]);
$s = $stm->fetch();


//if the id doesnt exist (enter from url) - return back "ïndex.php"
if (!$s) {
    redirect('/');
}

$stm = $_db->prepare('SELECT * FROM orders WHERE member_id = ?');
$stm->execute([$id]);
$orders = $stm->fetchAll();

?>
<div id="info"><?= temp("info") ?></div>

<div class="dashboard_container">
    <div class="title">
        <h2>Member Details</h2>
    </div>

    <!-- Container for the whole detail view -->
    <div class="details_container">
        <!-- Profile Picture -->
        <div class="profile_picture">
            <?php if (!empty($s->image)): ?>
                <img src="../uploadsImage/userProfile/<?= htmlspecialchars($s->image) ?>" alt="Member Photo">
            <?php else: ?>
                <p>No photo available</p>
            <?php endif; ?>
        </div>

        <!-- Member details -->
        <div class="member_details">
            <!-- Name and Information -->
            <div class="info_block">
                <p><strong>Member ID:</strong> <?= $s->member_id ?></p>
                <p><strong>First Name:</strong> <?= $s->firstname ?></p>
                <p><strong>Last Name:</strong> <?= $s->lastname ?></p>
                <p><strong>Date of Birth:</strong> <?= $s->birthdate ?></p>
                <p><strong>Email Address:</strong> <?= $s->email ?></p>
                <p><strong>Phone:</strong> <?= $s->phone ?></p>
                <p><strong>Role:</strong> <?= $s->role ?></p>
                <p><strong>Gender:</strong>
                    <?php
                    if ($s->gender == 'M') {
                        echo "Male";
                    } else {
                        echo "Female";
                    }
                    ?>
                </p>
            </div>
            <div class="address_block">
                <p><strong>Address Line 1:</strong> <?= $s->address1 ?></p>
                <p><strong>Address Line 2:</strong> <?= $s->address2 ?></p>
                <p><strong>City:</strong> <?= $s->city ?></p>
                <p><strong>State:</strong> <?= $s->state ?></p>
                <p><strong>Postcode:</strong> <?= $s->postcode ?></p>
            </div>
        </div>

        <!-- Status and Points -->
        <div class="status_points">
            <div class="status_section">
                <p><strong>Status:</strong>
                    <span class="<?= $status_class ?>"><?= $s->status ?></span>
                </p>
            </div>

            <div class="points_section">
                <p><strong>Member Points:</strong> <i><?= $s->member_points ?></i></p>
            </div>
        </div>

        <!-- Action Buttons (Update, Delete) -->
        <div class="action_buttons">

            <button id="member_update_btn" data-get="memberUpdate.php?member_id=<?= $s->member_id ?>">
                <i class="fas fa-user-edit"></i> Update
            </button>

            <?php if ($s->status === 'active'): ?>
                <!-- Show Deactivate and Suspend buttons if the member is Active -->
                <button type="button" class="member_deactivate_btn"
                    data-post="memberStatus.php?member_id=<?= $s->member_id ?>&status=inactive"
                    data-confirm="Are you sure you want to deactivate this member?">
                    <i class="fas fa-trash-alt"></i> Deactivate
                </button>

                <button class="member_suspend_btn" data-post="memberStatus.php?member_id=<?= $s->member_id ?>&status=<?= 'suspend' ?>" data-confirm>
                    <i class="fas fa-ban"></i> Suspend
                </button>
            <?php elseif ($s->status === 'suspend'): ?>
                <!-- Show Activate button if the member is Suspended -->
                <button class="member_activate_btn" data-post="memberStatus.php?member_id=<?= $s->member_id ?>&status=<?= 'active' ?>" data-confirm>
                    <i class="fas fa-check"></i> Activate
                </button>
            <?php elseif ($s->status === 'inactive'): ?>
                <!-- Show Activate button if the member is Inactive -->
                <button class="member_activate_btn" data-post="memberStatus.php?member_id=<?= $s->member_id ?>&status=<?= 'active' ?>" data-confirm>
                    <i class="fas fa-check"></i> Activate
                </button>
            <?php endif; ?>

        </div>
    </div>
    <section class="order_customer_section">
        <h2>Order <i style="font-size: 24px;;" class="fas fa-clipboard-list"></i> (<?= count($orders) ?>)
        </h2>
        <table class="customer-table" style="width: 90%;">
            <tr class="customer-table-header">
                <th class="customer-table-header-cell">Order ID</th>
                <th class="customer-table-header-cell">Status</th>
                <th class="customer-table-header-cell">Qty</th>
                <th class="customer-table-header-cell">Total (RM)</th>
                <th class="customer-table-header-cell">Action</th>
            </tr>

            <?php if ($orders): ?>
                <?php foreach ($orders as $order): ?>
                    <tr class="customer-table-row">
                        <td class="customer-table-cell"><?= $order->order_id ?></td>
                        <td class="customer-table-cell"><?= $order->order_status ?></td>
                        <td class="customer-table-cell"><?= $order->total_qty ?></td>
                        <td class="customer-table-cell"><?= $order->subtotal ?></td>
                        <td class="customer-table-cell"><a href="adminOrderDetails.php?orderId=<?= $order_id ?>">View</a></td>
                    <tr>
                    <?php endforeach; ?>
                <?php else: ?>

                    <tr>
                        <td class="customer-table-cell" colspan="4">No order found.</td>
                    </tr>

                    </tr>
                <?php endif; ?>
                </tr>
        </table>
    </section>
</div>



<?php include('_footerAdmin.php') ?>