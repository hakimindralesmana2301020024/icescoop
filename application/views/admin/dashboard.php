<div class="admin-dashboard">
    <div class="dashboard-grid">
        <div class="card stat">
            <div class="stat-title">Total Orders</div>
            <div class="stat-value"><?= isset($total_orders) ? number_format((int)$total_orders) : '-'; ?></div>
        </div>
        <div class="card stat">
            <div class="stat-title">Pending Orders</div>
            <div class="stat-value"><?= isset($pending_orders) ? htmlspecialchars($pending_orders) : '-'; ?></div>
        </div>
        <div class="card stat">
            <div class="stat-title">Active Users (30d)</div>
            <div class="stat-value"><?= isset($active_users) ? htmlspecialchars($active_users) : '-'; ?></div>
        </div>
        <div class="card stat">
            <div class="stat-title">Unread Messages</div>
            <div class="stat-value"><?= isset($unread_messages) ? (int)$unread_messages : 0; ?></div>
            <div style="margin-top:8px"><a href="<?= base_url('index.php/admin/messages'); ?>" class="btn btn-sm btn-msg">Lihat Pesan</a></div>
        </div>
    </div>
