$(() => {
    console.log("Document is ready");

    const $manageDropdown = $("#management");
    const $dropdownMenu = $("#dropdownMenu");

    // Toggle dropdown menu visibility
    $manageDropdown.on("click", function (event) {
        event.preventDefault();
        console.log("Dropdown clicked");
        if ($dropdownMenu.hasClass("show")) {
            console.log("Dropdown is currently shown");
            $dropdownMenu.removeClass("show");
            setTimeout(() => $dropdownMenu.hide(), 500);
        } else {
            console.log("Dropdown is currently hidden");
            $dropdownMenu.show();
            setTimeout(() => $dropdownMenu.addClass("show"), 10);
        }
    });

    // Show dropdown menu if on a management page
    const managementPages = ['Admin Order', 'Members', 'Reviews', 'Products',];
    if (managementPages.includes(currentPageTitle)) {
        console.log("Current page is in management section");
        $dropdownMenu.show();
        setTimeout(() => $dropdownMenu.addClass("show"), 10);
    }

    // Circle chart percentage data
    const $circle = $(".circle");
    const positivePercentage = $circle.data("positive") || 0;
    const neutralPercentage = $circle.data("neutral") || 0;
    const negativePercentage = $circle.data("negative") || 0;

    console.log(`Positive: ${positivePercentage}, Neutral: ${neutralPercentage}, Negative: ${negativePercentage}`);

    $circle.css("background", `conic-gradient(
        #4caf50 0% ${positivePercentage}%, 
        #ddd ${positivePercentage}% ${parseFloat(positivePercentage) + parseFloat(neutralPercentage)}%, 
        #f44336 ${parseFloat(positivePercentage) + parseFloat(neutralPercentage)}% 100%
    )`);

    /*Ron*/
    // Initiate GET request
    $('[data-get]').on('click', e => {
        e.preventDefault();
        const url = e.target.dataset.get;
        location = url || location;
    });

    // Initiate POST request
    $('[data-post]').on('click', e => {
        e.preventDefault();
        const url = e.target.dataset.post;
        const f = $('<form>').appendTo(document.body)[0];
        f.method = 'POST';
        f.action = url || location;
        f.submit();
    });

    //Confirmation Message
    // Confirmation Message
    $('[data-confirm]').on('click', function (e) {
        const confirmMessage = $(this).attr('data-confirm') || 'Are you sure?';

        // Show confirmation dialog
        if (!confirm(confirmMessage) == true) {
            e.preventDefault();  // Prevent default action
            e.stopPropagation(); // Prevent event bubbling
            return false;        // Ensure the action does not proceed
        }


    });


    // Convert text to uppercase
    $('[data-upper]').on('input', function (e) {
        const a = e.target.selectionStart;
        const b = e.target.selectionEnd;
        e.target.value = e.target.value.toUpperCase();
        e.target.setSelectionRange(a, b);
    });


});
