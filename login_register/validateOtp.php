<?php
require_once('../_base.php'); // Ensure this file contains your database connection and utility functions

// Clean up expired OTPs from the database
$_db->query('DELETE FROM email_otp WHERE otp_expiration < NOW() AND otp_used=0');

if (is_post()) {
    $email = req("email");
    $enteredOtp = req("otp");

    // Fetch the stored OTP from the database using PDO
    $query = "SELECT otp, otp_expiration, otp_used FROM email_otp WHERE email = ? AND otp_used = 0";
    $stm = $_db->prepare($query);
    $stm->execute([$email]);
    $row = $stm->fetch();

    if ($row) {
        if (strtotime($row->otp_expiration) > time() && $row->otp === $enteredOtp) {

            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'failure', 'message' => 'Invalid OTP or OTP expired.']);
        }
    } else {
        echo json_encode(['status' => 'failure', 'message' => 'No valid OTP found.']);
    }
} else {
    echo json_encode(['status' => 'failure']);
}
exit();
