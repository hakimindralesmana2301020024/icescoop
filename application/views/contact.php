<?php ?>
<div class="contact-hero">
    <div class="contact-hero-inner">
        <h1 class="contact-title">Contact Us</h1>
        <div class="breadcrumb-pill"><a href="<?= base_url('index.php'); ?>">Home</a> / Contact Us</div>
    </div>
    <svg class="hero-decor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 80" preserveAspectRatio="none"><path fill="#fff" d="M0,60 C360,120 1080,0 1440,60 L1440,80 L0,80 Z"></path></svg>
</div>

<div class="container contact-wrap">
    <div class="contact-inner">
        <div class="left-col">
            <h2 class="get-in">Get in <span>Touch</span> With Us</h2>
            <p class="sub">Reach out and connect with us today for any inquiries or assistance!</p>

            <div class="cards">
                <div class="contact-card">
                    <div class="circle"><i class="bi bi-geo-alt-fill"></i></div>
                    <div class="card-text">
                        <h4>Location</h4>
                        <p><span class="location-text">Tepi Laut, Tanjungpinang</span></p>
                    </div>
                </div>

                <div class="contact-card">
                    <div class="circle"><i class="bi bi-telephone-fill"></i></div>
                    <div class="card-text">
                        <h4>Phone Number</h4>
                        <p>+6283137412551<br/>+6282220439122</p>
                    </div>
                </div>

                <div class="contact-card">
                    <div class="circle"><i class="bi bi-envelope-fill"></i></div>
                    <div class="card-text">
                        <h4>Email us at</h4>
                        <p>sweetscooptpl@gmail.com</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="right-col">
            <?php if (
                isset(
                    $this->session
                ) && $this->session->flashdata('contact_success')): ?>
                <div class="alert alert-success"><?= htmlspecialchars($this->session->flashdata('contact_success')); ?></div>
            <?php endif; ?>
            <?php if (isset($this->session) && $this->session->flashdata('contact_error')): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($this->session->flashdata('contact_error')); ?></div>
            <?php endif; ?>

            <form class="contact-form" method="post" action="<?= base_url('index.php/contact/submit'); ?>">
                <div class="row two">
                    <input type="text" name="first_name" placeholder="First Name" required>
                    <input type="text" name="last_name" placeholder="Last Name">
                </div>

                <div class="row two">
                    <input type="email" name="email" placeholder="Email address" required>
                    <input type="text" name="phone" placeholder="Phone">
                </div>

                <div class="row">
                    <textarea name="message" rows="6" placeholder="Message"></textarea>
                </div>

                <div class="row">
                    <button type="submit" class="btn-submit">Submit Now <span class="arrow">➜</span></button>
                </div>
            </form>
        </div>
    </div>
</div>


