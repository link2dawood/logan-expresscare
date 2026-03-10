  <!-- Footer Start -->
    <footer class="main-footer bg-section dark-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <!-- About Footer Start -->
                    <div class="about-footer">
                        <!-- Footer Logo Start -->
                        <div class="footer-logo">
                            <img src="images/footer-logo-light.png" alt="">
                        </div>
                        <!-- Footer Logo End -->

                        <!-- About Footer Content Start -->
                        <div class="about-footer-content">
                            <p>Providing compassionate, professional,  reliable home nursing care tailored to your needs.</p>
                        </div>           
                        <!-- About Footer Content End -->

                        <!-- Footer Social Link Start -->
                        <div class="footer-social-links">
	<ul>
		<li><a href="https://www.instagram.com/loganexpresscare"><i class="fa-brands fa-instagram"></i></a></li>
		<li><a href="https://www.facebook.com/profile.php?id=61578906639892"><i class="fa-brands fa-facebook-f"></i></a></li>
		<li><a href="https://www.linkedin.com/company/logan-express-care/"><i class="fa-brands fa-linkedin-in"></i></a></li>
		<li><a href="https://wa.me/loganexpresscare" target="_blank"><i class="fa-brands fa-whatsapp"></i></a></li>
	</ul>
</div>
                        <!-- Footer Social Link End -->
                    </div>
                    <!-- About Footer End -->
                </div>

                <div class="col-lg-3 col-md-6">
                    <!-- Footer Contact Details Start -->
                    <div class="footer-contact-details footer-links">
                        <!-- Footer Contact Item Start -->
                        <div class="footer-contact-item">
                            <h3>Contact Information</h3>
                            <p>460 Lonsdale Street, level 13 & 14, Melbourne, Victoria, 3000, Australia</p>
                        </div>
                        <!-- Footer Contact Item End -->
                
                        <!-- Footer Contact Item Start -->
                        <div class="footer-contact-item">
                            <h3>Email Address</h3>
                            <p><a href="mailto:info@loganexpresscare.com.au">info@loganexpresscare.com.au</a></p>
                        </div>
                        <div class="footer-contact-item">
                            <h3>Phone Number</h3>
                            <p><a href="tel:1300 556 002">1300 556 002</a></p>
                        </div>
                        <!-- Footer Contact Item End -->
                    </div>
                    <!-- Footer Contact Details End --> 
                </div>

                <div class="col-lg-2 col-md-6">
                    <!-- Footer Links Start -->
                    <div class="footer-links">
                        <h3>Quick Links</h3>
                        <ul>
                            <li><a href="index.php">Home</a></li>
                            <li><a href="about.php">About us</a></li>
                            <li><a href="services.php">services</a></li>
                            <li><a href="contact.php">contact us</a></li>
                        </ul>
                    </div>
                    <!-- Footer Links End -->
                </div>

                <div class="col-lg-3">
                    <!-- Footer Newsletter Box Start -->
                    <div class="footer-newsletter-box footer-links">
                        <h3>Newsletter Subscription</h3>
                        <p>Stay Updated With Health Tips & Services</p>
                        <!-- Footer Newsletter Form Start -->
                        <div class="footer-newsletter-form">
                            <form id="newslettersForm" action="#" method="POST">
                                <div class="form-group">
                                    <input type="email" name="mail" class="form-control"  id="mail" placeholder="Enter your email" required>
                                    <button type="submit" class="newsletter-btn"><i class="fa-regular fa-paper-plane"></i></button>
                                </div>
                            </form>
                        </div>
                        <!-- Footer Newsletter Box End -->
                    </div>
                    <!-- Footer Links End -->                        
                </div>

                <div class="col-lg-12">
                    <!-- Footer Copyright Start -->
                    <div class="footer-copyright">
                        <!-- Footer Copyright Text Start -->
                        <div class="footer-copyright-text">
                            <p>Copyright © 2025 All Rights Reserved.</p>
                        </div>
                        <!-- Footer Copyright Text End -->

                        <!-- Footer Privacy Policy Start -->
                        <!--<div class="footer-privacy-policy">-->
                        <!--    <ul>-->
                        <!--        <li><a href="#">privacy policy</a></li>-->
                        <!--        <li><a href="#">terms & condition</a></li>-->
                        <!--    </ul>-->
                        <!--</div>-->
                        <!-- Footer Privacy Policy End -->
                    </div>
                    <!-- Footer Copyright End -->
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer End -->

    <!-- Jquery Library File -->
    <script src="js/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap js file -->
    <script src="js/bootstrap.min.js"></script>
    <!-- Validator js file -->
    <script src="js/validator.min.js"></script>
    <!-- SlickNav js file -->
    <script src="js/jquery.slicknav.js"></script>
    <!-- Swiper js file -->
    <script src="js/swiper-bundle.min.js"></script>
    <!-- Counter js file -->
    <script src="js/jquery.waypoints.min.js"></script>
    <script src="js/jquery.counterup.min.js"></script>
    <!-- Magnific js file -->
    <script src="js/jquery.magnific-popup.min.js"></script>
    <!-- SmoothScroll -->
    <script src="js/SmoothScroll.js"></script>
    <!-- Parallax js -->
    <script src="js/parallaxie.js"></script>
    <!-- MagicCursor js file -->
    <script src="js/gsap.min.js"></script>
    <script src="js/magiccursor.js"></script>
    <!-- Text Effect js file -->
    <script src="js/SplitText.js"></script>
    <script src="js/ScrollTrigger.min.js"></script>
    <!-- YTPlayer js File -->
    <script src="js/jquery.mb.YTPlayer.min.js"></script>
    <!-- Wow js file -->
    <script src="js/wow.min.js"></script>
    <!-- Main Custom js file -->
    <script src="js/function.js"></script>
    <script>
        function toggleBox() {
    const box = document.getElementById("showmenu");

    if (box.style.display === "none") {
        box.style.display = "block";
    } else {
        box.style.display = "none";
    }
}

    </script>
    <script>
$(document).ready(function () {
    $('#contactForms').on('submit', function (e) {
        e.preventDefault();

        let form = $(this);
        let url = form.attr('action');
        let formData = form.serialize();

        $('#submitBtn').prop('disabled', true).html('<span>Submitting...</span>');
        $('#msgSubmit').html('');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('#msgSubmit').html(
                        '<div style="color:#155724;background:#d4edda;padding:12px 15px;border-radius:6px;border:1px solid #c3e6cb;">' 
                        + response.message + 
                        '</div>'
                    );
                    $('#contactForm')[0].reset();
                } else {
                    $('#msgSubmit').html(
                        '<div style="color:#721c24;background:#f8d7da;padding:12px 15px;border-radius:6px;border:1px solid #f5c6cb;">' 
                        + response.message + 
                        '</div>'
                    );
                }

                $('#submitBtn').prop('disabled', false).html('<span>Submit Message</span>');
            },
            error: function (xhr, status, error) {
                $('#msgSubmit').html(
                    '<div style="color:#721c24;background:#f8d7da;padding:12px 15px;border-radius:6px;border:1px solid #f5c6cb;">Something went wrong. Please try again.</div>'
                );
                $('#submitBtn').prop('disabled', false).html('<span>Submit Message</span>');
                console.log(xhr.responseText);
            }
        });
    });
});
</script>
</body>
</html>