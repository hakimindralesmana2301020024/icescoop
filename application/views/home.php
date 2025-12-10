<section class="hero">
    <div class="container">
        <div class="hero-left">
            <h4 class="sub"><?php echo isset($hero_subtitle) && $hero_subtitle ? htmlspecialchars($hero_subtitle) : 'Welcome to'; ?></h4>
            <h1 class="title"><?php echo isset($hero_title) && $hero_title ? htmlspecialchars($hero_title) : '<span class="accent">Sweet Scoop</span>'; ?></h1>
            <p class="lead"><?php echo isset($intro) && $intro ? htmlspecialchars($intro) : 'Relish the timeless taste of handcrafted ice cream, made with passion and the finest ingredients.'; ?></p>
            <a class="btn primary" href="<?= base_url('about'); ?>">
  About
</a>

        </div>
        <div class="hero-right">
            <?php $hero_img = isset($hero_image) && $hero_image ? $hero_image : 'assets/images/placeholder.svg'; ?>
            <img src="<?php echo base_url($hero_img); ?>" alt="banner" />
        </div>
    </div>
</section>

<script>
// Ensure any occurrence of "Sweet Scoop" inside the hero title is wrapped with .accent
(function(){
    try{
        var h = document.querySelector('.hero-left .title');
        if (!h) return;
        // only replace plain text occurrences (avoid double-wrapping)
        var html = h.innerHTML;
        if (html.indexOf('Sweet Scoop') !== -1 && html.indexOf('class="accent"') === -1) {
            h.innerHTML = html.replace(/Sweet Scoop/g, '<span class="accent">Sweet Scoop</span>');
        }
    }catch(e){/* ignore */}
})();
</script>

<script>
// Ensure any occurrence of "Tepi Laut" inside the page (hero or content) is wrapped with .location-text
(function(){
    try{
        // search within main content for text nodes containing 'Tepi Laut'
        var walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null, false);
        var nodes = [];
        while(walker.nextNode()){
            var txt = walker.currentNode.nodeValue;
            if (txt && txt.indexOf('Tepi Laut') !== -1) nodes.push(walker.currentNode);
        }
        nodes.forEach(function(textNode){
            var parent = textNode.parentNode;
            if (!parent) return;
            // don't re-wrap if already inside .location-text
            if (parent.closest && parent.closest('.location-text')) return;
            var replaced = textNode.nodeValue.replace(/Tepi Laut/g, '<span class="location-text">Tepi Laut</span>');
            if (replaced !== textNode.nodeValue){
                var span = document.createElement('span');
                span.innerHTML = replaced;
                parent.replaceChild(span, textNode);
            }
        });
    }catch(e){/* ignore */}
})();
</script>

<section id="featured" class="featured container">
    <h2>Our Classic Favorites</h2>
    <p class="muted">Check out our top products that our customers love</p>
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
            <a class="btn primary" href="<?= base_url('menu'); ?>">
  Explore Our Menu
</a>

        </div>
    </div>
</section>







