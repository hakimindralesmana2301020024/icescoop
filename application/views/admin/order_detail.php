<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="admin-page order-detail-page">
    <div class="container py-4">
        <div class="d-flex align-items-start justify-content-between mb-3">
            <div>
                <h2 class="mb-1">Order #<?= htmlspecialchars($order['id']); ?></h2>
                <div class="text-muted small">Created: <?= htmlspecialchars($order['created_at'] ?? '-'); ?></div>
            </div>
            <div class="text-end">
                <?php $st = $order['status'] ?? ''; $badge = 'badge bg-light text-dark';
                    if ($st==='pending_payment') $badge='badge bg-danger text-white';
                    if ($st==='paid_pending_confirmation') $badge='badge bg-warning text-dark';
                    if ($st==='pending_confirmation') $badge='badge bg-info text-dark';
                    if ($st==='confirmed') $badge='badge bg-success text-white';
                    if ($st==='cancelled') $badge='badge bg-secondary text-white';
                ?>
                <span class="<?= $badge ?>" style="font-size:0.95rem; padding:0.45em 0.7em"><?= htmlspecialchars(str_replace('_',' ',ucwords($st, '_')) ?: '-'); ?></span>
            </div>
        </div>
        <?php if ($this->session->flashdata('admin_msg')): ?>
            <div class="alert alert-success"><?= htmlspecialchars($this->session->flashdata('admin_msg')); ?></div>
        <?php endif; ?>

        <div class="row gx-4">
            <div class="col-md-7">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="mb-2">Customer</h5>
                        <div class="customer-section">
                            <div class="fw-bold"><?= htmlspecialchars($order['customer_name'] ?? '-'); ?></div>
                            <div class="text-muted small customer-addr" title="<?= htmlspecialchars($order['customer_address'] ?? '-'); ?>"><?= nl2br(htmlspecialchars($order['customer_address'] ?? '-')); ?></div>
                            <div class="text-muted small mt-1">Phone: <?= htmlspecialchars($order['customer_phone'] ?? '-'); ?></div>
                        </div>

                        <h5 class="mb-2">Items</h5>
                        <?php if (!empty($order['cart']) && is_array($order['cart'])): ?>
                            <div class="list-group">
                                <?php foreach ($order['cart'] as $it): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-semibold"><?= htmlspecialchars($it['name'] ?? ''); ?></div>
                                            <div class="text-muted small">
                                                Qty: <?= (int)($it['qty'] ?? 0); ?>
                                                <?php if (!empty($it['size_label'])): ?>(<?= htmlspecialchars($it['size_label']); ?>)<?php elseif(!empty($it['size'])): ?>(<?= htmlspecialchars($it['size']); ?>)<?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold"><?= isset($it['total']) ? 'Rp ' . number_format($it['total'],0,',','.') : (isset($it['price']) ? 'Rp ' . number_format($it['price'],0,',','.') : '-'); ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">-</p>
                        <?php endif; ?>

                        <div class="summary-row mt-3">
                            <div class="d-flex justify-content-between">
                                <div class="text-muted">Subtotal</div>
                                <div class="fw-semibold"><?= isset($order['summary']['subtotal']) ? 'Rp ' . number_format($order['summary']['subtotal'],0,',','.') : '-'; ?></div>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <div class="text-muted">Total</div>
                                <div class="summary-grand"><?= isset($order['summary']['total']) ? 'Rp ' . number_format($order['summary']['total'],0,',','.') : '-'; ?></div>
                            </div>
                        </div>

                        <?php if (!empty($order['proof_image'])): ?>
                            <h5 class="mt-3">Bukti Pembayaran</h5>
                            <p>
                                <a href="<?= base_url(ltrim($order['proof_image'], '/')); ?>" target="_blank" rel="noopener" title="Lihat bukti pembayarannya">
                                    <img src="<?= base_url(ltrim($order['proof_image'], '/')); ?>" alt="proof" class="img-fluid" style="max-width:360px;border:1px solid #eee;padding:6px;background:#fff" />
                                </a>
                            </p>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

            <aside class="col-md-5">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5>Actions</h5>
                        <form method="post" action="<?= base_url('index.php/admin/order_update_post'); ?>">
                            <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']); ?>" />
                            <div class="mb-2">
                                <label>Status</label>
                                <select name="status" class="form-select">
                                    <?php $st = $order['status'] ?? ''; ?>
                                    <option value="pending_payment" <?= $st==='pending_payment' ? 'selected' : '' ?>>pending_payment</option>
                                    <option value="pending_confirmation" <?= $st==='pending_confirmation' ? 'selected' : '' ?>>pending_confirmation</option>
                                    <option value="confirmed" <?= $st==='confirmed' ? 'selected' : '' ?>>confirmed</option>
                                    <option value="paid_pending_confirmation" <?= $st==='paid_pending_confirmation' ? 'selected' : '' ?>>paid_pending_confirmation</option>
                                    <option value="cancelled" <?= $st==='cancelled' ? 'selected' : '' ?>>cancelled</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label>Admin Note</label>
                                <textarea name="admin_note" class="form-control" rows="4"><?= htmlspecialchars($order['admin_note'] ?? ''); ?></textarea>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary" type="submit">
                                    <svg viewBox="0 0 24 24" width="16" height="16" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path fill="currentColor" d="M5 5v14h14V7.5L16.5 5H5zM19 5v2h-2V5h2zM7 9h10v2H7V9z"/></svg>
                                    Save
                                </button>
                                <a href="<?= base_url('index.php/admin/orders'); ?>" class="btn btn-outline-secondary">Back to Orders</a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6>Metadata</h6>
                        <p class="small text-muted mb-1">ID: <?= htmlspecialchars($order['id']); ?></p>
                        <p class="small mb-1">Payment: <strong><?= htmlspecialchars($order['payment_method'] ?? '-'); ?></strong></p>
                        <p class="small mb-1">Status: <strong><?= htmlspecialchars($order['status'] ?? '-'); ?></strong></p>
                        <p class="small">Created: <?= htmlspecialchars($order['created_at'] ?? '-'); ?></p>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>
