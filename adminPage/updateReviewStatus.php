<?php
require('../_base.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id']) && isset($_POST['status'])) {
    $review_id = $_POST['review_id'];
    $status = $_POST['status'];

    $sql = "UPDATE reviews SET status = ? WHERE review_id = ?";
    $stmt = $_db->prepare($sql);
    $stmt->execute([$status, $review_id]);

    echo ucfirst($status) . " status updated successfully!";
}

redirect("reviewPage.php");
?>
