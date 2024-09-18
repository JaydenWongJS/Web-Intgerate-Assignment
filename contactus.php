<?php
$title = "Contact Us";

require('_base.php');
include('_header.php');
include('nav_bar.php');
clear_cart();
?>

<?php
if (is_post()) {
    $name = req("name");
    $email = req("email");
    $subject = req("subject");
    $message = req("message");


    $namePattern = " /^[A-Za-z\s]+$/";
    $emailPattern = "/^[^\s@]+@[^\s@]+\.[^\s@]+$/";
    //validation personal

    if (empty($name)) {
        $_err['name'] = ' Name Required';
    } else if (!preg_match($namePattern, $name)) {
        $_err['name'] = ' Name should contain Alphabet Only';
    }

    //Validate email
    if (empty($email)) {
        $_err['email'] = 'Email Required';
    } else if (!is_email($email)) {
        $_err['email'] = 'Invalid Email Format Sir';
    }

    if (empty($subject)) {
        $_err['subject'] = 'Subject Required';
    }

    if (empty($message)) {
        $_err['message'] = 'Message Required';
    }

    if (!$_err) {
        // Prepare and send the email with PHPMailer
        $body = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Contact Us Submission</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 0;
                }
                .email-container {
                    width: 100%;
                    background-color: #ffffff;
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    max-width: 600px;
                    margin: 20px auto;
                    padding: 20px;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                }
                .email-header {
                    background-color: #007bff;
                    color: #ffffff;
                    padding: 15px;
                    border-radius: 8px 8px 0 0;
                    text-align: center;
                }
                .email-header h2 {
                    margin: 0;
                    font-size: 20px;
                }
                .email-content {
                    padding: 20px;
                }
                .email-content p {
                    margin: 10px 0;
                    color: #333;
                }
                .email-content .label {
                    font-weight: bold;
                    color: #007bff;
                }
                .email-footer {
                    text-align: center;
                    padding: 15px;
                    font-size: 12px;
                    color: #777;
                    border-top: 1px solid #ddd;
                }
            </style>
        </head>
        <body>
            <div class="email-container">
                <div class="email-header">
                    <h2>New Contact Us Submission</h2>
                </div>
                <div class="email-content">
                    <p><span class="label">Name:</span> ' . htmlspecialchars($name) . '</p>
                    <p><span class="label">Email:</span> ' . htmlspecialchars($email) . '</p>
                    <p><span class="label">Subject:</span> ' . htmlspecialchars($subject) . '</p>
                    <p><span class="label">Message:</span></p>
                    <p>' . nl2br(htmlspecialchars($message)) . '</p>
                </div>
                <div class="email-footer">
                    <p>This email was generated automatically from the Contact Us form on SMART website.</p>
                </div>
            </div>
        </body>
        </html>
        ';

        $m = get_mail();
        $m->addAddress("infosmart609@gmail.com"); // Add your business email as the recipient
        
        // Set the client's email as the Reply-To address
        $m->addReplyTo($email, $name);
        
        $m->Subject = "Contact Us Submission";
        $m->Body = $body;
        $m->isHTML(true);
        if($m->send()){
            $name="";
            $email="";
            $subject="";
            $message = "";
            echo "Email sent successfully";
        }else{
            echo "Failed to send email";
        }
    }
}

?>
<div class="banner contact_us_banner">
    <img id="background-video" src="image/minions.gif">
    <div class="overlay"></div>
    <div class="banner-text contact_title">
        <h1>Get In Touch With Us</h1>
    </div>
</div>

<div class="contact_us_container">
    <div class="map">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3984.2022302241294!2d101.61819347310039!3d3.040388353815491!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc4b56b7f0bd15%3A0xe676c32ccfdcb00b!2sSmartMaster%20HQ!5e0!3m2!1sen!2smy!4v1720497929560!5m2!1sen!2smy" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>

    <div class="contact_form_box">
        <div class="contact_form_title">
            <h1 class="founder">
                Contact US <i style="margin-left:5px;color:#ff9800" class='far fa-paper-plane'></i>
            </h1>
        </div>
        <form class="contact_form" id="contact_form" method="post" action="">
            <div class="input-group">
                <span> <i class='far fa-address-card'></i></span>
                <?= html_text_type("text", "name", "input-text", "placeholder='Name' data-upper"); ?>
            </div>

            <?= err("name", "invalidName") ?>


            <div class="input-group">
                <span> <i class="fa fa-envelope"></i> </span>
                <?= html_text_type("text", "email", "input-text", "placeholder='Your Email'"); ?>
            </div>

            <?= err("email", "invalidEmail") ?>

            <div class="input-group">
                <span> <i class='fas fa-pen-alt'></i> </span>
                <?= html_text_type("text", "subject", "input-text", "placeholder='Your Subject' data-upper"); ?>
            </div>

            <?= err("subject", "invalidSubject") ?>

            <div class="contact_message">
                <label for="message">Please Drop Us a Message Here : </label>
                <?= html_textarea("message", "4", "50", "placeholder='Enter the Message'") ?>
            </div>

            <?= err("message", "invalidMessage") ?>


            <div style="text-align: right; width: 100%;">
                <input type="submit" name="send" id="send" class="send-button" value="Send Us A Message" />
            </div>
        </form>
    </div>
</div>

<div id="loadingSpinner" style="display: none;"></div>
<div class="overlay_all"></div>

<?php include('_footer.php') ?>