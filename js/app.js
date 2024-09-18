
$(() => {
    console.log("app.js is intersecting")
    const slideContainer = $('.slide');
    const slideCount = $('.latest_product').length;
    const slideWidth = $('.latest_product').outerWidth(true);
    const slidesToShow = 4;
    let currentIndex = 0;

    function updateSlidePosition() {
        const offset = -currentIndex * slideWidth;
        slideContainer.css('transform', `translateX(${offset}px)`);
    }

    $('.arrow_left').click(function () {
        if (currentIndex > 0) {
            currentIndex--;
            updateSlidePosition();
        }
    });

    $('.arrow_right').click(function () {
        if (currentIndex < slideCount - slidesToShow) {
            currentIndex++;
            updateSlidePosition();
        }
    });

    $("#filter").click(function () {
        $(".fixed_filter_bar").css({
            display: 'block',
        });
        $("body").addClass("no-scroll");
        $(".overlay_filter").show();
    });

    $("#closeFilter").click(function () {
        $(".fixed_filter_bar").css({
            display: 'none',
        });
        $("body").removeClass("no-scroll");
        $(".overlay_filter").hide();
    });

    // Intersection Observer
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.3
    };

    const observer = new IntersectionObserver(function (entries, observer) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                $(entry.target).addClass('visible');
                // Start animation for .mission elements
                const missionItems = $(entry.target).find('.mission');
                missionItems.each(function () {
                    $(this).addClass('roll_effect'); // Add the animation class
                });
                observer.unobserve(entry.target); // Stop observing once it is visible
            }
        });
    }, observerOptions);

    const missionItems = $('.come_in');
    missionItems.each(function (index) {
        setTimeout(function () {
            observer.observe(this);
        }.bind(this), index * 200); // Delay each item by 200ms
    });

    // Video fullscreen toggle
    const videoElement = $("#myVideo");

    videoElement.on("click", function () {
        if (document.fullscreenElement) {
            document.exitFullscreen();
        } else {
            if (this.requestFullscreen) {
                this.requestFullscreen();
            } else if (this.mozRequestFullScreen) { // Firefox
                this.mozRequestFullScreen();
            } else if (this.webkitRequestFullscreen) { // Chrome, Safari and Opera
                this.webkitRequestFullscreen();
            } else if (this.msRequestFullscreen) { // IE/Edge
                this.msRequestFullscreen();
            }
        }
    });



    //nav bar 
    $(window).on('scroll', function () {
        var navBar = $('.nav_bar');
        var navLinks = $('.nav_url .link');

        if ($(window).scrollTop() >= 70) {
            navBar.addClass('scrolled');
            // Change text color to white for links
            navLinks.css('color', 'white');
        } else {
            navBar.removeClass('scrolled');
            // Revert text color to its original state
            navLinks.css('color', '');
        }
    });

    // back button
    $('.goBack').on('click', function (event) {
        event.preventDefault();
        history.back();
    });


 const radioButtons = $('input[name="paymentMethod"]');
    const labels = $('label');
    const fieldsetPayment = $('.fieldset_payment');
    const paymentMethodName = $('#payment_method_name');
    const eWalletInfo = $('#eWalletFields');  // Assuming you have an ID for E-wallet fields
    const bankInfo = $('#bankFields');  // Assuming you have an ID for bank fields

    // Initial state (hide both fields until a selection is made)
    eWalletInfo.hide();
    bankInfo.hide();

    // Listen for changes on radio buttons
    radioButtons.on('change', function() {
        // Remove active class from all labels, then add to selected label
        labels.removeClass('active');
        const selectedLabel = $(`label[for=${$(this).attr('id')}]`);
        selectedLabel.addClass('active');

        // Show the fieldset (if hidden) after a selection is made
        fieldsetPayment.show();

        // Update the legend and show the appropriate fields
        switch ($(this).val()) {
            case 'tng':
                paymentMethodName.html('Selected Payment Method: <span class="type">Touch \'n Go</span>');
                eWalletInfo.show();
                bankInfo.hide();
                break;
            case 'grab':
                paymentMethodName.html('Selected Payment Method: <span class="type">Grab</span>');
                eWalletInfo.show();
                bankInfo.hide();
                break;
            case 'boost':
                paymentMethodName.html('Selected Payment Method: <span class="type">Boost</span>');
                eWalletInfo.show();
                bankInfo.hide();
                break;
            case 'maybank':
                paymentMethodName.html('Selected Payment Method: <span class="type">Maybank</span>');
                eWalletInfo.hide();
                bankInfo.show();
                break;
            case 'publicBank':
                paymentMethodName.html('Selected Payment Method: <span class="type">Public Bank</span>');
                eWalletInfo.hide();
                bankInfo.show();
                break;
            case 'hongLeongBank':
                paymentMethodName.html('Selected Payment Method: <span class="type">Hong Leong Bank</span>');
                eWalletInfo.hide();
                bankInfo.show();
                break;
            default:
                paymentMethodName.html('');
                eWalletInfo.hide();
                bankInfo.hide();
        }
    });

    // Check for preselected payment method (if page is loaded with a value pre-selected)
    const selectedMethod = $('input[name="paymentMethod"]:checked').val();
    if (selectedMethod) {
        radioButtons.trigger('change');  // Trigger change to show fields for pre-selected option
    }
    var initialSubtotal = parseFloat($('#subtotal').text().replace('RM ', '').replace(',', ''));

    // Listen for change events on the voucher select element
    $('#voucher_points_used').change(function() {
        // Get the selected voucher value
        var selectedOptionText = $(this).find('option:selected').text();
        var discountValue = selectedOptionText.match(/\(RM(\d+)\)/);
        
        // Default discount to 0 if no valid voucher is selected
        var discount = discountValue ? parseFloat(discountValue[1]) : 0;

        // Calculate new subtotal
        var newSubtotal = initialSubtotal - discount;
        
        // Update the subtotal element's text
        $('#subtotal').text('RM ' + newSubtotal.toFixed(2));

        // Change text color based on whether a valid voucher is selected
        if (discount > 0) {
            $('#subtotal').css("color", "red");
        } else {
            $('#subtotal').css("color", "black"); // Reset to black if no voucher is selected
        }
    });

    //product filter 
    $("#filter").click(function () {
        $("#filterPanel").show();
        $("#closeFilter").show();
        $("#filter").hide();
    });

    $("#closeFilter").click(function () {
        $("#filterPanel").hide();
        $("#closeFilter").hide();
        $("#filter").show();
    });


