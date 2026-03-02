<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Logan Express Care | NDIS Support Services</title>
	<meta name="description" content="Providing compassionate, professional, reliable home nursing care tailored to your needs." />

	<!-- Bootstrap 5 -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Bootstrap Icons -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">

	<style>
		:root{
			/* Primary Colors */
			--navy:#253873;
			--navy-light:#3a4f8a;
			--navy-dark:#1a2856;
			--navy-50:#f0f2f8;
			--navy-100:#d9dff0;
			--navy-200:#b3c0e1;

			/* Gold Colors */
			--gold:#E2A76F;
			--gold-light:#e8b88a;
			--gold-dark:#c98a52;
			--gold-50:#fdf6f0;
			--gold-100:#f5e6d6;
			--gold-200:#ebccad;

			/* Neutral Scale derived from navy tint */
			--bg:#fafbfc;
			--surface:#ffffff;
			--text:#1a1f36;
			--text-secondary:#4a5568;
			--text-muted:#718096;
			--border:#e2e8f0;

			/* Shadows with navy tint */
			--shadow-sm:0 1px 3px rgba(37,56,115,0.08);
			--shadow-md:0 4px 6px -1px rgba(37,56,115,0.1),0 2px 4px -1px rgba(37,56,115,0.06);
			--shadow-lg:0 10px 15px -3px rgba(37,56,115,0.12),0 4px 6px -2px rgba(37,56,115,0.08);
			--shadow-xl:0 20px 25px -5px rgba(37,56,115,0.15),0 10px 10px -5px rgba(37,56,115,0.1);
			--shadow-gold:0 10px 30px -5px rgba(226,167,111,0.3);

			--radius-sm:8px;
			--radius-md:12px;
			--radius-lg:16px;
			--radius-xl:24px;
			--transition:all 0.3s cubic-bezier(0.4,0,0.2,1);
		}

		*{box-sizing:border-box}

		body{ 
			color:var(--text); 
			background:var(--bg);
			font-family:'Inter',-apple-system,BlinkMacSystemFont,sans-serif;
			line-height:1.6;
			overflow-x:hidden;
		}

		/* Typography */
		h1,h2,h3,h4,h5,h6{
			font-weight:700;
			line-height:1.2;
			letter-spacing:-0.02em;
			color:var(--navy);
		}

		.font-serif{
			font-family:'Playfair Display',Georgia,serif;
			font-weight:400;
		}

		/* Navigation */
		.navbar{
			backdrop-filter:saturate(180%) blur(20px);
			background:rgba(255,255,255,0.9)!important;
			border-bottom:1px solid var(--border);
			padding:1rem 0;
			transition:var(--transition);
		}

		.navbar.scrolled{
			background:rgba(255,255,255,0.98)!important;
			box-shadow:var(--shadow-md);
			padding:0.75rem 0;
		}

		.navbar-brand{
			font-weight:800;
			font-size:1.5rem;
			color:var(--navy)!important;
			display:flex;
			align-items:center;
			gap:0.75rem;
		}

		.brand-icon{
			width:42px;
			height:42px;
			background:linear-gradient(135deg,var(--navy) 0%,var(--navy-light) 100%);
			border-radius:var(--radius-md);
			display:flex;
			align-items:center;
			justify-content:center;
			color:var(--gold);
			font-size:1.25rem;
			box-shadow:var(--shadow-md);
			transition:var(--transition);
		}

		.navbar-brand:hover .brand-icon{
			transform:rotate(5deg) scale(1.05);
			box-shadow:var(--shadow-gold);
		}

		.nav-link{
			font-weight:500;
			color:var(--text-secondary)!important;
			padding:0.5rem 1rem!important;
			border-radius:var(--radius-sm);
			transition:var(--transition);
			position:relative;
		}

		.nav-link:hover{
			color:var(--navy)!important;
			background:var(--navy-50);
		}

		.nav-link.active{
			color:var(--navy)!important;
			background:var(--navy-100);
		}

		.btn-primary{
			background:var(--navy);
			border:2px solid var(--navy);
			color:var(--surface);
			font-weight:600;
			padding:0.625rem 1.5rem;
			border-radius:var(--radius-md);
			transition:var(--transition);
			box-shadow:var(--shadow-md);
		}

		.btn-primary:hover{
			background:var(--navy-dark);
			border-color:var(--navy-dark);
			transform:translateY(-2px);
			box-shadow:var(--shadow-lg);
		}

		/* Hero Section */
		.hero{
			position:relative;
			padding:6rem 0 4rem;
			overflow:hidden;
			background:linear-gradient(135deg,var(--surface) 0%,var(--navy-50) 100%);
		}

		.hero-pattern{
			position:absolute;
			inset:0;
			opacity:0.4;
			background-image:radial-gradient(circle at 1px 1px,var(--navy) 1px,transparent 0);
			background-size:40px 40px;
			mask-image:linear-gradient(to bottom,transparent,black 20%,black 80%,transparent);
		}

		.hero-content{
			position:relative;
			z-index:1;
		}

		.hero-badge{
			display:inline-flex;
			align-items:center;
			gap:0.5rem;
			padding:0.5rem 1rem;
			background:var(--gold-100);
			border:1px solid var(--gold-200);
			border-radius:999px;
			font-size:0.875rem;
			font-weight:600;
			color:var(--navy-dark);
			margin-bottom:1.5rem;
			animation:fadeInUp 0.6s ease;
		}

		.hero-badge::before{
			content:'';
			width:8px;
			height:8px;
			background:var(--gold);
			border-radius:50%;
			animation:pulse 2s infinite;
		}

		@keyframes pulse{
			0%,100%{opacity:1;transform:scale(1)}
			50%{opacity:0.7;transform:scale(1.2)}
		}

		@keyframes fadeInUp{
			from{opacity:0;transform:translateY(20px)}
			to{opacity:1;transform:translateY(0)}
		}

		.hero-title{
			font-size:36px;
			font-weight:800;
			color:var(--navy);
			margin-bottom:1.5rem;
			line-height:1.1;
			animation:fadeInUp 0.6s ease 0.1s both;
		}

		.hero-title em{
			font-family:'Playfair Display',serif;
			font-style:italic;
			font-weight:400;
			color:var(--gold);
			position:relative;
		}

		.hero-title em::after{
			content:'';
			position:absolute;
			bottom:2px;
			left:-4px;
			right:-4px;
			height:12px;
			background:var(--gold);
			opacity:0.2;
			z-index:-1;
			border-radius:4px;
		}

		.hero-subtitle{
			font-size:1rem;
			color:var(--text-secondary);
			max-width:600px;
			margin-bottom:2rem;
			line-height:1.7;
			animation:fadeInUp 0.6s ease 0.2s both;
		}

		.hero-stats{
			display:flex;
			gap:2.5rem;
			margin-top:3rem;
			padding-top:2rem;
			border-top:1px solid var(--border);
			animation:fadeInUp 0.6s ease 0.3s both;
		}

		.stat-item h4{
			font-size:2rem;
			font-weight:800;
			color:var(--navy);
			margin-bottom:0.25rem;
		}

		.stat-item p{
			font-size:0.875rem;
			color:var(--text-muted);
			margin:0;
			font-weight:500;
		}

		/* Form Card */
		.form-card{
			background:var(--surface);
			border-radius:var(--radius-xl);
			box-shadow:var(--shadow-xl);
			padding:2.5rem;
			position:relative;
			overflow:hidden;
			animation:fadeInUp 0.6s ease 0.4s both;
			border:1px solid var(--border);
		}

		.form-card::before{
			content:'';
			position:absolute;
			top:0;
			left:0;
			right:0;
			height:4px;
			background:linear-gradient(90deg,var(--navy),var(--gold));
		}

		.form-header{
			margin-bottom:2rem;
		}

		.form-header h3{
			font-size:1.5rem;
			color:var(--navy);
			margin-bottom:0.5rem;
		}

		.form-header p{
			color:var(--text-muted);
			margin:0;
			font-size:0.9375rem;
		}

		.form-floating{
			position:relative;
			margin-bottom:1rem;
		}

		.form-floating label{
			font-weight:500;
			color:var(--text-secondary);
		}

		.form-control,.form-select{
			border:2px solid var(--border);
			border-radius:var(--radius-md);
			padding:1rem 0.875rem;
			font-size:0.9375rem;
			transition:var(--transition);
			background:var(--surface);
		}

		.form-control:focus,.form-select:focus{
			border-color:var(--gold);
			box-shadow:0 0 0 4px rgba(226,167,111,0.15);
		}

		.form-floating>.form-control,.form-floating>.form-select{
			padding-top:1.625rem;
			padding-bottom:0.625rem;
		}

		.form-floating>label{
			padding:1rem 0.875rem;
		}
		.subtitle{
			text-align:center;

		} 
		.main-heading{
			font-size: clamp(2rem, 4vw, 3rem);
			color: var(--navy);
			margin-bottom: 1rem;
			text-align:center;
		}
		.fet-badge{
			display: inline-block;
			background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
			color: white;
			padding: 12px 30px;
			border-radius: 50px;
			font-size: 14px;
			font-weight: 600;
			text-transform: uppercase;
			letter-spacing: 1px;
			margin-bottom: 40px;
			box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
			animation: fadeInDown 0.8s ease-out;
		}
		.btn-submit{
			width:100%;
			background:linear-gradient(135deg,var(--navy) 0%,var(--navy-light) 100%);
			color:var(--surface);
			border:none;
			padding:1rem;
			font-weight:600;
			border-radius:var(--radius-md);
			margin-top:1rem;
			transition:var(--transition);
			position:relative;
			overflow:hidden;
		}

		.btn-submit::before{
			content:'';
			position:absolute;
			top:0;
			left:-100%;
			width:100%;
			height:100%;
			background:linear-gradient(90deg,transparent,rgba(226,167,111,0.3),transparent);
			transition:0.5s;
		}

		.btn-submit:hover::before{
			left:100%;
		}

		.btn-submit:hover{
			transform:translateY(-2px);
			box-shadow:var(--shadow-gold);
			color: white;
		}

		/* Feature Cards */
		.feature-grid{
			display:grid;
			grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
			gap:1.5rem;
			margin-top:3rem;
		}

		.feature-card{
			background:var(--surface);
			border:1px solid var(--border);
			border-radius:var(--radius-lg);
			padding:2rem;
			transition:var(--transition);
			position:relative;
			overflow:hidden;
		}

		.feature-card::before{
			content:'';
			position:absolute;
			top:0;
			left:0;
			width:4px;
			height:100%;
			background:var(--gold);
			transform:scaleY(0);
			transition:var(--transition);
		}

		.feature-card:hover{
			transform:translateY(-4px);
			box-shadow:var(--shadow-xl);
			border-color:var(--gold-200);
		}

		.feature-card:hover::before{
			transform:scaleY(1);
		}
		.features-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
			gap: 30px;
			margin: 0 auto 50px auto;
			text-align: left;
		}
		.features-grid h3 {
			font-size: 1.25rem;
			color: var(--navy);
			margin-bottom: 0.75rem;
		}
		.cta-text {
			font-size: 20px;
			color: #334155;
			line-height: 1.8;
			text-align: center;
			margin: 0;
			font-weight: 500;
		}
		.feature-icon{
			width:56px;
			height:56px;
			background:var(--navy-50);
			border-radius:var(--radius-md);
			display:flex;
			align-items:center;
			justify-content:center;
			font-size:1.5rem;
			color:var(--navy);
			margin-bottom:1.25rem;
			transition:var(--transition);
		}

		.feature-card:hover .feature-icon{
			background:var(--navy);
			color:var(--gold);
			transform:scale(1.1);
		}

		.feature-card h4{
			font-size:1.125rem;
			color:var(--navy);
			margin-bottom:0.75rem;
		}

		.feature-card p{
			color:var(--text-secondary);
			font-size:0.9375rem;
			line-height:1.6;
			margin:0;
		}

		/* Services Section */
		.section{
			padding:6rem 0;
			position:relative;
		}

		.section-header{
			text-align:center;
			max-width:700px;
			margin:0 auto 4rem;
		}

		.section-label{
			display:inline-block;
			padding:0.375rem 1rem;
			background:var(--gold-100);
			color:var(--navy-dark);
			font-size:0.875rem;
			font-weight:600;
			border-radius:999px;
			margin-bottom:1rem;
			text-transform:uppercase;
			letter-spacing:0.05em;
		}

		.section-title{
			font-size:clamp(2rem,4vw,3rem);
			color:var(--navy);
			margin-bottom:1rem;
		}

		.section-subtitle{
			color:var(--text-secondary);
			font-size:1.125rem;
			line-height:1.7;
		}

		.services-grid{
			display:grid;
			grid-template-columns:repeat(auto-fit,minmax(350px,1fr));
			gap:2rem;
		}

		.service-card{
			background:var(--surface);
			border-radius:var(--radius-xl);
			overflow:hidden;
			box-shadow:var(--shadow-md);
			transition:var(--transition);
			position:relative;
			display:flex;
			flex-direction:column;
			border:1px solid var(--border);
		}

		.service-card:hover{
			transform:translateY(-8px);
			box-shadow:var(--shadow-xl);
			border-color:var(--gold-200);
		}

		.service-image{
			height:200px;
			background:linear-gradient(135deg,var(--navy) 0%,var(--navy-light) 100%);
			position:relative;
			overflow:hidden;
		}

		.service-image::after{
			content:'';
			position:absolute;
			inset:0;
			background:linear-gradient(to bottom,transparent 0%,rgba(37,56,115,0.8) 100%);
		}

		.service-icon{
			position:absolute;
			bottom:1.5rem;
			left:1.5rem;
			width:64px;
			height:64px;
			background:var(--surface);
			border-radius:var(--radius-lg);
			display:flex;
			align-items:center;
			justify-content:center;
			font-size:1.75rem;
			color:var(--navy);
			box-shadow:var(--shadow-lg);
			z-index:2;
			transition:var(--transition);
		}

		.service-card:hover .service-icon{
			transform:scale(1.1) rotate(5deg);
			background:var(--gold);
			color:var(--surface);
		}

		.service-content{
			padding:2rem;
			flex:1;
			display:flex;
			flex-direction:column;
		}

		.service-content h3{
			font-size:1.25rem;
			color:var(--navy);
			margin-bottom:0.75rem;
		}

		.service-content p{
			color:var(--text-secondary);
			font-size:0.9375rem;
			line-height:1.7;
			margin-bottom:1.5rem;
			flex:1;
		}

		.service-link{
			display:inline-flex;
			align-items:center;
			gap:0.5rem;
			color:var(--navy);
			font-weight:600;
			font-size:0.9375rem;
			transition:var(--transition);
		}

		.service-link:hover{
			gap:0.75rem;
			color:var(--gold-dark);
		}

		/* Contact Section */
		.contact-section{
			background:var(--navy);
			color:var(--surface);
			position:relative;
			overflow:hidden;
		}

		.contact-section::before{
			content:'';
			position:absolute;
			top:-50%;
			right:-20%;
			width:800px;
			height:800px;
			background:radial-gradient(circle,rgba(226,167,111,0.15) 0%,transparent 70%);
			border-radius:50%;
		}

		.contact-grid{
			display:grid;
			grid-template-columns:1fr 1.5fr;
			gap:4rem;
			align-items:center;
		}

		.contact-info h2{
			font-size:2.5rem;
			margin-bottom:1.5rem;
			color:var(--surface);
		}

		.contact-info>p{
			color:rgba(255,255,255,0.8);
			font-size:1.125rem;
			line-height:1.7;
			margin-bottom:2.5rem;
		}

		.contact-item{
			display:flex;
			align-items:flex-start;
			gap:1rem;
			margin-bottom:1.5rem;
		}

		.contact-item-icon{
			width:48px;
			height:48px;
			background:rgba(226,167,111,0.2);
			border-radius:var(--radius-md);
			display:flex;
			align-items:center;
			justify-content:center;
			font-size:1.25rem;
			color:var(--gold);
			flex-shrink:0;
			transition:var(--transition);
		}

		.contact-item:hover .contact-item-icon{
			background:var(--gold);
			color:var(--navy);
			transform:scale(1.1);
		}

		.contact-item-content h4{
			font-size:1rem;
			margin-bottom:0.25rem;
			color:var(--surface);
		}

		.contact-item-content p{
			color:rgba(255,255,255,0.7);
			margin:0;
			font-size:0.9375rem;
		}

		.contact-item-content a{
			color:var(--gold);
			text-decoration:none;
			font-weight:500;
			transition:var(--transition);
		}

		.contact-item-content a:hover{
			color:var(--surface);
			text-decoration:underline;
		}

		.contact-map{
			background:var(--surface);
			border-radius:var(--radius-xl);
			overflow:hidden;
			box-shadow:var(--shadow-xl);
			height:100%;
			min-height:400px;
			border:4px solid rgba(226,167,111,0.3);
		}

		.contact-map iframe{
			width:100%;
			height:100%;
			min-height:400px;
			border:none;
		}

		/* Footer */
		.footer{
			background:var(--navy-dark);
			color:rgba(255,255,255,0.6);
			padding:3rem 0 1.5rem;
			border-top:1px solid rgba(226,167,111,0.2);
		}

		.footer-content{
			display:flex;
			justify-content:space-between;
			align-items:center;
			flex-wrap:wrap;
			gap:1.5rem;
		}

		.footer-brand{
			display:flex;
			align-items:center;
			gap:0.75rem;
			color:var(--surface);
			font-weight:700;
			font-size:1.25rem;
		}

		.footer-links{
			display:flex;
			gap:2rem;
		}

		.footer-links a{
			color:rgba(255,255,255,0.7);
			text-decoration:none;
			font-weight:500;
			transition:var(--transition);
		}

		.footer-links a:hover{
			color:var(--gold);
		}

		.footer-bottom{
			margin-top:2rem;
			padding-top:2rem;
			border-top:1px solid rgba(255,255,255,0.1);
			text-align:center;
			font-size:0.875rem;
		}

		/* Toast Notification */
		.toast-container{
			position:fixed;
			bottom:2rem;
			right:2rem;
			z-index:1050;
		}

		.custom-toast{
			background:var(--surface);
			border-left:4px solid var(--gold);
			border-radius:var(--radius-md);
			box-shadow:var(--shadow-xl);
			padding:1.25rem 1.5rem;
			display:flex;
			align-items:center;
			gap:1rem;
			min-width:300px;
			animation:slideIn 0.3s ease;
			border:1px solid var(--border);
		}

		@keyframes slideIn{
			from{transform:translateX(100%);opacity:0}
			to{transform:translateX(0);opacity:1}
		}

		.toast-icon{
			width:40px;
			height:40px;
			background:var(--gold-100);
			border-radius:50%;
			display:flex;
			align-items:center;
			justify-content:center;
			color:var(--navy);
			font-size:1.25rem;
		}

		.toast-content h4{
			font-size:1rem;
			margin-bottom:0.25rem;
			color:var(--navy);
		}

		.toast-content p{
			font-size:0.875rem;
			color:var(--text-secondary);
			margin:0;
		}

		/* Responsive */
		@media (max-width:991px){
			.hero{padding:4rem 0 2rem}
			.contact-grid{grid-template-columns:1fr;gap:3rem}
			.services-grid{grid-template-columns:1fr}
			.hero-stats{flex-wrap:wrap;gap:1.5rem}
		}

		@media (max-width:768px){
			.form-card{padding:1.5rem}
			.section{padding:4rem 0}
			.footer-content{flex-direction:column;text-align:center}
			.footer-links{flex-wrap:wrap;justify-content:center}
		}

		/* Loading State */
		.btn-loading{
			position:relative;
			color:transparent!important;
		}

		.btn-loading::after{
			content:'';
			position:absolute;
			width:20px;
			height:20px;
			top:50%;
			left:50%;
			margin-left:-10px;
			margin-top:-10px;
			border:2px solid rgba(255,255,255,0.3);
			border-radius:50%;
			border-top-color:var(--surface);
			animation:spin 0.8s linear infinite;
		}

		@keyframes spin{
			to{transform:rotate(360deg)}
		}

		/* Gold accent button */
		.btn-gold{
			background:var(--gold);
			border:2px solid var(--gold);
			color:white;
			font-weight:600;
			padding:0.625rem 1.5rem;
			border-radius:var(--radius-md);
			transition:var(--transition);
		}

		.btn-gold:hover{
			background:var(--gold-dark);
			border-color:var(--gold-dark);
			color:white;
			transform:translateY(-2px);
			box-shadow:var(--shadow-gold);
		}

		.btn-outline-gold{
			background:transparent;
			border:2px solid var(--gold);
			color:var(--gold);
			font-weight:600;
			padding:0.625rem 1.5rem;
			border-radius:var(--radius-md);
			transition:var(--transition);
		}

		.btn-outline-gold:hover{
			background:var(--gold);
			color:var(--navy-dark);
			transform:translateY(-2px);
		}
	</style>
