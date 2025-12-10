<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="order-detail-page container py-4">
    <div class="mb-3">
        <a href="<?= base_url('index.php/order/cart'); ?>" class="btn btn-sm btn-light"><i class="bi bi-arrow-left"></i> Back to Cart / Orders</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <?php
                $st = strtolower($order['status'] ?? '');
                $badgeClass = 'badge bg-secondary';
                if ($st === 'pending_payment') $badgeClass = 'badge bg-danger';
                if ($st === 'paid_pending_confirmation') $badgeClass = 'badge bg-warning text-dark';
                if ($st === 'pending_confirmation') $badgeClass = 'badge bg-info text-dark';
                if ($st === 'confirmed') $badgeClass = 'badge bg-success';
                if ($st === 'cancelled') $badgeClass = 'badge bg-light text-dark';
            ?>
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h4 class="card-title mb-1">Order #<?= htmlspecialchars($order['id'] ?? '-'); ?></h4>
                    <div class="text-muted">Placed: <?= htmlspecialchars($order['created_at'] ?? '-'); ?></div>
                </div>
                <div class="text-end">
                    <div class="fw-semibold">Total: <?= isset($order['summary']['total']) ? 'Rp ' . number_format($order['summary']['total'],0,',','.') : '-'; ?></div>
                    <div class="mt-2"><span class="<?= $badgeClass ?> py-1 px-2"><?= htmlspecialchars($order['status'] ?? '-'); ?></span></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-7">
                    <h6>Customer</h6>
                    <div><strong><?= htmlspecialchars($order['customer_name'] ?? '-'); ?></strong></div>
                    <div class="text-muted">Phone: <?= htmlspecialchars($order['customer_phone'] ?? '-'); ?></div>
                    <div class="mt-2">Address: <div class="text-muted" style="white-space:pre-wrap"><?= htmlspecialchars($order['customer_address'] ?? '-'); ?></div></div>

                    <hr />

                    <h6>Items</h6>
                    <div class="list-group mb-3">
                        <?php if (!empty($order['cart']) && is_array($order['cart'])): foreach ($order['cart'] as $it): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold"><?= htmlspecialchars($it['name'] ?? '-') ?></div>
                                    <div class="text-muted small">
                                        <?php if (!empty($it['size_label'])): ?>Size: <?= htmlspecialchars($it['size_label']); ?><?php elseif(!empty($it['size'])): ?>Size: <?= htmlspecialchars($it['size']); ?><?php endif; ?>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div><?= (int)($it['qty'] ?? 0); ?> × <?= format_rp($it['price'] ?? 0); ?></div>
                                    <div class="fw-semibold"><?= format_rp($it['total'] ?? ((float)($it['price'] ?? 0) * (int)($it['qty'] ?? 0))); ?></div>
                                </div>
                            </div>
                        <?php endforeach; else: ?>
                            <div class="list-group-item">No items found</div>
                        <?php endif; ?>
                    </div>

                </div>
                <div class="col-md-5">
                    <h6>Payment</h6>
                    <div>Method: <strong><?= htmlspecialchars(strtoupper($order['payment_method'] ?? '-')); ?></strong></div>
                    <div class="mt-3">
                        <?php if (!empty($order['proof_image'])): ?>
                            <div class="mb-2">Uploaded proof:</div>
                            <a href="<?= base_url(ltrim($order['proof_image'], '/')); ?>" target="_blank" rel="noopener noreferrer">
                                <img src="<?= base_url(ltrim($order['proof_image'], '/')); ?>" alt="proof" class="img-fluid rounded" />
                            </a>
                            <div class="mt-2"><a href="<?= base_url(ltrim($order['proof_image'], '/')); ?>" target="_blank" class="small">Open full image</a></div>
                        <?php else: ?>
                            <div class="text-muted">No proof uploaded.</div>
                        <?php endif; ?>
                    </div>

                    <hr />
                    <div class="mb-2">Order Summary</div>
                    <div class="d-flex justify-content-between"><div>Subtotal</div><div><?= format_rp($order['summary']['subtotal'] ?? 0); ?></div></div>
                    <div class="d-flex justify-content-between"><div>Shipping</div><div><?= format_rp($order['summary']['shipping'] ?? 0); ?></div></div>
                    <div class="d-flex justify-content-between fw-bold mt-2"><div>Grand Total</div><div><?= format_rp($order['summary']['total'] ?? 0); ?></div></div>

                    <div class="mt-4 text-center">
                        <?php if (!empty($order['status']) && $order['status'] !== 'cancelled'): ?>
                            <form method="post" action="<?= base_url('index.php/order/cancel_post'); ?>" onsubmit="return confirm('Batalkan pesanan ini?');">
                                <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']); ?>" />
                                <button type="submit" class="btn btn-danger">Batalkan Pesanan</button>
                            </form>
                        <?php else: ?>
                            <div class="text-muted">Order cannot be modified.</div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
