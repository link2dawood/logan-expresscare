<?php include 'layouts/header.php'; ?>

    <!-- Page Header Start -->
    <div class="page-header bg-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <!-- Page Header Box Start -->
                    <div class="page-header-box">
                        <h1 class="text-anime-style-2" data-cursor="-opaque">Book <span>appointment</span></h1>
                        <nav class="wow fadeInUp">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="./">home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">book appointment</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- Page Header Box End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Page Book appointment Start -->
    <div class="page-book-appointment">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <!-- Book Appointment Content Start -->
                    <div class="book-appointment-content">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">Book appointment</h3>
                            <h2 class="text-anime-style-2" data-cursor="-opaque">Schedule compassionate <span>care with ease</span></h2>
                        </div>
                        <!-- Section Title End -->

                        <!-- Contact Info Body Start -->
                        <div class="appointment-info-body">
                            <!-- Contact Info Item Start -->
                            <div class="contact-info-item wow fadeInUp" data-wow-delay="0.2s">
                                <!-- Icon Box Start -->
                                <div class="icon-box">
                                    <img src="images/icon-mail.svg" alt="">
                                </div>
                                <!-- Icon Box End -->

                                <!-- Contact Item Content Start -->
                                <div class="contact-item-content">
                                    <h3>Quick contact</h3>
                                    <p><a href="tel:+1232548963">+00-123-2548-963</a></p>
                                    <p><a href="mailto:info@domainname.com">info@domainname.com</a></p>
                                </div>
                                <!-- Contact Item Content End -->
                            </div>
                            <!-- Contact Info Item End -->
                            
                            <!-- Contact Info Item Start -->
                            <div class="contact-info-item wow fadeInUp" data-wow-delay="0.4s">
                                <!-- Icon Box Start -->
                                <div class="icon-box">
                                    <img src="images/icon-schedule.svg" alt="">
                                </div>
                                <!-- Icon Box End -->
                                
                                <!-- Contact Item Content Start -->
                                <div class="contact-item-content">
                                    <h3>Schedule</h3>
                                    <p>Mon - Fri 8:00 AM - 8:00 PM</p>
                                    <p>Saturday - Sunday  : Close</p>
                                </div>
                                <!-- Contact Item Content End -->
                            </div>
                            <!-- Contact Info Item End -->
                            
                            <!-- Contact Info Item Start -->
                            <div class="contact-info-item location-item wow fadeInUp" data-wow-delay="0.6s">
                                <!-- Icon Box Start -->
                                <div class="icon-box">
                                    <img src="images/icon-location.svg" alt="">
                                </div>
                                <!-- Icon Box End -->

                                <!-- Contact Item Content Start -->
                                <div class="contact-item-content">
                                    <h3>location</h3>
                                    <p>123 Maplewood Drive, Pinehill Residential Area, Near Greenview Senior Park, Pinehill, California - 90210, United States.</p>
                                </div>
                                <!-- Contact Item Content End -->
                            </div>
                            <!-- Contact Info Item End -->
                        </div>
                        <!-- Contact Info Body End -->
                    </div>
                    <!-- Book Appointment Content End -->
                </div>

                <div class="col-lg-6">
                    <!-- Book Appointment Form Start -->
                    <div class="contact-form wow fadeInUp" data-wow-delay="0.2s">
                        <form id="appointmentForm" action="#" method="POST" data-toggle="validator">
                            <div class="row">                                
                                <div class="form-group col-md-6 mb-4">
                                    <input type="text" name="fname" class="form-control" id="fname" placeholder="First Name" required>
                                    <div class="help-block with-errors"></div>
                                </div>
                                
                                <div class="form-group col-md-6 mb-4">
                                    <input type="text" name="lname" class="form-control" id="lname" placeholder="Last Name" required>
                                    <div class="help-block with-errors"></div>
                                </div>

                                <div class="form-group col-md-6 mb-4">
                                    <input type="text" name="phone" class="form-control" id="phone" placeholder="Phone No." required>
                                    <div class="help-block with-errors"></div>
                                </div>
                                
                                <div class="form-group col-md-6 mb-4">
                                    <input type="email" name ="email" class="form-control" id="email" placeholder="E-mail" required>
                                    <div class="help-block with-errors"></div>
                                </div>
                                
                                <div class="form-group col-md-6 mb-4">
                                    <select name="services" class="form-control form-select" id="services" required>
                                        <option value="" disabled selected>Services</option>
                                        <option value="social_&_recreational_activities">Social & Recreational Activities</option>
                                        <option value="memory_&_dementia_care">Memory & Dementia Care</option>
                                        <option value="healthy_meals_&_nutrition_plans">Healthy Meals & Nutrition Plans</option>
                                        <option value="24/7_skilled_nursing_care">24/7 Skilled Nursing Care</option>
                                        <option value="assisted_living_support">Assisted Living Support</option>
                                        <option value="respite_care_for_families">Respite Care for Families</option>
                                    </select>
                                    <div class="help-block with-errors"></div>
                                </div>
            
                                <div class="form-group col-md-6 mb-4">
                                    <input type="date" name="date" class="form-control" id="date" required>
                                    <div class="help-block with-errors"></div>
                                </div>

                                <div class="form-group col-md-12 mb-5">
                                    <textarea name="message" class="form-control" id="message" rows="4" placeholder="Write Message..."></textarea>
                                    <div class="help-block with-errors"></div>
                                </div>
            
                                <div class="col-md-12">
                                    <button type="submit" class="btn-default"><span>Submit Message</span></button>
                                    <div id="msgSubmit" class="h3 hidden"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- Book Appointment Form End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Book appointment End -->

    <!-- How It Work Section Start -->
    <div class="how-it-work bg-section dark-section parallaxie">
        <div class="container">
            <div class="row section-row">
                <div class="col-lg-12">
                    <!-- Section Title Start -->
                    <div class="section-title section-title-center">
                        <h3 class="wow fadeInUp">How it work</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">Your trusted partner in home <span>health and wellness</span></h2>
                    </div>
                    <!-- Section Title End -->
                </div>
            </div>

            <div class="row work-steps-list">
                <div class="col-lg-3 col-md-6">
                    <!-- How Steps Item Start -->
                    <div class="work-steps-item active wow fadeInUp">
                        <div class="icon-box">
                            <img src="images/icon-work-step-1.svg" alt="">
                        </div>
                        <div class="work-step-no">
                            <h3>01</h3>
                        </div>
                        <div class="work-step-content">
                            <h3>Free Consultation & Assessment</h3>
                            <p>We carefully match you with a caregiver or nurse who fits</p>
                        </div>
                        <div class="work-step-btn">
                            <a href="#" class="readmore-btn">learn more</a>
                        </div>
                    </div>
                    <!-- How Steps Item End -->
                </div>
               
                <div class="col-lg-3 col-md-6">
                    <!-- How Steps Item Start -->
                    <div class="work-steps-item wow fadeInUp" data-wow-delay="0.2s">
                        <div class="icon-box">
                            <img src="images/icon-work-step-2.svg" alt="">
                        </div>
                        <div class="work-step-no">
                            <h3>02</h3>
                        </div>
                        <div class="work-step-content">
                            <h3>Caregiver Matching & Introduction</h3>
                            <p>We carefully match you with a caregiver or nurse who fits</p>
                        </div>
                        <div class="work-step-btn">
                            <a href="#" class="readmore-btn">learn more</a>
                        </div>
                    </div>
                    <!-- How Steps Item End -->
                </div>
               
                <div class="col-lg-3 col-md-6">
                    <!-- How Steps Item Start -->
                    <div class="work-steps-item wow fadeInUp" data-wow-delay="0.4s">
                        <div class="icon-box">
                            <img src="images/icon-work-step-3.svg" alt="">
                        </div>
                        <div class="work-step-no">
                            <h3>03</h3>
                        </div>
                        <div class="work-step-content">
                            <h3>Ongoing Support & Adjustments</h3>
                            <p>We carefully match you with a caregiver or nurse who fits</p>
                        </div>
                        <div class="work-step-btn">
                            <a href="#" class="readmore-btn">learn more</a>
                        </div>
                    </div>
                    <!-- How Steps Item End -->
                </div>
               
                <div class="col-lg-3 col-md-6">
                    <!-- How Steps Item Start -->
                    <div class="work-steps-item wow fadeInUp" data-wow-delay="0.6s">
                        <div class="icon-box">
                            <img src="images/icon-work-step-4.svg" alt="">
                        </div>
                        <div class="work-step-no">
                            <h3>04</h3>
                        </div>
                        <div class="work-step-content">
                            <h3>Free Consultation & Assessment</h3>
                            <p>We carefully match you with a caregiver or nurse who fits</p>
                        </div>
                        <div class="work-step-btn">
                            <a href="#" class="readmore-btn">learn more</a>
                        </div>
                    </div>
                    <!-- How Steps Item End -->
                </div>

                <div class="col-lg-12">
                    <!-- Section Footer Text Start-->
                    <div class="section-footer-text wow fadeInUp" data-wow-delay="0.8s">
                        <p>Compassionate care to enhance daily living. <a href="contact.html">Contact us today!</a></p>
                    </div>
                    <!-- Section Footer Text End-->
                </div>
            </div>
        </div>
    </div>
    <!-- How It Work Section End -->

    <!-- Why Choose Us Section Start -->
    <div class="why-choose-us">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <!-- Why Choose Content Start -->
                    <div class="why-choose-content">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">Why choose us</h3>
                            <h2 class="text-anime-style-2" data-cursor="-opaque">A community where seniors thrive, <span>not just survive</span></h2>
                        </div>
                        <!-- Section Title End -->
                        
                        <!-- Why Choose Item List Start -->
                        <div class="why-choose-item-list">
                            <!-- Why Choose Item Start -->
                            <div class="why-choose-item wow fadeInUp" data-wow-delay="0.2s">
                                <div class="icon-box">
                                    <img src="images/icon-why-choose-1.svg" alt="">
                                </div>
                                <div class="why-choose-item-content">
                                    <h3>Compassionate & Experienced Staff</h3>
                                    <p>Our modern, fully-equipped facility is designed with senior comfort and safety in mind. With advanced health monitoring.</p>
                                </div>
                            </div>
                            <!-- Why Choose Item End -->
                            
                            <!-- Why Choose Item Start -->
                            <div class="why-choose-item wow fadeInUp" data-wow-delay="0.4s">
                                <div class="icon-box">
                                    <img src="images/icon-why-choose-2.svg" alt="">
                                </div>
                                <div class="why-choose-item-content">
                                    <h3>State-of-the-Art Facilities and Services</h3>
                                    <p>Our modern, fully-equipped facility is designed with senior comfort and safety in mind. With advanced health monitoring.</p>
                                </div>
                            </div>
                            <!-- Why Choose Item End -->
                            
                            <!-- Why Choose Item Start -->
                            <div class="why-choose-item wow fadeInUp" data-wow-delay="0.6s">
                                <div class="icon-box">
                                    <img src="images/icon-why-choose-3.svg" alt="">
                                </div>
                                <div class="why-choose-item-content">
                                    <h3>Holistic Approach to Senior Well-Being</h3>
                                    <p>Our modern, fully-equipped facility is designed with senior comfort and safety in mind. With advanced health monitoring.</p>
                                </div>
                            </div>
                            <!-- Why Choose Item End -->
                        </div>
                        <!-- Why Choose Item List End -->
                    </div>
                    <!-- Why Choose Content End -->
                </div>

                <div class="col-lg-6">
                    <!-- Why Choose Images Start -->
                    <div class="why-choose-images">
                        <!-- Why Choose Image 1 Start -->
                        <div class="why-choose-img-1">
                            <figure class="image-anime reveal">
                                <img src="images/why-choose-img-1.jpg" alt="">
                            </figure>
                            
                            <!-- Contact Us Circle Start -->
                            <div class="contact-us-circle">
                                <a href="contact.html">
                                    <img src="images/contact-us-circle.svg" alt="">
                                </a>
                            </div>
                            <!-- Contact Us Circle End -->
                        </div>
                        <!-- Why Choose Image 1 End -->
                        
                        <!-- Why Choose Image 2 Start -->
                        <div class="why-choose-img-2">
                            <figure class="image-anime">
                                <img src="images/why-choose-img-2.jpg" alt="">
                            </figure>
                        </div>
                        <!-- Why Choose Image 2 End -->
                    </div>
                    <!-- Why Choose Images End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Why Choose Us Section End -->

    <!-- CTA Box Section Start -->
    <div class="cta-box bg-section">
        <div class="container">
            <div class="row section-row">
                <div class="col-lg-12">
                    <!-- CTA Box Content Start -->
                    <div class="cta-box-content">
                        <!-- Section Title Start -->
                        <div class="section-title section-title-center">
                            <h3 class="wow fadeInUp">Join us today</h3>
                            <h2 class="text-anime-style-2" data-cursor="-opaque">Schedule a personalized tour or <span>care consultation</span></h2>
                        </div>
                        <!-- Section Title End -->
                        
                        <!-- CTA Box Button Start -->
                        <div class="cta-box-btn wow fadeInUp" data-wow-delay="0.2s">
                            <a href="book-appointment.html" class="btn-default">Book appointment</a>
                        </div>
                        <!-- CTA Box Button End -->
                    </div>
                    <!-- CTA Box Content End -->
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <!-- CTA Box Image Start -->
                    <div class="cta-box-image">
                        <figure>
                            <img src="images/cta-box-image.png" alt="">
                        </figure>
                    </div>
                    <!-- CTA Box Image End -->
                </div>
            </div>
        </div>
    </div>
    <!-- CTA Box Section End -->

    <!-- Our FAQs Section Start -->
    <div class="our-faqs">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <!-- FAQs Content Start -->
                    <div class="faqs-content">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">Frequently asked questions</h3>
                            <h2 class="text-anime-style-2" data-cursor="-opaque">Get the answers you need about <span>our senior care</span></h2>
                            <p class="wow fadeInUp" data-wow-delay="0.2s">Reach out today to learn more about our personalized services, schedule a free visit, or speak with a care specialist.</p>
                        </div>
                        <!-- Section Title End -->

                        <!-- Our Faqs Button Start -->
                        <div class="our-faqs-btn wow fadeInUp" data-wow-delay="0.4s">
                            <a href="contact.html" class="btn-default">Contact Us Now</a>
                        </div>
                        <!-- Our Faqs Button End -->
                    </div>
                    <!-- FAQs Content End -->
                </div>

                <div class="col-lg-6">
                    <!-- FAQ Accordion Start -->
                    <div class="faq-accordion" id="accordion">
                        <!-- FAQ Item Start -->
                        <div class="accordion-item wow fadeInUp">
                            <h2 class="accordion-header" id="heading1">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                    Q1. What types of care services do you offer?
                                </button>
                            </h2>
                            <div id="collapse1" class="accordion-collapse collapse" aria-labelledby="heading1" data-bs-parent="#accordion">
                                <div class="accordion-body">
                                    <p>We offer a variety of care options, including assisted living, memory care, services, and palliative care. Our care plans are customized to meet the unique needs of each resident, ensuring that they receive.</p>
                                </div>
                            </div>
                        </div>
                        <!-- FAQ Item End -->

                        <!-- FAQ Item Start -->
                        <div class="accordion-item wow fadeInUp" data-wow-delay="0.2s">
                            <h2 class="accordion-header" id="heading2">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                    Q2. How do you ensure the safety of residents?
                                </button>
                            </h2>
                            <div id="collapse2" class="accordion-collapse collapse show" aria-labelledby="heading2" data-bs-parent="#accordion">
                                <div class="accordion-body">
                                    <p>We offer a variety of care options, including assisted living, memory care, services, and palliative care. Our care plans are customized to meet the unique needs of each resident, ensuring that they receive.</p>
                                </div>
                            </div>
                        </div>
                        <!-- FAQ Item End -->

                        <!-- FAQ Item Start -->
                        <div class="accordion-item wow fadeInUp" data-wow-delay="0.4s">
                            <h2 class="accordion-header" id="heading3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                    Q3. Can I schedule a tour of your facility?
                                </button>
                            </h2>
                            <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="heading3" data-bs-parent="#accordion">
                                <div class="accordion-body">
                                    <p>We offer a variety of care options, including assisted living, memory care, services, and palliative care. Our care plans are customized to meet the unique needs of each resident, ensuring that they receive.</p>
                                </div>
                            </div>
                        </div>
                        <!-- FAQ Item End -->

                        <!-- FAQ Item Start -->
                        <div class="accordion-item wow fadeInUp" data-wow-delay="0.6s">
                            <h2 class="accordion-header" id="heading4">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                    Q4. How do you personalize care for each resident?
                                </button>
                            </h2>
                            <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="heading4" data-bs-parent="#accordion">
                                <div class="accordion-body">
                                    <p>We offer a variety of care options, including assisted living, memory care, services, and palliative care. Our care plans are customized to meet the unique needs of each resident, ensuring that they receive.</p>
                                </div>
                            </div>
                        </div>
                        <!-- FAQ Item End -->

                        <!-- FAQ Item Start -->
                        <div class="accordion-item wow fadeInUp" data-wow-delay="0.8s">
                            <h2 class="accordion-header" id="heading5">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
                                    Q5. What types of activities are available for residents?
                                </button>
                            </h2>
                            <div id="collapse5" class="accordion-collapse collapse" aria-labelledby="heading5" data-bs-parent="#accordion">
                                <div class="accordion-body">
                                    <p>We offer a variety of care options, including assisted living, memory care, services, and palliative care. Our care plans are customized to meet the unique needs of each resident, ensuring that they receive.</p>
                                </div>
                            </div>
                        </div>
                        <!-- FAQ Item End -->
                    </div>
                    <!-- FAQ Accordion End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Our FAQs Section End -->

    <?php include 'layouts/footer.php'; ?>