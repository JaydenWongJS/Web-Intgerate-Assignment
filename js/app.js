
$(document).ready(function() {
    const slideContainer = $('.slide');
    const slideCount = $('.latest_product').length;
    const slideWidth = $('.latest_product').outerWidth(true);
    const slidesToShow = 4;
    let currentIndex = 0;

    function updateSlidePosition() {
        const offset = -currentIndex * slideWidth;
        slideContainer.css('transform', `translateX(${offset}px)`);
    }

    $('.arrow_left').click(function() {
        if (currentIndex > 0) {
            currentIndex--;
            updateSlidePosition();
        }
    });

    $('.arrow_right').click(function() {
        if (currentIndex < slideCount - slidesToShow) {
            currentIndex++;
            updateSlidePosition();
        }
    });

    $("#filter").click(function() {
        $(".fixed_filter_bar").css({
             display:'block',
        });
        $("body").addClass("no-scroll");
        $(".overlay_filter").show();
    });

    $("#closeFilter").click(function() {
        $(".fixed_filter_bar").css({
            display:'none',
        });
        $("body").removeClass("no-scroll");
        $(".overlay_filter").hide();
    });

});



document.addEventListener("DOMContentLoaded", function () {
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.3
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                // Start animation for .mission elements
                const missionItems = entry.target.querySelectorAll('.mission');
                missionItems.forEach(item => {
                    item.classList.add('roll_effect'); // Add the animation class
                });
                observer.unobserve(entry.target); // Stop observing once it is visible
            }
        });
    }, observerOptions);

    const missionItems = document.querySelectorAll('.come_in');
    missionItems.forEach((item, index) => {
        setTimeout(() => {
            observer.observe(item);
        }, index * 200); // Delay each item by 200ms
    });

    const videoElement = document.getElementById("myVideo");

    videoElement.addEventListener("click", function () {
        if (document.fullscreenElement) {
            document.exitFullscreen();
        } else {
            if (videoElement.requestFullscreen) {
                videoElement.requestFullscreen();
            } else if (videoElement.mozRequestFullScreen) { // Firefox
                videoElement.mozRequestFullScreen();
            } else if (videoElement.webkitRequestFullscreen) { // Chrome, Safari and Opera
                videoElement.webkitRequestFullscreen();
            } else if (videoElement.msRequestFullscreen) { // IE/Edge
                videoElement.msRequestFullscreen();
            }
        }
    });

});


function validateContactForm(event) {
    console.log("Contact Form Is interceptiong, js");
    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const subject = document.getElementById("subject").value.trim();
    const message = document.getElementById("message").value.trim();
    const namePattern = /^[A-Za-z\s]+$/;
    const errors = [];

    if (!name) {
        errors.push("Name is empty!");
    } else if (!name.match(namePattern)) {
        errors.push("Please enter only character for Name!");
    }

    if (!email) {
        errors.push("Email is empty!");
    } else if (!email.match(/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/)) {
        errors.push("Please enter a valid email format!");
    }

    if (!subject) {
        errors.push("Subject is empty!");
    }

    if (!message) {
        errors.push("Message is empty!");
    }

    if (errors.length > 0) {
        alert("Errors:\n" + errors.join("\n"));
        return false; // Prevent form submission
    }

    return true; // Allow form submission
}