<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;600&display=swap');
    .contact-hero{background:linear-gradient(90deg,#fff0f8,#f6fbff);padding:80px 0 40px;position:relative}
    .contact-hero-inner{max-width:1200px;margin:0 auto;padding:0 18px;text-align:center}
    .contact-title{font-family:'Playfair Display',serif;font-size:58px;margin:10px 0;color:#222}
    .breadcrumb-pill{display:inline-block;background:#fff;padding:8px 16px;border-radius:30px;margin-top:14px;color:#888;box-shadow:0 6px 24px rgba(115,85,200,0.06)}
    .hero-decor{position:absolute;left:0;right:0;bottom:-1px;height:60px}

    .contact-wrap{max-width:1200px;margin:60px auto;padding:0 18px}
    .contact-inner{display:grid;grid-template-columns:460px 1fr;gap:48px;align-items:start}
    .left-col .get-in{font-size:34px;margin:0;font-weight:600}
    .left-col .get-in span{color:#ff2d91;font-family:'Poppins',sans-serif}
    .sub{color:#7b7b7b;margin:12px 0 28px}

    .cards{display:flex;flex-direction:column;gap:18px}
    .contact-card{display:flex;gap:18px;align-items:center;background:#fff;border-radius:14px;padding:18px;box-shadow:0 18px 50px rgba(19,14,75,0.06)}
    .contact-card .circle{width:64px;height:64px;border-radius:16px;background:linear-gradient(180deg,#f6e8fb,#f2f0ff);display:flex;align-items:center;justify-content:center;font-size:22px;color:#7b4cff}
    .card-text h4{margin:0 0 6px;font-size:16px}
    .card-text p{margin:0;color:#6b6b6b;font-size:14px}
    /* ensure location uses site accent (pink) */
    .contact-card .card-text .location-text{color:var(--accent) !important;font-weight:600}

    .contact-form{background:transparent}
    .row{margin-bottom:14px}
    .row.two{display:flex;gap:12px}
    .row.two input{flex:1}
    .contact-form input,.contact-form textarea{width:100%;padding:14px;border-radius:30px;border:1px solid #efecec;background:#fff;font-size:14px}
    .contact-form textarea{border-radius:16px}
    .btn-submit{background:linear-gradient(90deg,#ff3ea1,#ff6aa7);color:#fff;padding:12px 28px;border-radius:30px;border:none;font-weight:600;cursor:pointer;box-shadow:0 10px 30px rgba(255,90,140,0.18)}
    .btn-submit .arrow{margin-left:8px}

    .map-full{margin-top:40px}

    @media (max-width:980px){
        .contact-inner{grid-template-columns:1fr;}
        .contact-hero{padding:60px 0 20px}
        .contact-title{font-size:40px}
        .left-col{order:2}
        .right-col{order:1}
        .row.two{flex-direction:column}
    }
</style>
<!-- Modal markup + script to show popup after form submit -->
<div id="contact-modal-overlay" style="display:none"></div>
<div id="contact-modal" role="dialog" aria-modal="true" style="display:none">
    <div id="contact-modal-card">
        <button id="contact-modal-close" aria-label="Close">×</button>
        <div id="contact-modal-body"></div>
    </div>
</div>
<script>
    (function(){
        // Modal helpers
        function showModal(html, autoClose){
            var overlay = document.getElementById('contact-modal-overlay');
            var modal = document.getElementById('contact-modal');
            var body = document.getElementById('contact-modal-body');
            overlay.style.display = 'block';
            modal.style.display = 'block';
            body.innerHTML = html;
            if (autoClose) {
                setTimeout(hideModal, autoClose);
            }
        }
        function hideModal(){
            var overlay = document.getElementById('contact-modal-overlay');
            var modal = document.getElementById('contact-modal');
            overlay.style.display = 'none';
            modal.style.display = 'none';
        }
        document.getElementById('contact-modal-overlay').addEventListener('click', hideModal);
        document.getElementById('contact-modal-close').addEventListener('click', hideModal);

        document.addEventListener('DOMContentLoaded', function(){
            // If we rendered a flash alert, show it as modal
            var success = document.querySelector('.alert-success');
            var error = document.querySelector('.alert-danger');
            if (success) {
                showModal('<strong>Success</strong><div style="margin-top:8px">' + success.textContent.trim() + '</div>', 4000);
                // optionally remove inline alert to avoid duplication
                success.parentNode.removeChild(success);
            } else if (error) {
                showModal('<strong>Error</strong><div style="margin-top:8px">' + error.textContent.trim() + '</div>', 6000);
                error.parentNode.removeChild(error);
            }
        });
    })();
</script>

<style>
    /* Modal styles (lightweight) */
    #contact-modal-overlay{position:fixed;left:0;top:0;right:0;bottom:0;background:rgba(0,0,0,0.45);z-index:9999}
    #contact-modal{position:fixed;left:50%;top:50%;transform:translate(-50%,-50%);z-index:10000;max-width:420px;width:90%;}
    #contact-modal-card{background:#fff;border-radius:12px;padding:18px 18px 14px;box-shadow:0 20px 60px rgba(16,24,40,0.2);position:relative}
    #contact-modal-close{position:absolute;right:8px;top:6px;border:0;background:transparent;font-size:20px;cursor:pointer;color:#666}
    #contact-modal-body{color:#222;font-size:15px}
</style>
