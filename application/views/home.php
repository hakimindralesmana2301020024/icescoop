<section class="hero">
    <div class="container">
        <div class="hero-left">
            <h4 class="sub">Welcome to The</h4>
            <h1 class="title">Discover <span class="accent">Sweet</span> Delights!</h1>
            <p class="lead">Relish the timeless taste of handcrafted ice cream, made with passion and the finest ingredients.</p>
            <a class="btn primary" href="#featured">Browse Our Classic Flavors</a>
        </div>
        <div class="hero-right">
            <img src="<?php echo base_url('assets/images/placeholder.svg'); ?>" alt="banner" />
        </div>
    </div>
</section>

<section id="featured" class="featured container">
    <h2>Our Classic Favorites</h2>
    <p class="muted">Check out our top products that our customers love.</p>
    <div class="cards">
        <?php foreach($featured as $item): ?>
            <div class="card">
                <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['title']; ?>">
                <h3><?php echo $item['title']; ?></h3>
                <p class="desc"><?php echo $item['desc']; ?></p>
                <div class="meta">
                    <span class="price"><?php echo $item['price']; ?></span>
                    <span class="rating"><?php echo $item['rating']; ?>/5</span>
                </div>
                <button class="btn add">Add to cart</button>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="relive">
    <div class="container relive-inner">
        <div class="relive-left">
            <img src="<?php echo base_url('assets/images/placeholder.svg'); ?>" alt="relive-image" />
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
        <?php foreach($categories as $cat): ?>
            <div class="cat">
                <img src="<?php echo $cat['image']; ?>" alt="<?php echo $cat['name']; ?>">
                <div class="cat-name"><?php echo $cat['name']; ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="best-sellers container">
    <h2>Our Best Sellers</h2>
    <p class="muted">Discover the favorites that keep our customers coming back for more.</p>
    <div class="cards small">
        <?php foreach($best_sellers as $b): ?>
            <div class="card">
                <img src="<?php echo $b['image']; ?>" alt="<?php echo $b['title']; ?>">
                <h3><?php echo $b['title']; ?></h3>
                <div class="meta">
                    <span class="price"><?php echo $b['price']; ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="special">
    <div class="container special-inner">
        <div class="special-left">
            <h2 class="special-title">Summer Special!</h2>
            <p class="special-sub">Buy One Sundae, Get One 50% Off!</p>
            <p class="special-lead">Use code: <strong>SUMMER50</strong> at checkout.</p>
            <a class="btn primary special-cta" href="#">Get This Deal</a>
        </div>
        <div class="special-right">
            <div class="special-badge">50%<span>OFF</span></div>
            <img class="special-image" src="<?php echo base_url('assets/images/placeholder.svg'); ?>" alt="special-bowl" />
        </div>
    </div>
</section>

<section class="testimonials container">
    <h2>Hear from Our Happy Ice Cream Lovers</h2>
    <p class="muted">Short testimonials from our customers.</p>
    <div class="test-list">
        <?php foreach($testimonials as $t): ?>
            <div class="test">
                <p class="quote">"<?php echo $t['text']; ?>"</p>
                <p class="who"><?php echo $t['name']; ?> — <?php echo $t['role']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>
