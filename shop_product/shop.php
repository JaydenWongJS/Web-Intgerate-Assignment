<?php
$title = "Shop";
require_once('../_base.php');
include('../_header.php');
include('../nav_bar.php');
?>

<!---- add to cart message --------->
<div id="info"><?= temp("add_cart_info") ?></div>

<div class="overlay_filter"></div>
<form class="fixed_filter_bar">
    <div class="closeFilter_box">
        <span id="closeFilter" class="closeFilter">
            <i class="fas fa-times close-icon"></i>
        </span>
    </div>
    <div class="filter_section">
        <label class="filter_label_category">Category:</label>
        <div class="radio_group">
            <input type="radio" id="category1" name="category" value="Category1">
            <label for="category1" class="category_label_filter"> Category 1</label>
            <input type="radio" id="category2" name="category" value="Category2">
            <label for="category2" class="category_label_filter"> Category 2</label>
        </div>
    </div>
    <div class="filter_section">
        <label class="filter_label_price">Price Range:</label>
        <div class="radio_group">
            <input type="radio" id="price1" name="price" value="0-100">
            <label for="price1" class="category_label_filter"> 0 - 100</label>
            <input type="radio" id="price2" name="price" value="Category2">
            <label for="price2" class="category_label_filter"> 100 - 200 </label>
        </div>
    </div>

    <input name="submit" class="submit_filter" value="Filter">

</form>


<section class="product_container">
    <h1 class="related-product-title">
        <i class="fas fa-dollar-sign"></i> Shop
    </h1>
    <div class="filter_product">
        <div style="text-align: center;">
            <button class="filter_button" id="filter"><i class="fa fa-filter"></i> Filter</button>
        </div>
        <form action="" method="post" class="form_text_searchProduct">
            <input type="search" name="searchProdct" id="searchProdct" class="searchProdct" placeholder="Search Here ....." />
            <button class="searchProductButton"><i class="fas fa-search"></i></button>
        </form>
        <form id="form-select" class="">
            <select class="form-select" onchange="submitSortingSize()" name="sorting">
                <option selected disabled>Sorting</option>
                <option value="highRate">High Rating</option>
                <option value="exp">Most Popular</option>
                <option value="inexp">Price Low To High</option>
                <option value="rating">Price Low To High</option>
            </select>
        </form>
    </div>


    <div class="all_product_box">
        <?php
        // Fetch products with their attributes and options
        $query = "
    SELECT p.product_id, p.product_name , p.product_image,p.total_rating,a.attributes_id, a.attributes_type, o.option_id, o.option_value, pa.price
    FROM products p
    LEFT JOIN product_attributes pa ON p.product_id = pa.product_id
    LEFT JOIN attributes a ON pa.attributes_id = a.attributes_id
    LEFT JOIN options o ON pa.option_id = o.option_id
        Where p.status='active'
    ORDER BY p.product_id, a.attributes_id, o.option_id

";

        $stmt = $_db->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll();

        $products = [];
        foreach ($results as $row) {
            $product_id = $row->product_id;
            if (!isset($products[$product_id])) { //examples when the first product is set then set the attributes and options,then loop and see not same prodcuts id then create a new 
                $products[$product_id] = [
                    'product_name' => $row->product_name,
                    'product_image' => $row->product_image,
                    'total_rating' => $row->total_rating,
                    'price_range' => [
                        'min' => $row->price,
                        'max' => $row->price
                    ],
                    'attributes' => []
                ];
            } else {
                // Update price range
                if ($row->price < $products[$product_id]['price_range']['min']) {
                    $products[$product_id]['price_range']['min'] = $row->price;
                }
                if ($row->price > $products[$product_id]['price_range']['max']) {
                    $products[$product_id]['price_range']['max'] = $row->price;
                }
            }

            $attribute_id = $row->attributes_id;
            if ($attribute_id) {
                if (!isset($products[$product_id]['attributes'][$attribute_id])) {
                    $products[$product_id]['attributes'][$attribute_id] = [
                        'attributes_type' => $row->attributes_type,
                        'options' => []
                    ];
                }
                $products[$product_id]['attributes'][$attribute_id]['options'][] = [
                    'option_id' => $row->option_id,
                    'option_value' => $row->option_value,
                    'price' => $row->price
                ];
            }
        }
        ?>

        <div class="all_product_box">
            <?php foreach ($products as $product_id => $product): ?>
                <div class="single_product">
                    <div class="tag">
                        <?php if ($product["total_rating"] >= 3.5) : ?>
                            <span>Related Products</span>
                        <?php endif; ?>
                    </div>
                    <div class="product_image">
                        <!-- Replace with dynamic image path if available -->
                        <img src="uploadsImage/productImage/<?= htmlspecialchars($product['product_image']); ?>" alt="Product Image">
                    </div>
                    <h3 class="product_name"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <div class="rating">
                        <div class="rating">
                            <?php for ($i = 0; $i < floor($product["total_rating"]); $i++): ?>
                                <i class="fas fa-star"></i>
                            <?php endfor; ?>
                        </div>

                    </div>
                    <p class="price"><?= getProductRangePrice($product); ?></p>

                    <a href="productDetails.php?product_id=<?= $product_id; ?>" class="view_button">View</a>
                </div>
            <?php endforeach; ?>
        </div>




    </div>
</section>
<script src="../js/app.js"></script>
</body>

</html>