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
                    <div class="cart-table-count">(04 items)</div>
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
                                <div class="cart-prod-name"><?php echo $item['name']; ?></div>
                                <div class="cart-prod-meta">Color: <?php echo $item['color']; ?> | Size: <?php echo $item['size']; ?></div>
                            </div>
                        </div>
                        <div class="cart-col price accent">$<?php echo $item['price']; ?></div>
                        <div class="cart-col qty">
                            <div class="qty-group">
                                <button class="qty-btn">-</button>
                                <input type="text" value="<?php echo $item['qty']; ?>" class="qty-input" />
                                <button class="qty-btn">+</button>
                            </div>
                        </div>
                        <div class="cart-col total">$<?php echo $item['total']; ?></div>
                        <div class="cart-col remove"><button class="remove-btn"><i class="bi bi-x"></i></button></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="cart-actions">
                    <a href="#" class="continue-shopping"><i class="bi bi-arrow-left"></i> Continue Shopping</a>
                </div>
            </div>
            <aside class="cart-summary">
                <div class="summary-box">
                    <div class="summary-title">Order Summary</div>
                    <form class="summary-coupon">
                        <input type="text" placeholder="Apply Coupons" class="coupon-input" />
                        <button class="coupon-btn">Apply</button>
                    </form>
                    <div class="summary-details">
                        <div class="summary-row">
                            <span>Sub Total:</span>
                            <span>$<?php echo $summary['subtotal']; ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping:</span>
                            <span>$<?php echo $summary['shipping']; ?></span>
                        </div>
                    </div>
                    <div class="summary-grand">
                        <span>Grand Total:</span>
                        <span class="accent">$<?php echo $summary['total']; ?></span>
                    </div>
                    <button class="btn primary summary-checkout">Proceed to checkout</button>
                    <div class="summary-note">Safe and Secure Payments. 100% Authentic Products</div>
                </div>
            </aside>
        </div>
    </section>
</div>
