<?php
require('../../_base.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'])) {
    $review_id = $_POST['review_id'];
    $admin_reply = isset($_POST['admin_reply']) ? $_POST['admin_reply'] : '';

    if (!empty($admin_reply)) {
        $sql = "UPDATE reviews SET admin_reply = ? WHERE review_id = ?";
        $stmt = $_db->prepare($sql);
        $stmt->execute([$admin_reply, $review_id]);

        echo "Reply saved successfully!";
    } else {
        echo "Reply cannot be empty!";
    }
}

redirect("reviewPage.php");
?>