$(document).ready(function () {
    $('#contact_form').on('submit', function (event) {

        const noError = validateContactForm(); // Validate the form

        if (noError) {
            // Show loading spinner and overlay if there are no errors
            $('#loadingSpinner').show();
            $('.overlay_all').show();

            // Disable the submit button to prevent multiple clicks
            $('#sendButton').prop('disabled', true);

            // Proceed with form submission
            $('#contact_form').submit(); // Submit the form programmatically
        } else {
            event.preventDefault(); // Prevent default form submission
            // Handle errors as needed
            console.log("Form has errors, not showing loading spinner.");
        }
    });



    window.addEventListener('scroll', function () {
        var navBar = document.querySelector('.nav_bar');
        var navLinks = document.querySelectorAll('.nav_url .link');

        if (window.scrollY >= 70) {
            navBar.classList.add('scrolled');
            // Change text color to white for links
            navLinks.forEach(function (link) {
                link.style.color = 'white';
            });
        } else {
            navBar.classList.remove('scrolled');
            // Revert text color to its original state
            navLinks.forEach(function (link) {
                link.style.color = '';
            });
        }
    });

    //back button
    document.querySelector('.goBack').addEventListener('click', function(event) {
        event.preventDefault();
        history.back();
    });

    //payment
    const radioButtons = document.querySelectorAll('.e-wallet-radio');
    const labels = document.querySelectorAll('.e-wallet-pic');
    const eWalletInfo = document.querySelector('.input_e_wallet_info');
    const bankInfo = document.querySelector('.input_bank_info');
    const paymentMethodName = document.getElementById('payment_method_name');
    const fieldsetPayment = document.querySelector('.fieldset_payment');

    fieldsetPayment.style.display = 'none';

    radioButtons.forEach(radio => {
        radio.addEventListener('change', () => {
            labels.forEach(label => label.classList.remove('active'));
            const selectedLabel = document.querySelector(`label[for=${radio.id}]`);
            selectedLabel.classList.add('active');

            fieldsetPayment.style.display = 'block';

            // Update the legend with the selected payment method name
            switch (radio.value) {
                case 'tng':
                    paymentMethodName.innerHTML = 'Selected Payment Method : <span class="type">Touch \'n Go</span>';
                    eWalletInfo.style.display = 'block';
                    bankInfo.style.display = 'none';
                    break;
                case 'grab':
                    paymentMethodName.innerHTML = 'Selected Payment Method : <span class="type">Grab</span>';
                    eWalletInfo.style.display = 'block';
                    bankInfo.style.display = 'none';
                    break;
                case 'boost':
                    paymentMethodName.innerHTML = 'Selected Payment Method : <span class="type">Boost</span>';
                    eWalletInfo.style.display = 'block';
                    bankInfo.style.display = 'none';
                    break;
                case 'maybank':
                    paymentMethodName.innerHTML = 'Selected Payment Method : <span class="type">Maybank</span>';
                    eWalletInfo.style.display = 'none';
                    bankInfo.style.display = 'block';
                    break;
                case 'publicBank':
                    paymentMethodName.innerHTML = 'Selected Payment Method : <span class="type">Public Bank</span>';
                    eWalletInfo.style.display = 'none';
                    bankInfo.style.display = 'block';
                    break;
                case 'hongLeongBank':
                    paymentMethodName.innerHTML = 'Selected Payment Method : <span class="type">Hong Leong Bank</span>';
                    eWalletInfo.style.display = 'none';
                    bankInfo.style.display = 'block';
                    break;
                default:
                    paymentMethodName.innerHTML = '';
                    eWalletInfo.style.display = 'none';
                    bankInfo.style.display = 'none';
            }
        });
    });


});

//personal info
const updateButton = document.getElementById("update_personal_info");

function changePersonalDetails() {
    console.log("Changing Display Details Become Form Input")
    const personalInfo = document.getElementById("personal_info");
    const personalInfoInputForm = document.getElementById("personal_info_input_form");

    personalInfo.style.display = "none";
    updateButton.style.display = "none";
    personalInfoInputForm.style.display = "block";

}

const cancelButton = document.getElementById("cancel");
cancelButton.addEventListener("click", function () {
    const personalInfo = document.getElementById("personal_info");
    const personalInfoInputForm = document.getElementById("personal_info_input_form");
    const updateButton = document.getElementById("update_personal_info");

    personalInfo.style.display = "block";
    updateButton.style.display = "block";
    personalInfoInputForm.style.display = "none";
});

//address info
const updateAddressButton = document.getElementById("update_address_info");
function changeAddressDetails() {
    console.log("Changing Display Adresss Details Become Form Input")
    const addressInfo = document.getElementById("address_info");
    const addressInfoInputForm = document.getElementById("address_info_input_form");

    addressInfo.style.display = "none";
    updateAddressButton.style.display = "none";
    addressInfoInputForm.style.display = "block";
}

const cancelAddressButton = document.getElementById("cancelAddress");
cancelAddressButton.addEventListener("click", function () {
    const addressInfo = document.getElementById("address_info");
    const addressInfoInputForm = document.getElementById("address_info_input_form");

    addressInfo.style.display = "block";
    updateAddressButton.style.display = "block";
    addressInfoInputForm.style.display = "none";
});




function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}



function toggleAddressForm() {
    const addressForm = document.getElementById('addressForm');
    const checkbox = document.getElementById('useAnotherAddress');
    const default_address = document.getElementById('default_address');
    if (checkbox.checked) {
        addressForm.style.display = 'block';
        default_address.style.display = 'none';
    } else {
        addressForm.style.display = 'none';
        default_address.style.display = 'block';
    }
}

