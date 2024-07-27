<?php
require('_base.php');
include('_header.php');

?>


    <div class="product-container">
        <div class="product-image">
            <img src="image/Nitro_5_AGW_KSP08-3.png" alt="Product Image">
        </div>
        <div class="product-details">
            <h1>Acer Nitro 5 2024</h1>
            <div>
                <div class="rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="category">Computer</p>
                <p class="price">RM439.00</p>
            </div>
            <div class="options">
                <label for="" class="spec">Spec</label>
                <div class="option_box">
                    <input type="radio" class="input-radio" id="12GB+128ROM" name="size" value="12GB+128ROM">
                    <label class="option" for="12GB+128ROM">12GB+128ROM</label>

                    <input type="radio" class="input-radio" id="12GB+512ROM" name="size" value="12GB+512ROM">
                    <label class="option" for="12GB+512ROM">12GB+512ROM</label>

                    <input type="radio" class="input-radio" id="12GB+1TB" name="size" value="12GB+1TB">
                    <label class="option" for="12GB+1TB">12GB+1TB</label>

                    <input type="radio" class="input-radio" id="12GB+1TB" name="size" value="12GB+1TB">
                    <label class="option" for="12GB+1TB">12GB+1TB</label>

                    <input type="radio" class="input-radio" id="12GB+1TB" name="size" value="12GB+1TB">
                    <label class="option" for="12GB+1TB">12GB+1TB</label>

                    <input type="radio" class="input-radio" id="12GB+1TB" name="size" value="12GB+1TB">
                    <label class="option" for="12GB+1TB">12GB+1TB</label>

                    <input type="radio" class="input-radio" id="12GB+1TB" name="size" value="12GB+1TB">
                    <label class="option" for="12GB+1TB">12GB+1TB</label>
                   
                </div>

            </div>
            
            <div class="clearAndQty_box">
                <div>
                    <small class="clear-btn">Clear</small>
                </div>

                <div class="qty_input">
                    <label>QTY:</label>
                    <input type="number" name="qty" value="1" id="qty"/>
                </div>
            </div>
           
            
            
            <div  style="text-align: center;">
                <button id="add-to-cart">ADD TO CART</button>
            </div>
     
        </div>
    </div>
    <div class="product-container">
        <div class="tab-container">
            <div class="tabs">
                <button class="tab-link active_tab" data-tab="short">Description</button>
                <button class="tab-link" data-tab="medium">Use Case</button>
                <button class="tab-link" data-tab="long">Spec</button>
            </div>
            <div class="tab-content active_tab" id="short">
                <h2>Descriptipon</h2>
                <p>Praesent nonummy mi in odio. Nullam accumsan lorem in dui. Vestibulum turpis sem, aliquet eget, lobortis pellentesque, rutrum eu, nisl. Nullam accumsan lorem in dui. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu.</p>
            </div>
            <div class="tab-content" id="medium">
                <h2></h2>
                <h2>Usage Scenarios and Case Studies</h2>
                <p>Learn how our product can be utilized in different scenarios to achieve the best results. Here are a few examples:</p>
                <h3>Business Use:</h3>
                <p>Our product helps businesses streamline their operations, increase productivity, and reduce costs. Case Study: ABC Corp improved their workflow efficiency by 30%.</p>
                <h3>Personal Use:</h3>
                <p>Ideal for personal use, offering high performance for gaming, video editing, and more. Customer Review: "This product exceeded my expectations in every way."</p>
                <h3>Educational Use:</h3>
                <p>Perfect for students and educators, providing a powerful tool for learning and teaching. Case Study: XYZ School saw a 25% improvement in student engagement.</p>
                <h3>Support and FAQs:</h3>
                <p>For more information and support, visit our <a href="support-page-url">Support Page</a> and check our <a href="faq-page-url">FAQs</a>.</p>
            
            </div>
            <div class="tab-content" id="long">
                <h2>Spec</h2>
                <p>This is the long section content.</p>
            </div>
        </div>
    
    </div>


    <div class="review-container">
        <form class="review_form">
            <h2>Review Form: Product Name</h2>
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
                <textarea  cols="50" rows="4" placeholder="Write your review here..."></textarea>
            </div> 
            <button type="submit" class="submit-btn">Submit Review</button>
        </form>
    </div>

    
<section class="product_container">
    <h1 class="related-product-title">
        <i class="fas fa-box"></i> Related Product
    </h1>

    <div class="all_product_box">
            <div class="single_product">
                <div class="tag">
                    <span>Related Products</span>
                </div>
                <div class="product_image">
                    <img src="image/Nitro_5_AGW_KSP08-3.png" alt="Product Image">
                </div>
                <h3 class="product_name">Acer Nitro</h3>
                <div class="rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="price">RM 100</p>
                <a href="#" class="view_button">View</a>
            </div>

            <div class="single_product">
                <div class="tag">
                    <span>Related Products</span>
                </div>
                <div class="product_image">
                    <img src="image/hongLeongBank.png" alt="Product Image">
                </div>
                <h3 class="product_name">Product Name</h3>
                <div class="rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="price">RM 100</p>
                <a href="#" class="view_button">View</a>
            </div>
            

            <div class="single_product">
                <div class="tag">
                    <span>Related Products</span>
                </div>
                <div class="product_image">
                    <img src="image/hongLeongBank.png" alt="Product Image">
                </div>
                <h3 class="product_name">Product Name</h3>
                <div class="rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="price">RM 100</p>
                <a href="#" class="view_button">View</a>
            </div>

            <div class="single_product">
                <div class="tag">
                    <span>Related Products</span>
                </div>
                <div class="product_image">
                    <img src="image/Nitro_5_AGW_KSP08-3.png" alt="Product Image">
                </div>
                <h3 class="product_name">Acer Nitro</h3>
                <div class="rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="price">RM 100</p>
                <a href="#" class="view_button">View</a>
            </div>

           
    </div>
</section>

  
<script>
    $(document).ready(function() {
        $('.clear-btn').on('click', function() {
            $('.input-radio:checked').prop('checked', false);
        });

        
    $('.tab-link').on('click', function() {
        var tabId = $(this).data('tab');

        $('.tab-link').removeClass('active_tab');
        $(this).addClass('active_tab');

        $('.tab-content').removeClass('active_tab');
        $('#' + tabId).addClass('active_tab');
    });

    });
    </script>
    
    <?php
    include('_footer.php');
    ?>