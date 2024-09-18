<?php

require_once '_base.php';
require 'lib/PHPMailer.php';
require 'lib/SMTP.php';

if (isLoggedIn()) {
    redirect('/');
}

function sendEmail($firstname, $lastname, $email, $_subject, $_message, $link, $type)
{
    $name = $firstname . " " . $lastname;
    $customerEmail = $email;
    $subject = $_subject;
    $message = $_message;

    $mail = new PHPMailer(true);

        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Port = 587;
        $mail->Username = 'infosmart609@gmail.com'; // Your Gmail address
        $mail->Password = 'txeiqgbjoklqkflq'; // Your Gmail app password
        $mail->CharSet = 'utf-8';

        //Recipients
        $mail->setFrom('infosmart609@gmail.com', 'Smart'); // Your email address
        $mail->addAddress($customerEmail); // Customer's email address
        $mail->addReplyTo('infosmart609@gmail.com', 'Smart'); // Your email address for replies

        $mail->isHTML(true);
        $mail->Subject = $subject;

        // Customize email content based on type
        $bodyContent = '
        <div class="header">
            Subject: ' . $subject . '
        </div>
        <div class="content">
            <p><span>Name:</span> ' . $name . '</p>
            <p><span>Email:</span> ' . $customerEmail . '</p>
            <p style="margin-top:20px;"><span>Message:</span> ' . $message . '</p>
        </div>
        <div class="footer">
            <a class="activate" href="' . $link . '">' . ($type == 'activation' ? 'Activate Account' : 'Reset Password') . '</a>
        </div>';

        $mail->Body = '
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f5f5f5;
                }
                .container {
                    padding:10px;
                    max-width: 600px;
                    margin: 20px auto;
                    background-color: #fff;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                    border-radius: 8px;
                    overflow: hidden;
                }
                .header {
                    background-color: #f5f5f5;
                    padding: 20px;
                    font-size: 20px;
                    font-weight: bold;
                    text-align: center;
                }
                .content {
                    padding: 20px;
                }
                .content p {
                    margin: 10px 0;
                }
                .content p span {
                    font-weight: bold;
                }
                .footer {
                    text-align: center;
                }
                .activate {
                    padding: 5px 13px;
                    border:3px solid  black;
                    background-color: white;
                    color: black;
                    font-weight: 700;
                }
            </style>
        </head>
        <body>
            <div class="container">
                ' . $bodyContent . '
            </div>
        </body>
        </html>';

        if ($mail->send()) {
            if ($type == 'activation') {
                temp('emailActivated', "Please go to your email to activate account");
                redirect("login.php");
            } elseif ($type == 'reset') {
                temp('info_reset', "Please go to your email to reset your password");
                redirect("forgetPassword.php");
            }
        } else {
            echo '<script>
                    window.location.href = "/";
                  </script>';
        }
}
