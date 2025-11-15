<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Standalone Login page (no header/footer) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Berkshire+Swash&family=Archivo:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="<?php echo base_url('assets/css/icecream.css') . '?v=' . @filemtime(FCPATH.'assets/css/icecream.css'); ?>">

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-left">
            <div class="brand">IcyTales</div>
                        <h2 class="auth-title">Selamat Datang</h2>
                        <p class="auth-sub">Masuk ke akun Anda</p>

                        <?php if ($this->session->flashdata('error')): ?>
                            <div class="alert error"><?php echo $this->session->flashdata('error'); ?></div>
                        <?php endif; ?>
                        <?php if ($this->session->flashdata('success')): ?>
                            <div class="alert success"><?php echo $this->session->flashdata('success'); ?></div>
                        <?php endif; ?>

                        <?php echo validation_errors('<div class="alert error">','</div>'); ?>

                        <form class="auth-form" method="post" action="<?php echo base_url('index.php/login'); ?>">
                            <label>Email</label>
                            <input type="email" name="email" class="input" placeholder="you@example.com" required>

                            <label>Kata sandi</label>
                            <input type="password" name="password" class="input" placeholder="••••••••" required>

                            <div class="auth-row">
                                <label class="checkbox"><input type="checkbox"> Ingat saya</label>
                                <a href="#" class="forgot">Lupa kata sandi?</a>
                            </div>

                            <button class="btn auth-btn">Masuk</button>
                        </form>

                        <div class="auth-foot">Belum punya akun? <a href="<?php echo base_url('index.php/login/register'); ?>">Daftar</a></div>
        </div>
        <div class="auth-right">
            <div class="auth-illustration"></div>
        </div>
    </div>
</div>
