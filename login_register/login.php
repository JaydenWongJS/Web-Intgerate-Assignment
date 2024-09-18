<?php
require '../_base.php';
?>

<?php

if (!empty(isLoggedIn())) {
    redirect('/');
}

// Define constants
define('MAX_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 minutes lockout time

// Initialize login attempt tracking
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['lockout_time'] = 0;
}

// Check if the account is currently locked
if ($_SESSION['login_attempts'] >= MAX_ATTEMPTS && time() - $_SESSION['lockout_time'] < LOCKOUT_TIME) {
    $remaining = LOCKOUT_TIME - (time() - $_SESSION['lockout_time']);
    $minutes = floor($remaining / 60);
    $seconds = $remaining % 60;
    temp('loginChecking', "Account locked. Try again in $minutes minutes and $seconds seconds.");
} else {
    // Reset attempts if lockout period has passed
    if ($_SESSION['login_attempts'] >= MAX_ATTEMPTS) {
        $_SESSION['login_attempts'] = 0;
    }

    if (is_post()) {
        $email = req("email");
        $password = req("password");
        $passwordLenght = strlen($password);
        $rememberMe = req("rememberMe") == 1; // Check if the remember me checkbox is checked

        $emailPattern = "/^[^\s@]+@[^\s@]+\.[^\s@]+$/";
        //Validate email
        if ($email == '') {
            $_err['email'] = 'Required';
        } else if (!is_email($email)) {
            $_err['email'] = 'Invalid Email Format Sir';
        }

        //Validate password
        if ($password == '') {
            $_err['password'] = 'Password Required';
        } else if ($passwordLenght < 8 && !preg_match("/[A-Z]/", $password)) {
            $_err['password'] = 'Password need more than 8 letters and At Least One Uppercase';
        } else if ($passwordLenght < 8) {
            $_err['password'] = 'Password Lenght Must more than 8 letter';
        } else if (!preg_match("/[A-Z]/", $password)) {
            $_err['password'] = 'At Least One Letter Uppercase';
        }


        // Check if the email exists in the member table
        if (is_exists($email, "member", "email")) {

            $stm = $_db->prepare("SELECT * FROM member WHERE email = ? AND password=SHA(?)");
            $stm->execute([$email, $password]);
            $memberDetails = $stm->fetch();

            // Verify the account and password
            if ($stm->rowCount() > 0) {
                // Login successful
                $_SESSION['login_attempts'] = 0;
                $_SESSION['lockout_time'] = 0; // Reset lockout time

                if ($memberDetails->status == "active" &&  $memberDetails->email_activation == 1) {
                    // Start session and store member_id
                    // Set welcome message and redirect
                    temp('welcome_message', "Welcome, " . $memberDetails->firstname . "!");

                    // Handle the Remember Me functionality
                    if ($rememberMe) {
                        // Set a cookie for 30 days
                        setcookie("email", $email, time() + (86400 * 30), "/");
                    } else {
                        // Clear the cookies
                        setcookie("email", "", time() - 3600, "/");
                    }

                    if ($memberDetails->role == "member") {
                        login($memberDetails);//default index
                    } else if ($memberDetails->role == "admin") {
                        login($memberDetails,"/../adminPage/adminDashboard.php");
                        
                    }
                } else if ($memberDetails->status == "inactive"  &&  $memberDetails->email_activation == 0) {
                    echo "Do you need to resend email actiavtion_link ?<a href='resendActivationLink.php?email=$email'>Resend</a><span>";

                    $email = "";
                    $password = "";
                    temp('loginChecking', "Check your email to activate,before login !");
                } else if ($memberDetails->status == "suspend"  &&  $memberDetails->email_activation == 1) {
                    $email = "";
                    $password = "";
                    temp('loginChecking', "Your account been <b style=' color: #ff0000; '>Suspended</b> !");
                }else if ($memberDetails->status == "inactive" && $memberDetails->email_activation ==1){
                    $email = "";
                    $password = "";
                    temp('loginChecking', "Your account has been deleted! Any problem? <a style='color:white;' href='../contactus.php'>Contact Us</a>");
                }
            } else {
                // Incorrect password
                $_SESSION['login_attempts']++;
                if ($_SESSION['login_attempts'] >= MAX_ATTEMPTS) {
                    $_SESSION['lockout_time'] = time();
                    temp('loginChecking', "Account locked due to too many failed login attempts. Try again in 15 minutes.");
                } else {
                    temp('loginChecking', "Incorrect Email Or Password. Attempt " . $_SESSION['login_attempts'] . " of " . MAX_ATTEMPTS);
                }

                $email = "";
                $password = "";
            }
        } else {
            // Handle email not found
            $_SESSION['login_attempts']++;
            temp('loginChecking', "<b style='color: #ff0000;'> Error: </b>No account with the email ! Attempt " . $_SESSION['login_attempts'] . " of " . MAX_ATTEMPTS);
            if ($_SESSION['login_attempts'] >= MAX_ATTEMPTS) {
                $_SESSION['lockout_time'] = time();
                temp('loginChecking', "Account locked due to too many failed login attempts. Try again in 15 minutes.");
            }
            $email = "";
            $password = "";
        }
    }
}

if (!empty($_COOKIE["email"])) {
    $email = $_COOKIE["email"];
    $rememberMe = "checked";
} else {
    $rememberMe = "";
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
    <link rel="icon" type="image/x-icon" href="/image/logo.png">
</head>

<body>
    <div id="info"><?= temp('emailActivated'); ?></div>
    <div id="info"><?= temp('loginChecking') ?></div>
    <div id="info"><?= temp('reset_message') ?></div>

    <div class="login-form">
        <div class="title">
            <small><a href="../index.php">@ SMaster Suit SDN BHD @</a></small>
            <h1>Welcome Back</h1>
            <?= err("wrongUser", "invalidWrongUser") ?>
        </div>

        <div class="container">
            <div class="left">
                <img src="../image/login_gif.gif" alt="login_gif" height="300" width="300">
            </div>

            <form class="right" method="post" action="">
                <div class="input-group">
                    <?= html_text_type("text", "email", "input-text", " placeholder=''") ?>
                    <!-- <input type="email" id="email" class="input-text" name="email" placeholder=" " oninput="checkEmail(event)"  /> -->
                    <label>Email</label>
                </div>

                <?= err("email", 'invalidTextField') ?>

                <div class="input-group">
                    <?= html_text_type("password", "password", "input-text", " placeholder=''") ?>
                    <!-- <input type="password" class="input-text" id="password" name="password" value="" placeholder=" " oninput="checkPassword(event)" > -->
                    <label>Password</label>
                    <span id="eye">
                        <i class="fa fa-eye"></i>
                    </span>
                </div>
                <?= err("password", "invalidPasswordField") ?>

                <div class="forgetPasswordBox">
                    <div>
                        <?= html_checkBox("rememberMe"); ?>
                        <label for="rememberMe">Remember Me</label>
                    </div>
                    <small><a class="forgetPassword" href="forgetPassword.php">Forget Password ?</a></small>
                </div>

                <button type="submit" id="submitButton">Login</button>
                <div class="register">
                    <p>Haven't Registered Yet? <a href="register.php">Click Here Now</a></p>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/validation.js"></script>
</body>

</html>