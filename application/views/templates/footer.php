    </main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div class="footer-brand">
                <div class="logo">Swett Scoop</div>
            </div>

            <div class="footer-middle">
                <div class="footer-nav">
                    <h4>Navigation</h4>
                    <ul>
                        <li><a href="<?= base_url('home'); ?>">Home</a></li>
                        <li><a href="<?= base_url('about'); ?>">About</a></li>
                        <li><a href="<?= base_url('menu'); ?>">Menu</a></li>
                        <li><a href="<?= base_url('blog'); ?>">Blog</a></li>
                        <li><a href="<?= base_url('contact'); ?>">Contact</a></li>
                    </ul>
                </div>

                <div class="footer-contact">
                    <h4>Contact</h4>
                    <div class="contact-item">
                        <div class="icon"><i class="bi bi-geo-alt-fill" aria-hidden="true"></i></div>
                        <div class="contact-text"><strong>Address:</strong><br/>Tepi Laut, Tanjungpinang</div>
                    </div>
                    <div class="contact-item">
                        <div class="icon"><i class="bi bi-envelope-fill" aria-hidden="true"></i></div>
                        <div class="contact-text"><strong>Email:</strong><br/>sweetscooptpl@gmail.com</div>
                    </div>
                </div>
                
                <div class="footer-extra">
                    <h4>Call Us</h4>
                    <div class="call-item">
                        <div class="icon"><i class="bi bi-telephone-fill" aria-hidden="true"></i></div>
                        <div>
                            <p class="phone">+6283137412551</p>
                            <p class="call-note">Got Questions? Call us 24/7</p>
                        </div>
                    </div>
                    <div class="socials">
                        <a href="https://www.instagram.com/swett.scoop/" 
                            class="social" 
                            aria-label="Instagram" 
                            target="_blank" 
                            rel="noopener noreferrer">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="https://www.tiktok.com/@swett.scoop" 
                            class="social" 
                            aria-label="TikTok" 
                            target="_blank" 
                            rel="noopener noreferrer">
                            <i class="bi bi-tiktok"></i>
                        </a>    
                    </div>
                </div>

            </div>

        </div>

        <div class="footer-bottom">
            <div class="container">
                <div class="hr"></div>
                <p class="copyright">Copyright &copy; <?php echo date('Y'); ?> Swett Scoop. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
