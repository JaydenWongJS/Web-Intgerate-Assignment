<?php
 require('_base.php');
require('_sendEmail.php');
?>
<?php

if (is_post()) {
    $firstname = req("firstname");
    $lastname = req("lastname");
    $email = req("email");
    $birthdate = req("birthdate");
    $gender = req("gender");
    $phoneNo = req("phoneNo");

    $password = req("password");
    $passwordLenght = strlen($password);

    $address1 = req("address1");
    $address2 = req("address2");
    $city = req("city");
    $state = req("state");
    $postcode = req("postcode");

    $namePattern = " /^[A-Za-z\s]+$/";
    $emailPattern = "/^[^\s@]+@[^\s@]+\.[^\s@]+$/";
    $phonePattern = "/^01[0-46-9]\d{7,8}$|^0\d{1,2}\d{7,8}$/";
    $postcodePattern="/^[0-9]+$/";


    //validation personal

    if (empty($firstname) && empty($lastname)) {
        $_err['firstLastName'] = 'First and Last Name Required';
    } else {
        if (empty($firstname)) {
            $_err['firstLastName'] = 'First Name Required';
        } else if (!preg_match($namePattern, $firstname)) {
            $_err['firstLastName'] = 'First Name Must be Only alphabet';
        }

        if (empty($lastname)) {
            $_err['firstLastName'] = 'Last Name Required';
        } else if (!preg_match($namePattern, $lastname)) {
            $_err['firstLastName'] = 'Last Name Must be Only alphabet';
        }
    }

    //Validate email
    if ($email == '') {
        $_err['email'] = 'Required';
    } else if (!preg_match($emailPattern, $email)) {
        $_err['email'] = 'Invalid Email Format Sir';
    }else if(!is_unique($email,"member","email")){
        $_err['email'] = 'An Email has been registered Before';
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
        $_err['phoneNo'] = 'phoneNo Required';
    } else if (!preg_match($phonePattern, $phoneNo)) {
        $_err['phoneNo'] = 'phoneNo Must Be Malaysia Format';
    }else if(!is_unique($phoneNo,"member","phone")){
        $_err['phoneNo'] = 'This Phone Number has been registered Before';
    }


    //Validate password
    if ($password == '') {
        $_err['password'] = 'Password Required';
    } else if($passwordLenght < 8 && !preg_match("/[A-Z]/", $password)){
        $_err['password'] = 'Password need more than 8 letters and At Least One Uppercase';
        $GLOBALS["password"]="";
    } else if ($passwordLenght < 8) {
        $_err['password'] = 'Password Lenght Must more than 8 letter';
        $GLOBALS["password"]="";
    } else if (!preg_match("/[A-Z]/", $password)) {
        $_err['password'] = 'At Least One Letter Uppercase';
        $GLOBALS["password"]="";
    } 

    //address part
    if (empty($address1)) {
        $_err["address1"]="Address1 required";
    }

    if (empty($address2)) {
        $_err["address2"]="Address2 required";
    }

    if (empty($city)) {
        $_err["city"]="City required";
    }

    if (empty($state)) {
        $_err["state"]="State required";
    }

    if(empty($postcode)){
        $_err["postcode"]="PostCode required";
    }else if(!preg_match($postcodePattern,$postcode)){
        $_err["postcode"]="Postcode must contain only numeric characters";
    }


    if (!$_err) {
        $nextMemberId = getNextIdWithPrefix('member', 'member_id', 'M', 2);
        $email_activation = 0;
        $status = "inactive";
        $member_points = 0;

         // Generate a unique token
          $activation_token = bin2hex(random_bytes(32));//prevent invalid access from url example: user type >memberId there also can help to actiavte

        $subject="Activate Account";
        $message="Congrats to being one of our member ! Click the following button to activate your account before login ! ";
        $activation_link = "http://localhost:8000/activateMember.php?member_id=$nextMemberId&token=$activation_token";

        $stm = $_db->prepare('INSERT INTO member
        (member_id, firstname, lastname, email, gender, phone, password, address1, address2, city, state, postcode, email_activation, status, member_points, activation_token)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
$stm->execute([$nextMemberId, $firstname, $lastname, $email, $gender, $phoneNo, $password, $address1, $address2, $city, $state, $postcode, $email_activation, $status, $member_points, $activation_token]);
        if ($stm->rowCount() > 0) {
            sendEmail($nextMemberId,$firstname,$lastname,$email,$subject,$message,$activation_link);
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
    <link rel="stylesheet" href="css/register.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>


    <div class="register-form">
    <div class="displayActivateMessage"><?= temp('info_activate') ?></div>
        <a class="goBack" href="login.php"> <i class="fa fa-arrow-circle-left"></i> Back To Login</a>
        <form id="registerForm" action="" method="post">
            <div class="title">
                <h1>REGISTER</h1>
            </div>

            <fieldset>
                <legend> <i class="fa fa-id-card"></i> Personal Info </legend>
                <div class="input-group-name">
                    <div class="input-group-firstname">
                        <span> <i class="fa fa-user input-icon"></i> </span>
                        <?= html_text_type("text", "firstname", "input-text", 'placeholder="First Name" oninput="checkFirstLastName()" data-upper'); ?>
                    </div>

                    <div class="input-group-lastname">
                        <span> <i class="fa fa-user input-icon"></i> </span>
                        <?= html_text_type("text", "lastname", "input-text", 'placeholder="Last Name" oninput="checkFirstLastName()" data-upper '); ?>
                    </div>
                </div>

                <?= err("firstLastName", 'invalidFirstLastField') ?>

                <div class="input-group">
                    <span> <i class="fa fa-envelope"></i> </span>
                    <?= html_text_type("text", "email", "input-text", 'placeholder="Email"  oninput="checkEmail(event)"'); ?>
                </div>

                <?= err("email", "invalidTextField"); ?>


                <div class="input-group">
                    <span> <i class="fa fa-birthday-cake"></i> </span>
                    <?= html_text_type("date", "birthdate", "input-text", 'oninput="checkBirthdate()"'); ?>
                </div>

                <?= err("birthdate", "invalidBirthdateField"); ?>

                <div class="input-group">
                    <span> <i class="fa fa-venus"></i> </span>
                    <?= html_select("gender", $_genders, "Select Gender", "class='input-text' onchange='checkGender()'"); ?>
                </div>
                <?= err("gender", "invalidGenderField"); ?>

                <div class="input-group">
                    <span> <i class="fa fa-phone"></i> </span>
                    <?= html_text_type("tel", "phoneNo", "input-text", 'placeholder="Phone No"  oninput="checkPhone(event)"'); ?>
                </div>

                <?= err("phoneNo", "invalidPhoneField"); ?>


                <div class="input-group-name">
                    <div class="input-group-firstname">
                        <span> <i class="fa fa-lock"></i> </span>
                        <?= html_text_type("password", "password", "input-text", 'placeholder="Password" id="password" oninput="checkPassword(event); "'); ?>
                    </div>
                </div>
                    <?= err("password", "invalidPasswordField"); ?>
                 

                <div class="show-password">
                    <button id="showPasswordButton" type="button">Show Password</button>
                </div>

            </fieldset>

            <fieldset>
                <legend> <i class="fa fa-map"></i> Address Details</legend>
                <div class="grid-container">
                    <div class="full-width">
                        <label for="address1">Address 1</label>
                        <?= html_text_type("text", "address1", "input-address", 'placeholder="XXXX"   oninput="checkAddress1()" data-upper'); ?>
                        <?= err("address1", "invalidAddress1Field"); ?>
                    </div>
                    <div class="full-width">
                        <label for="address2">Address 2</label>
                        <?= html_text_type("text", "address2", "input-address", 'placeholder="XXXX"   oninput="checkAddress2()" data-upper'); ?>
                        <?= err("address2", "invalidAddress2Field"); ?>
                    </div>
                    <div>
                        <label for="city">City</label>
                        <?= html_text_type("text", "city", "input-address", 'placeholder="XXXX"   oninput="checkCity()" data-upper'); ?>
                        <?= err("city", "invalidCityField"); ?>
                    </div>
                    <div>
                        <label for="state">State</label>
                        <?= html_text_type("text", "state", "input-address", 'placeholder="XXXX"   oninput="checkState()" data-upper'); ?>
                        <?= err("state", "invalidStateField"); ?>
                    </div>
                    <div class="full-width">
                        <label for="postcode">Postcode</label>
                        <?= html_text_type("text", "postcode", "input-address", 'placeholder="XXXX"   oninput="checkPostcode()"'); ?>
                        <?= err("postcode", "invalidPostcodeField"); ?>
                    </div>
                </div>
            </fieldset>



            <div style="text-align: end; margin-top:10px;">
                <input style="font-weight: 900;" type="submit" value="Register" name="submit" class="submit" id="registerButton" />
            </div>

        </form>
    </div>


    <script src="js/validation.js"></script>
    <script>
          // Auto uppercase
  $(() => {
    $('[data-upper]').on('input', e => {
      const a = e.target.selectionStart;
      const b = e.target.selectionEnd;
      e.target.value = e.target.value.toUpperCase();
      e.target.setSelectionRange(a, b);
  });

  const $showPasswordButton = $("#showPasswordButton");
    const $passwordField = $("#password");

    $showPasswordButton.on("click", function() {
        if ($passwordField.attr("type") === "password") {
            $passwordField.attr("type", "text");
            $showPasswordButton.text("Hide Password");
        } else {
            $passwordField.attr("type", "password");
            $showPasswordButton.text("Show Password");
        }
    });
  });
  
    </script>
</body>

</html>