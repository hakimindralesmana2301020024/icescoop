<section class="hero">
    <div class="container">
        <div class="hero-left">
            <h4 class="sub"><?php echo isset($hero_subtitle) && $hero_subtitle ? htmlspecialchars($hero_subtitle) : 'Welcome to The'; ?></h4>
            <h1 class="title"><?php echo isset($hero_title) && $hero_title ? htmlspecialchars($hero_title) : 'Discover <span class="accent">Sweet</span> Delights!'; ?></h1>
            <p class="lead"><?php echo isset($intro) && $intro ? htmlspecialchars($intro) : 'Relish the timeless taste of handcrafted ice cream, made with passion and the finest ingredients.'; ?></p>
            <a class="btn primary" href="#featured">Browse Our Classic Flavors</a>
        </div>
        <div class="hero-right">
            <?php $hero_img = isset($hero_image) && $hero_image ? $hero_image : 'assets/images/placeholder.svg'; ?>
            <img src="<?php echo base_url($hero_img); ?>" alt="banner" />
        </div>
    </div>
</section>

<section id="featured" class="featured container">
    <h2>Our Classic Favorites</h2>
    <p class="muted">Check out our top products that our customers loveeeeehh.</p>
    <div class="cards">
        <?php foreach((isset($featured) && is_array($featured) ? $featured : []) as $item): ?>
            <?php $img = isset($item['image']) && $item['image'] ? $item['image'] : 'assets/images/placeholder.svg'; ?>
            <div class="icecard">
                <div class="fav" aria-hidden="true">
                    <!-- heart (outline) SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 16 16" aria-hidden="true"><path stroke="#F83D8E" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" d="M8 14s6-4.35 6-7.5S11.523 2 8 5.002 2 2.5 2 6.5 8 14 8 14z"/></svg>
                </div>
                <div class="ice-img"><img src="<?php echo base_url($img); ?>" alt="<?php echo htmlspecialchars($item['title'] ?? ''); ?>" /></div>
                <div class="ice-info">
                    <div class="ice-title-row">
                        <div class="ice-title"><?php echo htmlspecialchars($item['title'] ?? ''); ?></div>
                        <div class="ice-rating" aria-label="rating"><span class="star">★</span> <span><?php echo htmlspecialchars($item['rating'] ?? ''); ?>/5</span></div>
                    </div>
                    <div class="ice-desc"><?php echo htmlspecialchars($item['desc'] ?? ''); ?></div>
                    <div class="ice-price"><?= format_rp($item['price'] ?? 0); ?></div>
                </div>
                <button class="ice-cart" title="Add to cart" aria-label="Add to cart">
                    <!-- cart SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M0 1h2l1.6 9.59A2 2 0 005.56 13h6.88a2 2 0 001.96-1.41L16 4H4" stroke="#fff" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        <?php endforeach; ?>
    </div>
</section>

    <section class="relive">
    <div class="container relive-inner">
        <div class="relive-left">
            <?php $rel = isset($home['relive']) ? $home['relive'] : []; $relimg = isset($rel['image']) && $rel['image'] ? $rel['image'] : 'assets/images/placeholder.svg'; ?>
            <img src="<?php echo base_url($relimg); ?>" alt="relive-image" />
        </div>
        <div class="relive-right">
            <h2 class="relive-title">Relive the Sweet Memories of Classic <span class="accent">Ice Creams</span></h2>
            <p class="relive-lead">From rich chocolate fudge to creamy vanilla sundaes, discover our menu of classic ice cream creations.</p>
            <a class="btn primary" href="#featured">Explore Our Menu</a>
        </div>
    </div>
</section>


<section class="categories container">
    <h2>Explore Our Categories</h2>
    <p class="muted">Browse through our different categories to find your favorite ice cream treats.</p>
    <div class="cats">
        <?php foreach((isset($categories) && is_array($categories) ? $categories : []) as $cat): ?>
            <div class="cat">
                <?php $cimg = isset($cat['image']) && $cat['image'] ? $cat['image'] : 'assets/images/placeholder.svg'; ?>
                <img src="<?php echo base_url($cimg); ?>" alt="<?php echo htmlspecialchars($cat['name'] ?? ''); ?>">
                <div class="cat-name"><?php echo htmlspecialchars($cat['name'] ?? ''); ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="best-sellers container">
    <h2>Our Best Sellers</h2>
    <p class="muted">Discover the favorites that keep our customers coming back for more.</p>
    <div class="cards small">
        <?php foreach((isset($best_sellers) && is_array($best_sellers) ? $best_sellers : []) as $b): ?>
            <div class="card">
                <?php $bimg = isset($b['image']) && $b['image'] ? $b['image'] : 'assets/images/placeholder.svg'; ?>
                <img src="<?php echo base_url($bimg); ?>" alt="<?php echo htmlspecialchars($b['title'] ?? ''); ?>">
                <h3><?php echo htmlspecialchars($b['title'] ?? ''); ?></h3>
                    <div class="meta">
                        <span class="price"><?= format_rp($b['price'] ?? 0); ?></span>
                    </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="special">
    <div class="container special-inner">
        <div class="special-left">
            <?php $spec = isset($home['special']) && is_array($home['special']) ? $home['special'] : []; ?>
            <h2 class="special-title"><?php echo htmlspecialchars($spec['title'] ?? 'Summer Special!'); ?></h2>
            <p class="special-sub"><?php echo htmlspecialchars($spec['sub'] ?? 'Buy One Sundae, Get One 50% Off!'); ?></p>
            <p class="special-lead"><?php echo htmlspecialchars($spec['lead'] ?? 'Use code: SUMMER50 at checkout.'); ?></p>
            <a class="btn primary special-cta" href="#">Get This Deal</a>
        </div>
        <div class="special-right">
            <?php $specimg = isset($spec['image']) && $spec['image'] ? $spec['image'] : 'assets/images/placeholder.svg'; ?>
            <img class="special-image" src="<?php echo base_url($specimg); ?>" alt="special-bowl" />
        </div>
    </div>
</section>

<section class="testimonials container">
    <h2>Hear from Our Happy Ice Cream Lovers</h2>
    <p class="muted">Short testimonials from our customers.</p>
    <div class="test-list">
        <?php foreach((isset($testimonials) && is_array($testimonials) ? $testimonials : []) as $t): ?>
            <div class="test">
                <p class="quote">"<?php echo htmlspecialchars($t['text'] ?? ''); ?>"</p>
                <p class="who"><?php echo htmlspecialchars($t['name'] ?? ''); ?> — <?php echo htmlspecialchars($t['role'] ?? ''); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>
