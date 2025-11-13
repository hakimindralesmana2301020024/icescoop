<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="menu-page">
    <!-- Hero / Page title -->
    <section class="menu-hero">
        <div class="container menu-hero-inner">
            <h1 class="menu-hero-title">Shop Layout 01</h1>
            <div class="menu-breadcrumb">
                <span class="breadcrumb-pill">Home&nbsp;&nbsp;/&nbsp;&nbsp;Shop</span>
            </div>
        </div>
    </section>

    <section class="menu-main">
        <div class="container menu-main-inner">
            <aside class="menu-sidebar">
                <form class="menu-search">
                    <div class="search-box">
                        <input type="text" placeholder="Search" class="search-input" />
                        <button type="submit" class="search-btn"><i class="bi bi-search"></i></button>
                    </div>
                </form>
                <div class="menu-categories">
                    <h4>Categories</h4>
                    <ul>
                        <?php foreach($categories as $cat): ?>
                        <li class="<?php if($cat['active']) echo 'active'; ?>">
                            <label><input type="checkbox" <?php if($cat['active']) echo 'checked'; ?> disabled> <?php echo $cat['name']; ?></label>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="menu-filter-price">
                    <h4>Filter By Price</h4>
                    <div class="price-range">
                        <span>Range: <span class="accent">$3 - $6.5</span></span>
                    </div>
                </div>
                <div class="menu-featured">
                    <h4>Featured Products</h4>
                    <ul>
                        <?php foreach($featured as $f): ?>
                        <li>
                            <span class="feat-thumb"><img src="<?php echo base_url('assets/images/placeholder.svg'); ?>" alt="<?php echo $f['name']; ?>" /></span>
                            <span class="feat-name"><?php echo $f['name']; ?></span>
                            <span class="feat-price accent">$<?php echo $f['price']; ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </aside>
            <section class="menu-content">
                <div class="menu-toolbar">
                    <div class="toolbar-left">
                        <button class="toolbar-btn active"><i class="bi bi-grid"></i></button>
                        <button class="toolbar-btn"><i class="bi bi-list"></i></button>
                        <span class="toolbar-count">Showing 1–6 of 6 results</span>
                    </div>
                    <div class="toolbar-right">
                        <select class="sort-select">
                            <option>Default Sorting</option>
                        </select>
                    </div>
                </div>
                <div class="menu-products">
                    <?php foreach($products as $p): ?>
                    <div class="product-card">
                        <div class="product-fav"><i class="bi bi-heart"></i></div>
                        <div class="product-img"><img src="<?php echo $p['img']; ?>" alt="<?php echo $p['name']; ?>" /></div>
                        <div class="product-info">
                            <div class="product-title"><?php echo $p['name']; ?></div>
                            <div class="product-rating">
                                <span class="star"><i class="bi bi-star-fill"></i></span>
                                <span class="star"><i class="bi bi-star-fill"></i></span>
                                <span class="star"><i class="bi bi-star-fill"></i></span>
                                <span class="star"><i class="bi bi-star-fill"></i></span>
                                <span class="star"><i class="bi bi-star-half"></i></span>
                                <span class="rating-score"><?php echo $p['rating']; ?></span>
                            </div>
                            <div class="product-desc"><?php echo $p['desc']; ?></div>
                        </div>
                        <div class="product-bottom">
                            <div class="product-price accent">$<?php echo $p['price']; ?></div>
                            <button class="product-cart"><i class="bi bi-cart"></i></button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="menu-pagination">
                    <a href="#" class="page">1</a>
                    <a href="#" class="page">2</a>
                    <a href="#" class="page">3</a>
                    <a href="#" class="page active">4</a>
                </div>
            </section>
        </div>
    </section>
</div>