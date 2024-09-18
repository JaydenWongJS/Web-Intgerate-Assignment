<?php

$title = "Reviews";
require('../_base.php');
include('_headerAdmin.php');
include('_sideBar.php');

// Retrieve search and filter input
$product_name = req('product_name', '');
$status = req('status', '');

if (isset($_GET['product_name'])) {
    $product_name = $_GET['product_name'];
}

// Define SQL query with reviews table
$query = 'SELECT r.*, p.product_name, p.product_image, m.firstname, m.lastname, m.member_id 
          FROM reviews r
          JOIN products p ON r.product_id = p.product_id
          JOIN member m ON r.member_id = m.member_id
          WHERE 1=1';

$params = [];

// Filter by product name
if ($product_name !== '') {
    $query .= ' AND p.product_name LIKE ?';
    $params[] = "%$product_name%";
}

// Filter by review status
if ($status !== '') {
    $query .= ' AND r.status = ?';
    $params[] = $status;
}

// Sorting
$fields = [
    'review_id'    => 'Review ID',
    'product_name' => 'Product Name',
    'member_id'    => 'Member ID',
    'rating'       => 'Rating',
    'status'       => 'Status',
    'created_at'   => 'Created At',
    'updated_at'   => 'Updated At',
];

$sort = req('sort');
key_exists($sort, $fields) || $sort = 'review_id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

// Append sorting to the query
$query .= " ORDER BY $sort $dir";

// Paging
$page = req('page', 1);

require_once '../lib/SimplePager.php';
$p = new SimplePager($query, $params, 10, $page);
$arr = $p->result;

?>

<div class="dashboard_container">
    <div class="title">
        <h2>Manage Reviews</h2>
    </div>

    <!--SEARCH & FILTER-->
    <div class="review_page_search_filter">
        <form>
            <button onclick="location.href='reviewPage.php'" type="button" class="clear_filter_btn"><i class="fas fa-redo"> Reset</i></button>
            <?= html_text('product_name', 'placeholder="Search by product name"', $product_name, 'input-text') ?>
            <?= html_select_ron('status', ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'], 'All Status', $status, 'select-box') ?>
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <div class="review_page_content">
        <!--TABLE OF REVIEWS-->
        <div class="review_page_content_table">
    <table>
        <tr>
            <?= table_headers($fields, $sort, $dir, "page=$page&product_name=$product_name&status=$status") ?>
            <th>Admin Reply</th> <!-- 新增一列 -->
            <th>Actions</th> <!-- 用于放置 Save Reply 和 Approve/Reject 按钮 -->
        </tr>
        <?php foreach ($arr as $r): ?>
            <tr class="review-row" data-review='<?= json_encode($r) ?>'>
                <td><?= $r->review_id ?></td>
                <td><?= $r->product_name ?></td>
                <td><?= $r->member_id ?></td>
                <td><?= $r->rating ?></td>
                <td>
                    <?php
                    $status_class = '';
                    if ($r->status === 'pending') {
                        $status_class = 'review_status_pending';
                    } elseif ($r->status === 'approved') {
                        $status_class = 'review_status_approved';
                    } elseif ($r->status === 'rejected') {
                        $status_class = 'review_status_rejected';
                    }
                    ?>
                    <span class="<?= $status_class ?>"><?= $r->status ?></span>
                </td>
                <td><?= $r->created_at ?></td>
                <td><?= $r->updated_at ?></td>

                <!-- 管理员回复列，仅在状态为 approved 时显示 -->
                <td>
                    <?php if ($r->status === 'approved'): ?>
                        <form action="saveReviewReply.php" method="POST">
                            <textarea name="admin_reply" rows="3"><?= htmlspecialchars($r->admin_reply) ?></textarea>
                            <input type="hidden" name="review_id" value="<?= $r->review_id ?>">
                            <button type="submit">Save Reply</button>
                        </form>
                    <?php else: ?>
                        <p>N/A</p>
                    <?php endif; ?>
                </td>

                <!-- Actions: Approve/Reject -->
                <td>
                    <?php if ($r->status === 'pending'): ?>
                        <form action="updateReviewStatus.php" method="POST">
                            <input type="hidden" name="review_id" value="<?= $r->review_id ?>">
                            <button type="submit" name="status" value="approved">Approve</button>
                            <button type="submit" name="status" value="rejected">Reject</button>
                        </form>
                    <?php elseif ($r->status === 'approved'): ?>
                        <form action="updateReviewStatus.php" method="POST">
                            <input type="hidden" name="review_id" value="<?= $r->review_id ?>">
                            <button type="submit" name="status" value="rejected">Reject</button>
                        </form>
                    <?php elseif ($r->status === 'rejected'): ?>
                        <form action="updateReviewStatus.php" method="POST">
                            <input type="hidden" name="review_id" value="<?= $r->review_id ?>">
                            <button type="submit" name="status" value="approved">Approve</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
</div>


        <!--RIGHT SIDE BORDER-->
        <div class="review_details">
            <h3>Review Details</h3>
            <p id="review_details_content">Hover over a row to see details here</p>
        </div>
    </div>

    <?= $p->html("sort=$sort&dir=$dir&product_name=$product_name&status=$status") ?>

</div>

<?php include('_footerAdmin.php') ?>

<script>
    document.querySelectorAll('.review-row').forEach(row => {
        row.addEventListener('mouseenter', function () {
            const reviewData = JSON.parse(this.getAttribute('data-review'));
            const detailsContent = `
                <strong>Review ID:</strong> ${reviewData.review_id}<br>
                <strong>Product Name:</strong> ${reviewData.product_name}<br>
                <strong>Member ID:</strong> ${reviewData.member_id}<br>
                <strong>Member Name:</strong> ${reviewData.firstname} ${reviewData.lastname}<br>
                <strong>Rating:</strong> ${reviewData.rating}<br>
                <strong>Review Content:</strong> ${reviewData.review_content}<br>
                <strong>Status:</strong> ${reviewData.status}<br>
                <strong>Created At:</strong> ${reviewData.created_at}<br>
                <strong>Updated At:</strong> ${reviewData.updated_at}<br>
                <strong>Product Image:</strong><br> 
                <img src="/path/to/uploads/${reviewData.product_image}" alt="${reviewData.product_name}" style="width:100px;height:auto;">
            `;
            document.getElementById('review_details_content').innerHTML = detailsContent;
        });
    });
</script>

<style>
    .review_page_content {
        display: flex;
        justify-content: space-between;
    }

    .review_page_content_table {
        width: 70%;
    }

    .review_details {
        width: 28%;
        border-left: 2px solid #ccc;
        padding-left: 20px;
        background-color: #f9f9f9;
    }

    .review_details h3 {
        margin-top: 0;
    }

    /* Hover effect */
    .review-row:hover {
        background-color: #f1f1f1;
        cursor: pointer;
    }

    .review_details img {
        margin-top: 10px;
    }
</style>
