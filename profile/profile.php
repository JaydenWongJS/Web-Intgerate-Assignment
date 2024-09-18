<?php
require('../_base.php');

//--------------------------------------------------------
auth("member");
clear_cart();

$title=$_user->firstname."'s Profile ";
include('../_header.php');
include('../nav_bar.php');


$memberId=$_user->member_id ;
if (is_post()) {
    if (isset($_POST["confirmChangePersonal"])) {
        $firstname = req("firstname");
        $lastname = req("lastname");
        $birthdate = req("birthdate");
        $gender = req("gender");
        $phoneNo = req("phoneNo");

        $namePattern = " /^[A-Za-z\s]+$/";
        $phonePattern = "/^01[0-46-9]\d{7,8}$|^0\d{1,2}\d{7,8}$/";
        $postcodePattern = "/^[0-9]+$/";

        if (empty($firstname)) {
            $_err['firstname'] = 'First Name Required';
        } else if (!preg_match($namePattern, $firstname)) {
            $_err['firstname'] = 'First Name Must be Only alphabet';
        }

        if (empty($lastname)) {
            $_err['lastname'] = 'Last Name Required';
        } else if (!preg_match($namePattern, $lastname)) {
            $_err['lastname'] = 'Last Name Must be Only alphabet';
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
        } else {
            $phoneCheckQuery = "SELECT member_id FROM member WHERE phone = ?";
            $phoneCheckStmt = $_db->prepare($phoneCheckQuery);
            $phoneCheckStmt->execute([$phoneNo]);
            $existingPhoneRecord = $phoneCheckStmt->fetch();

            if ($existingPhoneRecord && $existingPhoneRecord->member_id != $memberId) {
                $_err['phoneNo'] = 'The phone number entered just now has been registered before';
            }
        }



        if (!$_err) {
            $updateQuery = "UPDATE member SET firstname = ?, lastname = ?, birthdate = ?, gender = ?, phone = ? WHERE member_id = ?";
            $stmt = $_db->prepare($updateQuery);
            $stmt->execute([$firstname, $lastname, $birthdate, $gender, $phoneNo, $memberId]);

            if ($stmt->rowCount() > 0) {
                $_user->firstname = $firstname;
                $_user->lastname = $lastname;
                $_user->birthdate = $birthdate;
                $_user->phone = $phoneNo;
                $_user->gender = $gender;
                temp("updateProfileInfo", "<b class='successInfo'>Personal Details Successful Updated </b>");
            }
        } else {
            temp("updateProfileInfo", "<b class='fail'>Personal Details Failed To Update </b> ");
            echo '<script>
            $(document).ready(function() {
                $("#personal_info").hide();
                $("#update_personal_info").hide();
                $("#personal_info_input_form").show();
            });
        </script>';
        }
    }

    if (isset($_POST["confirmChangeAddress"])) {
        $address1 = req("address1");
        $address2 = req("address2");
        $city = req("city");
        $state = req("state");
        $postcode = req("postcode");

        $postcodePattern = "/^[0-9]+$/";

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
        } else if (!preg_match($postcodePattern, $postcode)) {
            $_err["postcode"] = "Postcode must contain only numeric characters";
        }

        if (!$_err) {
            $updateQuery = "UPDATE member SET  address1 = ?, address2 = ?, city = ?, state = ?, postcode = ? WHERE member_id = ?";
            $stmt = $_db->prepare($updateQuery);
            $stmt->execute([$address1, $address2, $city, $state, $postcode, $memberId]);
            if ($stmt->rowCount() > 0) {
                $_user->address1 = $address1;
                $_user->address2 = $address2;
                $_user->city = $city;
                $_user->state = $state;
                $_user->postcode = $postcode;
                temp("updateProfileInfo", "<b class='successInfo'>Address Details Successful Updated</b>");
            }
        } else {
            temp("updateProfileInfo", "<b class='fail' >Address Details Failed To Update</b>");
            echo '<script> 
                $(document).ready(function() {
             $("#address_info").hide();
            $("#update_address_info").hide();
            $("#address_info_input_form").show();
                });
        </script>';
        }
    }
}

//original data from db
$memberDetails = retrieveAllValue($memberId, "member", "member_id");
//personal details
$member_points = $memberDetails->member_points;
$firstname = $memberDetails->firstname;
$lastname = $memberDetails->lastname;

$name = $firstname . " " . $lastname;

$birthdate = $memberDetails->birthdate;
$email = $memberDetails->email;
$phoneNo = $memberDetails->phone;
$gender = $memberDetails->gender;
$image = !empty($memberDetails->image) ? "../uploadsImage/userProfile/" . $memberDetails->image : "../image/user_icon-removebg-preview.png";

