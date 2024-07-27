<?php

include('_header.php');
require('_base.php');

?>
<body>
    <?php
  if(is_post()){
    $name=req("firstName");

    if(empty($name)){
        echo"name error";
    }
  }
    ?>

    <form action="" method="post" class="check_out_container" onsubmit=" return validateCheckOutForm()">
        <a class="goBack" style="top:0;" href="#"> <i class="fa fa-arrow-circle-left"></i> Back</a>
        <div class="check_out_info">
            <h2 style="margin-top:10px;">Personal Information</h2>
            <p class="note">*Please note that personal information cannot be changed during checkout.
                If you wish to make changes, please go to your profile to edit.</p>
            <div class="check_out_input_form" id="personal_info_input_form">
                <div class="check_out_input">
                    <label for="firstName">First Name:</label>
                    <input type="text" name="firstName" id="firstName" value="" placeholder="XXXXX" class="disabled" readonly>
                </div>
                <div class="check_out_input">
                    <label for="lastName">Last Name:</label>
                    <input type="text" name="lastName" id="lastName" value="CF" placeholder="XXXXX" class="disabled" readonly>
                </div>
                <div class="check_out_input">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" value="yfung2574@gmail.com" placeholder="example@gmail.com" class="disabled" readonly>
                </div>
                <div class="check_out_input">
                    <label for="tel">Contact No:</label>
                    <input type="tel" name="tel" id="tel" value="0128082165" placeholder="01X-XXX XXXX" class="disabled" readonly>
                </div>

                <h2 style="margin:10px 0px 0px 0px;">Address Details</h2>

                <div class="order-summary-address" id="default_address">
                    <p>B-02-13, Sri Cassia Apartment</p>
                    <p>Laman Puteri Puchong</p>
                    <p>47100 Puchong,</p>
                    <p>Selangor</p>
                </div>

                <div id="addressForm" style="display: none;">
                    <div class="check_out_input">
                        <label for="address1">Address 1:</label>
                        <input type="text" name="address1" id="address1" placeholder="xxxxxx">
                    </div>
                    <div class="check_out_input">
                        <label for="address2">Address 2:</label>
                        <input type="text" name="address2" id="address2" placeholder="xxxxxxx">
                    </div>
                    <div class="check_out_input">
                        <label for="city">City:</label>
                        <input type="text" name="city" id="city" placeholder="xxxxxx">
                    </div>
                    <div class="check_out_input">
                        <label for="state">State:</label>
                        <input type="text" name="state" id="state" placeholder="xxxxxx">
                    </div>
                    <div class="check_out_input">
                        <label for="postcode">Postcode:</label>
                        <input type="text" name="postcode" id="postcode" placeholder="12345">
                    </div>
                </div>

                <div style="margin:10px 0px 15px 0px;">
                    <input type="checkbox" id="useAnotherAddress" name="useAnotherAddress" value="1" onchange="toggleAddressForm()">
                    <label for="useAnotherAddress">Ship To Another Address</label>
                </div>

            </div>

            <div style="margin-top: 10px;">
                <h2>Payment Method</h2>
                <div>
                    <h3>
                        <li>E-Wallet</li>
                    </h3>
                </div>
                <div class="e-wallet-container">
                    <label class="e-wallet-pic" for="tng"><img class="wallet-pic" src="image/tng.png" alt="tng"></label>
                    <input class="e-wallet-radio" type="radio" name="payment" id="tng" value="tng" />

                    <label class="e-wallet-pic" for="grab"><img class="wallet-pic" src="image/grab.png" alt="grab"></label>
                    <input class="e-wallet-radio" type="radio" name="payment" id="grab" value="grab" />

                    <label class="e-wallet-pic" for="boost"><img class="wallet-pic" src="image/boost.png" alt="boost"></label>
                    <input class="e-wallet-radio" type="radio" name="payment" id="boost" value="boost" />
                </div>

                <div>
                    <h3>
                        <li>Bank</li>
                    </h3>
                </div>

                <div class="bank-container">
                    <label class="e-wallet-pic" for="maybank"><img class="wallet-pic" src="image/maybank.png" alt="maybank"></label>
                    <input class="e-wallet-radio" type="radio" name="payment" id="maybank" value="maybank" />

                    <label class="e-wallet-pic" for="publicBank"><img class="wallet-pic" src="image/publicBank.png" alt="publicBank"></label>
                    <input class="e-wallet-radio" type="radio" name="payment" id="publicBank" value="publicBank" />

                    <label class="e-wallet-pic" for="hongLeongBank"><img class="wallet-pic" src="image/hongLeongBank.png" alt="hongLeongBank"></label>
                    <input class="e-wallet-radio" type="radio" name="payment" id="hongLeongBank" value="hongLeongBank" />
                </div>

                <fieldset class="fieldset_payment">
                    <legend id="payment_method_name"></legend>
                    <div class="input_e_wallet_info" style="display: none;">

                        <div class="check_out_input">
                            <label for="phoneNoEwallet">Phone No:</label>
                            <input type="tel" name="phoneNoEwallet" id="phoneNoEwallet" placeholder="XXXXXXXXX">
                        </div>
                        <div class="check_out_input">
                            <label for="passwordEwallet">Password:</label>
                            <input type="password" name="passwordEwallet" id="passwordEwallet" placeholder="*********">
                        </div>
                    </div>


                    <div class="input_bank_info" style="display: none;">
                        <div class="check_out_input">
                            <label for="cardName">Name On Card:</label>
                            <input type="text" name="cardName" id="cardName" placeholder="XXXX XXXXX" \>
                        </div>

                        <div class="check_out_input">
                            <label for="cardNo">Card No:</label>
                            <input type="text" name="cardNo" id="cardNo" placeholder="XXXX XXXX XXXX">
                        </div>
                        <div class="check_out_input">
                            <label for="expiredDate">Expired Date:</label>
                            <input type="text" name="expiredDate" id="expiredDate" placeholder="XX/XX">
                        </div>
                        <div class="check_out_input">
                            <label for="cvc">CVC:</label>
                            <input type="text" name="cvc" id="cvc" placeholder="XXX" />
                        </div>
                    </div>
                </fieldset>



            </div>


        </div>


        <div class="order_info">
            <h1 style="text-align: center;">
                <i class="fas fa-receipt"></i>
                SUMMARY ORDER
            </h1>

            <table class="styled-table-orderSummary">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Thumbnail</th>
                        <th>Product ID</th>
                        <th>Name</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>

                </thead>

                <tbody>
                    <tr>
                        <td>1</td>
                        <td><img class="img_summary" src="image/maydayConcert.jpg" alt=""></td>
                        <td>W110</td>
                        <td>MayDay Concert</td>
                        <td>x 2</td>
                        <td>RM 100</td>
                        <td>RM 200</td>
                    </tr>

                    <tr>
                        <td>2</td>
                        <td><img class="img_summary" src="image/maydayConcert.jpg" alt=""></td>
                        <td>W110</td>
                        <td>MayDay Concert</td>
                        <td>x 2</td>
                        <td>RM 100</td>
                        <td>RM 200</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="row_total">
                        <th colspan="5">Processing Fee (10%)</th>
                        <th colspan="2">RM 40</th>
                    </tr>
                    <tr class="row_total">
                        <th colspan="5">Tax (6%)</th>
                        <th colspan="2">RM 10</th>
                    </tr>
                    <tr class="row_total row_subtotal">
                        <th colspan="5">Subtotal</th>
                        <th colspan="2">RM 400</th>
                    </tr>
                </tfoot>
            </table>

            <div style="text-align: end;">
                <button type="button" class="checkOutButton" id="checkOutButton" name="change" onclick="openModal('checkOutModal')">Check Out</button>
            </div>
            <div class="overlay_all" id="checkOutModal" style="display: none;">
                <div class="modal">
                    <h3 style="text-align: left;margin-left:10px;"><i class="fas fa-cart-arrow-down"></i> Order Will Be
                        Placed</h3>
                    <h2 class="comfirmMessage">Are You Sure Want To Place Order ?</h2>
                    <button type="button" class="notConfirmUpdate" onclick="closeModal('checkOutModal')">NO</button>
                    <input type="submit" name="comfirmCheckOut" class="comfirmUpdate" value="Yes" />
                    <!-- <a href="paymentSucess.html" name="comfirmCheckOut" class="comfirmUpdate"
                        style="padding: 20px 30px;">Yes</a> -->
                </div>
            </div>
        </div>
    </form>


    <div class="overlay_all" id="errors" style="display: none;">
        <div class="modal">
            <h3 style="text-align: left;margin-left:10px;color: red;"><i class="fas fa-exclamation-circle"></i>Errors
            </h3>
            <h2 class="comfirmMessage">Make Sure All The Fields Are Filled !</h2>
     
            <button type="button" style="margin-top:30px;" class="notConfirmUpdate" onclick="closeModal('errors')">OK</button>
        </div>
    </div>

    <script src="js/app.js"></script>
    <!-- Google Translate API script -->
    <!-- <script type="text/javascript">
function googleTranslateElementInit() {
new google.translate.TranslateElement({
  pageLanguage: 'en', 
  includedLanguages: 'en,ms,zh-CN', // Languages to display in the dropdown
  layout: google.translate.TranslateElement.InlineLayout.SIMPLE, // Display layout
  autoDisplay: false // Disables automatic display of the widget
}, 'google_translate_element');
}

</script>
<script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script> -->
</body>

</html>