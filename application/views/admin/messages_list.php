<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="admin-card">
    <h2>Contact Messages</h2>
    <p class="hint">Recent messages submitted via Contact Us form.</p>
    <div class="admin-table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email / Phone</th>
                    <th>Message</th>
                    <th>Received</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($messages)): ?>
                    <tr><td colspan="6">No messages yet.</td></tr>
                <?php else: foreach ($messages as $m): ?>
                    <tr class="<?= $m['status']==0 ? 'unread' : '' ?>">
                        <td><?= htmlspecialchars($m['id']) ?></td>
                        <td><?= htmlspecialchars($m['name']) ?></td>
                        <td><?= htmlspecialchars($m['email']) ?><br/><?= htmlspecialchars($m['phone']) ?></td>
                        <td style="max-width:420px;white-space:pre-wrap;overflow:hidden;"><?= nl2br(htmlspecialchars($m['message'])) ?></td>
                        <td><?= htmlspecialchars($m['created_at']) ?></td>
                        <td>
                            <?php if ($m['status']==0): ?><a class="action-edit" href="<?= base_url('index.php/admin/mark_message_read/'.$m['id']); ?>">Mark read</a><?php endif; ?>
                            <a class="action-delete" href="<?= base_url('index.php/admin/delete_message/'.$m['id']); ?>" onclick="return confirm('Delete this message?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
