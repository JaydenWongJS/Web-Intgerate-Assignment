
const eye = document.getElementById("eye");
const passwordField = document.getElementById("password");

eye.addEventListener("click", function () {
    const passwordType = passwordField.type;
    if (passwordType === "password") {
        passwordField.type = "text";
        eye.innerHTML = '<i class="fa fa-eye-slash"></i>';
    } else {
        passwordField.type = "password";
        eye.innerHTML = '<i class="fa fa-eye"></i>';
    }
});


function checkFirstLastName() {
    const firstNameField = document.getElementById("firstname");
    const lastNameField = document.getElementById("lastname");
    const invalidFirstLast = document.getElementById("invalidFirstLastField");
    const namePattern = /^[A-Za-z\s]+$/;

    let errors = [];

    if (firstNameField.value.trim() === "") {
        errors.push('First name is required');
    } else if (!namePattern.test(firstNameField.value.trim())) {
        errors.push('First name must contain only letters and spaces');
    }

    if (lastNameField.value.trim() === "") {
        errors.push('Last name is required');
    } else if (!namePattern.test(lastNameField.value.trim())) {
        errors.push('Last name must contain only letters and spaces');
    }

    if (errors.length > 0) {
        invalidFirstLast.style.display = "block";
        invalidFirstLast.innerHTML = '<i class="fa fa-exclamation-circle"></i> ' + errors.join('<br/><i class="fa fa-exclamation-circle"></i> ');
    } else {
        invalidFirstLast.style.display = "none";
    }
}


function checkGender() {
    console.log("Changing Gener")
    const genderField = document.getElementById("gender");
    const invalidGender = document.getElementById("invalidGenderField");

    if (genderField.value === "") {
        invalidGender.style.display = "block";
        invalidGender.innerHTML = '<i class="fa fa-exclamation-circle"></i> Please select a gender';
    } else {
        invalidGender.style.display = "none";
    }
}

function checkBirthdate() {
    const birthdateField = document.getElementById("birthdate");
    const invalidBirthdate = document.getElementById("invalidBirthdateField");

    const birthdate = new Date(birthdateField.value);
    const today = new Date();

    let errors = [];

    if (!birthdateField.value) {
        errors.push('Birthdate is required');
    } else if (birthdate >= today) {
        errors.push('Birthdate must be a past date');
    }

    if (errors.length > 0) {
        invalidBirthdate.style.display = "block";
        invalidBirthdate.innerHTML = '<i class="fa fa-exclamation-circle"></i> ' + errors.join('<br/><i class="fa fa-exclamation-circle"></i> ');
    } else {
        invalidBirthdate.style.display = "none";
    }
}


function checkPhone(event) {
    const invalidPhone = document.getElementById("invalidPhoneField");
    const phonePattern = /^01[0-46-9]\d{7,8}$|^0\d{1,2}\d{7,8}$/;

    if (event.target.value === "") {
        invalidPhone.style.display = "block";
        invalidPhone.innerHTML = '<i class="fa fa-exclamation-circle"></i> Phone number is required';
    } else if (!phonePattern.test(event.target.value)) {
        invalidPhone.style.display = "block";
        invalidPhone.innerHTML = '<i class="fa fa-exclamation-circle"></i> Invalid phone number format';
    } else {
        invalidPhone.style.display = "none";
    }
}



let isEmailValid = false;
let isPasswordValid = false;

const email = document.getElementById("email");
const password = document.getElementById("password");

email.addEventListener('input', checkEmail);
password.addEventListener('input', checkPassword);

function checkEmail(e) {
    const invalidEmail = document.getElementById("invalidTextField");
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (e.target.value === "") {
        invalidEmail.style.display = "block";
        invalidEmail.innerHTML = '<i class="fa fa-exclamation-circle"></i> Email is required';
        isEmailValid = false;
    } else if (!emailPattern.test(e.target.value)) {
        invalidEmail.style.display = "block";
        invalidEmail.innerHTML = '<i class="fa fa-exclamation-circle"></i> Invalid Email Format';
        isEmailValid = false;
    } else {
        invalidEmail.style.display = "none";
        isEmailValid = true;
    }
    updateSubmitButtonState();
}

