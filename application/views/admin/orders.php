<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="admin-page">
    <!-- Orders styles consolidated into main admin CSS -->
    <div class="container p-4">
        <h2 class="mb-3">Orders</h2>
        <?php if ($this->session->flashdata('admin_msg')): ?>
            <div class="alert alert-success mb-3"><?= htmlspecialchars($this->session->flashdata('admin_msg')); ?></div>
        <?php endif; ?>

        <div class="card mb-3">
            <div class="card-body">
                <form class="row g-2" method="get" action="<?= base_url('index.php/admin/orders'); ?>">
                    <div class="col-auto">
                        <?php $fs = $filterStatus ?? ''; ?>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All statuses</option>
                            <?php $statuses = ['pending_payment','paid_pending_confirmation','pending_confirmation','confirmed','cancelled']; ?>
                            <?php foreach($statuses as $st): ?>
                                <option value="<?= $st ?>" <?= $fs===$st ? 'selected' : '' ?>><?= $st ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm btn-primary" type="submit">Filter</button>
                        <a href="<?= base_url('index.php/admin/orders'); ?>" class="btn btn-sm btn-light">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <?php if (empty($orders)): ?>
            <div class="alert alert-info">No orders found.</div>
        <?php else: ?>
            <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:90px">ID</th>
                        <th>Customer</th>
                        <th style="width:120px">Items</th>
                        <th style="width:120px">Proof</th>
                        <th style="width:120px">Total</th>
                        <th style="width:120px">Payment</th>
                        <th style="width:140px">Status</th>
                        <th style="width:160px">Created</th>
                        <th style="width:180px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $o): ?>
                    <?php
                        // status badge mapping
                        $st = $o['status'] ?? '';
                        $badgeClass = 'badge bg-secondary';
                        if ($st === 'pending_payment') $badgeClass = 'badge bg-danger';
                        if ($st === 'paid_pending_confirmation') $badgeClass = 'badge bg-warning text-dark';
                        if ($st === 'pending_confirmation') $badgeClass = 'badge bg-info text-dark';
                        if ($st === 'confirmed') $badgeClass = 'badge bg-success';
                        if ($st === 'cancelled') $badgeClass = 'badge bg-light text-dark';
                    ?>
                    <tr class="order-row">
                        <td class="order-id"><a href="<?= base_url('index.php/admin/order_detail/' . urlencode($o['id'])); ?>" class="fw-bold">#<?= htmlspecialchars($o['id'] ?? ''); ?></a></td>
                        <td class="customer-col">
                            <?php $initial = strtoupper(substr(trim($o['customer_name'] ?? '-'),0,1)); ?>
                            <div class="customer-block">
                                <div class="avatar"><?= htmlspecialchars($initial); ?></div>
                                <div class="customer-info">
                                    <div class="customer-name"><?= htmlspecialchars($o['customer_name'] ?? '-'); ?></div>
                                    <div class="customer-addr" title="<?= htmlspecialchars($o['customer_address'] ?? '-'); ?>"><?= htmlspecialchars(substr($o['customer_address'] ?? '-',0,80)); ?></div>
                                    <div class="customer-phone text-muted small mt-1"><?= htmlspecialchars($o['customer_phone'] ?? '-'); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="items-col">
                            <?php if (!empty($o['cart']) && is_array($o['cart'])): ?>
                                <div class="small">
                                    <?php $first = $o['cart'][0] ?? null; if ($first): ?>
                                        <?= htmlspecialchars($first['name'] ?? ''); ?>
                                        <?php if(!empty($first['size_label'])): ?><small>(<?= htmlspecialchars($first['size_label']); ?>)</small><?php endif; ?>
                                    <?php endif; ?>
                                    <div class="items-summary text-muted">(<?= count($o['cart']); ?> item<?php if(count($o['cart'])>1) echo 's'; ?>)</div>
                                </div>
                            <?php else: ?>-
                            <?php endif; ?>
                        </td>
                        <td class="proof-col">
                            <?php if (!empty($o['proof_image'])): ?>
                                <img class="proof-thumb" src="<?= base_url(ltrim($o['proof_image'],'/')); ?>" alt="proof" />
                            <?php else: ?>
                                <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="total-col text-end"><div class="total-amount"><?= isset($o['summary']['total']) ? 'Rp ' . number_format($o['summary']['total'],0,',','.') : '-'; ?></div></td>
                        <td class="status-col"><span class="<?= $badgeClass ?> status-label"><?= htmlspecialchars(str_replace('_',' ',ucwords($st, '_')) ?: '-'); ?></span></td>
                        <td class="payment-col"><?= htmlspecialchars($o['payment_method'] ?? '-'); ?></td>
                        <td class="created-col"><?= htmlspecialchars($o['created_at'] ?? '-'); ?></td>
                        <td class="actions-col">
                            <div class="btn-group" role="group">
                                <a class="btn btn-sm btn-outline-primary" href="<?= base_url('index.php/admin/order_detail/' . urlencode($o['id'])); ?>">
                                    <svg viewBox="0 0 24 24" width="14" height="14" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 5c-7 0-11 7-11 7s4 7 11 7 11-7 11-7-4-7-11-7zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10zM12 9.5a2.5 2.5 0 1 0 .001 5.001A2.5 2.5 0 0 0 12 9.5z"/></svg>
                                    View
                                </a>
                                <form method="post" action="<?= base_url('index.php/admin/order_confirm_post'); ?>" style="display:inline-block">
                                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($o['id']); ?>" />
                                    <button class="btn btn-sm btn-success" type="submit">
                                        <svg viewBox="0 0 24 24" width="14" height="14" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path fill="currentColor" d="M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z"/></svg>
                                        Confirm
                                    </button>
                                </form>
                                <form method="post" action="<?= base_url('index.php/admin/order_cancel_post'); ?>" style="display:inline-block" onsubmit="return confirm('Cancel this order?');">
                                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($o['id']); ?>" />
                                    <button class="btn btn-sm btn-danger" type="submit">
                                        <svg viewBox="0 0 24 24" width="14" height="14" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path fill="currentColor" d="M18.3 5.71 12 12l6.3 6.29-1.41 1.42L10.59 13.4 4.29 19.71 2.88 18.3 9.18 12 2.88 5.71 4.29 4.29 10.59 10.6 16.88 4.29z"/></svg>
                                        Cancel
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
    </div>
</div>
