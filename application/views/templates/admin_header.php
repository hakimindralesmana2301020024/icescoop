<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - IcyTales</title>
    <?php $admin_css = FCPATH . 'assets/css/admin.css'; ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') . '?v=' . (file_exists($admin_css) ? filemtime($admin_css) : time()); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
    <div class="admin-shell">
            <aside class="admin-sidebar">
            <div class="admin-brand">IcyTales <span class="muted">Admin</span></div>
            <nav class="admin-nav">
                <a href="<?= base_url('index.php/admin'); ?>" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
                <a href="#"><i class="bi bi-people"></i> Users</a>
                <div class="nav-item has-sub" id="menu-nav">
                    <button class="sub-toggle" id="menu-sub-toggle" aria-expanded="false" aria-controls="menu-sub-list">
                        <i class="bi bi-list-ul"></i>
                        <span>Menu</span>
                        <i class="bi bi-caret-down-fill sub-caret" aria-hidden="true"></i>
                    </button>
                    <div class="admin-submenu" id="menu-sub-list" role="menu">
                        <a href="#">Home</a>
                        <a href="#">About</a>
                        <a href="#">Menu</a>
                        <a href="#">Blog</a>
                    </div>
                </div>
                <a href="#"><i class="bi bi-basket"></i> Orders</a>
                <a href="<?= base_url('index.php/login/logout'); ?>"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </nav>
        </aside>
        <div class="admin-main">
            <header class="admin-topbar">
                <div class="top-left">
                    <div class="admin-menu-wrapper">
                        
                        <div class="admin-title">Admin Dashboard</div>
                        
                    </div>
                </div>
                <div class="top-right">
                    <?php $ci =& get_instance(); $name = $ci->session->userdata('username') ?: 'Admin'; ?>
                    <span class="top-user"><i class="bi bi-person-circle"></i> <?= htmlspecialchars($name); ?></span>
                </div>
            </header>
            <section class="admin-content">
            
            <script>
            // Sidebar submenu toggle for admin 'Menu'
            (function(){
                var toggle = document.getElementById('menu-sub-toggle');
                var navItem = document.getElementById('menu-nav');
                var submenu = document.getElementById('menu-sub-list');
                if (!toggle || !navItem || !submenu) return;

                toggle.addEventListener('click', function(e){
                    e.preventDefault();
                    var open = navItem.classList.contains('open');
                    if (open) {
                        navItem.classList.remove('open');
                        toggle.setAttribute('aria-expanded','false');
                    } else {
                        navItem.classList.add('open');
                        toggle.setAttribute('aria-expanded','true');
                    }
                });

                // Close submenu when clicking outside (optional)
                document.addEventListener('click', function(e){
                    if (!navItem.contains(e.target) && navItem.classList.contains('open')){
                        navItem.classList.remove('open');
                        toggle.setAttribute('aria-expanded','false');
                    }
                });
            })();
            </script>
