<?php

$memberId = "A09";
$number = (int)explode("A", $memberId)[1]; // Extract and cast to integer

$number += 1; // Increment the number

if ($number >= 10) {
    $memberId = "A" . $number; // Concatenate "A" with the number
} else {
    $memberId = "A0" . $number; // Concatenate "A0" with the number
}



$title = "Profile";
include('_header.php');
// Generate the QR code URL using goqr.me
$qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($memberId);
?>


<div class="profile_container">
    <div class="qr_container">
        <div class="qr_box_top">
            <div class="qr_left">
                <h2>SMART CLUB <i style="color:red;" class="fa fa-heart"></i> MEMBER</h2>
                <h3>Member ID :
                    <?php echo $memberId; ?>
                </h3>
                <h4>Points Earn : 1900 pts</h4>
            </div>
            <div class="qr_right">
                <div class="qr_code">
                    <div id="qrcode"></div>
                </div>


                <h3>QR Code</h3>
            </div>
        </div>

        <div class="qr_box_bottom">
            <a href="myOrder.php" class="beautiful_box">My Orders</a>
        </div>
    </div>
    <div class="personal_info_container">
        <div class="profile_image_container">
            <div class="teams_image">
                <img src="image/user_icon-removebg-preview.png" alt="team Image">
            </div>
            <small><a class="change_profile_pic" onclick="openModal('imageUpdateModal')" href="#">Change Profile Picture</a></small>
            <h2 class="member_name"><cite>Hi , Yong CF</cite></h2>
        </div>

        <div class="personal_details_container">
            <div class="your_info">
                <div class="title_info">
                    <h2>Personal Info</h2>
                    <button type="button" class="update" id="update_personal_info" onclick="changePersonalDetails()">Update</button>
                </div>

                <div class="overall_info" id="personal_info">
                    <p class="details_info">
                        <i class="fas fa-user"></i>
                        <span>Name</span> <br />&nbsp;Yong CF
                    </p>
                    <p class="details_info">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Date Of Birth</span><br />&nbsp;19 Sept 2004
                    </p>
                    <p class="details_info">
                        <i class="fa fa-envelope"></i>
                        <span>Email</span><br />&nbsp;yfung2574@gmail.com
                    </p>
                    <p class="details_info">
                        <i class="fas fa-phone" style="transform: rotate(90deg);"></i>
                        <span>Contact No</span><br />&nbsp;012-8082165
                    </p>
                    <p class="details_info">
                        <i class="fas fa-mars"></i>
                        <span>Gender</span><br />&nbsp;Male
                    </p>
                </div>

                <form action="" method="post" class="overall_info" id="personal_info_input_form" style="display: none;">
                    <p class="details_info">
                        <label>First Name : </label>
                        <br />
                        <input type="text" class="input_underline" name="firstName" id="firstName" value="" placeholder="Yong" />
                    </p>
                    <p class="details_info">
                        <label>Last Name : </label>
                        <br />
                        <input type="text" class="input_underline" name="lastName" id="lastName" value="" placeholder="Cheng Fung" />
                    </p>

                    <p class="details_info">
                        <label>Date Of Birth : </label>
                        <br />
                        <input type="date" class="input_underline" name="dob" id="dob" value="" />
                    </p>

                    <p class="details_info">
                        <label>Email : </label>
                        <br />
                        <input type="email" class="input_underline" name="email" id="email" value="" placeholder="example@gmail.com" />
                    </p>

                    <p class="details_info">
                        <label>Contact No : </label>
                        <br />
                        <input type="tel" class="input_underline" name="tel" id="tel" value="" placeholder="012-8082165" />
                    </p>

                    <p class="details_info">
                        <label>Gender : </label>
                        <br />
                        <select name="gender" id="gender" class="input_underline">
                            <option style="color:black;" value="none" selected>Select Gender</option>
                            <option style="color:black;" value="male">Male</option>
                            <option style="color:black;" value="female">Female</option>
                        </select>
                    </p>

                    <p class="button-container">
                        <button type="button" id="cancel">Cancel</button>
                        <button type="button" class="comfirmUpdate" id="changeInfo" name="change" onclick="openModal('personalInfoModal')">Change Info</button>
                    </p>
                    <div class="overlay_all" id="personalInfoModal" style="display: none;">
                        <div class="modal">
                            <h3 style="text-align: left;margin-left:10px;">Change Personal Info</h3>
                            <h2 class="comfirmMessage">Are You Sure Want To Update?</h2>
                            <button type="button" class="notConfirmUpdate" onclick="closeModal('personalInfoModal')">NO</button>
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
                    <button class="update" id="update_address_info" onclick="changeAddressDetails()">Update</button>
                </div>

                <div class="overall_info" id="address_info">
                    <p class="details_info">
                        <span>Address 1</span> <br />&nbsp;B-02-13 , Sri Cassia Apartment
                    </p>
                    <p class="details_info">
                        <span>Address 2</span><br />&nbsp;Laman Puteri Puchong
                    </p>
                    <p class="details_info">
                        <span>City</span><br />&nbsp;Puchong
                    </p>
                    <p class="details_info">
                        <span>State</span><br />&nbsp;Selangor
                    </p>
                    <p class="details_info">
                        <span>Postcode</span><br />&nbsp;47100
                    </p>
                </div>
                <form action="" method="post" class="overall_info" id="address_info_input_form" style="display: none;">
                    <p class="details_info">
                        <label>Address 1 : </label>
                        <br />
                        <input type="text" class="input_underline" name="address1" id="address1" value="" placeholder="B-02-13, Sri Cassia Apartment" />
                    </p>
                    <p class="details_info">
                        <label>Address 2 : </label>
                        <br />
                        <input type="text" class="input_underline" name="lastName" id="lastName" value="" placeholder="Laman Puteri 1" />
                    </p>

                    <p class="details_info">
                        <label>City : </label>
                        <br />
                        <input type="text" class="input_underline" name="city" id="city" value="" placeholder="Puchong" />
                    </p>

                    <p class="details_info">
                        <label>State : </label>
                        <br />
                        <input type="text" class="input_underline" name="state" id="state" value="" placeholder="Selangor" />
                    </p>

                    <p class="details_info">
                        <label>PostCode : </label>
                        <br />
                        <input type="text" class="input_underline" name="postcode" id="postcode" value="" placeholder="47100" />
                    </p>

                    <p class="button-container">
                        <button type="button" id="cancelAddress">Cancel</button>
                        <button type="button" id="changeAddress" class="comfirmUpdate" name="change" onclick="openModal('addressModal')">Change Address</button>
                    </p>
                    <div class="overlay_all" id="addressModal" style="display: none;">
                        <div class="modal">
                            <h3 style="text-align: left;margin-left:10px;">Change Address Info</h3>
                            <h2 class="comfirmMessage">Are You Sure Want To Update?</h2>
                            <button type="button" class="notConfirmUpdate" onclick="closeModal('addressModal')">NO</button>
                            <input type="submit" name="confirmChangeAddress" class="comfirmUpdate" value="Yes" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="overlay_all" id="successModal" style="display: none;">
    <div class="modal">
        <h2>Your Info Successful Updated</h2>
        <img src="image/update_success.gif" alt="success_register" height="320" width="400">

        <button type="button" class="modalCloseBtn" id="modalCloseBtn" onclick="closeModal('successModal')">OK</button>
    </div>
