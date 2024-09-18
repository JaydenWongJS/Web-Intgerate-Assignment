<?php
$title = "Reviews";
require_once('_base.php');
include('_header.php');
include('nav_bar.php');

clear_cart();
auth("member");

$orderId = req("order_id");
$memberId = $_user->member_id;

// 检查订单状态并获取订单中的会员ID
$sqlOrderStatus = "SELECT order_status FROM orders WHERE order_id = ? AND member_id = ?";
$stmOrderStatus = $_db->prepare($sqlOrderStatus);
$stmOrderStatus->execute([$orderId, $memberId]);
$orderStatus = $stmOrderStatus->fetch(PDO::FETCH_ASSOC);

if (!$orderStatus || $orderStatus['order_status'] !== 'Completed') {
    echo "订单未完成，无法进行评论。";
    exit;
}

// 获取订单详情和评论状态
$sqlOrderDetails = "SELECT od.order_detail_id, p.product_id, p.product_name, p.product_image, od.review_status, r.rating, r.review_content, r.status AS review_status
FROM order_details od
JOIN product_attributes pa ON od.product_attribute_id = pa.product_attribute_id
JOIN products p ON pa.product_id = p.product_id
LEFT JOIN reviews r ON r.product_id = p.product_id AND r.order_detail_id = od.order_detail_id
WHERE od.order_id = ?";
$stmOrderDetails = $_db->prepare($sqlOrderDetails);
$stmOrderDetails->execute([$orderId]);
$orderDetails = $stmOrderDetails->fetchAll(PDO::FETCH_ASSOC);

if (empty($orderDetails)) {
    echo "没有找到可评论的商品。";
    exit;
}
?>

<div class="container">
    <a class="goBack" href="myOrder.php" id="goBack"> <i class="fa fa-arrow-circle-left"></i> Back</a>
    <h2>Review Products</h2>

    <form class="review_form" action="submitReview.php" method="post">
        <input type="hidden" name="order_id" value="<?= htmlspecialchars($orderId) ?>">

        <?php foreach ($orderDetails as $index => $detail): ?>
            <div class="review_section">
                <h3><?= htmlspecialchars($detail['product_name']) ?></h3>
                <input type="hidden" name="order_details_id[]" value="<?= htmlspecialchars($detail['order_detail_id']) ?>">

                <div class="star_rate">
                    <?php
                    $rating = isset($detail['rating']) ? $detail['rating'] : 0;
                    for ($i = 1; $i <= 5; $i++): ?>
                        <input type="radio" id="star_<?= $i ?>_<?= $index ?>" name="rating_<?= $detail['order_detail_id'] ?>" value="<?= $i ?>" <?= $rating == $i ? 'checked' : '' ?>>
                        <label for="star_<?= $i ?>_<?= $index ?>"><?= $i ?> 星</label>
                    <?php endfor; ?>
                </div>

                <div class="review_comment">
                    <label for="comment_<?= $index ?>">评论内容：</label>
                    <textarea name="review_content_<?= $detail['order_detail_id'] ?>" rows="4" cols="50"><?= isset($detail['review_content']) ? htmlspecialchars($detail['review_content']) : '' ?></textarea>
                </div>
            </div>
            <hr>
        <?php endforeach; ?>

        <button type="submit" class="submit-btn">提交评论</button>
    </form>
</div>

<?php
include '_footer.php';
?>
