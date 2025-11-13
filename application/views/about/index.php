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
            <h1 class="about-hero-title">About Us</h1>
            <div class="about-breadcrumb">
                <span class="breadcrumb-pill">Home&nbsp;&nbsp;/&nbsp;&nbsp;About Us</span>
            </div>
        </div>
    </section>

    <!-- Journey Section (image left, copy right) -->
    <section class="container about-journey">
        <div class="relive-inner">
            <div class="relive-left">
                <img src="<?php echo base_url('assets/images/placeholder.svg'); ?>" alt="Our story" class="about-hero-image"/>
            </div>
            <div class="relive-right">
                <h2 class="relive-title">Our <span class="accent">Journey</span> Began With a Simple Dream</h2>
                <p class="relive-lead">Our goal is to make the best ice cream using only the finest, natural ingredients. From rich, creamy classics to adventurous new creations, every flavor is meticulously crafted in-house to ensure the highest quality and freshness.</p>
                <p class="relive-lead">We take pride in offering a diverse range of options, including dairy-free, vegan, and gluten-free choices, so everyone can find their perfect scoop.</p>
                <a href="#" class="btn primary">Read More</a>
            </div>
        </div>
    </section>

    <!-- Mission Section (purple background, image right) -->
    <section class="special about-mission">
        <div class="container special-inner">
            <div class="special-left">
                <h2 class="special-title">Our Mission is to<br/>Create Moments</h2>
                <p class="special-lead">We strive to foster a welcoming and joyful environment where customers of all ages can gather, celebrate, and make lasting memories. Our commitment extends beyond serving great ice cream.</p>
                <a href="#" class="special-cta">Read More</a>
            </div>
            <div class="special-right">
                <img src="<?php echo base_url('assets/images/placeholder.svg'); ?>" alt="Team enjoying" class="special-image"/>
            </div>
        </div>
    </section>

    <!-- Team -->
    <section class="about-team">
        <div class="container text-center">
            <h2 class="title">Our <span class="accent">Team Members</span></h2>
            <p class="muted">Get to know the friendly faces behind your favorite flavors.</p>

            <div class="team-grid">
                <div class="team-member">
                    <img src="<?php echo base_url('assets/images/placeholder.svg'); ?>" alt="Marvin"/>
                    <h4 class="team-name">Marvin Joner</h4>
                    <div class="muted">Bakery Worker</div>
                </div>
                <div class="team-member">
                    <img src="<?php echo base_url('assets/images/placeholder.svg'); ?>" alt="Patricia"/>
                    <h4 class="team-name">Patricia Woodrum</h4>
                    <div class="muted">Staff Worker</div>
                </div>
                <div class="team-member">
                    <img src="<?php echo base_url('assets/images/placeholder.svg'); ?>" alt="Hannaz"/>
                    <h4 class="team-name">Hannaz Stone</h4>
                    <div class="muted">Shop Worker</div>
                </div>
            </div>
        </div>
    </section>


</main>
