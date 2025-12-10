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

<script>
document.addEventListener('DOMContentLoaded', function(){
    var dec = document.getElementById('qty-dec');
    var inc = document.getElementById('qty-inc');
    var input = document.getElementById('qty-input');
    if(!input) return;
    function clamp(v){ v = parseInt(v) || 0; if(v<1) v = 1; return v; }
    if(dec) dec.addEventListener('click', function(){ input.value = clamp(parseInt(input.value)-1); });
    if(inc) inc.addEventListener('click', function(){ input.value = clamp(parseInt(input.value)+1); });

    // size pill behavior: when a pill radio changes, set hidden size_id and update price
    var sizePills = document.querySelectorAll('.size-pill');
    var sizeRadioInputs = document.querySelectorAll('.size-radio');
    var sizeIdInput = document.getElementById('size-id-input');
    var priceElem = document.getElementById('product-price');
    var basePrice = priceElem ? parseFloat(priceElem.getAttribute('data-base-price') || 0) : 0;
    if (sizeRadioInputs && sizeRadioInputs.length && sizeIdInput) {
        sizeRadioInputs.forEach(function(radio){
                var handleChange = function(){
                    if (!this.checked) return;
                    var val = this.value;
                    sizeIdInput.value = val;
                    // find pill element to get price data
                    var pill = this.closest('.size-pill');
                    if (pill && pill.dataset && pill.dataset.price) {
                        var p = parseFloat(pill.dataset.price) || basePrice;
                        if (priceElem) priceElem.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(p).replace(/,/g, '.');
                    }
                    // mark active
                    sizePills.forEach(function(pb){ pb.classList.remove('active'); });
                    if (pill) pill.classList.add('active');
                };
                radio.addEventListener('change', handleChange);
                // set initial value if checked on load and update UI
                if (radio.checked) {
                    handleChange.call(radio);
                }
        });
    }
});
</script>

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
                <div class="menudetail-price accent" id="product-price" data-base-price="<?= isset($product['price']) ? (float)$product['price'] : 0; ?>"><?= format_rp($product['price'] ?? 0); ?></div>
                <div class="menudetail-desc"><?= nl2br(htmlspecialchars($product['desc'] ?? $product['description'] ?? '')); ?></div>

                <?php
                // If sizes are not provided from the DB yet, provide a hardcoded fallback
                if (empty($product['sizes']) || !is_array($product['sizes'])) {
                    $base = isset($product['price']) ? $product['price'] : 0;
                    $product['sizes'] = [
                        ['ps_id' => 'S', 'price' => $base, 'label' => 'Small'],
                        ['ps_id' => 'M', 'price' => $base, 'label' => 'Medium'],
                        ['ps_id' => 'L', 'price' => $base, 'label' => 'Large'],
                    ];
                }
                if (!empty($product['sizes']) && is_array($product['sizes'])): ?>
                <style>
                .menudetail-size { margin:12px 0 18px; }
                .menudetail-size .label { font-weight:600; margin-bottom:8px; display:block; }
                .size-pills { display:flex; gap:10px; }
                .size-pill { width:36px; height:36px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; border:1px solid #ddd; cursor:pointer; background:#fff; }
                .size-pill.active { background:#F83D8E; border-color:#F83D8E; color:#fff; }
                .size-pill input { display:none; }
                </style>
                <div class="menudetail-size">
                    <div class="label">Size:</div>
                    <div class="size-pills" id="size-pills">
                        <?php foreach ($product['sizes'] as $i => $s): ?>
                            <?php $initial = strtoupper(substr($s['label'] ?? '', 0, 1)); ?>
                            <label class="size-pill <?= $i===0 ? 'active' : '' ?>" data-price="<?= htmlspecialchars($s['price']); ?>">
                                <input type="radio" name="size_id_radio" class="size-radio" value="<?= htmlspecialchars($s['ps_id']); ?>" <?= $i===0 ? 'checked' : '' ?> />
                                <span class="pill-text"><?= htmlspecialchars($initial ?: $s['label']); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="menudetail-buy">
                    <form id="add-to-cart-form" method="post" action="<?= base_url('index.php/order/add'); ?>">
                        <input type="hidden" name="mode" value="set" />
                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id'] ?? '') ?>" />
                        <input type="hidden" name="size_id" id="size-id-input" value="" />
                        <div class="qty-group">
                            <button type="button" class="qty-btn" id="qty-dec">-</button>
                            <input type="text" name="qty" id="qty-input" value="1" class="qty-input" />
                            <button type="button" class="qty-btn" id="qty-inc">+</button>
                        </div>
                        <button type="submit" class="btn primary add-to-cart">Add to Cart</button>
                    </form>
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
