<?php
// require_once("../_base.php");
$title = "Products";
require('../../_base.php');
include('../_headerAdmin.php');
include('../_sideBar.php');
?>
<style>
    .card {
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        transition: box-shadow 0.3s ease;
        background-color: #fff;
        width: 95%;
        margin-bottom: 10px;
    }

    .card-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .card-content {
        padding: 16px;
    }

    .card h3 {
        margin: 0 0 10px 0;
        font-size: 18px;
        color: #333;
    }

    .card p {
        margin: 0;
        font-size: 14px;
        color: #777;
    }

    .card:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        cursor: pointer;
    }
</style>
<div class="container">
    <h1>Admin Product Page </h1>
    <div class="divline"></div>
    <div class="card">
        <a href="displayProductsAttributes.php" class="card-link">
            <div class="card-content">  
                <h3>Display All Product </h3>
                <p>Start by adding a new product category to organize your products.</p>
            </div>
        </a>
    </div>
    <div class="card">
        <a href="addCategory.php" class="card-link">
            <div class="card-content">
                <h3>Add Product Category </h3>
                <p>Start by adding a new product category to organize your products.</p>
            </div>
        </a>
    </div>
    <div class="card">
        <a href="addAttributes.php" class="card-link">
            <div class="card-content">
                <h3>Add Attribute Types</h3>
                <p>Start by adding a new product category to organize your products.</p>
            </div>
        </a>
    </div>
    <div class="card">
        <a href="addOption.php" class="card-link">
            <div class="card-content">
                <h3>Add Options </h3>
                <p>Start by adding a new product category to organize your products.</p>
            </div>
        </a>
    </div>
    <div class="card">
        <a href="addProduct.php" class="card-link">
            <div class="card-content">
                <h3>Add Product </h3>
                <p>Start by adding a new product category to organize your products.</p>
            </div>
        </a>
    </div>
    <div class="card">
        <a href="add_product_attribute.php" class="card-link">
            <div class="card-content">
                <h3>Add Product Attributes </h3>
                <p>Start by adding a new product category to organize your products.</p>
            </div>
        </a>
    </div>
</div>
<?php include('../_footerAdmin.php') ?>