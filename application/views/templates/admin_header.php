<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Swett Scoop</title>
    <!-- Bootstrap for quick admin layout/styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUa6mY5yY2Qv3K6Zr6Y1b5Qb5Q1p6u0Q5p5Y5b1Q5p5Q" crossorigin="anonymous">
    <?php $admin_css = FCPATH . 'assets/css/admin.css'; ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') . '?v=' . (file_exists($admin_css) ? filemtime($admin_css) : time()); ?>">
    <!-- orders styles consolidated into main admin.css (no separate include) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
    <div class="admin-shell">
            <aside class="admin-sidebar">
            <div class="admin-brand"><strong>Swett Scoop</strong> <span class="muted">Admin</span></div>
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
                        <a href="<?= base_url('index.php/admin/home'); ?>">Home</a>
                        <a href="<?= base_url('index.php/admin/about'); ?>">About</a>
                        <a href="<?= base_url('index.php/admin/menu'); ?>">Menu</a>
                        <a href="<?= base_url('index.php/admin/blog'); ?>">Blog</a>
                    </div>
                </div>
                <?php
                    // compute pending_payment count (try DB, fallback to session)
                    $pending_count = 0;
                    $ci2 =& get_instance();
                    try {
                        $ci2->load->database();
                        if ($ci2->db->table_exists('orders')) {
                            $row = $ci2->db->select('COUNT(*) as cnt')->from('orders')->where('status','pending_payment')->get()->row_array();
                            $pending_count = !empty($row['cnt']) ? (int)$row['cnt'] : 0;
                        }
                    } catch (Exception $e) {
                        // ignore
                    }
                    if ($pending_count === 0) {
                        $sessOrders = $ci2->session->userdata('orders') ?: [];
                        foreach ($sessOrders as $so) if (!empty($so['status']) && $so['status'] === 'pending_payment') $pending_count++;
                    }
                    // compute unread contact messages
                    $msg_count = 0;
                    try {
                        if ($ci2->db->table_exists('contact_messages')) {
                            $rmsg = $ci2->db->select('COUNT(*) as cnt')->from('contact_messages')->where('status',0)->get()->row_array();
                            $msg_count = !empty($rmsg['cnt']) ? (int)$rmsg['cnt'] : 0;
                        }
                    } catch (Exception $e) { $msg_count = 0; }
                ?>
                <a href="<?= base_url('index.php/admin/orders'); ?>" id="admin-orders-link"><i class="bi bi-basket"></i> Orders 
                    <?php if($pending_count>0): ?><span id="badge-pending" class="badge bg-danger" style="margin-left:6px"><?= $pending_count ?></span><?php else: ?><span id="badge-pending" class="badge bg-danger" style="margin-left:6px;display:none"></span><?php endif; ?>
                    <?php // additional statuses
                    $paidCount = 0; try { if (isset($ci2) && $ci2->db->table_exists('orders')) { $r2 = $ci2->db->select('COUNT(*) as cnt')->from('orders')->where('status','paid_pending_confirmation')->get()->row_array(); $paidCount = !empty($r2['cnt']) ? (int)$r2['cnt'] : 0; } } catch(Exception $e) {}
                    $pendConf = 0; try { if (isset($ci2) && $ci2->db->table_exists('orders')) { $r3 = $ci2->db->select('COUNT(*) as cnt')->from('orders')->where('status','pending_confirmation')->get()->row_array(); $pendConf = !empty($r3['cnt']) ? (int)$r3['cnt'] : 0; } } catch(Exception $e) {}
                    ?>
                    <?php if($paidCount>0): ?><span id="badge-paid" class="badge bg-warning text-dark" style="margin-left:6px"><?= $paidCount ?></span><?php else: ?><span id="badge-paid" class="badge bg-warning text-dark" style="margin-left:6px;display:none"></span><?php endif; ?>
                    <?php if($pendConf>0): ?><span id="badge-pendconf" class="badge bg-secondary text-white" style="margin-left:6px"><?= $pendConf ?></span><?php else: ?><span id="badge-pendconf" class="badge bg-secondary text-white" style="margin-left:6px;display:none"></span><?php endif; ?>
                </a>
                <a href="<?= base_url('index.php/admin/messages'); ?>" id="admin-messages-link" style="margin-top:6px;display:inline-block"><i class="bi bi-envelope"></i> Messages
                    <?php if($msg_count>0): ?><span id="badge-messages" class="badge bg-danger" style="margin-left:6px"><?= $msg_count ?></span><?php else: ?><span id="badge-messages" class="badge bg-danger" style="margin-left:6px;display:none"></span><?php endif; ?>
                </a>
                <a href="<?= base_url('index.php/login/logout'); ?>"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </nav>
        </aside>
        <div class="admin-main">
            <header class="admin-topbar">
                <div class="top-left">
                    <div class="admin-menu-wrapper">
                        <button id="admin-burger" class="admin-burger" aria-label="Toggle sidebar" aria-expanded="false"><i class="bi bi-list"></i></button>
                        
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
            <script>
            // Mobile sidebar burger toggle
            (function(){
                var burger = document.getElementById('admin-burger');
                var shell = document.querySelector('.admin-shell');
                if (!burger || !shell) return;
                burger.addEventListener('click', function(e){
                    e.preventDefault();
                    var open = shell.classList.contains('open-sidebar');
                    if (open){
                        shell.classList.remove('open-sidebar');
                        burger.setAttribute('aria-expanded','false');
                    } else {
                        shell.classList.add('open-sidebar');
                        burger.setAttribute('aria-expanded','true');
                    }
                });
                // close when clicking outside the sidebar
                document.addEventListener('click', function(e){
                    if (!shell.classList.contains('open-sidebar')) return;
                    var sidebar = document.querySelector('.admin-sidebar');
                    if (sidebar && !sidebar.contains(e.target) && !burger.contains(e.target)){
                        shell.classList.remove('open-sidebar');
                        burger.setAttribute('aria-expanded','false');
                    }
                });
            })();
            </script>
            <script>
            // Poll admin orders counts every 15 seconds and update badges
            (function(){
                function updateCounts(){
                    // Orders counts
                    fetch('<?= base_url('index.php/admin/orders_count'); ?>')
                        .then(function(r){ return r.json(); })
                        .then(function(data){
                            var bp = document.getElementById('badge-pending');
                            var bpaid = document.getElementById('badge-paid');
                            var bpc = document.getElementById('badge-pendconf');
                            if (bp) { if (data.pending_payment && data.pending_payment>0) { bp.style.display='inline-block'; bp.textContent = data.pending_payment; } else { bp.style.display='none'; } }
                            if (bpaid) { if (data.paid_pending_confirmation && data.paid_pending_confirmation>0) { bpaid.style.display='inline-block'; bpaid.textContent = data.paid_pending_confirmation; } else { bpaid.style.display='none'; } }
                            if (bpc) { if (data.pending_confirmation && data.pending_confirmation>0) { bpc.style.display='inline-block'; bpc.textContent = data.pending_confirmation; } else { bpc.style.display='none'; } }
                        }).catch(function(){ /* ignore */ });

                    // Messages counts (unread)
                    fetch('<?= base_url('index.php/admin/messages_count'); ?>')
                        .then(function(r){ return r.json(); })
                        .then(function(d){
                            var bm = document.getElementById('badge-messages');
                            if (bm) {
                                if (d.unread && d.unread>0) { bm.style.display='inline-block'; bm.textContent = d.unread; } else { bm.style.display='none'; }
                            }
                        }).catch(function(){ /* ignore */ });
                }
                // initial
                updateCounts();
                setInterval(updateCounts, 15000);
            })();
            </script>
