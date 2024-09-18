<?php
require('../_base.php');
require('../_sendEmail.php');
?>
<?php
if (isLoggedIn()) {
    redirect('/');
}


$otpVerifiedSuccess = false;
$readOnlyEmail="";

if (is_post()) {
    $firstname = req("firstname");
    $lastname = req("lastname");
    $email = req("email");

    $otpInput = req("otpInput");

    $birthdate = req("birthdate");
    $gender = req("gender");
    $phoneNo = req("phoneNo");

    $password = req("password");
    $passwordLenght = strlen($password);

    $f = get_file('photo');

    $address1 = req("address1");
    $address2 = req("address2");
    $city = req("city");
    $state = req("state");
    $postcode = req("postcode");


    // Google reCAPTCHA verification
    $recaptchaSecret = '6Le7aBwqAAAAAOhkCD4V58zOFY-JYRthtYNdo5wh'; // Your secret key
    $recaptchaResponse = $_POST['g-recaptcha-response'];
    $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
    $response = file_get_contents($verifyUrl . '?secret=' . $recaptchaSecret . '&response=' . $recaptchaResponse);
    $responseData = json_decode($response);

    if (isset($_SESSION["otp"]) && !empty($_SESSION["otp"])) {
        $otpInput = $_SESSION["otp"];
    } 
    
    if (empty($otpInput)) {
        $_err['otpInput'] = "OTP is required.";
    } else {
        // Fetch the stored OTP from the database using PDO
        $query = "SELECT otp, otp_expiration, otp_used FROM email_otp WHERE email = ? AND otp_used = 0";
        $stm = $_db->prepare($query);
        $stm->execute([$email]);
        $row = $stm->fetch();

        if ($row) {
            $currentOtp = $row->otp;
            $currentOtpTime = strtotime($row->otp_expiration);
            $currentTime = time(); // Get current time

            // Check if the provided OTP matches the current OTP
            if ($otpInput == $currentOtp) {
                // Check if the OTP is expired
                if ($currentTime > $currentOtpTime) {
                    $_err['otpInput'] = "OTP has expired.";
                    $otpInput="";
                    unset( $_SESSION["otp"]);
                } else {
                    $_SESSION["otp"]=$otpInput;
                    $readOnlyEmail='readonly';
                    $otpVerifiedSuccess = true;
                }
            } else {
                $_err['otpInput'] = "Invalid OTP provided.";
                $otpInput="";
                unset( $_SESSION["otp"]);
            }
        } else {
            $_err['otpInput'] = "No valid OTP found for this email.";
            $otpInput="";
            unset( $_SESSION["otp"]);
        }
    }



    if (!$responseData->success) {
        $_err['recaptcha'] = 'reCAPTCHA verification failed. Please try again.';
    }

    //validation personal

    if (empty($firstname) && empty($lastname)) {
        $_err['firstLastName'] = 'First and Last Name Required';
    } else {
        if (empty($firstname)) {
            $_err['firstLastName'] = 'First Name Required';
        } else if (!is_character_string($firstname)) {
            $_err['firstLastName'] = 'First Name Must be Only alphabet';
        }

        if (empty($lastname)) {
            $_err['firstLastName'] = 'Last Name Required';
        } else if (!is_character_string($lastname)) {
            $_err['firstLastName'] = 'Last Name Must be Only alphabet';
        }
    }

    //Validate email
    if ($email == '') {
        $_err['email'] = 'Required';
    } else if (!is_email($email)) {
        $_err['email'] = 'Invalid Email Format Sir';
    } else if (!is_unique($email, "member", "email")) {
        $_err['email'] = $email . ' is NOT ALLOWED to use !';
    }


    if (empty($birthdate)) {
        $_err['birthdate']  = 'Birthdate is required';
    } else {
        $birthdateDateTime = DateTime::createFromFormat('Y-m-d', $birthdate);
        $today = new DateTime();

        if (!$birthdateDateTime) {
            $_err['birthdate']  = 'Invalid birthdate format !';
        } else if ($birthdateDateTime >= $today) {
            $_err['birthdate']  = 'Birthdate must be a past date !';
        }
    }

    // Validate gender
    if ($gender == '') {
        $_err['gender'] = 'Required';
    } else if (!array_key_exists($gender, $_genders)) {
        $_err['name'] = 'Invalid value';
    }

    if (empty($phoneNo)) {
        $_err['phoneNo'] = 'Phone Number Required';
    } else if (!is_malaysia_phone($phoneNo)) {
        $_err['phoneNo'] = 'Phone Number Must Be Malaysia Format';
    } else if (!is_unique($phoneNo, "member", "phone")) {
        $_err['phoneNo'] = $phoneNo . ' Not Allowed To Use !';
    }


    //Validate password
    if ($password == '') {
        $_err['password'] = 'Password Required';
    } else if ($passwordLenght < 8 && !preg_match("/[A-Z]/", $password)) {
        $_err['password'] = 'Password need more than 8 letters and At Least One Uppercase';
        $password == '';
    } else if ($passwordLenght < 8) {
        $_err['password'] = 'Password Lenght Must more than 8 letter';
        $password == '';
    } else if (!preg_match("/[A-Z]/", $password)) {
        $_err['password'] = 'At Least One Letter Uppercase';
        $password == '';
    }

    //file validation
    if ($f == null) {
        $_err['photo'] = 'Profile Photo Is required !';
    } else if (!str_starts_with($f->type, "image/")) {
        $_err['photo'] = 'Must Be An Image Only !';
    } else if ($f->size > 1 * 1024 * 1024) {
        $_err['photo'] = 'Max 1MB only !';
    }
    //address part
    if (empty($address1)) {
        $_err["address1"] = "Address1 required";
    }

    if (empty($address2)) {
        $_err["address2"] = "Address2 required";
    }

    if (empty($city)) {
        $_err["city"] = "City required";
    }

    if (empty($state)) {
        $_err["state"] = "State required";
    }

    if (empty($postcode)) {
        $_err["postcode"] = "PostCode required";
    } else if (!is_postcode($postcode)) {
        $_err["postcode"] = "Postcode must contain only numeric characters";
    }


    if (!$_err) {
        // Save the new profile photo
        $photo = save_photo($f, '../uploadsImage/userProfile');

        $nextMemberId = getNextIdWithPrefix('member', 'member_id', 'M', 2);

        $email_activation = 0;
        $status = "inactive";
        $member_points = 0;

        // Generate a unique token
        $activation_token = sha1(uniqid() . rand());

        $subject = "Activate Member Account";
        $message = "Congrats on becoming one of our members! Click the following button to activate your account before logging in!";
        $activation_link = base("login_register/activateMember.php?token=$activation_token");

        // Prepare the queries
        $stm = $_db->prepare('
            INSERT INTO member
            (member_id, firstname, lastname, birthdate, email, gender, phone, password, role, address1, address2, city, state, postcode, email_activation, status, member_points, image)
            VALUES (?, ?, ?, ?, ?, ?, ?, SHA(?), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);
            
            INSERT INTO token (token_id, expire, member_id) 
            VALUES (?, ADDTIME(NOW(), "00:05"), ?);

            Update email_otp SET otp_used=1 WHERE email=?;
        ');

        $stm->execute([
            $nextMemberId,
            $firstname,
            $lastname,
            $birthdate,
            $email,
            $gender,
            $phoneNo,
            $password,
            "member",
            $address1,
            $address2,
            $city,
            $state,
            $postcode,
            $email_activation,
            $status,
            $member_points,
            $photo,
            $activation_token,
            $nextMemberId,
            $email
        ]);

        if ($stm->rowCount() > 0) {
            sendEmail($firstname, $lastname, $email, $subject, $message, $activation_link, 'activation');
            unset( $_SESSION["otp"]);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../css/register.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="icon" type="image/x-icon" href="../image/logo.png">
</head>

<body>

    <div id="loadingOverlay" style="display: none;">
        <div class="spinner"></div>
        <p>Registering your account, please wait...</p>
    </div>

    <div class="register-form">
        <div class="displayActivateMessage"><?= temp('info_activate') ?></div>
        <a class="goBack" href="login.php"> <i class="fa fa-arrow-circle-left"></i> Back To Login</a>
        <form id="registerForm" action="" method="post" enctype="multipart/form-data">
            <div class="title">
                <h1>REGISTER</h1>
            </div>

            <fieldset>
                <legend> <i class="fa fa-id-card"></i> Personal Info </legend>
                <div class="input-group-name">
                    <div class="input-group-firstname">
                        <span> <i class="fa fa-user input-icon"></i> </span>
                        <?= html_text_type("text", "firstname", "input-text", 'placeholder="First Name" data-upper'); ?>
                    </div>

                    <div class="input-group-lastname">
                        <span> <i class="fa fa-user input-icon"></i> </span>
                        <?= html_text_type("text", "lastname", "input-text", 'placeholder="Last Name"  data-upper '); ?>
                    </div>
                </div>

                <?= err("firstLastName", 'invalidFirstLastField') ?>

                <div class="input-group">
                    <span><i class="fa fa-envelope"></i></span>
                    <?= html_text_type("text", "email", "input-text", 'placeholder="Email" ' . $readOnlyEmail); ?>
                </div>

                <?= err("email", "invalidTextField"); ?>

                <?php if (!$otpVerifiedSuccess) { ?>
                    <div class="email_verified_group">
                        <div class="email_verify_button_section">
                            <button type="button" class="verifyEmailButton" id="verifyEmailButton">Verify Email (60 seconds)</button>
                        </div>

                         <?= html_text_type("text", "otpInput", "", 'placeholder="Enter the OTP Code!"');?> 
                    </div>
                <?php } else { ?>
                    <div class="email_verified_group" style="color: green; font-weight:bold;">
                        <i class='fa fa-check-circle'></i><span> Verified successfully</span>
                    </div>
                <?php } ?>

                <!-- PHP error output -->
                <?= err("otpInput", "invalidOtpInputField") ?>

                <div class="input-group">
                    <span> <i class="fa fa-birthday-cake"></i> </span>
                    <?= html_text_type("date", "birthdate", "input-text", ''); ?>
                </div>

                <?= err("birthdate", "invalidBirthdateField"); ?>

                <div class="input-group">
                    <span> <i class="fa fa-venus"></i> </span>
                    <?= html_select("gender", $_genders, "Select Gender", "class='input-text'"); ?>
                </div>
                <?= err("gender", "invalidGenderField"); ?>

                <div class="input-group">
                    <span> <i class="fa fa-phone"></i> </span>
                    <?= html_text_type("tel", "phoneNo", "input-text", 'placeholder="Phone No" '); ?>
                </div>

                <?= err("phoneNo", "invalidPhoneField"); ?>


                <div class="input-group-name">
                    <div class="input-group-firstname">
                        <span> <i class="fa fa-lock"></i> </span>
                        <?= html_text_type("password", "password", "input-text", 'placeholder="Password" id="password"'); ?>
                    </div>
                </div>
                <?= err("password", "invalidPasswordField"); ?>


                <div class="show-password">
                    <button id="showPasswordButton" type="button">Show Password</button>
                </div>

                <div>

                </div>

                <div class="file-upload" id="dropArea">
                    <p>Upload Profile Picture</p>
                    <small style="color:red;font-size:small;">INFO : Reclick the picture to upload, if u wish to change another profile picture</small>
                    <label class="upload" tabindex="0">
                        <?= html_file('photo', 'image/*', 'hidden'); ?>
                        <img src="/image/photo.jpg" id="userPic" height="200" width="200" />
                    </label>
                </div>

                <?= err("photo", "invalidPhotoField"); ?>

            </fieldset>

            <fieldset>
                <legend> <i class="fa fa-map"></i> Address Details</legend>
                <div class="grid-container">
                    <div class="full-width">
                        <label for="address1">Address 1</label>
                        <?= html_text_type("text", "address1", "input-address", 'placeholder="XXXX"   data-upper'); ?>
                        <?= err("address1", "invalidAddress1Field"); ?>
                    </div>
                    <div class="full-width">
                        <label for="address2">Address 2</label>
                        <?= html_text_type("text", "address2", "input-address", 'placeholder="XXXX"   data-upper'); ?>
                        <?= err("address2", "invalidAddress2Field"); ?>
                    </div>
                    <div>
                        <label for="city">City</label>
                        <?= html_text_type("text", "city", "input-address", 'placeholder="XXXX" data-upper'); ?>
                        <?= err("city", "invalidCityField"); ?>
                    </div>
                    <div>
                        <label for="state">State</label>
                        <?= html_text_type("text", "state", "input-address", 'placeholder="XXXX"  data-upper'); ?>
                        <?= err("state", "invalidStateField"); ?>
                    </div>
                    <div class="full-width">
                        <label for="postcode">Postcode</label>
                        <?= html_text_type("text", "postcode", "input-address", 'placeholder="XXXX" '); ?>
                        <?= err("postcode", "invalidPostcodeField"); ?>
                    </div>
                </div>
            </fieldset>

            <!---recaptcha  -->
            <div class="g-recaptcha" data-sitekey="6Le7aBwqAAAAAMhd8CqjYDfXySNv1pHl7KanTjFi"></div>
            <?= err("recaptcha", "invalidTextField"); ?>


            <div style="text-align: end; margin-top:10px;">
                <input style="font-weight: 900;" type="submit" value="Register" name="submit" class="submit" id="registerButton" />
            </div>

        </form>
    </div>


    <script src="../js/validation.js"></script>


</body>

</html>