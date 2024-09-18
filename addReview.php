<?php
$title = "Shop";
include('_header.php');
include('nav_bar.php');
require '_base.php';
auth("member");
clear_cart();
?>


        <form class="review_form">
        <a class="goBack"  href="#" id="goBack"> <i class="fa fa-arrow-circle-left"></i> Back</a>
            <h2>Review Form: Product Name</h2>
            <label for="member_id">Member ID :</label>
            <?= html_text_type("text","member_id"," ") ?>
            <div class="star_rate">
                <input type="radio" class="input-radio-star" id="1star" name="star" value="1">
                <label class="star" for="1star">1 <i class="fas fa-star"></i></label>
                <input type="radio" class="input-radio-star" id="2star" name="star" value="2">
                <label class="star" for="2star">2 <i class="fas fa-star"></i></label>
                <input type="radio" class="input-radio-star" id="3star" name="star" value="3">
                <label class="star" for="3star">3 <i class="fas fa-star"></i></label>
                <input type="radio" class="input-radio-star" id="4star" name="star" value="4">
                <label class="star" for="4star">4 <i class="fas fa-star"></i></label>
                <input type="radio" class="input-radio-star" id="5star" name="star" value="5">
                <label class="star" for="5star">5 <i class="fas fa-star"></i></label>
            </div>

            <div class="review_comment">
                <?= html_textarea("comment","4","50") ?>
            </div> 
            <button type="submit" class="submit-btn">Submit Review</button>
        </form>
    
<?php
include '_footer.php';
?>