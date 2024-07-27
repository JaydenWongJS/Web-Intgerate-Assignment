<?php require ('_base.php')?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Passowrd</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body class="body_forget_password">

    <div class="forget-form">
        
    <div class="titleForget">
        <h2>Forget Password</h2>
    </div>
        <div class="container">
            <div class="left_forget">
                <img src="image/undraw_Forgot_password_re_hxwm.png" alt="forget" height="400" width="500">
            </div>

            <form class="right_forget" method="post" action="" onsubmit=" ">
                <div class="input-group">
                    <div  class="input-group">
                        <?= html_text_type("text","email", "input-text", " placeholder='' oninput='checkEmail(event)'") ?>
                        <label for="email">Email</label> 
                    </div>


                    <button type="submit" id="resetPassword" class="black-button">Reset</button>
                <div class="backLoginBox">
                    <p><a class="backToLogin" href="login.php">Back To Login</a></p>
                </div>
            </form>
        </div>
    </div>
    
</body>
</html>

