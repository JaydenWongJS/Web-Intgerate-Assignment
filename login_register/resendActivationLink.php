<?php
require_once('../_base.php');
require_once('../_sendEmail.php');

if (isLoggedIn()) {
    redirect('/');
}

// (1) Delete expired tokens
$_db->query('DELETE FROM token WHERE expire < NOW()');

if (is_get()) {
    $email = req("email");

    if ($email) {
        $stm = $_db->prepare("SELECT * FROM member WHERE email = ?");
        $stm->execute([$email]);
        $memberDetails = $stm->fetch();

        if ($stm->rowCount() > 0) {
            //store each in variable for more clean
            $member_id = $memberDetails->member_id;
            $firstname = $memberDetails->firstname;
            $lastname = $memberDetails->lastname;
            $email_activation = $memberDetails->email_activation;
            $status = $memberDetails->status;

            // Check for cooldown
            $cooldown = 60; // 60 seconds cooldown
            if (isset($_SESSION['last_resend_time']) && time() - $_SESSION['last_resend_time'] < $cooldown) {
                temp("emailActivated", "You must wait " . ($cooldown - (time() - $_SESSION['last_resend_time'])) . " seconds before resending the activation email.");
                redirect("login.php");
            }

            if ($email_activation == 0 && $status == "inactive") {
                // Generate a unique token
                $activation_token = sha1(uniqid() . rand());

                $subject = "Resend Email Activation";
                $message = "Resend Message : Click the below button to activate your account !";
                $activation_link = base("login_register/activateMember.php?token=$activation_token");

                $stm = $_db->prepare(' 
                  DELETE FROM token WHERE member_id = ?;
                INSERT INTO token (token_id, expire, member_id) 
            VALUES (?, ADDTIME(NOW(), "00:05"), ?);');
                $stm->execute([$member_id, $activation_token, $member_id]);

                $_SESSION['last_resend_time'] = time(); // Update last resend time

                sendEmail($firstname, $lastname, $email, $subject, $message, $activation_link, 'activation');
            }
        }
    }
}
