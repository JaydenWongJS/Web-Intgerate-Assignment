<?php
require_once('_base.php');
auth("member");

$orderId = req("order_id");
$memberId = $_user->member_id;

if (isset($_POST['order_details_id'])) {
    foreach ($_POST['order_details_id'] as $index => $orderDetailId) {
        $productId = req("product_id_" . $orderDetailId);
        $rating = req("rating_" . $orderDetailId);
        $reviewContent = req("review_content_" . $orderDetailId);

        // 调试输出 product_id
        echo "Product ID: " . htmlspecialchars($productId) . "<br>";

        // 检查 product_id 是否为空或无效
        if (empty($productId)) {
            echo "错误：product_id 为空，无法插入评论";
            exit;
        }

        // 检查 product_id 是否存在于 products 表中
        $sqlCheckProduct = "SELECT COUNT(*) FROM products WHERE product_id = ?";
        $stmCheckProduct = $_db->prepare($sqlCheckProduct);
        $stmCheckProduct->execute([$productId]);
        $productExists = $stmCheckProduct->fetchColumn();

        if (!$productExists) {
            echo "错误：product_id " . htmlspecialchars($productId) . " 不存在于 products 表中。";
            exit;
        }

        // 插入或更新评论
        $sql = "INSERT INTO reviews (product_id, member_id, rating, review_content, order_details_id, status)
                VALUES (?, ?, ?, ?, ?, 'pending')
                ON DUPLICATE KEY UPDATE rating = VALUES(rating), review_content = VALUES(review_content), updated_at = CURRENT_TIMESTAMP";
        $stm = $_db->prepare($sql);

        try {
            $stm->execute([$productId, $memberId, $rating, $reviewContent, $orderDetailId]);
        } catch (PDOException $e) {
            echo "错误: " . $e->getMessage();
            exit;
        }
    }
}

redirect("myOrder.php");
?>
