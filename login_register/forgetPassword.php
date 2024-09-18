<?php
require('../_base.php');
require('../_sendEmail.php');

if (isLoggedIn()) {
    redirect('/');
}

if (is_post()) {
    $email = req("email");
    $emailFounded = true;

    // Validate email
    if ($email == '') {
        $_err['email'] = 'Required';
    } else if (!is_email($email)) {
        $_err['email'] = 'Invalid Email Format Sir';
    } else if (!is_exists($email, "member", "email")) {
        $_err['email'] = "Sorry we're not able to found the email";
        $emailFounded = false;
    }

    if ($emailFounded && empty($_err)) {
        $stm = $_db->prepare("SELECT * FROM member WHERE email = ?");
        $stm->execute([$email]);
        $memberDetails = $stm->fetch();

        // Store details in variables
        $member_id = $memberDetails->member_id;
        $firstname = $memberDetails->firstname;
        $lastname = $memberDetails->lastname;
        $email_activation = $memberDetails->email_activation;
        $status = $memberDetails->status;

        if ($email_activation == 1 && $status == "active") {
            // Generate a unique token
            $forgetPasswordToken = sha1(uniqid() . rand());

            $subject = "Reset Password";
            $message = "Click the below button to reset your password";
            $forgetLink = base("login_register/resetPassword.php?token=$forgetPasswordToken");

            // Delete old token for this member
            $stm = $_db->prepare("DELETE FROM token WHERE member_id = ?");
            $stm->execute([$member_id]);

            // Insert new token
            $stm = $_db->prepare('INSERT INTO token (token_id,expire, member_id) VALUES (?,ADDTIME(NOW(), "00:05"), ?)');
            $stm->execute([$forgetPasswordToken, $member_id]);

            // Send the reset password email
            sendEmail($firstname, $lastname, $email, $subject, $message, $forgetLink, 'reset');
            temp('info_reset', "Check your Email to Reset Password");
        } else {
            temp('info_reset', "You may check if your account is activated or if there are other issues.");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Passowrd</title>
    <link rel="stylesheet" href="../css/login.css">
    <link rel="icon" type="image/x-icon" href="../image/logo.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
</head>

<body class="body_forget_password">

    <div id="loadingOverlay" style="display:none;">
        <div class="spinner"></div>
        <p>Sending Email.......</p>
    </div>

    <div id="info"><?= temp('info_reset') ?></div>
    <div class="forget-form">

        <div class="titleForget">
            <h2>Forget Password</h2>
        </div>
        <div class="container">
            <div class="left_forget">
                <img src="../image/undraw_Forgot_password_re_hxwm.png" alt="forget" height="400" width="500">
            </div>

            <form class="right_forget" method="post" action="" id="send_resetForm">
                <div class="input-group">
                    <div class="input-group">
                        <?= html_text_type("text", "email", "input-text", " placeholder=''") ?>
                        <label for="email">Email</label>
                    </div>

                    <?= err("email", 'invalidTextField') ?>

                    <button type="submit" id="resetPassword" class="black-button">Reset</button>
                    <div class="backLoginBox">
                        <p><a class="backToLogin" href="login.php">Back To Login</a></p>
                    </div>
            </form>
        </div>
    </div>

    <script src="../js/validation.js"></script>
</body>

</html>