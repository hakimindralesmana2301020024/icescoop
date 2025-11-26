<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="cart-page">
    <!-- Hero / Page title -->
    <section class="cart-hero">
        <div class="container cart-hero-inner">
            <h1 class="cart-hero-title">Shopping Cart</h1>
            <div class="cart-breadcrumb">
                <span class="breadcrumb-pill">Home&nbsp;&nbsp;/&nbsp;&nbsp;Cart</span>
            </div>
        </div>
    </section>

    <section class="cart-main">
        <div class="container cart-main-inner">
            <div class="cart-table-wrap">
                <div class="cart-table-head">
                    <div class="cart-table-title">Shopping Cart</div>
                    <div class="cart-table-count">(<?php echo count($cart); ?> items)</div>
                </div>
                <div class="cart-table">
                    <div class="cart-header">
                        <div class="cart-col product">Product Details</div>
                        <div class="cart-col price">Price</div>
                        <div class="cart-col qty">Quantity</div>
                        <div class="cart-col total">Total</div>
                        <div class="cart-col remove"></div>
                    </div>
                    <?php foreach($cart as $item): ?>
                    <div class="cart-row">
                        <div class="cart-col product">
                            <div class="cart-prod-img"><img src="<?php echo $item['img']; ?>" alt="<?php echo $item['name']; ?>" /></div>
                            <div class="cart-prod-info">
                                <div class="cart-prod-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                <div class="cart-prod-meta">
                                    <?php
                                        $meta = [];
                                        if (!empty($item['color'])) $meta[] = 'Color: ' . htmlspecialchars($item['color']);
                                        if (!empty($item['size'])) $meta[] = 'Size: ' . htmlspecialchars($item['size']);
                                        echo !empty($meta) ? implode(' | ', $meta) : '';
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="cart-col price accent"><?= format_rp($item['price'] ?? 0); ?></div>
                        <div class="cart-col qty">
                            <form method="post" action="<?= base_url('index.php/order/update'); ?>" class="cart-update-form">
                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($item['id']); ?>" />
                                <div class="qty-group">
                                    <button type="button" class="qty-btn" data-decrement>−</button>
                                    <input type="text" name="qty" value="<?php echo htmlspecialchars($item['qty']); ?>" class="qty-input update-qty" />
                                    <button type="button" class="qty-btn" data-increment>+</button>
                                </div>
                                <div style="display:none"><button type="submit" class="update-submit">Update</button></div>
                            </form>
                        </div>
                        <div class="cart-col total"><?= format_rp($item['total'] ?? 0); ?></div>
                        <div class="cart-col remove">
                            <form method="post" action="<?= base_url('index.php/order/remove'); ?>">
                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($item['id']); ?>" />
                                <button type="submit" class="remove-btn" title="Remove"><i class="bi bi-x"></i></button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                                <script>
                                document.addEventListener('DOMContentLoaded', function(){
                                    document.querySelectorAll('.cart-update-form').forEach(function(form){
                                        var dec = form.querySelector('[data-decrement]');
                                        var inc = form.querySelector('[data-increment]');
                                        var input = form.querySelector('.update-qty');
                                        function clamp(v){ v = parseInt(v) || 0; if(v<0) v = 0; return v; }
                                        if(dec) dec.addEventListener('click', function(){ input.value = clamp(parseInt(input.value)-1); form.querySelector('.update-submit').click(); });
                                        if(inc) inc.addEventListener('click', function(){ input.value = clamp(parseInt(input.value)+1); form.querySelector('.update-submit').click(); });
                                        // allow manual change and submit on blur
                                        input.addEventListener('blur', function(){ input.value = clamp(input.value); form.querySelector('.update-submit').click(); });
                                    });
                                });
                                </script>
                <div class="cart-actions">
                    <a href="<?= base_url('index.php/menu'); ?>" class="continue-shopping"><i class="bi bi-arrow-left"></i> Continue Shopping</a>
                </div>
            </div>
            <aside class="cart-summary">
                <div class="summary-box">
                    <div class="summary-title">Order Summary</div>
                    <div class="summary-details">
                        <div class="summary-row">
                            <span>Sub Total:</span>
                            <span><?= format_rp($summary['subtotal'] ?? 0); ?></span>
                        </div>
                    </div>
                    <div class="summary-grand">
                        <span>Grand Total:</span>
                        <span class="accent"><?= format_rp($summary['total'] ?? 0); ?></span>
                    </div>
                    <a href="<?= base_url('index.php/order/checkout'); ?>" class="btn primary summary-checkout">Proceed to checkout</a>
                    <div class="summary-note">Safe and Secure Payments. 100% Authentic Products</div>
                </div>
            </aside>
        </div>
    </section>
</div>
