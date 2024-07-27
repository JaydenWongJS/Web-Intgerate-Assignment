<?php
require("_base.php");

if (is_get()) {
    $memberId = req("member_id");
    $token = req("token"); // Get the token from the request cuz token is unique there cant known what it is if u type in url

    if ($memberId && $token) {
        // Check if the token and member_id are valid
        $checkStm = $_db->prepare("SELECT * FROM member WHERE member_id = ? AND activation_token = ?");
        $checkStm->execute([$memberId, $token]);

        //if valid means can fetch row lah
        if ($checkStm->rowCount() > 0) {
            // Fetch the member details
            $checkActivation = $checkStm->fetch();

            $status = $checkActivation->status;
            $email_activation = $checkActivation->email_activation;

            if ($status == "inactive" && $email_activation == 0 ) {
                // Update the member status and email_activation
                $stm = $_db->prepare("UPDATE member SET email_activation = 1, status = 'active', activation_token = NULL WHERE member_id = ?");//set null is avoid user to get the token adn take the member id to reaccess this page
                $stm->execute([$memberId]);

                if ($stm->rowCount() > 0) {
                    // Set the session variable and redirect
                    temp('emailActivated', "Your Account Has Been <b style='color:rgb(129, 217, 91)'>Activated, Login Now</b>");
                    redirect("login.php");
                    exit();
                } else {
                    echo "Not able to update: No rows affected.";
                }
            } else {
                temp('emailActivated', "Invalid Error Programmer!");
                redirect("login.php");
            }
        } else {
            temp('emailActivated', "Invalid Access to this page!");
            redirect("login.php");
            // echo "No record found with memberId: $memberId or invalid token.";
        }
    } else {
        temp('emailActivated', "Invalid Access to this page!");
        redirect("login.php");
        // echo "memberId or token is not provided.";
    }
}
?>
