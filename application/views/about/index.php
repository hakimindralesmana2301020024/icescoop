<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Minimal preview includes so About page renders standalone (fonts, icons, css) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Berkshire+Swash&family=Archivo:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?php echo base_url('assets/css/icecream.css'); ?>">

<main class="about-page">

    <!-- Hero / Page title -->
    <section class="about-hero">
        <div class="container about-hero-inner">
            <h1 class="about-hero-title"><?php echo htmlspecialchars($about['hero_title'] ?? 'About Us'); ?></h1>
            <div class="about-breadcrumb">
                <span class="breadcrumb-pill">Home&nbsp;&nbsp;/&nbsp;&nbsp;<?php echo htmlspecialchars($about['hero_title'] ?? 'About Us'); ?></span>
            </div>
        </div>
    </section>

    <!-- Journey Section (image left, copy right) -->
    <section class="container about-journey">
        <div class="relive-inner">
            <div class="relive-left">
                <?php if (!empty($about['journey_image'])): ?>
                    <img src="<?php echo base_url($about['journey_image']); ?>" alt="Our story" class="about-hero-image"/>
                <?php else: ?>
                    <img src="<?php echo base_url('assets/images/placeholder.svg'); ?>" alt="Our story" class="about-hero-image"/>
                <?php endif; ?>
            </div>
            <div class="relive-right">
                <h2 class="relive-title"><?php echo htmlspecialchars($about['journey_title'] ?? 'Our <span class="accent">Journey</span> Began With a Simple Dream'); ?></h2>
                <p class="relive-lead"><?php echo nl2br(htmlspecialchars($about['journey_lead1'] ?? 'Our goal is to make the best ice cream using only the finest, natural ingredients. From rich, creamy classics to adventurous new creations, every flavor is meticulously crafted in-house to ensure the highest quality and freshness.')); ?></p>
                <p class="relive-lead"><?php echo nl2br(htmlspecialchars($about['journey_lead2'] ?? 'We take pride in offering a diverse range of options, including dairy-free, vegan, and gluten-free choices, so everyone can find their perfect scoop.')); ?></p>
            </div>
        </div>
    </section>


    <!-- Team -->
    <section class="about-team">
        <div class="container text-center">
            <h2 class="title">Our <span class="accent">Team Members</span></h2>
            <p class="muted">Get to know the friendly faces behind your favorite flavors.</p>

            <div class="team-grid">
                <?php $team = isset($about['team']) && is_array($about['team']) ? $about['team'] : [];
                if (empty($team)) {
                    // default hard-coded people (preserve original look when empty)
                    $team = [
                        ['name'=>'Marvin Joner','role'=>'Bakery Worker','image'=>base_url('assets/images/placeholder.svg')],
                        ['name'=>'Patricia Woodrum','role'=>'Staff Worker','image'=>base_url('assets/images/placeholder.svg')],
                        ['name'=>'Hannaz Stone','role'=>'Shop Worker','image'=>base_url('assets/images/placeholder.svg')],
                    ];
                }
                foreach ($team as $m): ?>
                    <div class="team-member">
                        <img src="<?= htmlspecialchars( (strpos($m['image'] ?? '', 'http') === 0) ? $m['image'] : base_url($m['image'] ?? 'assets/images/placeholder.svg') ); ?>" alt="<?= htmlspecialchars($m['name'] ?? ''); ?>"/>
                        <h4 class="team-name"><?= htmlspecialchars($m['name'] ?? ''); ?></h4>
                        <div class="muted"><?= htmlspecialchars($m['role'] ?? ''); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>


</main>