function validateCheckOutForm() {
    const checkBoxAnotherAddress = document.getElementById("useAnotherAddress");
    const malaysia_postcode_pattern = /^\d{5}$/;
    const phonePattern = /^01[0-46-9]\d{7,8}$|^0\d{1,2}\d{7,8}$/;
    const emailPattern = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/;

    const address1 = document.getElementById("address1").value;
    const address2 = document.getElementById("address2").value;
    const city = document.getElementById("city").value;
    const state = document.getElementById("state").value;
    const postCode = document.getElementById("postcode").value;

    const errors = [];

    // Validate personal information fields
    const firstName = document.getElementById("firstName").value;
    const lastName = document.getElementById("lastName").value;
    const email = document.getElementById("email").value;
    const tel = document.getElementById("tel").value;

    if (firstName == "") {
        errors.push("First Name should not be empty !");
    }

    if (lastName == "") {
        errors.push("Last Name should not be empty !");
    }

    if (email == "") {
        errors.push("Email should not be empty !");
    } else if (!email.match(emailPattern)) {
        errors.push("Please enter a valid email format");
    }


    if (tel == "") {
        errors.push("Contact No should not be empty !");
    } else if (!tel.match(phonePattern)) {
        errors.push("Please Enter Malaysia Format")
    }


    if (checkBoxAnotherAddress.checked) {
        if (address1 == "") {
            errors.push("Address 1 should not be empty !");
        }
        if (address2 == "") {
            errors.push("Address 2 should not be empty !");
        }
        if (city == "") {
            errors.push("City should not be empty !");
        }
        if (state == "") {
            errors.push("State should not be empty !");
        }
        if (postCode == "") {
            errors.push("PostCode should not be empty !");
        } else if (!postCode.match(malaysia_postcode_pattern)) {
            errors.push("PostCode should consist of exactly 5 digits !");
        }
    }

    const radios = document.getElementsByName("payment");
    let paymentMethodSelected = false;

    for (let i = 0; i < radios.length; i++) {
        if (radios[i].checked) {
            paymentMethodSelected = true;
            const paymentMethod = radios[i].value;
            if (['tng', 'grab', 'boost'].includes(paymentMethod)) {
                const phoneNoEwallet = document.getElementById("phoneNoEwallet").value;
                const passwordEwallet = document.getElementById("passwordEwallet").value;
                if (phoneNoEwallet == "") {
                    errors.push("Please fill in your e-wallet phone number");
                }else if(!phoneNoEwallet.match(phonePattern)){
                    errors.push("Please follow Malaysia phone format");
                }
                if (passwordEwallet == "") {
                    errors.push("Please fill in your e-wallet password");
                }
            }
            if (['maybank', 'publicBank', 'hongLeongBank'].includes(paymentMethod)) {
                const cardName = document.getElementById("cardName").value;
                const cardNo = document.getElementById("cardNo").value;
                const expiredDate = document.getElementById("expiredDate").value;
                const cvc = document.getElementById("cvc").value;
                if (cardName == "") {
                    errors.push("Please fill in the name on your card");
                }
                if (cardNo == "") {
                    errors.push("Please fill in your card number");
                }
                if (expiredDate == "") {
                    errors.push("Please fill in your card's expiration date");
                }
                if (cvc == "") {
                    errors.push("Please fill in your card's CVC");
                }
            }
            break;
        }
    }

    if (!paymentMethodSelected) {
        errors.push("Payment method is required !");
    }

    // Display errors in the errors_message div
    const errorsMessageDiv = document.getElementById("errors_message");
    errorsMessageDiv.innerHTML = ""; // Clear previous messages

    const checkOutModal = document.getElementById("checkOutModal");
    checkOutModal.style.display = "none";

    if (errors.length > 0) {
        errorsMessageDiv.innerHTML = ""; // Clear previous messages
        errors.forEach(function (error, index) {
            const p = document.createElement("p");
            p.textContent = `${index + 1}. ${error}`; // Number the error messages
            errorsMessageDiv.appendChild(p);
        });
        document.getElementById("errors").style.display = "block"; // Show the errors modal
        return false; // Prevent form submission
    }
    window.location.href = "paymentSuccess.html"; // Redirect to payment success page
    return true; // Allow form submission
}



$("#filter").click(function() {
    $("#filterPanel").show();
    $("#closeFilter").show();
    $("#filter").hide();
});

$("#closeFilter").click(function() {
    $("#filterPanel").hide();
    $("#closeFilter").hide();
    $("#filter").show();
});