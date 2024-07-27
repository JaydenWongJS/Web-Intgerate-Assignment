<?php $title="Contact Us" ?>
<?php
 include('_header.php'); 
 require('_base.php');
?>

    <div class="banner contact_us_banner">
        <img id="background-video" src="image/minions.gif">
        <div class="overlay"></div>
        <div class="banner-text contact_title">
            <h1>Get In Touch With Us</h1>
        </div>
    </div>

    <div class="contact_us_container">
        <div class="map">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3984.2022302241294!2d101.61819347310039!3d3.040388353815491!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc4b56b7f0bd15%3A0xe676c32ccfdcb00b!2sSmartMaster%20HQ!5e0!3m2!1sen!2smy!4v1720497929560!5m2!1sen!2smy"
                width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>

        <div class="contact_form_box">
            <div class="contact_form_title">
                <h1 class="founder">
                    Contact US <i style="margin-left:5px;color:#ff9800" class='far fa-paper-plane'></i>
                </h1>
            </div>
            <form class="contact_form" id="contact_form" method="post" action="sendContactForm.php">
            <div class="input-group">
                    <span> <i class='far fa-address-card'></i></span>
                    <?= html_text_type("text","name","input-text","placeholder='Name'"); ?>
                </div>
                <div class="input-group">
                    <span> <i class="fa fa-envelope"></i> </span>
                    <?= html_text_type("text","email","input-text","placeholder='Your Email'"); ?>
                </div>
                <div class="input-group">
                    <span> <i class='fas fa-pen-alt'></i> </span>
                    <?= html_text_type("text","subject","input-text","placeholder='Your Subject'"); ?>
                </div>
                <div class="contact_message">
                    <label for="message">Please Drop Us a Message Here : </label>
                  <?= html_textarea("message","4","50","placeholder='Enter the Message'") ?>
                </div>
                <div style="text-align: right; width: 100%;">
                    <input type="submit" name="send" id="send" class="send-button" value="Send Us A Message"/>
                </div>
            </form>
        </div>
    </div>

    <div id="loadingSpinner" style="display: none;"></div>
    <div class="overlay_all"></div>
    <?php include('_footer.php') ?>
