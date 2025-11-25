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
            <ul class="blog-cats">
                <li class="active">All</li>
                <li>Advices</li>
                <li>Announcements</li>
                <li>News</li>
                <li>Consultation</li>
                <li>Development</li>
            </ul>
        </div>
    </section>

    <section class="blog-list">
        <div class="container blog-list-inner">
            <?php if(isset($posts) && is_array($posts)): ?>
            <div class="posts-grid">
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

            <div class="blog-pagination">
                <a href="#">&lt;</a>
                <a href="#">1</a>
                <a href="#" class="active">2</a>
                <a href="#">3</a>
                <a href="#">4</a>
                <a href="#">5</a>
                <a href="#">&gt;</a>
            </div>
        </div>
    </section>
</div>
