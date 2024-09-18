<?php
require_once('../_base.php'); // Ensure this file contains your database connection and utility functions

// Delete expired OTPs that haven't been used
$_db->query('DELETE FROM email_otp WHERE otp_expiration < NOW() AND otp_used = 0');

if (is_post()) {
    $email = req("email");

    // Check if the email already has a used OTP
    $stm = $_db->prepare("SELECT * FROM email_otp WHERE email = ? AND otp_used = 1");
    $stm->execute([$email]);

    if ($stm->rowCount() > 0) {
        echo json_encode(['status' => 'failure', 'message' => 'OTP already used for this email.']);
        return;
    }

    // Generate a 6-digit OTP
    $otp_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    // Delete any existing unused OTP for this email
    $delete_query = "DELETE FROM email_otp WHERE email = ? AND otp_used = 0";
    $delete_stm = $_db->prepare($delete_query);
    $delete_stm->execute([$email]);

    // Insert the new OTP into the database
    $insert_query = "INSERT INTO email_otp (email, otp, otp_expiration, otp_used) VALUES (?, ?, ADDTIME(NOW(), '00:05:00'), ?)";
    $insert_stm = $_db->prepare($insert_query);
    $insert_stm->execute([$email, $otp_code, 0]);

    // Prepare and send the email with PHPMailer
    $body = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>OTP Verification</title>
        <style>
            .otp_container { border: 1px solid #ccc; text-align: center; border-radius: 5px; padding: 20px; max-width: 400px; margin: 0 auto; font-family: Arial, sans-serif; }
            .otp_title { font-weight: bold; background-color: blue; color: #fff; padding: 10px; border-radius: 5px 5px 0 0; }
            .otp_number { font-size: 24px; font-weight: bold; margin: 20px auto; padding: 10px; border: 3px solid black; width: 150px; border-radius: 5px; }
            .otp_message { font-size: 14px; margin: 15px 0; color: #333; }
        </style>
    </head>
    <body>
        <div class="otp_container">
            <div class="otp_title">
                <h2>OTP Verification Code</h2>
            </div>
            <div class="otp_message">
                <p>Dear Customer,</p>
                <p>Please find your OTP code below. You can copy and paste it during the account registration process.</p>
            </div>
            <div class="otp_number">' . $otp_code . '</div>
        </div>
    </body>
    </html>
    ';

    $m = get_mail();
    $m->addAddress($email);
    $m->Subject = "OTP Verification Code";
    $m->Body = $body;
    $m->isHTML(true);

    if ($m->send()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'failure', 'message' => 'Failed to send email.']);
    }
} else {
    echo json_encode(['status' => 'failure']);
}
exit();
?>
