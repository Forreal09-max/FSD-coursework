</div> <!-- Close container from header -->

<!-- FAQ Section -->
<section class="faq-section">
    <div class="container">
        <div class="section-label">Service Inquiries</div>
        <h2 style="font-size: 3rem; font-weight: 950; text-transform: uppercase; letter-spacing: -2px; line-height: 1;">Common<br>Questions.</h2>
        
        <div class="faq-grid">
            <div class="faq-item">
                <div class="faq-question">How do I verify my identity? <span>+</span></div>
                <div class="faq-answer">
                    All members must undergo a standard verification protocol before deployment. This involves providing a valid driver's license and secondary identification during the request phase.
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">What is the insurance policy? <span>+</span></div>
                <div class="faq-answer">
                    Every asset in the Maria Fleet includes comprehensive liability protection as standard. Additional damage waivers can be secured during the final agreement stage.
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">Can I extend my rental? <span>+</span></div>
                <div class="faq-answer">
                    Extensions are permitted subject to fleet availability. You must submit an extension request at least 24 hours before your current return deadline via the Member Identity portal.
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">What payment methods are accepted? <span>+</span></div>
                <div class="faq-answer">
                    We accept all major international credit cards, bank transfers, and digital assets. Payments are processed through our secure encrypted authentication gateway.
                </div>
            </div>
        </div>
    </div>
</section>

<footer>
    <div class="container">
        <div class="footer-main">
            <div class="footer-logo-block">
                <h2>MARIA<br>FLEET.</h2>
                <p style="color: var(--text-muted); max-width: 300px; font-weight: 600;">
                    Redefining premium mobility through design, performance, and creative excellence. Established 2026.
                </p>
            </div>
            
            <div class="footer-col">
                <h4>Navigation</h4>
                <ul class="footer-links">
                    <li><a href="<?php echo BASE_URL; ?>public/index.php">Hero</a></li>
                    <li><a href="<?php echo BASE_URL; ?>public/search.php">Collection</a></li>
                    <li><a href="#">Exotics</a></li>
                    <li><a href="#">Heritage</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Fleet Auth</h4>
                <ul class="footer-links">
                    <li><a href="<?php echo BASE_URL; ?>public/login.php">Sign In</a></li>
                    <li><a href="<?php echo BASE_URL; ?>public/register.php">Join Fleet</a></li>
                    <li><a href="#">Member Portal</a></li>
                    <li><a href="<?php echo BASE_URL; ?>public/dashboard.php">Admin Console</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Contact</h4>
                <ul class="footer-links">
                    <li><a href="mailto:ops@mariafleet.com">ops@mariafleet.com</a></li>
                    <li><a href="#">+1 (888) MARIA-FLT</a></li>
                    <li><a href="#">Milan, IT</a></li>
                    <li><a href="#">London, UK</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div>© 2026 Maria Creative Portfolio. All Rights Reserved.</div>
            <div>Automotive Excellence / Privacy / Terms</div>
        </div>
    </div>
</footer>

<script>
$(document).ready(function() {
    // FAQ Accordion
    $('.faq-question').on('click', function() {
        const item = $(this).parent('.faq-item');
        item.toggleClass('active');
        
        // Change icon
        const icon = $(this).find('span');
        icon.text(item.hasClass('active') ? '—' : '+');
        
        // Optional: Close others
        // $('.faq-item').not(item).removeClass('active').find('span').text('+');
    });
});
</script>

<script src="<?php echo BASE_URL; ?>assets/js/scripts.js"></script>

</body>
</html>