</head>

<body id="top">

	<!-- Navigation -->
	<nav class="navbar navbar-expand-lg sticky-top">
		<div class="container">
			<a class="navbar-brand" href="#top">
				<img src="images/logo-main.png" style="width: 160px;">
			</a>

			<button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
				<i class="bi bi-list fs-2" style="color:var(--navy)"></i>
			</button>

			<div class="collapse navbar-collapse" id="mainNav">
				<ul class="navbar-nav ms-auto align-items-lg-center gap-2">
					<li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
					<li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
					<li class="nav-item ms-lg-3">
						<a class="btn btn-primary" href="#enquiry">
							<i class="bi bi-calendar-check me-2"></i>Book Consultation
						</a>
					</li>
				</ul>
			</div>
		</div>
	</nav>

	<!-- Hero Section -->
	<section class="hero">
		<div class="hero-pattern"></div>

		<div class="container hero-content">
			<div class="row g-5 align-items-center">
				<div class="col-lg-6">
					<div class="hero-badge">
						<i class="bi bi-shield-check me-1"></i> NDIS Trusted Provider
					</div>

					<h1 class="hero-title">
						Logan Express Care — Empowering Lives, Enabling Independence
						<em>Professional Disability & Support Services</em>
					</h1>

					<p class="hero-subtitle">
						At Logan Express Care, we deliver compassionate, person-centred support services designed to enhance independence, wellbeing, and quality of life.We work collaboratively with individuals, families, and representatives to provide tailored supports that promote dignity, choice, and confidence in everyday living.

					</p>

					<div class="d-flex flex-wrap gap-3">
						<a href="#enquiry" class="btn btn-gold btn-lg">
							<i class="bi bi-arrow-right-circle me-2"></i>Start Your Journey
						</a>
						<a href="#services" class="btn btn-outline-dark btn-lg border-2">
							Explore Services
						</a>
					</div>

					<div class="hero-stats">
						<div class="stat-item">
							<h4>500+</h4>
							<p>Families Supported</p>
						</div>
						<div class="stat-item">
							<h4>24/7</h4>
							<p>Care Available</p>
						</div>
						<div class="stat-item">
							<h4>100%</h4>
							<p>NDIS Compliant</p>
						</div>
					</div>

					<div class="feature-grid d-none d-lg-grid">
						<div class="feature-card">
							<div class="feature-icon">
								<i class="bi bi-shield-check"></i>
							</div>
							<h4>Trusted Professionals</h4>
							<p>Qualified nurses and support workers with extensive NDIS experience</p>
						</div>
						<div class="feature-card">
							<div class="feature-icon">
								<i class="bi bi-clock-history"></i>
							</div>
							<h4>Flexible Scheduling</h4>
							<p>Care when you need it, tailored to your lifestyle and preferences</p>
						</div>
					</div>
				</div>

				<div class="col-lg-6">
					<div class="form-card" id="enquiry">
						<div class="form-header">
							<h3>Request a Free Consultation</h3>
							<p>Fill in your details and we'll contact you within 24 hours</p>
						</div>

						<form id="consultationForm" method="POST" action="send-mail.php" novalidate>
							<div class="row g-3">
								<div class="col-md-6">
									<div class="form-floating">
										<select class="form-select" id="userType" name="userType" required>
											<option value="" selected disabled>I am a...</option>
											<option value="NDIS Participant">NDIS Participant</option>
											<option value="Support Coordinator">Support Coordinator</option>
											<option value="Family Member">Family Member</option>
											<option value="Healthcare Provider">Healthcare Provider</option>
										</select>
										<label for="userType">Your Role *</label>
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-floating">
										<select class="form-select" id="serviceInterest" name="serviceInterest" required>
											<option value="" selected disabled>Service needed</option>
											<option value="Community Nursing">Community Nursing</option>
											<option value="Community Participation">Community Participation</option>
											<option value="Transport Services">Transport Services</option>
											<option value="Supported Living">Supported Living</option>
											<option value="Respite Care">Respite Care</option>
											<option value="Need Guidance">Need Guidance</option>
										</select>
										<label for="serviceInterest">Service Interest *</label>
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-floating">
										<input type="text" class="form-control" id="fullName" placeholder="John Smith" required name="fullName">
										<label for="fullName">Full Name *</label>
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-floating">
										<input type="tel" class="form-control" id="phoneNumber" placeholder="04xx xxx xxx" required name="phoneNumber">
										<label for="phoneNumber">Phone Number *</label>
									</div>
								</div>

								<div class="col-12">
									<div class="form-floating">
										<input type="email" class="form-control" id="emailAddress" placeholder="name@example.com" name="emailAddress">
										<label for="emailAddress">Email Address</label>
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-floating">
										<select class="form-select" id="contactMethod" required name="contactMethod">
											<option value="" selected disabled>Preferred method</option>
											<option value="Phone Call">Phone Call</option>
											<option value="SMS">SMS</option>
											<option value="Email">Email</option>
											<option value="WhatsApp">WhatsApp</option>
										</select>
										<label for="contactMethod">Contact Method *</label>
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-floating">
										<select class="form-select" id="contactTime" required name="contactTime">
											<option value="" selected disabled>Best time to call</option>
											<option value="Morning (8am–12pm)">Morning (8am–12pm)</option>
											<option value="Afternoon (12pm–4pm)">Afternoon (12pm–4pm)</option>
											<option value="Evening (4pm–8pm)">Evening (4pm–8pm)</option>
										</select>
										<label for="contactTime">Preferred Time *</label>
									</div>
								</div>

								<div class="col-12">
									<div class="form-floating">
										<textarea class="form-control" id="message" name="message" style="height:100px" placeholder="Tell us about your needs"></textarea>
										<label for="message">Additional Information (Optional)</label>
									</div>
								</div>
							</div>

							<button type="submit" class="btn btn-submit">
								<i class="bi bi-send-fill me-2"></i>Submit Request
							</button>

							<p class="text-center mt-3 mb-0" style="font-size:0.875rem;color:var(--text-muted)">
								<i class="bi bi-shield-lock me-1"></i>Your information is secure and confidential
							</p>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
	<div class="badge-section" style="display: flex;justify-content: center;margin-top: 50px;">
		<div class="fet-badge"> 
			We proudly support both Plan-Managed and Self-Managed participants.
		</div>
	</div>
	<section class="ndis-support-section">
		<div class="container">

			<h2 class="main-heading">Our Approach</h2>

			<p class="subtitle">
				We believe support should be respectful, empowering, and centred around you. Our services adapt to your lifestyle — not the other way around.
			</p>

			<div class="features-grid">
				<div class="feature-card">
					<div class="feature-icon"><i class="bi bi-bar-chart-fill"></i></div>
					<h3 class="feature-title">Promote Independence</h3>
					<p class="feature-text">Focus on skill development and building confidence for everyday tasks and long-term goals.</p>
				</div>

				<div class="feature-card">
					<div class="feature-icon"><i class="bi bi-heart-pulse-fill"></i></div>
					<h3 class="feature-title">Health & Wellbeing</h3>
					<p class="feature-text">Comprehensive support for daily living activities and maintaining optimal health.</p>
				</div>

				<div class="feature-card">
					<div class="feature-icon"><i class="bi bi-people-fill"></i></div>
					<h3 class="feature-title">Community Engagement</h3>
					<p class="feature-text">Encouraging meaningful social connections and active participation in community life.</p>
				</div>

				<div class="feature-card">
					<div class="feature-icon"><i class="bi bi-lightning-charge-fill"></i></div>
					<h3 class="feature-title">Reliable & Flexible</h3>
					<p class="feature-text">Care solutions that fit your schedule with dependable, professional support staff.</p>
				</div>
			</div>

			<div class="cta-box">
				<p class="cta-text">
					With <span class="brand-name">Logan Express Care</span>, you receive personalized support designed around your unique needs, goals, and aspirations.
				</p>
			</div>
		</div>
	</section>
	<!-- Services Section -->
	<section class="section" id="services">
		<div class="container">
			<div class="section-header">
				<span class="section-label">Our Services</span>
				<h2 class="section-title">Comprehensive NDIS Support</h2>
				<p class="section-subtitle">We offer a range of professional services designed to support your independence, health, and quality of life in the comfort of your community.</p>
			</div>

			<div class="services-grid">
				<article class="service-card">
					<div class="service-image">
						<div class="service-icon">
							<i class="bi bi-heart-pulse-fill"></i>
						</div>
					</div>
					<div class="service-content">
						<h3>Community Nursing Care</h3>
						<p>Professional nursing services delivered in your home or community setting. From medication management to wound care, our registered nurses provide clinical excellence with compassion.</p>
						<a href="#enquiry" class="service-link">
							Learn more <i class="bi bi-arrow-right"></i>
						</a>
					</div>
				</article>

				<article class="service-card">
					<div class="service-image">
						<div class="service-icon">
							<i class="bi bi-people-fill"></i>
						</div>
					</div>
					<div class="service-content">
						<h3>Community Participation</h3>
						<p>Build confidence, develop skills, and connect with your community through our tailored participation programs. We support you in pursuing your interests and achieving your goals.</p>
						<a href="#enquiry" class="service-link">
							Learn more <i class="bi bi-arrow-right"></i>
						</a>
					</div>
				</article>

				<article class="service-card">
					<div class="service-image">
						<div class="service-icon">
							<i class="bi bi-car-front-fill"></i>
						</div>
					</div>
					<div class="service-content">
						<h3>Transport Services</h3>
						<p>Safe, reliable transportation for appointments, shopping, social activities, and daily routines. Our drivers are trained in disability support and prioritize your comfort and safety.</p>
						<a href="#enquiry" class="service-link">
							Learn more <i class="bi bi-arrow-right"></i>
						</a>
					</div>
				</article>

				<article class="service-card">
					<div class="service-image">
						<div class="service-icon">
							<i class="bi bi-house-heart-fill"></i>
						</div>
					</div>
					<div class="service-content">
						<h3>Supported Independent Living</h3>
						<p>24/7 support in shared or individual living arrangements. We help you develop daily living skills while ensuring a safe, comfortable home environment that promotes autonomy.</p>
						<a href="#enquiry" class="service-link">
							Learn more <i class="bi bi-arrow-right"></i>
						</a>
					</div>
				</article>

				<article class="service-card">
					<div class="service-image">
						<div class="service-icon">
							<i class="bi bi-shield-plus"></i>
						</div>
					</div>
					<div class="service-content">
						<h3>Respite Care</h3>
						<p>Temporary relief for primary carers, providing peace of mind that your loved one is in capable hands. Flexible short-term care options to suit your family's needs.</p>
						<a href="#enquiry" class="service-link">
							Learn more <i class="bi bi-arrow-right"></i>
						</a>
					</div>
				</article>

				<article class="service-card">
					<div class="service-image" style="background:linear-gradient(135deg,var(--gold) 0%,var(--gold-dark) 100%)">
						<div class="service-icon" style="background:var(--navy);color:var(--gold)">
							<i class="bi bi-compass-fill"></i>
						</div>
					</div>
					<div class="service-content">
						<h3>Not Sure What You Need?</h3>
						<p>New to NDIS or uncertain about your options? Our coordinators provide free guidance to help you understand your plan and identify the services that best support your goals.</p>
						<a href="#enquiry" class="service-link">
							Get guidance <i class="bi bi-arrow-right"></i>
						</a>
					</div>
				</article>
			</div>
		</div>
	</section>

	<!-- Contact Section -->
	<section class="section contact-section" id="contact">
		<div class="container position-relative">
			<div class="contact-grid">
				<div class="contact-info">
					<h2>Get in Touch</h2>
					<p>Ready to start your care journey? Our team is here to answer your questions and guide you through the process.</p>

					<div class="contact-item">
						<div class="contact-item-icon">
							<i class="bi bi-geo-alt-fill"></i>
						</div>
						<div class="contact-item-content">
							<h4>Visit Us</h4>
							<p>460 Lonsdale Street, Levels 13 & 14<br>Melbourne, Victoria 3000</p>
						</div>
					</div>

					<div class="contact-item">
						<div class="contact-item-icon">
							<i class="bi bi-telephone-fill"></i>
						</div>
						<div class="contact-item-content">
							<h4>Call Us</h4>
							<p>
								<a href="tel:1300556002">1300 556 002</a> (Toll Free)<br>
								<a href="tel:0426005101">0426 005 101</a> (Mobile)
							</p>
						</div>
					</div>

					<div class="contact-item">
						<div class="contact-item-icon">
							<i class="bi bi-envelope-fill"></i>
						</div>
						<div class="contact-item-content">
							<h4>Email Us</h4>
							<p><a href="mailto:info@loganexpresscare.com.au">info@loganexpresscare.com.au</a></p>
						</div>
					</div>

					<div class="contact-item">
						<div class="contact-item-icon">
							<i class="bi bi-clock-fill"></i>
						</div>
						<div class="contact-item-content">
							<h4>Working Hours</h4>
							<p>Monday – Friday: 8:00 AM – 6:00 PM<br>
							After-hours support available for urgent care</p>
						</div>
					</div>

					<div class="d-flex gap-3 mt-4">
						<a href="tel:1300556002" class="btn btn-gold">
							<i class="bi bi-telephone me-2"></i>Call Now
						</a>
						<a href="#enquiry" class="btn btn-outline-gold">
							<i class="bi bi-calendar-check me-2"></i>Book Online
						</a>
					</div>
				</div>

				<div class="contact-map">
					<iframe 
					src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3152.016759089322!2d144.9556401767344!3d-37.813076434000244!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad65dff6d83cfb9%3A0x90d46b684cb8f6e9!2sRegus%20460%20Lonsdale%20Street!5e0!3m2!1sen!2s!4v1764745741199!5m2!1sen!2s" 
					allowfullscreen="" 
					loading="lazy" 
					referrerpolicy="no-referrer-when-downgrade">
				</iframe>
			</div>
		</div>
	</div>
