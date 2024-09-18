<?php
require("../_base.php");

if (isLoggedIn()) {
    redirect('/');
}

if (is_get()) {
    // Delete expired tokens
    $_db->query('DELETE FROM token WHERE expire < NOW()');

    $token = req("token");

    // Check if the token is valid and belongs to a member
    $stm = $_db->prepare('SELECT member_id FROM token WHERE token_id = ?');
    $stm->execute([$token]);
    $tokenData = $stm->fetch();

    if (!$tokenData) {
        temp('info_reset', 'Invalid or expired token. Try again.');
        redirect('forgetPassword.php');
    } else {
        $memberId = $tokenData->member_id;
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Reset Password</title>
            <link rel="stylesheet" href="../css/login.css">
            <link rel="icon" type="image/x-icon" href="../image/logo.png">
        </head>
        <body>
        <div id="info"><?= temp("reset_message"); ?></div>

        <form action="validateReset.php" method="post" class="reset_password_form">
            <h3>RESET PASSWORD</h3>
            <input type="hidden" name="token" value="<?= htmlspecialchars($token); ?>">
            <input type="hidden" name="member_id" value="<?= htmlspecialchars($memberId); ?>">

            <div class="reset_password_box">
                <label for="newPassword">New Password: </label>
                <?= html_text_type("password", "newPassword", "password_input", " placeholder='Enter New Password'") ?>
            </div>

            <div class="reset_password_box">
                <label for="confirmPassword">Confirm Password: </label>
                <?= html_text_type("password", "confirmPassword", "password_input", " placeholder='Confirm Password'") ?>
            </div>

            <div class="reset_password_submit_box">
                <input type="submit" value="Reset Password" name="reset_password_submit" class="reset_password_submit"/>
            </div>
        </form>
        </body>
        </html>
        <?php
    }
}
?>