// Update personal details
$("#update_personal_info").click(function () {
    $("#personal_info").hide();
    $("#update_personal_info").hide();
    $("#personal_info_input_form").show();
});

$("#cancel").click(function () {
    $("#personal_info").show();
    $("#update_personal_info").show();
    $("#personal_info_input_form").hide();
});

// Address Info
$("#update_address_info").click(function () {
    $("#address_info").hide();
    $("#update_address_info").hide();
    $("#address_info_input_form").show();
});

$("#cancelAddress").click(function () {
    $("#address_info").show();
    $("#update_address_info").show();
    $("#address_info_input_form").hide();
});

// Get the value from the hidden input
const memberId = $('#memberId').val();

// Generate QR code
$('#qrcode').qrcode({
    text: memberId,
    width: 128,
    height: 128
});

//switch address
$("#useAnotherAddress").change(function () {
    if ($(this).is(":checked")) {
        $("#addressForm").show();
        $("#default_address").hide();
    } else {
        $("#addressForm").hide();
        $("#default_address").show();
    }
});

// Modal handling
$('.change_profile_pic').on('click', function (e) {
    e.preventDefault();
    openModal('imageUpdateModal');
});


$('#changeInfo').on('click', function () {
    openModal('personalInfoModal');
});

$('#closePersonalInfoModal').on('click', function () {
    closeModal('personalInfoModal');
});

$('#changeAddress').on('click', function () {
    openModal('addressModal');
});

$('#closeAddressModal').on('click', function () {
    closeModal('addressModal');
});

$('#proceedToCheckOut').on('click', function () {
    openModal('checkOutModal');
});

$('#checkOutButton').on('click', function () {
    openModal('checkOutModal');
});


// Close checkout modal
$('#closeCheckOutModal').on('click', function () {
    closeModal('checkOutModal');
});

// Convert text to uppercase
$('[data-upper]').on('input', function (e) {
    const a = e.target.selectionStart;
    const b = e.target.selectionEnd;
    e.target.value = e.target.value.toUpperCase();
    e.target.setSelectionRange(a, b);
});
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

// Close modal and reset image preview and input
$('#modalCloseImageBtn').on('click', function () {
    const img = $('#userPic')[0];
    const fileInput = $('label.upload input[type=file]')[0];

    // Reset the image source
    if (img.dataset.src) {
        img.src = img.dataset.src;
    }

    // Clear the file input
    fileInput.value = '';

    // Close the modal (assuming you have a closeModal function)
    closeModal('imageUpdateModal');
});

// When the select all checkbox is checked or unchecked
$('#selectAllItems').change(function () {
    // Check or uncheck all checkboxes based on the select all checkbox
    $('.checkBoxProduct').prop('checked', $(this).prop('checked'));
});

// If any individual checkbox is unchecked, uncheck the select all checkbox
$('.checkBoxProduct').change(function () {
    if (!$(this).prop('checked')) {
        $('#selectAllItems').prop('checked', false);
    }

    // If all individual checkboxes are checked, also check the select all checkbox
    if ($('.checkBoxProduct:checked').length == $('.checkBoxProduct').length) {
        $('#selectAllItems').prop('checked', true);
    }
});

});

// Modal functions
function openModal(modalId) {
    $("#" + modalId).show();
}

function closeModal(modalId) {
    $("#" + modalId).hide();
}