</div>

<div class="overlay_all" id="imageUpdateModal" style="display:none;">
    <div class="modal">
        <div class="modal_header">
            <h2>Upload Profile Image</h2>
            <button type="button" class="xButton" id="modalCloseBtn" onclick="closeModal('imageUpdateModal')">X </button>
        </div>

        <form action="" method="post" class="modal_body">
            <div class="file-upload">
                <p>Drag and Drop file</p>
                <input type="file" id="fileInput" />
            </div>
            <input type="submit" value="Upload" name="uploadImage" class="comfirmUpdate" />
        </form>

    </div>
</div>

<script>
        document.addEventListener("DOMContentLoaded", function() {
            // Personal info
            const updateButton = document.getElementById("update_personal_info");
            const cancelButton = document.getElementById("cancel");
            const personalInfo = document.getElementById("personal_info");
            const personalInfoInputForm = document.getElementById("personal_info_input_form");

            updateButton.addEventListener("click", changePersonalDetails);

            function changePersonalDetails() {
                console.log("Changing Display Details Become Form Input");
                personalInfo.style.display = "none";
                updateButton.style.display = "none";
                personalInfoInputForm.style.display = "block";
            }

            cancelButton.addEventListener("click", function () {
                personalInfo.style.display = "block";
                updateButton.style.display = "block";
                personalInfoInputForm.style.display = "none";
            });

            // Address info
            const updateAddressButton = document.getElementById("update_address_info");
            const cancelAddressButton = document.getElementById("cancelAddress");
            const addressInfo = document.getElementById("address_info");
            const addressInfoInputForm = document.getElementById("address_info_input_form");

            updateAddressButton.addEventListener("click", changeAddressDetails);

            function changeAddressDetails() {
                console.log("Changing Display Address Details Become Form Input");
                addressInfo.style.display = "none";
                updateAddressButton.style.display = "none";
                addressInfoInputForm.style.display = "block";
            }

            cancelAddressButton.addEventListener("click", function () {
                addressInfo.style.display = "block";
                updateAddressButton.style.display = "block";
                addressInfoInputForm.style.display = "none";
            });
        });

    </script>

<script>
$(document).ready(function() {
    const memberId = "<?= $memberId ?>"; // Wrap in quotes for string
  
    $('#qrcode').qrcode({
        text: memberId,
        width: 128,
        height: 128
    });
});
</script>



<?php include('_footer.php') ?>