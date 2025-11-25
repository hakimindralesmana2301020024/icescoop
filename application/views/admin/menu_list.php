<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="admin-page">
    <div class="admin-card">
        <h2>Manage Menu Products</h2>
        <?php if ($this->session->flashdata('admin_msg')): ?>
            <div class="admin-alert success"><?= htmlspecialchars($this->session->flashdata('admin_msg')); ?></div>
        <?php endif; ?>

        <div style="margin-bottom:12px">
            <a class="btn btn-primary" href="<?= base_url('index.php/admin/menu_edit'); ?>">Add Product</a>
        </div>

        <table class="admin-table" style="width:100%">
            <thead><tr><th>ID</th><th>Image</th><th>Name</th><th>Price</th><th>Featured</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach($products as $p): ?>
                <tr>
                    <td><?php echo $p['id']; ?></td>
                    <td style="width:80px"><img src="<?php echo $p['img_url']; ?>" style="max-width:64px;height:auto" /></td>
                    <td><?php echo htmlspecialchars($p['name']); ?></td>
                    <td>Rp <?php echo htmlspecialchars($p['price']); ?></td>
                    <td><?php echo $p['featured'] ? 'Yes' : 'No'; ?></td>
                    <td>
                        <a href="<?= base_url('index.php/admin/menu_edit/'.$p['id']); ?>">Edit</a> |
                        <a href="<?= base_url('index.php/admin/menu_delete/'.$p['id']); ?>" onclick="return confirm('Delete product?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
