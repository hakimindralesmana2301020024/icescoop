<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="checkout-page">
    <section class="checkout-hero">
        <div class="container checkout-hero-inner">
            <h1 class="checkout-hero-title">Checkout</h1>
            <div class="checkout-breadcrumb">
                <span class="breadcrumb-pill">Home&nbsp;&nbsp;/&nbsp;&nbsp;Cart&nbsp;&nbsp;/&nbsp;&nbsp;Checkout</span>
            </div>
        </div>
    </section>

    <section class="checkout-main">
        <div class="container checkout-main-inner">
            <div class="checkout-left">
                <div class="billing-box">
                    <h3 class="billing-title">Payment</h3>
                    <form class="payment-form" method="post" action="<?= base_url('index.php/order/place'); ?>">
                        <div class="field"><label>Full name</label><input type="text" name="customer_name" class="input" placeholder="Full name" required /></div>
                        <div class="field"><label>WhatsApp number</label><input type="text" name="customer_phone" class="input" placeholder="08xx.... (WhatsApp)" required /></div>
                        <div class="field"><label>Address</label><textarea name="customer_address" class="input" placeholder="Delivery address" rows="3" required></textarea></div>

                        <label class="radio-row"><input type="radio" name="pay" value="qris" checked> QRIS</label>
                        <div id="qris-preview" style="margin:12px 0">
                            <img src="<?= base_url('assets/images/qris.png'); ?>" alt="QRIS" style="max-width:220px;display:block" onerror="this.style.display='none'" />
                        </div>

                        <label class="radio-row"><input type="radio" name="pay" value="cod"> Cash on Delivery (COD)</label>

                        <p class="small-note">By clicking the button, you agree to the Terms and Conditions</p>
                        <button type="submit" class="btn order-now">Place Order Now</button>
                    </form>
                </div>
            </div>

            <aside class="checkout-right">
                <div class="summary-card">
                    <div class="summary-head">Items <span class="summary-price">Price</span></div>
                    <div class="summary-list">
                        <?php if(isset($cart) && is_array($cart)): foreach($cart as $c): ?>
                        <div class="summary-item">
                            <div class="s-left"><span class="qty"><?php echo (int)($c['qty'] ?? 0); ?>x</span> <span class="s-name"><?php echo htmlspecialchars($c['name'] ?? ''); ?></span>
                                <?php if (!empty($c['size_label']) || !empty($c['size'])): ?>
                                    <div class="s-meta" style="font-size:13px;color:#666">Size: <?= htmlspecialchars($c['size_label'] ?? $c['size']); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="s-right"><?= format_rp($c['total'] ?? 0); ?></div>
                        </div>
                        <?php endforeach; endif; ?>
                    </div>
                    <div class="summary-total"><span>Grand Total</span><span class="accent"><?= format_rp($summary['total'] ?? 0); ?></span></div>
                </div>
            </aside>
        </div>
    </section>
</div>
