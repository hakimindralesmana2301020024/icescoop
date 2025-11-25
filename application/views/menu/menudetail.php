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
                    <?php if(!empty($product) && !empty($product['img_url'])): ?>
                        <img src="<?= $product['img_url']; ?>" alt="thumb1" class="thumb active" />
                    <?php else: ?>
                        <img src="<?php echo base_url('assets/images/placeholder.svg'); ?>" alt="thumb1" class="thumb active" />
                    <?php endif; ?>
                </div>
                <div class="gallery-main">
                    <?php if(!empty($product) && !empty($product['img_url'])): ?>
                        <img src="<?= $product['img_url']; ?>" alt="<?= htmlspecialchars($product['name'] ?? 'Product'); ?>" />
                    <?php else: ?>
                        <img src="<?php echo base_url('assets/images/placeholder.svg'); ?>" alt="Product" />
                    <?php endif; ?>
                </div>
            </div>
            <div class="menudetail-info">
                <div class="menudetail-rating">
                    <span class="star"><i class="bi bi-star-fill"></i></span>
                    <span class="star"><i class="bi bi-star-fill"></i></span>
                    <span class="star"><i class="bi bi-star-fill"></i></span>
                    <span class="star"><i class="bi bi-star-fill"></i></span>
                    <span class="star"><i class="bi bi-star-half"></i></span>
                    <span class="rating-score"><?= htmlspecialchars($product['rating'] ?? ''); ?></span>
                </div>
                <div class="menudetail-title"><?= htmlspecialchars($product['name'] ?? 'Product'); ?></div>
                <div class="menudetail-price accent">Rp <?= htmlspecialchars($product['price'] ?? ''); ?></div>
                <div class="menudetail-desc"><?= nl2br(htmlspecialchars($product['desc'] ?? $product['description'] ?? '')); ?></div>

                <div class="menudetail-buy">
                    <div class="qty-group">
                        <button class="qty-btn">-</button>
                        <input type="text" value="1" class="qty-input" />
                        <button class="qty-btn">+</button>
                    </div>
                    <button class="btn primary add-to-cart">Add to Cart</button>
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
                <?php if (!empty($product) && !empty($product['long_description'])): ?>
                    <?= nl2br(htmlspecialchars($product['long_description'])); ?>
                <?php else: ?>
                    <p>Deskripsi detail belum tersedia untuk produk ini.</p>
                <?php endif; ?>
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
