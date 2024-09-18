<?php
require("../_base.php");

if (is_get()) {

    // (1) Delete expired tokens
    $_db->query('DELETE FROM token WHERE expire < NOW()');

    $token = req("token");

    // (2) Check if the token exists in the token table
    $tokenQuery = $_db->prepare('SELECT * FROM token WHERE token_id = ?');
    $tokenQuery->execute([$token]);

    if ($tokenQuery->rowCount() == 0) {
        temp('emailActivated', 'Invalid Or Expired token. Try again');
        redirect("login.php");
    } else {
        // (3) Fetch member_id associated with the token
        $tokenData = $tokenQuery->fetch();
        $memberId = $tokenData->member_id;

        // (4) Check if the member is inactive and not yet activated
        $checkStm = $_db->prepare('SELECT * FROM member WHERE member_id = ? AND status = "inactive" AND email_activation = 0');
        $checkStm->execute([$memberId]);

        if ($checkStm->rowCount() > 0) {
            // Update the member status and email_activation
            $stm = $_db->prepare('
                UPDATE member SET email_activation = 1, status = "active" WHERE member_id = ?;
                DELETE FROM token WHERE token_id = ?;
            ');
            $stm->execute([$memberId, $token]);

            if ($stm->rowCount() > 0) {
                // Set the session variable and redirect
                temp('emailActivated', "Your Account Has Been <b style='color:rgb(129, 217, 91)'>Activated, Login Now</b>");
                redirect("login.php");
                exit();
            } else {
                echo "Not able to update: No rows affected.";
            }
        } else {
            temp('emailActivated', "Something went wrong in db query");
            redirect("login.php");
        }
    }
}
?>
