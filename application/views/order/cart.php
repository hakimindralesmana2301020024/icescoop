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
                <div class="billing-box">
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
                                            // prefer 'size_label' (new) falling back to legacy 'size'
                                            if (!empty($item['size_label'])) $meta[] = 'Size: ' . htmlspecialchars($item['size_label']);
                                            elseif (!empty($item['size'])) $meta[] = 'Size: ' . htmlspecialchars($item['size']);
                                            echo !empty($meta) ? implode(' | ', $meta) : '';
                                        ?>
                                </div>
                            </div>
                        </div>
                        <div class="cart-col price accent"><?= format_rp($item['price'] ?? 0); ?></div>
                        <div class="cart-col qty">
                                <form method="post" action="<?= base_url('index.php/order/update'); ?>" class="cart-update-form">
                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($item['id']); ?>" />
                                <input type="hidden" name="product_size_id" value="<?= htmlspecialchars($item['product_size_id'] ?? $item['size_id'] ?? $item['size_label'] ?? ''); ?>" />
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
                                <input type="hidden" name="product_size_id" value="<?= htmlspecialchars($item['product_size_id'] ?? $item['size_id'] ?? $item['size_label'] ?? ''); ?>" />
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
                <div class="summary-card">
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

    <?php
        // Render placed orders as a separate full-width section below the cart
        $orders = $this->session->userdata('orders') ?: [];
    ?>
    <?php if (!empty($orders)): ?>
    <section class="placed-orders-section" style="padding:30px 0;background:#fafafa">
        <div class="container">
            <div class="cart-table-head" style="margin-bottom:12px">
                <div class="cart-table-title">Placed Orders</div>
                <div class="cart-table-count">(<?php echo count($orders); ?> orders)</div>
            </div>

            <?php foreach (array_reverse($orders) as $ord): ?>
            <div class="summary-card" style="margin:12px 0;padding:16px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                    <div style="font-weight:600">Order ID: <?= htmlspecialchars($ord['id']); ?></div>
                    <div style="font-size:13px;color:#666"><?= htmlspecialchars(strtoupper($ord['payment_method'] ?? '')); ?> • <?= htmlspecialchars($ord['status'] ?? ''); ?> • <?= htmlspecialchars($ord['created_at'] ?? ''); ?></div>
                </div>

                <div class="cart-table">
                    <div class="cart-header">
                        <div class="cart-col product">Product Details</div>
                        <div class="cart-col price">Price</div>
                        <div class="cart-col qty">Quantity</div>
                        <div class="cart-col total">Total</div>
                    </div>
                    <?php if (!empty($ord['cart']) && is_array($ord['cart'])): foreach($ord['cart'] as $item): ?>
                    <?php $it_total = isset($item['total']) ? (float)$item['total'] : ((float)($item['price'] ?? 0) * (int)($item['qty'] ?? 0)); ?>
                    <div class="cart-row">
                        <div class="cart-col product">
                            <div class="cart-prod-info">
                                <div class="cart-prod-name"><?php echo htmlspecialchars($item['name'] ?? ''); ?></div>
                                <div class="cart-prod-meta" style="font-size:13px;color:#666;margin-top:6px">
                                    <?php if (!empty($item['size_label'])): ?>Size: <?= htmlspecialchars($item['size_label']); ?><?php elseif (!empty($item['size'])): ?>Size: <?= htmlspecialchars($item['size']); ?><?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="cart-col price accent"><?= format_rp($item['price'] ?? 0); ?></div>
                        <div class="cart-col qty"><?= (int)($item['qty'] ?? 0); ?></div>
                        <div class="cart-col total"><?= format_rp($it_total); ?></div>
                    </div>
                    <?php endforeach; endif; ?>
                </div>

                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:12px">
                    <div style="color:#666;font-size:13px">Customer: <?= htmlspecialchars($ord['customer_name'] ?? '-'); ?></div>
                    <div style="font-weight:700">Grand Total: <span class="accent"><?= format_rp($ord['summary']['total'] ?? 0); ?></span></div>
                </div>
                                <div style="margin-top:10px;text-align:right">
                                    <a href="<?= base_url('index.php/order/detail/' . urlencode($ord['id'])); ?>" class="btn btn-secondary" style="margin-right:8px;background:#6c757d;color:#fff;border:0;padding:8px 12px;border-radius:8px">Detail</a>
                                    <form method="post" action="<?= base_url('index.php/order/cancel_post'); ?>" style="display:inline-block" onsubmit="return confirm('Batalkan pesanan ini?');">
                                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($ord['id']); ?>" />
                                        <button type="submit" class="btn btn-danger" style="background:#ff6b6b;color:#fff;border:0;padding:8px 12px;border-radius:8px">Batalkan Pesanan</button>
                                    </form>
                                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

