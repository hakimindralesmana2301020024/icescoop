<?php /** @var array $post */ /** @var array $popular */ ?>
<div class="container blog-details">
    <article class="post-article">
        <div class="post-meta">DEVELOPMENT &nbsp; <span class="post-date"><?php echo $post['date']; ?></span></div>
        <h1 class="post-title"><?php echo $post['title']; ?></h1>

        <div class="hero-image">
            <img src="<?php echo $post['img']; ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
        </div>

        <div class="post-content">
            <?php
            // If the controller provided full HTML content (content_html), render it raw so
            // the author-provided markup (images, headings, lists) is preserved.
            // Fallback to a simple paragraph if only plain content is present.
            if (!empty($post['content'])) {
                echo $post['content'];
            } else {
                echo '<p>No content available.</p>';
            }
            ?>
        </div>
    </article>

    <section class="popular-posts">
        <div class="popular-header">
            <h2>Popular Post</h2>
            <a class="view-all" href="<?php echo base_url('index.php/blog'); ?>">View All</a>
        </div>

        <div class="popular-grid">
            <?php foreach ($popular as $k => $p): ?>
                <div class="card">
                    <div class="card-image"><img src="<?php echo $p['img']; ?>" alt="<?php echo htmlspecialchars($p['title']); ?>"></div>
                    <div class="card-body">
                        <small class="card-category">Travel</small>
                        <h3 class="card-title"><?php echo $p['title']; ?></h3>
                        <p class="card-excerpt"><?php echo $p['excerpt']; ?></p>
                        <?php $plink = isset($p['slug']) && !empty($p['slug']) ? base_url('index.php/blog/'.$p['slug']) : base_url('index.php/blog/'.$k); ?>
                        <a class="read-more" href="<?php echo $plink; ?>">Read More...</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<style>
    .container.blog-details{max-width:1100px;margin:40px auto;padding:0 18px}
    .post-meta{font-size:12px;color:#888;margin-bottom:8px}
    .post-title{font-size:34px;margin:6px 0 20px;font-weight:700}
    .hero-image img{width:100%;border-radius:14px;box-shadow:0 8px 30px rgba(15,15,15,0.08)}
    .post-content{margin-top:26px;color:#444;line-height:1.8; overflow:auto; box-sizing:border-box; padding-bottom:12px; text-align:justify; text-justify:inter-word}
    .post-content p{ text-align:justify }
    .post-content::after{content:"";display:table;clear:both}
    /* quill inserted alternating images (editor adds class quill-auto-aligned) */
    .post-content img.quill-auto-aligned{max-width:48%;height:auto;margin:6px 6px 12px 0}
    .post-content img.quill-auto-aligned[style*="float:right"]{margin-left:6px;margin-right:0}
    .post-quote{border-left:4px solid #7b4cff;padding:12px 16px;margin:20px 0;background:#fbf9ff;color:#6b6b6b;font-style:italic}
    .post-quote cite{display:block;margin-top:8px;font-style:normal;color:#7b4cff}
    .secondary-image{margin:24px 0}
    .secondary-image img{width:100%;max-width:560px;border-radius:10px}

    .popular-posts{margin-top:60px}
    .popular-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px}
    .popular-header h2{font-size:26px;margin:0}
    .popular-header .view-all{background:#7b4cff;color:#fff;padding:8px 14px;border-radius:8px;text-decoration:none}

    .popular-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
    .card{background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 8px 20px rgba(12,12,12,0.06)}
    .card-image img{width:100%;height:160px;object-fit:cover;display:block}
    .card-body{padding:14px}
    .card-category{font-size:11px;color:#777}
    .card-title{font-size:16px;margin:6px 0}
    .card-title a{color:#222;text-decoration:none}
    .card-excerpt{font-size:13px;color:#666}
    .read-more{display:inline-block;margin-top:8px;color:#7b4cff;text-decoration:none;font-weight:600}

    @media (max-width:900px){.popular-grid{grid-template-columns:repeat(2,1fr)}}
    @media (max-width:600px){.popular-grid{grid-template-columns:1fr}.post-title{font-size:24px}}
</style>
