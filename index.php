<?php $title = "Home" ?>
<?php
include('_header.php');
require('_base.php');
?>

<?php
$welcome_message = temp('welcome_message');
?>

<?php if (isset($welcome_message)) { ?>
    <div class="overlay_all" id="welcomeModal" style="display: block;">
        <div class="modal">
            <h3>Have A Nice Day At Smart</h3>
            <h2 class="comfirmMessage">
                <?= $welcome_message ?>
            </h2>
            <button type="button" class="notConfirmUpdate" onclick="closeModal('welcomeModal')">Ok</button>
        </div>
    </div>
<?php } ?>


<div class="banner">
    <video autoplay muted loop id="background-video">
        <source src="image/mixkit-neon-lasers-light-up-a-cyberpunk-dancer-50458-hd-ready.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    <div class="overlay"></div>
    <div class="banner-text">
        <h1>Welcome to Tech Haven</h1>
        <p>Discover the latest and greatest in electronic gadgets</p>
        <button class="outlined-button">Shop Now</button>
    </div>
</div>

<div class="statistics">
    <div class="left">
        <img src="image/gadjet.jpg" alt="Property Image">
    </div>
    <div class="right">
        <h2>OUR GADGETS ARE A HIT</h2>
        <p>From the latest smartphones to cutting-edge laptops, we offer a wide range of electronic gadgets. Enjoy competitive prices, fast shipping, and exceptional customer service with every purchase.</p>
        <button class="btn">Know More</button>
        <div class="rate">
            <div>
                <p class="count" data-target="1000">0</p>
                <small>Gadgets Sold</small>
            </div>
            <div>
                <p class="count" data-target="800">0</p>
                <small>Happy Customers</small>
            </div>
        </div>
    </div>

</div>


<section class="latest_product_container">
 
        <div class="latest_product_box">
            <span class="arrow_left"><i class="fas fa-arrow-left"></i></span>
            <span class="arrow_right"><i class="fas fa-arrow-right"></i></span>
            <div class="slide">
                <!-- Slide 1 -->
                <div class="latest_product">
                    <div class="tag">
                        <span>Latest Products</span>
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

                <!-- Slide 2 -->
                <div class="latest_product">
                    <div class="tag">
                        <span>Latest Products</span>
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

                <!-- Slide 3 -->
                <div class="latest_product">
                    <div class="tag">
                        <span>Latest Products</span>
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

                <!-- Slide 4 -->
                <div class="latest_product">
                    <div class="tag">
                        <span>Latest Products</span>
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

                <!-- Slide 5 -->
                <div class="latest_product">
                    <div class="tag">
                        <span>Latest Products</span>
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

                <!-- Slide 6 -->
                <div class="latest_product">
                    <div class="tag">
                        <span>Latest Products</span>
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
        </div>
    </section>

    <!--CATEGORY LIST SECTION-->
<div class="cat_ind">
    <h1 class="collaborate_title">Collaborate Partners</h1>
    <div class="category_list_index">
        <div class="category_list">
            <div class="category_box">
                <img src="image/google-removebg-preview.png" alt="Google">
                <span>Google</span>
            </div>
            <div class="category_box">
                <img src="image/smartmasterlogo.png" alt="Smart Master">
                <span>Smart master</span>
            </div>
            <div class="category_box">
                <img src="image/tarumt.png" alt="TARUMT">
                <span>TARUMT</span>
            </div>
            <div class="category_box">
                <img src="image/tengxun.png" alt="tengxun">
                <span>Teng Xun</span>
            </div>
            <div class="category_box">
                <img src="image/ioi.png" alt="IOI GROUP">
                <span>IOI GROUP</span>
            </div>
            <div class="category_box">
                <img src="image/inventech.jpeg" alt="inventech solution">
                <span>inventech</span>
            </div>
            <div class="category_box">
                <img src="image/msig.png" alt="msig">
                <span>msig</span>
            </div>
        </div>
    </div>
</div>


<section class="clients_box" id="clients_box">
    <div class="section-title">
        <h1>Testimonial</h1>
    </div>

    <section class="main">
        <div class="full-boxer">
            <div class="comment-box">
                <div class="box-top">
                    <div class="profile-photo">
                        <div class="profile-image">
                            <img src="image/kong.jpeg" alt="Profile Image">
                        </div>
                        <div class="Name">
                            <strong>Jensen Phang</strong>
                            <span>@Js_Phang</span>
                        </div>
                    </div>
                </div>
                <div class="comment">
                    <p>Good Quality! ❤️</p>
                </div>
            </div>

            <div class="comment-box">
                <div class="box-top">
                    <div class="profile-photo">
                        <div class="profile-image">
                            <img src="image/kong.jpeg" alt="Profile Image">
                        </div>
                        <div class="Name">
                            <strong>Jensen Phang</strong>
                            <span>@Js_Phang</span>
                        </div>
                    </div>
                </div>
                <div class="comment">
                    <p>Good Quality! ❤️</p>
                </div>
            </div>

            <div class="comment-box">
                <div class="box-top">
                    <div class="profile-photo">
                        <div class="profile-image">
                            <img src="image/kong.jpeg" alt="Profile Image">
                        </div>
                        <div class="Name">
                            <strong>Jensen Phang</strong>
                            <span>@Js_Phang</span>
                        </div>
                    </div>
                </div>
                <div class="comment">
                    <p>Good Quality! ❤️</p>
                </div>
            </div>


        </div>
    </section>
</section>




<script>
    document.addEventListener('DOMContentLoaded', () => {
        const counters = document.querySelectorAll('.count');

        const clientsBox = document.getElementById("clients_box");
        const speed = 200; // Adjust speed as necessary

        const observerOptions = {
            threshold: 0.5 // Trigger when 50% of the element is visible
        };
        //learn from youtube and gpt
        const countObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    clientsBox.classList.add("show");
                    const counter = entry.target;
                    const updateCount = () => {
                        const target = +counter.getAttribute('data-target');
                        const count = +counter.innerText;

                        const inc = target / speed;

                        if (count < target) {
                            counter.innerText = Math.ceil(count + inc);
                            setTimeout(updateCount, 1);
                        } else {
                            counter.innerText = target + " +";
                        }
                    };

                    updateCount();
                    observer.unobserve(counter); // Stop observing once the animation has started
                }
            });
        }, observerOptions);

        counters.forEach(counter => {
            countObserver.observe(counter);
        });


        const container = document.querySelector('.category_list');
        const items = document.querySelectorAll('.category_box');
        const cloneItems = Array.from(items).map(item => item.cloneNode(true)); // Clone items
        let scrollInterval;
        let scrollSpeed = 3; // Change the scroll speed by adjusting this value

        // Append cloned items to the end of the container
        cloneItems.forEach(cloneItem => {
            container.appendChild(cloneItem);
        });

        function startAutoScroll() {
            scrollInterval = setInterval(() => {
                container.scrollLeft += scrollSpeed;
                // Reset scroll position when all items have been scrolled
                if (container.scrollLeft >= container.scrollWidth / 2) {
                    container.scrollLeft = 0;
                }
            }, 20); // Adjust the interval for smoother/faster scrolling
        }

        function stopAutoScroll() {
            clearInterval(scrollInterval);
        }

        container.addEventListener('mouseover', stopAutoScroll);
        container.addEventListener('mouseout', startAutoScroll);

        startAutoScroll(); // Start auto scrolling initially

    });
</script>
<?php include('_footer.php') ?>