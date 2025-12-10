<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="admin-page">
    <div class="admin-card home-edit">
        <h2>Edit Home Page</h2>
        <?php if ($this->session->flashdata('admin_msg')): ?>
            <div class="admin-alert success"><?= htmlspecialchars($this->session->flashdata('admin_msg')); ?></div>
        <?php endif; ?>

        <?php $h = isset($home) && is_array($home) ? $home : []; ?>

        <form method="post" enctype="multipart/form-data">
            <h3>Hero</h3>
            <div class="mb-2">
                <label class="form-label">Hero Title</label>
                <input type="text" name="hero_title" value="<?= htmlspecialchars($h['hero_title'] ?? '') ?>" class="form-control" />
            </div>
            <div class="mb-2">
                <label class="form-label">Hero Subtitle</label>
                <input type="text" name="hero_subtitle" value="<?= htmlspecialchars($h['hero_subtitle'] ?? '') ?>" class="form-control" />
            </div>
            <div class="mb-2">
                <label class="form-label">Intro / Lead</label>
                <textarea name="intro" class="form-control" rows="3"><?= htmlspecialchars($h['intro'] ?? '') ?></textarea>
            </div>
            <div class="mb-2">
                <label class="form-label">Hero Image</label>
                <div class="mb-2">
                    <?php if (!empty($h['hero_image'])): ?><img id="preview-hero" src="<?= base_url($h['hero_image']) ?>" style="max-width:240px" /><?php else: ?><img id="preview-hero" src="<?= base_url('assets/images/placeholder.svg') ?>" style="max-width:240px" /><?php endif; ?>
                </div>
                <div class="input-group">
                    <input type="file" name="hero_image" id="hero_image" accept="image/*" class="form-control" />
                    <button type="button" class="btn btn-outline-secondary" id="btn-replace-hero">Pilih & Ganti</button>
                </div>
            </div>

            <hr />
            <h3>Featured Items (Home cards)</h3>
            <?php $featured = isset($h['featured_items']) && is_array($h['featured_items']) ? $h['featured_items'] : []; ?>
            <?php for ($i=0;$i<3;$i++):
                $it = isset($featured[$i]) ? $featured[$i] : ['title'=>'','desc'=>'','price'=>'','rating'=>'','image'=>''];
            ?>
            <div class="card mb-2 p-2">
                <h4>Item <?= $i+1 ?></h4>
                <div class="row">
                    <div class="col-md-3 text-center">
                        <?php if (!empty($it['image'])): ?><img id="preview-featured-<?= $i ?>" src="<?= base_url($it['image']) ?>" style="max-width:140px" /><?php else: ?><img id="preview-featured-<?= $i ?>" src="<?= base_url('assets/images/placeholder.svg') ?>" style="max-width:140px" /><?php endif; ?>
                        <div class="mb-2 input-group">
                            <input type="file" id="featured_image_<?= $i ?>" name="featured_image_<?= $i ?>" accept="image/*" class="form-control form-control-sm" />
                            <button type="button" class="btn btn-outline-secondary btn-replace-featured" data-target="featured_image_<?= $i ?>">Pilih & Ganti</button>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="mb-2"><label class="form-label">Title</label><input type="text" name="featured_title[<?= $i ?>]" value="<?= htmlspecialchars($it['title']) ?>" class="form-control" /></div>
                        <div class="mb-2"><label class="form-label">Description</label><textarea name="featured_desc[<?= $i ?>]" class="form-control" rows="2"><?= htmlspecialchars($it['desc']) ?></textarea></div>
                        <div class="mb-2"><label class="form-label">Price</label><input type="text" name="featured_price[<?= $i ?>]" value="<?= htmlspecialchars($it['price']) ?>" class="form-control" /></div>
                        <div class="mb-2"><label class="form-label">Rating</label><input type="text" name="featured_rating[<?= $i ?>]" value="<?= htmlspecialchars($it['rating']) ?>" class="form-control" /></div>
                    </div>
                </div>
            </div>
            <?php endfor; ?>

            <hr />
            <h3>Categories</h3>
            <?php $cats = isset($h['categories']) && is_array($h['categories']) ? $h['categories'] : []; ?>
            <?php for ($i=0;$i<3;$i++): $c = isset($cats[$i]) ? $cats[$i] : ['name'=>'','image'=>'']; ?>
            <div class="row mb-2 align-items-center">
                <div class="col-md-2 text-center">
                    <?php if (!empty($c['image'])): ?><img id="preview-cat-<?= $i ?>" src="<?= base_url($c['image']) ?>" style="max-width:100px" /><?php else: ?><img id="preview-cat-<?= $i ?>" src="<?= base_url('assets/images/placeholder.svg') ?>" style="max-width:100px" /><?php endif; ?>
                    <div class="mb-2 input-group">
                        <input type="file" id="category_image_<?= $i ?>" name="category_image_<?= $i ?>" accept="image/*" class="form-control form-control-sm" />
                        <button type="button" class="btn btn-outline-secondary btn-replace-cat" data-target="category_image_<?= $i ?>">Pilih & Ganti</button>
                    </div>
                </div>
                <div class="col-md-10">
                    <input type="text" name="category_name[<?= $i ?>]" value="<?= htmlspecialchars($c['name']) ?>" class="form-control" placeholder="Category name" />
                </div>
            </div>
            <?php endfor; ?>

            <!-- Best Sellers section removed as requested -->

            <!-- Special Section removed as requested -->
                <hr />
                <h3>Relive Section</h3>
                <div class="mb-2">
                    <label class="form-label">Relive Image</label>
                    <div class="mb-2">
                        <?php $rel = isset($h['relive']) ? $h['relive'] : []; ?>
                        <?php $relimg = isset($rel['image']) && $rel['image'] ? $rel['image'] : 'assets/images/placeholder.svg'; ?>
                        <img id="preview-relive" src="<?php echo base_url($relimg); ?>" alt="relive-image" />
                    </div>
                    <div class="mb-2 input-group">
                        <input type="file" id="relive_image" name="relive_image" accept="image/*" class="form-control" />
                        <button type="button" class="btn btn-outline-secondary" id="btn-replace-relive">Pilih & Ganti</button>
                    </div>
                </div>

            <!-- Testimonials and QRIS sections removed as requested -->

            <div class="form-actions mt-4">
                <button class="btn btn-primary" type="submit">Save Home</button>
                <a class="btn btn-secondary" href="<?= base_url('index.php/admin'); ?>">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    // hero
    var btnHero = document.getElementById('btn-replace-hero');
    var inpHero = document.getElementById('hero_image');
    if (btnHero && inpHero){ btnHero.addEventListener('click', function(){ inpHero.click(); }); inpHero.addEventListener('change', function(e){ var f = e.target.files && e.target.files[0]; if (!f) return; var url = URL.createObjectURL(f); var img = document.getElementById('preview-hero'); if (img) img.src = url; }); }

    // generic replace buttons
    function bindReplace(selectorPrefix, previewPrefix){
        document.querySelectorAll(selectorPrefix).forEach(function(b){
            var target = b.getAttribute('data-target');
            var inp = document.getElementById(target);
            if (!inp) return;
            b.addEventListener('click', function(){ inp.click(); });
            inp.addEventListener('change', function(e){ var f = e.target.files && e.target.files[0]; if (!f) return; var url = URL.createObjectURL(f); var id = previewPrefix + target.split('_').pop(); var img = document.getElementById(id); if (img) img.src = url; });
        });
    }
    bindReplace('.btn-replace-featured', 'preview-featured-');
    bindReplace('.btn-replace-cat', 'preview-cat-');
    var btnRel = document.getElementById('btn-replace-relive'); var inpRel = document.getElementById('relive_image'); if (btnRel && inpRel){ btnRel.addEventListener('click', function(){ inpRel.click(); }); inpRel.addEventListener('change', function(e){ var f = e.target.files && e.target.files[0]; if (!f) return; var url = URL.createObjectURL(f); var img = document.getElementById('preview-relive'); if (img) img.src = url; }); }
});
</script>
