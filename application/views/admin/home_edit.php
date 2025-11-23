<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="admin-page">
    <div class="admin-card">
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
            <?php for ($i=0;$i<6;$i++):
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
            <?php for ($i=0;$i<6;$i++): $c = isset($cats[$i]) ? $cats[$i] : ['name'=>'','image'=>'']; ?>
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

            <hr />
            <h3>Best Sellers</h3>
            <?php $best = isset($h['best_sellers']) && is_array($h['best_sellers']) ? $h['best_sellers'] : []; ?>
            <?php for ($i=0;$i<6;$i++): $b = isset($best[$i]) ? $best[$i] : ['title'=>'','price'=>'','image'=>'']; ?>
            <div class="row mb-2 align-items-center">
                <div class="col-md-2 text-center">
                    <?php if (!empty($b['image'])): ?><img id="preview-bs-<?= $i ?>" src="<?= base_url($b['image']) ?>" style="max-width:100px" /><?php else: ?><img id="preview-bs-<?= $i ?>" src="<?= base_url('assets/images/placeholder.svg') ?>" style="max-width:100px" /><?php endif; ?>
                    <div class="mb-2 input-group">
                        <input type="file" id="bs_image_<?= $i ?>" name="bs_image_<?= $i ?>" accept="image/*" class="form-control form-control-sm" />
                        <button type="button" class="btn btn-outline-secondary btn-replace-bs" data-target="bs_image_<?= $i ?>">Pilih & Ganti</button>
                    </div>
                </div>
                <div class="col-md-10">
                    <input type="text" name="bs_title[<?= $i ?>]" value="<?= htmlspecialchars($b['title']) ?>" class="form-control mb-1" placeholder="Title" />
                    <input type="text" name="bs_price[<?= $i ?>]" value="<?= htmlspecialchars($b['price']) ?>" class="form-control" placeholder="Price" />
                </div>
            </div>
            <?php endfor; ?>

            <hr />
            <h3>Special Section</h3>
            <?php $s = isset($h['special']) ? $h['special'] : []; ?>
            <div class="mb-2"><label class="form-label">Title</label><input type="text" name="special_title" value="<?= htmlspecialchars($s['title'] ?? '') ?>" class="form-control" /></div>
            <div class="mb-2"><label class="form-label">Sub</label><input type="text" name="special_sub" value="<?= htmlspecialchars($s['sub'] ?? '') ?>" class="form-control" /></div>
            <div class="mb-2"><label class="form-label">Lead</label><input type="text" name="special_lead" value="<?= htmlspecialchars($s['lead'] ?? '') ?>" class="form-control" /></div>
            <div class="mb-2">
                <label class="form-label">Special Image</label>
                <div class="mb-2">
                    <?php if (!empty($s['image'])): ?><img id="preview-special" src="<?= base_url($s['image']) ?>" style="max-width:180px" /><?php else: ?><img id="preview-special" src="<?= base_url('assets/images/placeholder.svg') ?>" style="max-width:180px" /><?php endif; ?>
                </div>
                <div class="input-group">
                    <input type="file" id="special_image" name="special_image" accept="image/*" class="form-control" />
                    <button type="button" class="btn btn-outline-secondary" id="btn-replace-special">Pilih & Ganti</button>
                </div>
            </div>

            <hr />
            <h3>Testimonials</h3>
            <?php $tests = isset($h['testimonials']) && is_array($h['testimonials']) ? $h['testimonials'] : []; ?>
            <?php for ($i=0;$i<5;$i++): $t = isset($tests[$i]) ? $tests[$i] : ['text'=>'','name'=>'','role'=>'']; ?>
            <div class="mb-2">
                <label class="form-label">Quote</label>
                <textarea name="test_text[<?= $i ?>]" class="form-control" rows="2"><?= htmlspecialchars($t['text']) ?></textarea>
                <div class="row mt-1">
                    <div class="col-md-6"><input type="text" name="test_name[<?= $i ?>]" value="<?= htmlspecialchars($t['name']) ?>" class="form-control" placeholder="Name"/></div>
                    <div class="col-md-6"><input type="text" name="test_role[<?= $i ?>]" value="<?= htmlspecialchars($t['role']) ?>" class="form-control" placeholder="Role"/></div>
                </div>
            </div>
            <?php endfor; ?>

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
    bindReplace('.btn-replace-bs', 'preview-bs-');
    var btnSpec = document.getElementById('btn-replace-special'); var inpSpec = document.getElementById('special_image'); if (btnSpec && inpSpec){ btnSpec.addEventListener('click', function(){ inpSpec.click(); }); inpSpec.addEventListener('change', function(e){ var f = e.target.files && e.target.files[0]; if (!f) return; var url = URL.createObjectURL(f); var img = document.getElementById('preview-special'); if (img) img.src = url; }); }
});
</script>
