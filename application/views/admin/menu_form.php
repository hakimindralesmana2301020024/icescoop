<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="admin-page">
    <div class="admin-card">
        <h2><?php echo !empty($product['id']) ? 'Edit Product' : 'Add Product'; ?></h2>
        <?php if ($this->session->flashdata('admin_msg')): ?>
            <div class="admin-alert success"><?= htmlspecialchars($this->session->flashdata('admin_msg')); ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= htmlspecialchars($product['id'] ?? '') ?>" />
            <div class="mb-2"><label class="form-label">Name</label><input type="text" name="name" value="<?= htmlspecialchars($product['name'] ?? '') ?>" class="form-control" required /></div>
            <div class="mb-2"><label class="form-label">Card (short) Description</label><textarea name="short_description" class="form-control" rows="2"><?= htmlspecialchars($product['short_description'] ?? $product['description'] ?? '') ?></textarea></div>
            <div class="mb-2"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description'] ?? '') ?></textarea></div>
            <div class="mb-2"><label class="form-label">Detail (long) Description</label><textarea name="long_description" class="form-control" rows="6"><?= htmlspecialchars($product['long_description'] ?? $product['description'] ?? '') ?></textarea></div>
            <div class="mb-2"><label class="form-label">Price (numeric)</label><input type="text" name="price" value="<?= htmlspecialchars($product['price'] ?? '') ?>" class="form-control" required /></div>
            <div class="mb-2"><label class="form-label">Rating (eg. 4.8)</label><input type="text" name="rating" value="<?= htmlspecialchars($product['rating'] ?? '') ?>" class="form-control" /></div>
            <div class="mb-2"><label class="form-label">Image</label>
                <div><?php if(!empty($product['image'])): ?><img src="<?= base_url($product['image']); ?>" style="max-width:160px" /><?php endif; ?></div>
                <input type="file" name="image" accept="image/*" class="form-control" />
            </div>
            <div class="mb-2"><label><input type="checkbox" name="featured" value="1" <?php if(!empty($product['featured'])) echo 'checked'; ?> /> Featured</label></div>
            <div class="mb-2"><label class="form-label">Categories</label>
                <div>
                    <?php foreach($categories as $c): ?>
                        <label style="display:inline-block;margin-right:12px"><input type="checkbox" name="categories[]" value="<?= $c['id'] ?>" <?php if(in_array($c['id'],$selected)) echo 'checked'; ?> /> <?= htmlspecialchars($c['name']) ?></label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="form-actions mt-4">
                <button class="btn btn-primary" type="submit">Save Product</button>
                <a class="btn btn-secondary" href="<?= base_url('index.php/admin/menu'); ?>">Cancel</a>
            </div>
        </form>
    </div>
</div>
