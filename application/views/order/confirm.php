<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="checkout-success">
    <section class="checkout-hero">
        <div class="container checkout-hero-inner">
            <h1 class="checkout-hero-title">Confirm COD Order</h1>
            <div class="checkout-breadcrumb">
                <span class="breadcrumb-pill">Home&nbsp;&nbsp;/&nbsp;&nbsp;Order&nbsp;&nbsp;/&nbsp;&nbsp;Confirm</span>
            </div>
        </div>
    </section>

    <section class="checkout-main">
        <div class="container checkout-main-inner">
            <div class="checkout-left">
                <div class="billing-box">
                    <h3>Please confirm your Cash-on-Delivery order</h3>
                    <p>No payment proof is required for COD. Review the details below and click <strong>Confirm Order</strong> to finalize.</p>

                    <?php if ($this->session->flashdata('order_error')): ?>
                        <div class="admin-alert error"><?= htmlspecialchars($this->session->flashdata('order_error')); ?></div>
                    <?php endif; ?>

                    <?php if (empty($order)): ?>
                        <p>Order not found.</p>
                    <?php else: ?>
                        <div class="billing-details">
                            <p><strong><?= htmlspecialchars($order['customer_name']); ?></strong></p>
                            <p><?= htmlspecialchars($order['customer_phone']); ?></p>
                            <p><?= nl2br(htmlspecialchars($order['customer_address'])); ?></p>
                        </div>

                        <div class="order-items">
                            <table class="table">
                                <thead>
                                    <tr><th>Item</th><th class="text-center">Qty</th><th class="text-right">Price</th></tr>
                                </thead>
                                <tbody>
                                <?php foreach ($order['cart'] as $it): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($it['name']); ?></td>
                                        <td class="text-center"><?= (int)($it['qty'] ?? 0); ?></td>
                                        <td class="text-right"><?= format_rp($it['total'] ?? ((float)$it['price'] * (int)$it['qty'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div style="margin-top:12px">
                            <form method="post" action="<?= base_url('index.php/order/confirm_post') ?>">
                                <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']) ?>" />
                                <button class="btn primary">Confirm Order</button>
                                <a class="btn btn-link" href="<?= base_url('index.php/menu') ?>">Cancel</a>
                            </form>
                        </div>
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
