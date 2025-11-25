<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="menu-page">

    <div class="container menu-main-inner">
    <aside class="menu-sidebar">
        <!-- Filters form: search, categories, price range -->
        <form id="menu-filters" method="get" action="<?= base_url('index.php/menu'); ?>">
            <div class="menu-search">
                <div class="search-box">
                    <input type="text" name="q" value="<?= htmlspecialchars($this->input->get('q')); ?>" placeholder="Search" class="search-input" />
                    <button type="submit" class="search-btn"><i class="bi bi-search"></i></button>
                </div>
            </div>

            <div class="menu-categories">
                <h4>Categories</h4>
                <ul>
                    <?php foreach($categories as $cat): ?>
                    <li class="<?= !empty($cat['active']) ? 'active' : '' ?>">
                        <label>
                            <input type="checkbox" name="cat[]" value="<?= $cat['id'] ?>" <?= !empty($cat['active']) ? 'checked' : '' ?> />
                            <?= htmlspecialchars($cat['name']); ?>
                        </label>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="menu-filter-price">
                <h4>Filter By Price</h4>
                <div class="price-range">
                    <label>Min: <input type="number" step="0.01" name="min_price" value="<?= htmlspecialchars($this->input->get('min_price')); ?>" style="width:100px" /></label>
                    <label style="margin-left:8px">Max: <input type="number" step="0.01" name="max_price" value="<?= htmlspecialchars($this->input->get('max_price')); ?>" style="width:100px" /></label>
                    <div style="margin-top:8px"><button type="submit" class="btn">Apply</button></div>
                </div>
            </div>
        </form>

        <div class="menu-featured">
            <h4>Featured Products</h4>
            <ul>
                <?php foreach($featured as $f): ?>
                <li>
                    <span class="feat-thumb"><img src="<?= base_url('assets/images/placeholder.svg'); ?>" alt="<?= htmlspecialchars($f['name']); ?>" /></span>
                    <span class="feat-name"><?= htmlspecialchars($f['name']); ?></span>
                    <span class="feat-price accent">Rp <?= htmlspecialchars($f['price']); ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </aside>

    <section class="menu-content">
        <div class="menu-toolbar">
            <div class="toolbar-left">
                <button id="view-grid" class="toolbar-btn <?= (isset($view_mode) && $view_mode !== 'list') ? 'active' : '' ?>" data-view="grid"><i class="bi bi-grid"></i></button>
                <button id="view-list" class="toolbar-btn <?= (isset($view_mode) && $view_mode === 'list') ? 'active' : '' ?>" data-view="list"><i class="bi bi-list"></i></button>
                <span class="toolbar-count">Showing results</span>
            </div>
            <div class="toolbar-right">
                <select class="sort-select">
                    <option>Default Sorting</option>
                </select>
            </div>
        </div>

        <div class="menu-products <?= (isset($view_mode) && $view_mode === 'list') ? 'list-mode' : '' ?>">
            <?php if(empty($products)): ?>
                <p>No products found.</p>
            <?php else: foreach($products as $p): ?>
            <div class="product-card">
                <div class="product-fav"><i class="bi bi-heart"></i></div>
                <div class="product-img">
                    <?php if(!empty($p['img'])): ?>
                        <img src="<?= $p['img']; ?>" alt="<?= htmlspecialchars($p['name']); ?>" />
                    <?php else: ?>
                        <img src="<?= base_url('assets/images/placeholder.svg'); ?>" alt="<?= htmlspecialchars($p['name']); ?>" />
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <div class="product-title"><?= htmlspecialchars($p['name']); ?></div>
                    <div class="product-rating">
                        <span class="star"><i class="bi bi-star-fill"></i></span>
                        <span class="star"><i class="bi bi-star-fill"></i></span>
                        <span class="star"><i class="bi bi-star-fill"></i></span>
                        <span class="star"><i class="bi bi-star-fill"></i></span>
                        <span class="star"><i class="bi bi-star-half"></i></span>
                        <span class="rating-score"><?= htmlspecialchars($p['rating']); ?></span>
                    </div>
                    <div class="product-desc"><?= htmlspecialchars($p['short_desc']); ?></div>
                </div>
                    <div class="product-bottom">
                    <div class="product-price accent">Rp <?= htmlspecialchars($p['price']); ?></div>
                    <a href="<?= base_url('index.php/menu/menudetail/' . (int)$p['id']); ?>" class="product-cart" title="View details"><i class="bi bi-cart"></i></a>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>

        <div class="menu-pagination">
            <?php if(!empty($pagination) && !empty($pagination['total']) && !empty($pagination['per_page'])):
                $total = (int)$pagination['total'];
                $per = (int)$pagination['per_page'];
                $current = (int)$pagination['page'];
                $pages = max(1, (int)ceil($total / $per));
                $base = base_url('index.php/menu');
                for ($i=1;$i<=$pages;$i++):
                    $qs = $_GET;
                    $qs['page'] = $i;
                    $url = $base . '?' . http_build_query($qs);
            ?>
                <a href="<?= htmlspecialchars($url); ?>" class="page <?= $i===$current ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; endif; ?>
        </div>
    </section>
    </div>

</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
    var form = document.getElementById('menu-filters');
    if(!form) return;
    var boxes = form.querySelectorAll('input[type="checkbox"][name="cat[]"]');
    boxes.forEach(function(b){ b.addEventListener('change', function(){ form.submit(); }); });
});
</script>

<script>
// View mode toggle: preserve other GET params and reload with view=grid|list
document.addEventListener('DOMContentLoaded', function(){
    function setViewParam(view){
        var params = new URLSearchParams(window.location.search);
        params.set('view', view);
        // keep existing page parameter when switching view
        window.location.search = params.toString();
    }
    var gridBtn = document.getElementById('view-grid');
    var listBtn = document.getElementById('view-list');
    if(gridBtn) gridBtn.addEventListener('click', function(){ setViewParam('grid'); });
    if(listBtn) listBtn.addEventListener('click', function(){ setViewParam('list'); });
});
</script>