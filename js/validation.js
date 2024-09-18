
$(() => {

    console.log("Start Jquery");
    console.log("Validation.js is loaded");

    const $passwordField = $("#password");
    const $eye = $("#eye");

    function changeEyeIcon() {
        const passwordType = $passwordField.attr("type");
        if (passwordType === "password") {
            $passwordField.attr("type", "text");
            $eye.html('<i class="fa fa-eye-slash"></i>');
        } else {
            $passwordField.attr("type", "password");
            $eye.html('<i class="fa fa-eye"></i>');
        }
    }

    $eye.on('click', changeEyeIcon);

    $('[data-upper]').on('input', e => {
        const a = e.target.selectionStart;
        const b = e.target.selectionEnd;
        e.target.value = e.target.value.toUpperCase();
        e.target.setSelectionRange(a, b);
    });

    const $showPasswordButton = $("#showPasswordButton");

    $showPasswordButton.on("click", function () {
        if ($passwordField.attr("type") === "password") {
            $passwordField.attr("type", "text");
            $showPasswordButton.text("Hide Password");
        } else {
            $passwordField.attr("type", "password");
            $showPasswordButton.text("Show Password");
        }
    });

    function checkFirstLastName() {
        const $firstNameField = $("#firstname");
        const $lastNameField = $("#lastname");
        const $invalidFirstLast = $("#invalidFirstLastField");
        const namePattern = /^[A-Za-z\s]+$/;

        let errors = [];

        if ($firstNameField.val().trim() === "") {
            errors.push('First name is required');
        } else if (!namePattern.test($firstNameField.val().trim())) {
            errors.push('First name must contain only letters and spaces');
        }

        if ($lastNameField.val().trim() === "") {
            errors.push('Last name is required');
        } else if (!namePattern.test($lastNameField.val().trim())) {
            errors.push('Last name must contain only letters and spaces');
        }

        if (errors.length > 0) {
            $invalidFirstLast.show();
            $invalidFirstLast.html('<i class="fa fa-exclamation-circle"></i> ' + errors.join('<br/><i class="fa fa-exclamation-circle"></i> '));
        } else {
            $invalidFirstLast.hide();
        }
    }

    const $email = $("#email");
    const $password = $("#password");

    $email.on('input', checkEmail);
    $password.on('input', checkPassword);
    $("#gender").on('change', checkGender);
    $("#birthdate").on('change', checkBirthdate);
    $("#phoneNo").on('input', checkPhone);
    $("#firstname, #lastname").on('input', checkFirstLastName);
    $("#address1").on('input', checkAddress1);
    $("#address2").on('input', checkAddress2);
    $("#city").on('input', checkCity);
    $("#state").on('input', checkState);
    $("#postcode").on('input', checkPostcode);

    function checkFirstLastName() {
        const $firstNameField = $("#firstname");
        const $lastNameField = $("#lastname");
        const $invalidFirstLast = $("#invalidFirstLastField");
        const namePattern = /^[A-Za-z\s]+$/;

        let errors = [];

        if ($firstNameField.val().trim() === "") {
            errors.push('First name is required');
        } else if (!namePattern.test($firstNameField.val().trim())) {
            errors.push('First name must contain only letters and spaces');
        }

        if ($lastNameField.val().trim() === "") {
            errors.push('Last name is required');
        } else if (!namePattern.test($lastNameField.val().trim())) {
            errors.push('Last name must contain only letters and spaces');
        }

        if (errors.length > 0) {
            $invalidFirstLast.show();
            $invalidFirstLast.html('<i class="fa fa-exclamation-circle"></i> ' + errors.join('<br/><i class="fa fa-exclamation-circle"></i> '));
        } else {
            $invalidFirstLast.hide();
        }
    }

    function checkGender() {
        const $genderField = $("#gender");
        const $invalidGender = $("#invalidGenderField");

        if ($genderField.val() === "") {
            $invalidGender.show();
            $invalidGender.html('<i class="fa fa-exclamation-circle"></i> Please select a gender');
        } else {
            $invalidGender.hide();
        }
    }

    function checkBirthdate() {
        const $birthdateField = $("#birthdate");
        const $invalidBirthdate = $("#invalidBirthdateField");

        const birthdate = new Date($birthdateField.val());
        const today = new Date();

        let errors = [];

        if (!$birthdateField.val()) {
            errors.push('Birthdate is required');
        } else if (birthdate >= today) {
            errors.push('Birthdate must be a past date');
        }

        if (errors.length > 0) {
            $invalidBirthdate.show();
            $invalidBirthdate.html('<i class="fa fa-exclamation-circle"></i> ' + errors.join('<br/><i class="fa fa-exclamation-circle"></i> '));
        } else {
            $invalidBirthdate.hide();
        }
    }

    function checkPhone(event) {
        const $invalidPhone = $("#invalidPhoneField");
        const phonePattern = /^01[0-46-9]\d{7,8}$|^0\d{1,2}\d{7,8}$/;

        if ($(event.target).val() === "") {
            $invalidPhone.show();
            $invalidPhone.html('<i class="fa fa-exclamation-circle"></i> Phone number is required');
        } else if (!phonePattern.test($(event.target).val())) {
            $invalidPhone.show();
            $invalidPhone.html('<i class="fa fa-exclamation-circle"></i> Invalid phone number format');
        } else {
            $invalidPhone.hide();
        }
    }

    let isEmailValid = false;
    let isPasswordValid = false;
    function checkEmail(event) {
        const $invalidEmail = $("#invalidTextField");
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if ($(event.target).val() === "") {
            $invalidEmail.show();
            $invalidEmail.html('<i class="fa fa-exclamation-circle"></i> Email is required');
            isEmailValid = false;
        } else if (!emailPattern.test($(event.target).val())) {
            $invalidEmail.show();
            $invalidEmail.html('<i class="fa fa-exclamation-circle"></i> Invalid Email Format');
            isEmailValid = false;
        } else {
            $invalidEmail.hide();
            isEmailValid = true;
        }
        // updateSubmitButtonState();
    }

    $("#verifyEmailButton").on('click', function () {
        const email = $("#email").val();

        if (!email) {
            alert("Please enter a valid email before verifying.");
            return;
        }

        $.ajax({
            url: 'sendOtp.php',
            type: 'POST',
            data: { email: email },
            success: function (response) {
                try {
                    var jsonResponse = JSON.parse(response);
                    console.log("The response was: ", jsonResponse);

                    if (jsonResponse.status === 'success') {
                        alert('OTP has been sent to your email.');
                        $("#verifyEmailButton").prop('disabled', true);

                        // Start 60-second timer
                        let countdown = 60;
                        let resendTimer = setInterval(function () {
                            countdown--;
                            $("#verifyEmailButton").text(`Resend OTP (${countdown}s)`);

                            if (countdown <= 0) {
                                clearInterval(resendTimer);
                                $("#verifyEmailButton").prop('disabled', false).text('Resend OTP');
                            }
                        }, 1000);
                    } else if (jsonResponse.status === 'failure' && jsonResponse.message === 'OTP already used for this email.') {
                        alert('This email has already verified an OTP. Please use a different email or contact support.');
                    } else {
                        alert('Failed to send OTP. Please try again.');
                    }
                } catch (e) {
                    console.error("Error parsing JSON response: ", e);
                    alert("An unknown error occurred. Please try again.");
                }
            },
            error: function () {
                alert("An error occurred while communicating with the server. Please try again.");
            }
        });
    });


    $("#otpInput").on('input', function () {
        const enteredOtp = $(this).val().trim();
        const email = $("#email").val().trim();
        const invalidOtpField = $("#invalidOtpInputField");
        const verifyEmailButton = $("#verifyEmailButton");

        // Clear previous messages
        invalidOtpField.hide();

        // Check if the entered OTP has exactly 6 digits
        if (enteredOtp.length === 6) {
            $.post('validateOtp.php', { email: email, otp: enteredOtp }, function (response) {
                try {
                    const jsonResponse = JSON.parse(response);
                    if (jsonResponse.status === 'success') {
                        $("#email").prop('readonly', true); // Ensure 'readonly' is set properly
                        invalidOtpField.html("<i class='fa fa-check-circle'></i><span> Verified successfully</span>")
                            .css("color", "green")
                            .show();
                        $('#otpInput').hide();
                        verifyEmailButton.hide();
                        
                    } else {
                        invalidOtpField.html("<i class='fa fa-exclamation-circle'></i> Invalid OTP entered. Please try again.")
                            .css("color", "red")
                            .show();
                            $("#email").prop('readonly', false); // Ensure 'readonly' is set properly
                    }
                } catch (e) {
                    console.error("Error parsing JSON response: ", e);
                    alert("An unknown error occurred. Please try again.");
                }
            });
        } else if (enteredOtp.length > 0) {
            // Show error message if OTP is not exactly 6 digits
            invalidOtpField.html("<i class='fa fa-exclamation-circle'></i> OTP must be exactly 6 digits.")
                .css("color", "red")
                .show();
        }
    });



    function checkPassword(event) {
        const $invalidPassword = $("#invalidPasswordField");

        let errors = [];
        if ($(event.target).val().length < 8) {
            errors.push('Must be at least 8 characters long');
        }
        if (!/[A-Z]/.test($(event.target).val())) {
            errors.push('Must contain at least one uppercase letter');
        }

        if (errors.length > 0) {
            $invalidPassword.show();
            $invalidPassword.html('<i class="fa fa-exclamation-circle"></i> ' + errors.join('<br/><i class="fa fa-exclamation-circle"></i> '));
            isPasswordValid = false;
        } else {
            $invalidPassword.hide();
            isPasswordValid = true;
        }
        // updateSubmitButtonState();
    }



    // function updateSubmitButtonState() {
    //     const $submitButton = $("#submitButton");
    //     if ($submitButton) {
    //         console.log("Submit Button in update function");
    //         console.log("email = ", isEmailValid, ", password = ", isPasswordValid);
    //         $submitButton.prop('disabled', !(isEmailValid && isPasswordValid));
    //     }
    // }

    function validateForm() {
        checkEmail({ target: $("#email")[0] });
        checkPassword({ target: $("#password")[0] });
        return isEmailValid && isPasswordValid;
    }

    $("#myForm").on('submit', function (event) {
        if (!validateForm()) {
            event.preventDefault();
        }
    });

    function checkAddress1() {
        const $address1Field = $("#address1");
        const $invalidAddress1Field = $("#invalidAddress1Field");

        if ($address1Field.val().trim() === "") {
            $invalidAddress1Field.show();
            $invalidAddress1Field.html('<i class="fa fa-exclamation-circle"></i> Address 1 is required');
        } else {
            $invalidAddress1Field.hide();
        }
    }

    function checkAddress2() {
        const $address2Field = $("#address2");
        const $invalidAddress2Field = $("#invalidAddress2Field");

        if ($address2Field.val().trim() === "") {
            $invalidAddress2Field.show();
            $invalidAddress2Field.html('<i class="fa fa-exclamation-circle"></i> Address 2 is required');
        } else {
            $invalidAddress2Field.hide();
        }
    }

    function checkCity() {
        const $cityField = $("#city");
        const $invalidCityField = $("#invalidCityField");

        if ($cityField.val().trim() === "") {
            $invalidCityField.show();
            $invalidCityField.html('<i class="fa fa-exclamation-circle"></i> City is required');
        } else {
            $invalidCityField.hide();
        }
    }

    function checkState() {
        const $stateField = $("#state");
        const $invalidStateField = $("#invalidStateField");

        if ($stateField.val().trim() === "") {
            $invalidStateField.show();
            $invalidStateField.html('<i class="fa fa-exclamation-circle"></i> State is required');
        } else {
            $invalidStateField.hide();
        }
    }

    function checkPostcode() {
        const $postcodeField = $("#postcode");
        const $invalidPostcodeField = $("#invalidPostcodeField");
        const postcodePattern = /^[0-9]+$/;

        if ($postcodeField.val().trim() === "") {
            $invalidPostcodeField.show();
            $invalidPostcodeField.html('<i class="fa fa-exclamation-circle"></i> Postcode is required');
        } else if (!postcodePattern.test($postcodeField.val().trim())) {
            $invalidPostcodeField.show();
            $invalidPostcodeField.html('<i class="fa fa-exclamation-circle"></i> Postcode must contain only numeric characters');
        } else {
            $invalidPostcodeField.hide();
        }
    }

    // Photo preview
    $('label.upload input[type=file]').on('change', function (e) {
        const f = e.target.files[0];
        const img = $('#userPic')[0];

        if (!img) return;

        img.dataset.src ??= img.src;

        if (f?.type.startsWith('image/')) {
            img.src = URL.createObjectURL(f);
        } else {
            img.src = img.dataset.src;
            e.target.value = '';
        }
    });

    let dropArea = $('#dropArea');
    let fileInput = $('#photo');

    dropArea.on('dragenter dragover', function (event) {
        event.preventDefault();
        event.stopPropagation();
        dropArea.addClass('dragover');
    });

    dropArea.on('dragleave dragend drop', function (event) {
        event.preventDefault();
        event.stopPropagation();
        dropArea.removeClass('dragover');
    });

    dropArea.on('drop', function (event) {
        let files = event.originalEvent.dataTransfer.files;
        fileInput[0].files = files;
        fileInput.trigger('change'); // Trigger change event for preview
    });

    dropArea.on('click', function () {
        fileInput.click();
    });

    //overlay for registering
    $('#registerForm').on('submit', function () {
        $('#loadingOverlay').css('display', 'flex'); // Show overlay using jQuery
    });

    $('#send_resetForm').on('submit', function () {
        $('#loadingOverlay').css('display', 'flex'); // Show overlay using jQuery
    });
});
