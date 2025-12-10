<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="checkout-success">
    <section class="checkout-hero">
        <div class="container checkout-hero-inner">
            <h1 class="checkout-hero-title">Thank you for your order</h1>
            <div class="checkout-breadcrumb">
                <span class="breadcrumb-pill">Home&nbsp;&nbsp;/&nbsp;&nbsp;Order&nbsp;&nbsp;/&nbsp;&nbsp;Success</span>
            </div>
        </div>
    </section>

    <section class="checkout-main">
        <div class="container checkout-main-inner">
            <div class="checkout-left">
                <div class="billing-box">
                    <h3 class="billing-title">Order Placed</h3>
                    <?php if (empty($order)): ?>
                        <p>No recent order found.</p>
                    <?php else: ?>
                        <p>Order ID: <strong><?= htmlspecialchars($order['id']); ?></strong></p>
                        <p>Name: <strong><?= htmlspecialchars($order['customer_name']); ?></strong></p>
                        <p>Phone: <strong><?= htmlspecialchars($order['customer_phone']); ?></strong></p>
                        <p>Address: <strong><?= nl2br(htmlspecialchars($order['customer_address'])); ?></strong></p>
                        <p>Payment method: <strong><?= htmlspecialchars(strtoupper($order['payment_method'])); ?></strong></p>
                        <p>Status: <strong><?= htmlspecialchars($order['status'] ?? ''); ?></strong></p>
                        <?php if (!empty($order['proof_image'])): ?>
                            <p>Proof uploaded:</p>
                            <div style="margin:8px 0"><img src="<?= base_url($order['proof_image']); ?>" style="max-width:320px" alt="proof" /></div>
                        <?php endif; ?>
                        <p>Placed at: <strong><?= htmlspecialchars($order['created_at']); ?></strong></p>
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