function checkPassword(e) {
    const invalidPassword = document.getElementById("invalidPasswordField");

    let errors = [];
    if (e.target.value.length < 8) {
        errors.push('Must be at least 8 characters long');
    }
    if (!/[A-Z]/.test(e.target.value)) {
        errors.push('Must contain at least one uppercase letter');
    }

    if (errors.length > 0) {
        invalidPassword.style.display = "block";
        invalidPassword.innerHTML = '<i class="fa fa-exclamation-circle"></i> ' + errors.join('<br/><i class="fa fa-exclamation-circle"></i> ');
        isPasswordValid = false;
    } else {
        invalidPassword.style.display = "none";
        isPasswordValid = true;
    }
    updateSubmitButtonState();
}

function checkComfirmPassword(e) {
    const invalidComfirmPasswordField = document.getElementById("invalidComfirmPasswordField");

    let errors = [];
    if (e.target.value.length < 8) {
        errors.push('Must be at least 8 characters long');
    }
    if (!/[A-Z]/.test(e.target.value)) {
        errors.push('Must contain at least one uppercase letter');
    }

    if (errors.length > 0) {
        invalidComfirmPasswordField.style.display = "block";
        invalidComfirmPasswordField.innerHTML = '<i class="fa fa-exclamation-circle"></i> ' + errors.join('<br/><i class="fa fa-exclamation-circle"></i> ');
    } else {
        invalidComfirmPasswordField.style.display = "none";
    }
    updateSubmitButtonState();
}

function updateSubmitButtonState() {
    const submitButton = document.getElementById("submitButton");
    if (submitButton) {
        console.log("Submit Button in update funtion")
        console.log("email = ",isEmailValid,",password = ",isPasswordValid)
        submitButton.disabled = !(isEmailValid && isPasswordValid);
    }

}




function validateForm() {
    checkEmail({ target: document.getElementById("email") });
    checkPassword({ target: document.getElementById("password") });
    return isEmailValid && isPasswordValid;
}

document.getElementById("myForm").addEventListener('submit', function (event) {
    if (!validateForm()) {
        event.preventDefault();
    }
});


function checkAddress1() {
    const address1Field = document.getElementById("address1");
    const invalidAddress1Field = document.getElementById("invalidAddress1Field");

    if (address1Field.value.trim() === "") {
        invalidAddress1Field.style.display = "block";
        invalidAddress1Field.innerHTML = '<i class="fa fa-exclamation-circle"></i> Address 1 is required';
    } else {
        invalidAddress1Field.style.display = "none";
    }
}

function checkAddress2() {
    const address2Field = document.getElementById("address2");
    const invalidAddress2Field = document.getElementById("invalidAddress2Field");

    if (address2Field.value.trim() === "") {
        invalidAddress2Field.style.display = "block";
        invalidAddress2Field.innerHTML = '<i class="fa fa-exclamation-circle"></i> Address 2 is required';
    } else {
        invalidAddress2Field.style.display = "none";
    }
}

function checkCity() {
    const cityField = document.getElementById("city");
    const invalidCityField = document.getElementById("invalidCityField");

    if (cityField.value.trim() === "") {
        invalidCityField.style.display = "block";
        invalidCityField.innerHTML = '<i class="fa fa-exclamation-circle"></i> City is required';
    } else {
        invalidCityField.style.display = "none";
    }
}

function checkState() {
    const stateField = document.getElementById("state");
    const invalidStateField = document.getElementById("invalidStateField");

    if (stateField.value.trim() === "") {
        invalidStateField.style.display = "block";
        invalidStateField.innerHTML = '<i class="fa fa-exclamation-circle"></i> State is required';
    } else {
        invalidStateField.style.display = "none";
    }
}

function checkPostcode() {
    const postcodeField = document.getElementById("postcode");
    const invalidPostcodeField = document.getElementById("invalidPostcodeField");
    const postcodePattern = /^[0-9]+$/;

    if (postcodeField.value.trim() === "") {
        invalidPostcodeField.style.display = "block";
        invalidPostcodeField.innerHTML = '<i class="fa fa-exclamation-circle"></i> Postcode is required';
    } else if (!postcodePattern.test(postcodeField.value.trim())) {
        invalidPostcodeField.style.display = "block";
        invalidPostcodeField.innerHTML = '<i class="fa fa-exclamation-circle"></i> Postcode must contain only numeric characters';
    } else {
        invalidPostcodeField.style.display = "none";
    }
}

