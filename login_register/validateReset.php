<?php
require('../_base.php');

if (isLoggedIn()) {
    redirect('/');
}

if (is_post()) {
    $memberId = req("member_id");
    $token = req("token");
    $newPassword = req("newPassword");
    $confirmPassword = req("confirmPassword");

    // Validate password
    if (empty($newPassword)) {
        $_err['newPassword'] = 'Password is required';
    } elseif (strlen($newPassword) < 8 || !preg_match("/[A-Z]/", $newPassword)) {
        $_err['newPassword'] = 'Password must be at least 8 characters long and include an uppercase letter';
    }

    if (empty($confirmPassword)) {
        $_err['confirmPassword'] = 'Confirm password is required';
    } elseif ($confirmPassword !== $newPassword) {
        $_err['confirmPassword'] = 'Passwords do not match';
    }

    // If there are no errors, proceed
    if (empty($_err)) {
        // Verify the token is valid for the given member ID
        $stm = $_db->prepare("SELECT token_id FROM token WHERE token_id = ? AND member_id = ? AND expire > NOW()");
        $stm->execute([$token, $memberId]);
        $tokenValid = $stm->fetch();

        if ($tokenValid) {
            // Update password and delete token
            $stm = $_db->prepare("UPDATE member SET password = SHA1(?) WHERE member_id = ?");
            $stm->execute([$newPassword, $memberId]);

            // Delete token
            $_db->prepare("DELETE FROM token WHERE token_id = ?")->execute([$token]);

            temp('reset_message', 'Password has been reset successfully!');
            redirect("login.php");
        } else {
            temp('reset_message', 'Invalid or expired token!');
            redirect("resetPassword.php?token=$token");
        }
    } else {
        // Display error messages
        $errorMessages = implode(", ", $_err);
        temp('reset_message', $errorMessages);
        redirect("resetPassword.php?token=$token");
    }
} else {
    redirect("forgetPassword.php");
}
?>
