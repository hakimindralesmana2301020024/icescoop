<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="qris-page">
    <section class="checkout-hero">
        <div class="container checkout-hero-inner">
            <h1 class="checkout-hero-title">QRIS Payment</h1>
            <div class="checkout-breadcrumb">
                <span class="breadcrumb-pill">Home&nbsp;&nbsp;/&nbsp;&nbsp;Order&nbsp;&nbsp;/&nbsp;&nbsp;QRIS</span>
            </div>
        </div>
    </section>

    <section class="checkout-main">
        <div class="container checkout-main-inner">
            <div class="checkout-left">
                <div class="billing-box">
                    <h3>Please scan this QR to pay</h3>
                    <div style="margin:18px 0">
                        <?php if (!empty($qris_image)): ?>
                            <img src="<?= base_url($qris_image); ?>" alt="QRIS" style="max-width:320px;display:block" onerror="this.style.display='none'" />
                        <?php else: ?>
                            <img src="<?= base_url('assets/images/qris.png'); ?>" alt="QRIS" style="max-width:320px;display:block" onerror="this.style.display='none'" />
                        <?php endif; ?>
                    </div>
                    <p>After you complete payment, please upload proof (screenshot or photo of your payment receipt).</p>
                    <?php if ($this->session->flashdata('order_error')): ?>
                        <div class="admin-alert error"><?= htmlspecialchars($this->session->flashdata('order_error')); ?></div>
                    <?php endif; ?>
                    <?php if (empty($order)): ?>
                        <p>Order not found.</p>
                    <?php else: ?>
                        <form method="post" action="<?= base_url('index.php/order/upload_proof'); ?>" enctype="multipart/form-data">
                            <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']); ?>" />
                            <div class="field"><label>Upload payment proof</label><input type="file" name="proof" accept="image/*" required /></div>
                            <div style="margin-top:12px"><button type="submit" class="btn primary">Upload Proof & Confirm</button></div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <aside class="checkout-right">
                <div class="summary-card">
                    <div class="summary-head">Items <span class="summary-price">Price</span></div>
                    <div class="summary-list">
                        <?php if(!empty($order) && isset($order['cart']) && is_array($order['cart'])): foreach($order['cart'] as $c): ?>
                        <div class="summary-item">
                            <div class="s-left"><span class="qty"><?php echo (int)($c['qty'] ?? 0); ?>x</span> <span class="s-name"><?php echo htmlspecialchars($c['name'] ?? ''); ?></span></div>
                            <div class="s-right"><?= format_rp($c['total'] ?? 0); ?></div>
                        </div>
                        <?php endforeach; endif; ?>
                    </div>
                    <div class="summary-total"><span>Grand Total</span><span class="accent"><?= format_rp($order['summary']['total'] ?? 0); ?></span></div>
                </div>
            </aside>
        </div>
    </section>
</div>