</section>

<!-- Footer -->
<footer class="footer">
	<div class="container">
		<div class="footer-bottom">
			<p>&copy; <span id="currentYear"></span> Logan Express Care. All rights reserved. NDIS Trusted Provider.</p>
		</div>
	</div>
</footer>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Update copyright year
    document.getElementById('currentYear').textContent = new Date().getFullYear();

    // Navbar scroll effect
    window.addEventListener('scroll', () => {
    	const navbar = document.querySelector('.navbar');
    	if (window.scrollY > 50) {
    		navbar.classList.add('scrolled');
    	} else {
    		navbar.classList.remove('scrolled');
    	}
    });

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    	anchor.addEventListener('click', function (e) {
    		e.preventDefault();
    		const target = document.querySelector(this.getAttribute('href'));
    		if (target) {
    			target.scrollIntoView({
    				behavior: 'smooth',
    				block: 'start'
    			});
    		}
    	});
    });

    // Form handling
    const form = document.getElementById('consultationForm');
    const toastContainer = document.getElementById('toastContainer');

    function showToast(title, message, type = 'success') {
    	const toast = document.createElement('div');
    	toast.className = 'custom-toast';
    	toast.innerHTML = `
    	<div class="toast-icon">
    	<i class="bi ${type === 'success' ? 'bi-check-lg' : 'bi-exclamation-triangle'}"></i>
    	</div>
    	<div class="toast-content">
    	<h4>${title}</h4>
    	<p>${message}</p>
    	</div>
    	`;
    	toastContainer.appendChild(toast);

    	setTimeout(() => {
    		toast.style.animation = 'slideIn 0.3s ease reverse';
    		setTimeout(() => toast.remove(), 300);
    	}, 5000);
    }

    form.addEventListener('submit', async (e) => {
    	e.preventDefault();

    	const submitBtn = form.querySelector('button[type="submit"]');
    	const originalText = submitBtn.innerHTML;

    	const requiredFields = ['userType', 'serviceInterest', 'fullName', 'phoneNumber', 'contactMethod', 'contactTime'];
    	const emptyFields = requiredFields.filter(id => !document.getElementById(id).value);

    	if (emptyFields.length > 0) {
    		showToast('Missing Information', 'Please fill in all required fields.', 'error');
    		return;
    	}

    	submitBtn.classList.add('btn-loading');
    	submitBtn.disabled = true;

    	const formData = new FormData(form);

    	try {
    		const response = await fetch('send-mail.php', {
    			method: 'POST',
    			body: formData
    		});

    		const result = await response.text();

    		if (result.trim() === "success") {
    			showToast('Success!', 'Our team will contact you soon.');
    			form.reset();
    		} else {
    			showToast('Success!', 'Our team will contact you soon.');
    			form.reset();
    		}

    	} catch (error) {
    		showToast('Error', 'Server error. Please try again later.', 'error');
    	}

    	submitBtn.classList.remove('btn-loading');
    	submitBtn.disabled = false;
    	submitBtn.innerHTML = originalText;
    });

    // Intersection Observer for animations
    const observerOptions = {
    	threshold: 0.1,
    	rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
    	entries.forEach(entry => {
    		if (entry.isIntersecting) {
    			entry.target.style.opacity = '1';
    			entry.target.style.transform = 'translateY(0)';
    		}
    	});
    }, observerOptions);

    // Observe service cards
    document.querySelectorAll('.service-card').forEach((card, index) => {
    	card.style.opacity = '0';
    	card.style.transform = 'translateY(30px)';
    	card.style.transition = `all 0.6s ease ${index * 0.1}s`;
    	observer.observe(card);
    });
</script>
</body>
</html>