<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IcyTales - Home</title>
    <?php $css_path = FCPATH . 'assets/css/icecream.css'; ?>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/icecream.css') . '?v=' . (file_exists($css_path) ? filemtime($css_path) : time()); ?>">
    <!-- Google Fonts: Poppins for navigation -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
    <header class="site-header">
        <div class="container">
            <div class="logo">IcyTales</div>
            <nav class="main-nav">
                <a href="<?= base_url('index.php/home'); ?>">Home</a>
                <a href="<?= base_url('index.php/about'); ?>">About</a>
                <a href="<?= base_url('index.php/menu'); ?>">Menu</a>
                <a href="<?= base_url('index.php/blog'); ?>">Blog</a>
                <?php $ci =& get_instance(); $cart_count = (isset($ci->session) ? $ci->session->userdata('cart_count') : 0) ?: 0; ?>
                <a href="<?= base_url('index.php/contact'); ?>" class="cta">Contact Us</a>
                    <?php $is_logged = (isset($ci->session) ? $ci->session->userdata('logged_in') : false); $username = (isset($ci->session) ? $ci->session->userdata('username') : '');
                        $return_url = function_exists('current_url') ? current_url() : base_url(); if (!empty($_SERVER['QUERY_STRING'])) { $return_url .= '?'.$_SERVER['QUERY_STRING']; }
                    ?>
                <a href="<?= base_url('index.php/order/cart'); ?>" class="cart-link" title="Keranjang">
                    <i class="bi bi-cart3" style="font-size:23px;vertical-align:middle"></i>
                    <?php if ($cart_count > 0): ?>
                        <span class="cart-badge"><?= htmlspecialchars($cart_count); ?></span>
                    <?php endif; ?>
                </a>
                <?php if ($is_logged): ?>
                    <div class="user-menu" id="user-menu">
                        <a href="#" class="user-toggle" id="user-toggle" aria-haspopup="true" aria-expanded="false" title="<?= htmlspecialchars($username); ?>">
                            <i class="bi bi-person-circle" style="font-size:23px;vertical-align:middle"></i>
                        </a>
                        <div class="user-dropdown" id="user-dropdown" role="menu" aria-labelledby="user-toggle">
                            <div class="user-item user-name">Hi, <?= htmlspecialchars($username); ?></div>
                            <a class="user-item" href="<?= base_url('index.php/login/logout?return='.urlencode($return_url)); ?>">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= base_url('index.php/login'); ?>" class="cta">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main class="site-main">

    <script>
    // Toggle the user dropdown on click and close when clicking outside
    (function(){
        var toggle = document.getElementById('user-toggle');
        var menu = document.getElementById('user-menu');
        if (!toggle || !menu) return;

        toggle.addEventListener('click', function(e){
            e.preventDefault();
            var isOpen = menu.classList.contains('open');
            if (isOpen) {
                menu.classList.remove('open');
                toggle.setAttribute('aria-expanded', 'false');
            } else {
                menu.classList.add('open');
                toggle.setAttribute('aria-expanded', 'true');
            }
        });

        // Close when clicking outside
        document.addEventListener('click', function(e){
            if (!menu.contains(e.target)) {
                menu.classList.remove('open');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });

        // Close on ESC
        document.addEventListener('keydown', function(e){
            if (e.key === 'Escape' || e.key === 'Esc') {
                menu.classList.remove('open');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    })();
    </script>
