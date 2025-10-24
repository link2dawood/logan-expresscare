<?php include 'layouts/header.php'; ?>

    <!-- Page Header Start -->
    <div class="page-header bg-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <!-- Page Header Box Start -->
                    <div class="page-header-box">
                        <h1 class="text-anime-style-2" data-cursor="-opaque">Application <span>Submitted</span></h1>
                        <nav class="wow fadeInUp">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="./">home</a></li>
                                <li class="breadcrumb-item"><a href="staff-onboarding.php">staff onboarding</a></li>
                                <li class="breadcrumb-item active" aria-current="page">success</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- Page Header Box End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Success Message Section Start -->
    <div class="success-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="success-content wow fadeInUp">
                        <!-- Success Icon -->
                        <div class="success-icon">
                            <div class="checkmark-circle">
                                <div class="checkmark-background"></div>
                                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                                    <circle class="checkmark-circle-svg" cx="26" cy="26" r="25" fill="none"/>
                                    <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Success Message -->
                        <div class="success-message">
                            <h1>Application Successfully Submitted!</h1>
                            <p class="lead">Thank you for completing your staff onboarding application.</p>
                        </div>

                        <!-- Next Steps -->
                        <div class="next-steps-box">
                            <h3>What Happens Next?</h3>
                            <div class="steps-timeline">
                                <div class="timeline-item">
                                    <div class="timeline-icon">
                                        <i class="fas fa-envelope-open-text"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h4>Application Review</h4>
                                        <p>Our HR team will review your application and documents within 2-3 business days.</p>
                                    </div>
                                </div>

                                <div class="timeline-item">
                                    <div class="timeline-icon">
                                        <i class="fas fa-phone-alt"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h4>Contact & Interview</h4>
                                        <p>If your application meets our requirements, we'll contact you to schedule an interview.</p>
                                    </div>
                                </div>

                                <div class="timeline-item">
                                    <div class="timeline-icon">
                                        <i class="fas fa-user-check"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h4>Verification Process</h4>
                                        <p>We'll verify all your documents and certifications with relevant authorities.</p>
                                    </div>
                                </div>

                                <div class="timeline-item">
                                    <div class="timeline-icon">
                                        <i class="fas fa-handshake"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h4>Welcome Onboard</h4>
                                        <p>Once approved, you'll receive your official welcome package and start date.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Important Information -->
                        <div class="info-boxes">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <div class="info-icon">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <h4>Check Your Email</h4>
                                        <p>You should receive a confirmation email shortly. Please check your spam folder if you don't see it.</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="info-box">
                                        <div class="info-icon">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <h4>Response Time</h4>
                                        <p>We aim to respond to all applications within 2-3 business days. Thank you for your patience.</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="info-box">
                                        <div class="info-icon">
                                            <i class="fas fa-question-circle"></i>
                                        </div>
                                        <h4>Have Questions?</h4>
                                        <p>If you have any questions about your application, please don't hesitate to contact our HR team.</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="info-box">
                                        <div class="info-icon">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <h4>Application Reference</h4>
                                        <p>Please keep a copy of your submitted documents for your records.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="contact-hr-section">
                            <h3>Need to Contact Us?</h3>
                            <div class="hr-contact-info">
                                <div class="contact-item">
                                    <i class="fas fa-envelope"></i>
                                    <a href="mailto:hr@loganexpresscare.com.au">hr@loganexpresscare.com.au</a>
                                </div>
                                <div class="contact-item">
                                    <i class="fas fa-phone"></i>
                                    <a href="tel:+61234567890">+61 (0) 123 456 7890</a>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <a href="./" class="btn-default">Return to Homepage</a>
                            <a href="about.php" class="btn-default btn-secondary">Learn More About Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Success Message Section End -->

    <style>
        .success-section {
            padding: 80px 0;
            min-height: 70vh;
        }

        .success-content {
            max-width: 900px;
            margin: 0 auto;
            text-align: center;
        }

        .success-icon {
            margin-bottom: 40px;
        }

        .checkmark-circle {
            width: 150px;
            height: 150px;
            margin: 0 auto;
            position: relative;
            border-radius: 50%;
            animation: scaleIn 0.5s ease;
        }

        .checkmark-background {
            width: 100%;
            height: 100%;
            background: var(--accent-color);
            border-radius: 50%;
            opacity: 0.2;
        }

        .checkmark {
            width: 150px;
            height: 150px;
            position: absolute;
            top: 0;
            left: 0;
        }

        .checkmark-circle-svg {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            stroke-width: 2;
            stroke: var(--accent-color);
            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
            animation-delay: 0.3s;
        }

        .checkmark-check {
            transform-origin: 50% 50%;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            stroke-width: 3;
            stroke: var(--accent-color);
            animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
        }

        @keyframes stroke {
            100% {
                stroke-dashoffset: 0;
            }
        }

        @keyframes scaleIn {
            0% {
                transform: scale(0);
            }
            100% {
                transform: scale(1);
            }
        }

        .success-message h1 {
            font-size: 42px;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .success-message .lead {
            font-size: 20px;
            color: var(--text-color);
            margin-bottom: 50px;
        }

        .next-steps-box {
            background: var(--secondary-color);
            border-radius: 20px;
            padding: 50px 40px;
            margin-bottom: 50px;
            text-align: left;
        }

        .next-steps-box h3 {
            text-align: center;
            font-size: 32px;
            margin-bottom: 40px;
        }

        .steps-timeline {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .timeline-item {
            display: flex;
            gap: 25px;
            align-items: flex-start;
        }

        .timeline-icon {
            width: 60px;
            height: 60px;
            min-width: 60px;
            background: var(--accent-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .timeline-content h4 {
            font-size: 20px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .timeline-content p {
            color: var(--text-color);
            margin: 0;
        }

        .info-boxes {
            margin-bottom: 50px;
        }

        .info-box {
            background: white;
            border: 2px solid var(--divider-color);
            border-radius: 15px;
            padding: 30px;
            height: 100%;
            text-align: left;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .info-box:hover {
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transform: translateY(-5px);
        }

        .info-icon {
            width: 50px;
            height: 50px;
            background: var(--secondary-color);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .info-icon i {
            font-size: 24px;
            color: var(--accent-color);
        }

        .info-box h4 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .info-box p {
            margin: 0;
            font-size: 15px;
        }

        .contact-hr-section {
            background: var(--primary-color);
            color: white;
            border-radius: 15px;
            padding: 40px;
            margin-bottom: 40px;
        }

        .contact-hr-section h3 {
            color: white;
            margin-bottom: 25px;
        }

        .hr-contact-info {
            display: flex;
            justify-content: center;
            gap: 50px;
            flex-wrap: wrap;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
        }

        .contact-item i {
            color: var(--accent-color);
            font-size: 20px;
        }

        .contact-item a {
            color: white;
            text-decoration: none;
            font-weight: 600;
        }

        .contact-item a:hover {
            color: var(--accent-color);
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .btn-secondary {
            background: var(--secondary-color);
            color: var(--primary-color);
        }

        .btn-secondary::before {
            filter: brightness(0);
        }

        .btn-secondary:hover::after {
            background: var(--primary-color);
        }

        .btn-secondary:hover {
            color: white;
        }

        .btn-secondary:hover::before {
            filter: brightness(1) invert(1);
        }

        @media (max-width: 767px) {
            .success-message h1 {
                font-size: 28px;
            }

            .success-message .lead {
                font-size: 16px;
            }

            .next-steps-box {
                padding: 30px 20px;
            }

            .next-steps-box h3 {
                font-size: 24px;
            }

            .timeline-item {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .hr-contact-info {
                flex-direction: column;
                gap: 20px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .action-buttons .btn-default {
                width: 100%;
            }
        }
    </style>

<?php include 'layouts/footer.php'; ?>


