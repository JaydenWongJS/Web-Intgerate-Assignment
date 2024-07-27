<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMAILER/src/Exception.php';
require 'PHPMAILER/src/PHPMailer.php';
require 'PHPMAILER/src/SMTP.php';

function sendEmail($id, $firstname, $lastname, $email, $_subject, $_mesasage,$activation_link)
{
    $name = $firstname . " " . $lastname;
    $customerEmail = $email;
    $subject = $_subject;
    $message = $_mesasage;

    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'yfung2574@gmail.com'; // Your Gmail address
        $mail->Password = 'ruddssvrjcfwvewe'; // Your Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        //Recipients
        $mail->setFrom('yfung2574@gmail.com', 'Smart'); // Your email address
        $mail->addAddress($email); // Customer's email address
        $mail->addReplyTo('yfung2574@gmail.com', 'Your Website'); // Your email address for replies


        $mail->isHTML(true);
        $mail->Subject =$subject;

        $mail->Body = '
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    background-color: #f5f5f5;
                }
                .container {
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
                    .footer{
                        text-align:center;
                     }
                    .activate{
                    padding:5px 13px;
                    background-color:black;
                    color:white;
                    font-weight:700;

                     }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    Subject: ' . $subject . '
                </div>
                <div class="content">
                    <p><span>Name:</span> ' . $name . '</p>
                    <p><span>Email:</span> ' . $customerEmail . '</p>
                    <p style="margin-top:20px;"><span>Message:</span> ' . $message . '</p>
                </div>
                <div class="footer">
                       <a class="activate" href="'.$activation_link.'">Activate Account</a>
                </div>
            </div>
        </body>
        </html>';

        if ($mail->send()) {
            temp('info_activate', "Please go to your email to activate account");
            redirect("register.php");
            // echo '<script>
            //         alert("Go to your email to activate your account");
            //         window.location.href = "register.php";
            //       </script>';
        } else {
            echo '<script>
                    window.location.href = "/";
                  </script>';
        }        
    } catch (Exception $e) {
        echo "<script>
        alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');
        document.location.href='index.php';
        </script>";
    }
}
