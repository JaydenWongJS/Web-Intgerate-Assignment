<?php
require '_base.php';
?>

<?php
if (is_post()) {
    $email = req("email");
    $password = req("password");
    $passwordLenght = strlen($password);

    $emailPattern = "/^[^\s@]+@[^\s@]+\.[^\s@]+$/";
    //Validate email
    if ($email == '') {
        $_err['email'] = 'Required';
    } else if (!preg_match($emailPattern, $email)) {
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
        // Fetch the member details
        $stm = $_db->prepare("SELECT * FROM member WHERE email = ?");
        $stm->execute([$email]);
        $memberDetails = $stm->fetch();

        // Verify the password
        if ($password == $memberDetails->password) {
            if( $memberDetails->status=="active" &&  $memberDetails->email_activation==1){
                     // Start session and store member_id
            session_start();
            $_SESSION["member_id"] = $memberDetails->member_id;
                          // Set welcome message and redirect
            temp('welcome_message', "Welcome, " . $memberDetails->firstname . "!");
            redirect('index.php');
            }else if( $memberDetails->status=="inactive"  &&  $memberDetails->email_activation==0 ){
                $email="";
                $password="";
                temp('loginChecking', "Check your email to activate,before login !");
            }else if($memberDetails->status=="suspend"  &&  !$memberDetails->email_activation >0 ){
                $email="";
                $password="";
                 temp('loginChecking', "Your account been <b style=' color: #ff0000; '>Suspended</b> !");
            }
  
        } else {
            // Handle incorrect password
            temp('loginChecking', "<b style='color: #ff0000;'>Error: </b>Incorrect password !");
            $email = "";
            $password = "";
        }
    } else {
        // Handle email not found
        temp('loginChecking', "<b style='color: #ff0000;'> Error: </b>No account with the email ! ");
        $email = "";
        $password = "";
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <div id="info"><?= temp('emailActivated'); ?></div>
    <div id="info"><?=temp('loginChecking')?></div>

    <div class="login-form">
        <div class="title">
            <small>@ SMaster Suit SDN BHD @</small>
            <h1>Welcome Back</h1>
            <?= err("wrongUser", "invalidWrongUser") ?>
        </div>

        <div class="container">
            <div class="left">
                <img src="image/login_gif.gif" alt="login_gif" height="300" width="300">
            </div>

            <form class="right" method="post" action="" onsubmit="return validateForm() ">
                <div class="input-group">
                    <?= html_text_type("text", "email", "input-text", " placeholder='' oninput='checkEmail(event)'") ?>
                    <!-- <input type="email" id="email" class="input-text" name="email" placeholder=" " oninput="checkEmail(event)"  /> -->
                    <label>Email</label>
                </div>

                <?= err("email", 'invalidTextField') ?>

                <div class="input-group">
                    <?= html_text_type("password", "password", "input-text", " placeholder='' oninput='checkPassword(event)'") ?>
                    <!-- <input type="password" class="input-text" id="password" name="password" value="" placeholder=" " oninput="checkPassword(event)" > -->
                    <label>Password</label>
                    <span id="eye">
                        <i class="fa fa-eye"></i>
                    </span>
                </div>
                <div class="forgetPasswordBox">
                    <small><a class="forgetPassword" href="forgetPassword.php">Forget Password ?</a></small>
                </div>
                <?= err("password", "invalidPasswordField") ?>

                <button type="submit" id="submitButton" disabled>Login</button>
                <div class="register">
                    <p>Haven't Registered Yet? <a href="register.php">Click Here Now</a></p>
                </div>
            </form>
        </div>
    </div>

    <script src="js/validation.js"></script>
</body>

</html>