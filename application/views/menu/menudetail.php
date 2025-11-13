<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="menudetail-page">
    <!-- Hero / Page title -->
    <section class="menudetail-hero">
        <div class="container menudetail-hero-inner">
            <h1 class="menudetail-hero-title">Single Product Layout 01</h1>
            <div class="menudetail-breadcrumb">
                <span class="breadcrumb-pill">Home&nbsp;&nbsp;/&nbsp;&nbsp;Shop&nbsp;&nbsp;/&nbsp;&nbsp;Product Detail</span>
            </div>
        </div>
    </section>

    <section class="menudetail-main">
        <div class="container menudetail-main-inner">
            <div class="menudetail-gallery">
                <div class="gallery-thumbs">
                    <img src="<?php echo base_url('assets/images/placeholder.svg'); ?>" alt="thumb1" class="thumb active" />
                    <img src="<?php echo base_url('assets/images/placeholder.svg'); ?>" alt="thumb2" class="thumb" />
                    <img src="<?php echo base_url('assets/images/placeholder.svg'); ?>" alt="thumb3" class="thumb" />
                    <img src="<?php echo base_url('assets/images/placeholder.svg'); ?>" alt="thumb4" class="thumb" />
                </div>
                <div class="gallery-main">
                    <img src="<?php echo base_url('assets/images/placeholder.svg'); ?>" alt="Classic Vanilla Ice Cream" />
                </div>
            </div>
            <div class="menudetail-info">
                <div class="menudetail-rating">
                    <span class="star"><i class="bi bi-star-fill"></i></span>
                    <span class="star"><i class="bi bi-star-fill"></i></span>
                    <span class="star"><i class="bi bi-star-fill"></i></span>
                    <span class="star"><i class="bi bi-star-fill"></i></span>
                    <span class="star"><i class="bi bi-star-half"></i></span>
                    <span class="rating-score">4.85</span>
                </div>
                <div class="menudetail-title">Classic Vanilla Ice Cream</div>
                <div class="menudetail-price accent">$5.99</div>
                <div class="menudetail-desc">Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magna.</div>
                <div class="menudetail-color">
                    <div class="label">Color:</div>
                    <div class="color-options">
                        <span class="color-dot" style="background:#b97a56"></span>
                        <span class="color-dot" style="background:#f8e1c1"></span>
                        <span class="color-dot" style="background:#fff"></span>
                        <span class="color-dot" style="background:#e3c7b7"></span>
                    </div>
                </div>
                <div class="menudetail-size">
                    <div class="label">Size:</div>
                    <div class="size-options">
                        <span class="size-btn">L</span>
                        <span class="size-btn active">M</span>
                        <span class="size-btn">S</span>
                    </div>
                </div>
                <div class="menudetail-buy">
                    <div class="qty-group">
                        <button class="qty-btn">-</button>
                        <input type="text" value="1" class="qty-input" />
                        <button class="qty-btn">+</button>
                    </div>
                    <button class="btn primary add-to-cart">Add to Cart</button>
                </div>
                <div class="menudetail-actions">
                    <label><input type="checkbox" checked disabled> Add to wishlist</label>
                    <label><input type="checkbox" disabled> Compare</label>
                </div>
            </div>
        </div>
    </section>

    <section class="menudetail-tabs">
        <div class="container">
            <div class="tabs">
                <div class="tab active">Description</div>
                <div class="tab">Additional Information</div>
                <div class="tab">Reviews</div>
            </div>
            <div class="tab-content">
                <p>Quia voluptatem sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.</p>
            </div>
        </div>
    </section>

    <section class="menudetail-related">
        <div class="container">
            <h2 class="menudetail-related-title">Related <span class="accent">Products</span></h2>
            <p class="menudetail-related-desc">Choose from some of related products</p>
            <div class="menudetail-related-list">
                <div class="product-card">
                    <div class="product-fav"><i class="bi bi-heart"></i></div>
                    <div class="product-img"><img src="<?php echo base_url('assets/images/placeholder.svg'); ?>" alt="Strawberry Sundae" /></div>
                    <div class="product-info">
                        <div class="product-title">Strawberry Sundae</div>
                        <div class="product-rating">
                            <span class="star"><i class="bi bi-star-fill"></i></span>
                            <span class="star"><i class="bi bi-star-fill"></i></span>
                            <span class="star"><i class="bi bi-star-fill"></i></span>
                            <span class="star"><i class="bi bi-star-fill"></i></span>
                            <span class="star"><i class="bi bi-star-half"></i></span>
                            <span class="rating-score">4.85</span>
                        </div>
                        <div class="product-desc">Strawberry ice cream with fresh strawberries</div>
                    </div>
                    <div class="product-bottom">
                        <div class="product-price accent">$5.99</div>
                        <button class="product-cart"><i class="bi bi-cart"></i></button>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-fav"><i class="bi bi-heart"></i></div>
                    <div class="product-img"><img src="<?php echo base_url('assets/images/placeholder.svg'); ?>" alt="Chocolate Chip Cookie Cone" /></div>
                    <div class="product-info">
                        <div class="product-title">Chocolate Chip Cookie Cone</div>
                        <div class="product-rating">
                            <span class="star"><i class="bi bi-star-fill"></i></span>
                            <span class="star"><i class="bi bi-star-fill"></i></span>
                            <span class="star"><i class="bi bi-star-fill"></i></span>
                            <span class="star"><i class="bi bi-star-fill"></i></span>
                            <span class="star"><i class="bi bi-star-half"></i></span>
                            <span class="rating-score">4.85</span>
                        </div>
                        <div class="product-desc">Chocolate chip cookie dough ice cream in a cone.</div>
                    </div>
                    <div class="product-bottom">
                        <div class="product-price accent">$4.49</div>
                        <button class="product-cart"><i class="bi bi-cart"></i></button>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-fav"><i class="bi bi-heart"></i></div>
                    <div class="product-img"><img src="<?php echo base_url('assets/images/placeholder.svg'); ?>" alt="Rocky Road Sundae" /></div>
                    <div class="product-info">
                        <div class="product-title">Rocky Road Sundae</div>
                        <div class="product-rating">
                            <span class="star"><i class="bi bi-star-fill"></i></span>
                            <span class="star"><i class="bi bi-star-fill"></i></span>
                            <span class="star"><i class="bi bi-star-fill"></i></span>
                            <span class="star"><i class="bi bi-star-fill"></i></span>
                            <span class="star"><i class="bi bi-star-half"></i></span>
                            <span class="rating-score">4.85</span>
                        </div>
                        <div class="product-desc">Marshmallow and nutty rocky road ice cream.</div>
                    </div>
                    <div class="product-bottom">
                        <div class="product-price accent">$5.89</div>
                        <button class="product-cart"><i class="bi bi-cart"></i></button>
                    </div>
                </div>
                <div class="product-card">
                    <div class="product-fav"><i class="bi bi-heart"></i></div>
                    <div class="product-img"><img src="<?php echo base_url('assets/images/placeholder.svg'); ?>" alt="Peach Melba Sundae" /></div>
                    <div class="product-info">
                        <div class="product-title">Peach Melba Sundae</div>
                        <div class="product-rating">
                            <span class="star"><i class="bi bi-star-fill"></i></span>
                            <span class="star"><i class="bi bi-star-fill"></i></span>
                            <span class="star"><i class="bi bi-star-fill"></i></span>
                            <span class="star"><i class="bi bi-star-fill"></i></span>
                            <span class="star"><i class="bi bi-star-half"></i></span>
                            <span class="rating-score">4.85</span>
                        </div>
                        <div class="product-desc">Peach ice cream topped with raspberry sauce.</div>
                    </div>
                    <div class="product-bottom">
                        <div class="product-price accent">$5.39</div>
                        <button class="product-cart"><i class="bi bi-cart"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