//address details
$address1 = $memberDetails->address1;
$address2 = $memberDetails->address2;
$city = $memberDetails->city;
$state = $memberDetails->state;
$postcode = $memberDetails->postcode;

// echo"gender is $gender";

// Initialize $gender_ as null
$gender_ = null;

// Check if the gender code exists in the $_genders array
if (array_key_exists($gender, $_genders)) {
    $gender_ = $_genders[$gender];
} else {
    $gender_ = "unknown";
}

// echo "gender: $gender_";
?>




<div id="info"><?= temp("updateProfileInfo"); ?></div>

<div class="profile_container">
    <div class="qr_container">
        <div class="qr_box_top">
            <div class="qr_left">
                <h2>SMART CLUB <i style="color:red;" class="fa fa-heart"></i> MEMBER</h2>
                <h3>Member ID :
                    <?= $memberId; ?>
                </h3>
                <input type="hidden" id="memberId" value="<?= $memberId; ?>" />
                <h4>Points Earn : <?= $member_points ?> pts</h4>
            </div>
            <div class="qr_right">
                <div class="qr_code">
                    <div id="qrcode"></div>
                </div>
                <h3>QR Code</h3>
            </div>
        </div>

        <div class="qr_box_bottom">
            <a href="../myOrder.php" class="beautiful_box">My Orders</a>
        </div>

        <div class="log_out" style="text-align: center;">
            <a href="../log_out.php" class="beautiful_box">Log Out</a>
        </div>
    </div>
    <div class="personal_info_container">
        <div class="profile_image_container">
            <div class="teams_image">
                <img src="<?= $image ?>" alt="team Image">
            </div>
            <small><a class="change_profile_pic" href="#">Change Profile Picture</a></small>
            <h2 class="member_name"><cite>Hi , <?= $firstname ?></cite></h2>
        </div>

        <div class="personal_details_container">
            <div class="your_info">
                <div class="title_info">
                    <h2>Personal Info</h2>
                    <button type="button" class="update" id="update_personal_info">Update</button>
                </div>

                <div class="overall_info" id="personal_info">
                    <p class="details_info">
                        <i class="fas fa-user"></i>
                        <span>Name</span> <br />&nbsp;<?= $name ?>
                    </p>
                    <p class="details_info">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Date Of Birth</span><br />&nbsp;<?= $birthdate ?>
                    </p>
                    <p class="details_info">
                        <i class="fa fa-envelope"></i>
                        <span>Email</span><br />&nbsp;<?= $email ?>
                    </p>
                    <p class="details_info">
                        <i class="fas fa-phone" style="transform: rotate(90deg);"></i>
                        <span>Contact No</span><br />&nbsp;<?= $phoneNo ?>
                    </p>
                    <p class="details_info">
                        <i class="fas fa-mars"></i>
                        <span>Gender</span><br />&nbsp;<?= $gender_ ?>
                    </p>
                </div>

                <form action="" method="post" class="overall_info" id="personal_info_input_form" style="display: none;">
                    <p class="details_info">
                        <label>First Name : </label>
                        <br />
                        <?= html_text_type("text", "firstname", "input_underline", 'placeholder="First Name" data-upper'); ?>
                    </p>
                    <?= err("firstname", 'invalidLastField') ?>
                    <p class="details_info">
                        <label>Last Name : </label>
                        <br />
                        <?= html_text_type("text", "lastname", "input_underline", 'placeholder="Last Name" data-upper'); ?>
                    </p>

                    <?= err("lastname", 'invalidLastField') ?>

                    <p class="details_info">
                        <label>Date Of Birth : </label>
                        <br />
                        <?= html_text_type("date", "birthdate", "input_underline", ''); ?>
                    </p>

                    <?= err("birthdate", "invalidBirthdateField"); ?>

                    <p class="details_info">
                        <label>Email : </label>
                        <br />
                        <?= html_text_type("text", "email", "input_underline", 'placeholder="Email" readonly'); ?>

                    <p class="details_info">
                        <label>Phone No : </label>
                        <br />
                        <?= html_text_type("tel", "phoneNo", "input_underline", 'placeholder="Phone No" '); ?>
                    </p>

                    <?= err("phoneNo", "invalidPhoneField"); ?>

                    <p class="details_info">
                        <label>Gender : </label>
                        <br />
                        <?= html_select("gender", $_genders, "Select Gender", "class='custom_select_style'"); ?>
                    </p>

                    <?= err("gender", "invalidGenderField"); ?>

                    <p class="button-container">
                        <button type="button" id="cancel">cancel</button>
                        <button type="reset" id="reset">reset</button>
                        <button type="button" class="comfirmUpdate" id="changeInfo" name="change">Change Info</button>
                    </p>
                    <div class="overlay_all" id="personalInfoModal" style="display: none;">
                        <div class="modal">
                            <h3 style="text-align: left;margin-left:10px;">Change Personal Info</h3>
                            <h2 class="comfirmMessage">Are You Sure Want To Update?</h2>
                            <button type="button" id="closePersonalInfoModal" class="notConfirmUpdate">NO</button>
                            <input type="submit" name="confirmChangePersonal" class="comfirmUpdate" value="Yes" />
                        </div>
                    </div>

                </form>
            </div>
        </div>


        <div class="personal_details_container">
            <div class="your_info">
                <div class="title_info">
                    <h2>Address Info</h2>
                    <button class="update" id="update_address_info">Update</button>
                </div>

                <div class="overall_info" id="address_info">
                    <p class="details_info">
                        <span>Address 1</span> <br />&nbsp;<?= $address1 ?>
                    </p>

                    <p class="details_info">
                        <span>Address 2</span><br />&nbsp;<?= $address2 ?>
                    </p>
                    <p class="details_info">
                        <span>City</span><br />&nbsp;<?= $city ?>
                    </p>
                    <p class="details_info">
                        <span>State</span><br />&nbsp;<?= $state ?>
                    </p>
                    <p class="details_info">
                        <span>Postcode</span><br />&nbsp;<?= $postcode ?>
                    </p>
                </div>
                <form action="" method="post" class="overall_info" id="address_info_input_form" style="display: none;">
                    <p class="details_info">
                        <label>Address 1 : </label>
                        <br />
                        <?= html_text_type("text", "address1", "input_underline", " placeholder='XXXXXXX' data-upper") ?>
                    </p>

                    <?= err("address1", "invalidAddress1Field"); ?>

                    <p class="details_info">
                        <label>Address 2 : </label>
                        <br />
                        <?= html_text_type("text", "address2", "input_underline", " placeholder='XXXXXXX' data-upper") ?>
                    </p>

                    <?= err("address2", "invalidAddress2Field"); ?>

                    <p class="details_info">
                        <label>City : </label>
                        <br />
                        <?= html_text_type("text", "city", "input_underline", " placeholder='XXXXXXX' data-upper") ?>
                    </p>

                    <?= err("city", "invalidCityField"); ?>

                    <p class="details_info">
                        <label>State : </label>
                        <br />
                        <?= html_text_type("text", "state", "input_underline", " placeholder='XXXXXXX' data-upper ") ?>
                    </p>

                    <?= err("state", "invalidStateField"); ?>

                    <p class="details_info">
                        <label>PostCode : </label>
                        <br />
                        <?= html_text_type("text", "postcode", "input_underline", " placeholder='XXXXXXX'") ?>
                    </p>

                    <?= err("postcode", "invalidPostcodeField"); ?>

                    <p class="button-container">
                        <button type="button" id="cancelAddress">Cancel</button>
                        <button type="reset" id="resetAddress">reset</button>
                        <button type="button" id="changeAddress" class="comfirmUpdate" name="change">Change Address</button>
                    </p>
                    <div class="overlay_all" id="addressModal" style="display: none;">
                        <div class="modal">
                            <h3 style="text-align: left; margin-left: 10px;">Change Address Info</h3>
                            <h2 class="comfirmMessage">Are You Sure You Want To Update?</h2>
                            <button type="button" id="closeAddressModal" class="notConfirmUpdate">NO</button>
                            <input type="submit" name="confirmChangeAddress" class="comfirmUpdate" value="Yes" />
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<div class="overlay_all" id="imageUpdateModal" style="display:none;">
    <div class="modal">
        <div class="modal_header">
            <h2>Upload Profile Image</h2>
            <button type="button" class="xButton" id="modalCloseImageBtn">X </button>
        </div>

        <form action="uploadProfileImage.php" method="post" class="modal_body" enctype="multipart/form-data">
            <div class="file-upload" id="dropArea">
                <p>Drag and Drop file</p>
                <label class="upload" tabindex="0">
                    <?= html_file('photo', 'image/*', 'hidden'); ?>
                    <img src="/image/photo.jpg" id="userPic" height="200" width="200"/>
                </label>
            </div>
            <input type="submit" value="Upload" name="uploadImage" class="comfirmUpdate" />
        </form>
    </div>
</div>




<?php include('../_footer.php') ?>