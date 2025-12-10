<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="blog-page">
    <section class="blog-hero">
        <div class="container blog-hero-inner">
            <h1 class="blog-hero-title">Blog</h1>
            <div class="blog-breadcrumb">
                <span class="breadcrumb-pill">Home&nbsp;&nbsp;/&nbsp;&nbsp;Blog</span>
            </div>
        </div>
    </section>

    <section class="blog-controls">
    <div class="container">
        <ul class="blog-cats" id="blog-cats">
            <!-- <li class="active" data-slug="all">All</li> -->

            <?php if (!empty($categories) && is_array($categories)): ?>
                <?php foreach($categories as $c): ?>
                    <li data-slug="<?= htmlspecialchars($c['slug']); ?>">
                        <?= htmlspecialchars($c['name']); ?>
                    </li>
                <?php endforeach; ?>

            <?php else: /*
                <li data-slug="activity">Activity</li>
                <li data-slug="announcements">Announcements</li>
                <li data-slug="news">News</li>
            */ ?>
            <?php endif; ?>

        </ul>
    </div>
</section>


    <section class="blog-list">
        <div class="container blog-list-inner">
            <?php if(isset($posts) && is_array($posts)): ?>
            <div class="posts-grid" id="posts-grid">
                <?php foreach($posts as $k => $p): ?>
                <article class="post-card">
                    <div class="post-img"><img src="<?php echo $p['img']; ?>" alt="<?php echo $p['title']; ?>"/></div>
                    <div class="post-body">
                        <div class="post-meta">Posted by <strong><?php echo $p['author']; ?></strong> &nbsp; | &nbsp; <?php echo $p['date']; ?></div>
                        <h3 class="post-title"><?php echo $p['title']; ?></h3>
                        <p class="post-excerpt"><?php echo $p['excerpt']; ?></p>
                        <?php
                            $link = isset($p['slug']) && !empty($p['slug']) ? base_url('index.php/blog/'.$p['slug']) : base_url('index.php/blog/'.$k);
                        ?>
                        <a href="<?php echo $link; ?>" class="read-more">Read More</a>
                    </div>
                </article>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!--
<div class="blog-pagination">
    <a href="#">&lt;</a>
    <a href="#">1</a>
    <a href="#" class="active">2</a>
    <a href="#">3</a>
    <a href="#">4</a>
    <a href="#">5</a>
    <a href="#">&gt;</a>
</div>
-->

        </div>
    </section>
</div>

    <script>
    // Blog category filter (AJAX)
    (function(){
        function onCatClick(e){
            var li = e.target.closest('li');
            if (!li) return;
            var slug = li.getAttribute('data-slug') || 'all';
            // set active
            document.querySelectorAll('#blog-cats li').forEach(function(x){ x.classList.remove('active'); });
            li.classList.add('active');

            // fetch posts
            fetch('<?= base_url('index.php/blog/filter'); ?>?category=' + encodeURIComponent(slug))
                .then(function(r){ return r.json(); })
                .then(function(data){
                    var grid = document.getElementById('posts-grid');
                    if (!grid) return;
                    grid.innerHTML = data.html || '<div style="padding:18px">No posts found.</div>';
                }).catch(function(){ /* ignore errors for now */ });
        }

        document.addEventListener('DOMContentLoaded', function(){
            var ul = document.getElementById('blog-cats');
            if (!ul) return;
            ul.addEventListener('click', function(e){ onCatClick(e); });
        });
    })();
    </script>
