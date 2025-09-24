<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABSM SYSTEM - Barangay Document Retrieval</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Styles */
        header {
            background: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-img {
            width: 40px;
            height: 40px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #0088FF;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 30px;
        }

        nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            position: relative;
            padding: 5px 0;
            transition: color 0.3s ease;
        }

        nav a:hover {
            color: #0088FF;
        }

        .info-box {
            display: none;
            position: absolute;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            width: 200px;
            z-index: 101;
            margin-top: 10px;
            animation: fadeIn 0.3s ease;
        }

        .info-box h4 {
            color: #0088FF;
            margin-bottom: 5px;
        }

        .info-box p {
            font-size: 14px;
            color: #666;
        }

        /* Mobile Menu */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #0088FF;
        }

        .mobile-nav {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            z-index: 1000;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .mobile-nav ul {
            list-style: none;
        }

        .mobile-nav li {
            margin: 20px 0;
        }

        .mobile-nav a {
            text-decoration: none;
            color: #333;
            font-size: 24px;
            font-weight: 500;
        }

        .close-mobile-menu {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            font-size: 30px;
            cursor: pointer;
            color: #0088FF;
        }

        /* Hero Section */
        .hero {
            padding: 80px 0;
            background: linear-gradient(135deg, #0088FF 0%, #0055AA 100%);
            color: white;
        }

        .hero-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 40px;
        }

        .hero-text {
            flex: 1;
        }

        .hero-text h1 {
            font-size: 48px;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero-text p {
            font-size: 20px;
            opacity: 0.9;
            margin-bottom: 30px;
        }

        .hero-image {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .hero-image img {
            max-width: 100%;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        /* Picture Box for Title Page */
        .title-image-box {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            text-align: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .title-image-box p {
            margin-bottom: 15px;
            font-size: 18px;
        }

        .placeholder-image {
            width: 100%;
            height: 200px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: rgba(255, 255, 255, 0.8);
            font-size: 16px;
        }

        .placeholder-image i {
            font-size: 48px;
            margin-bottom: 10px;
        }

        /* Services Section */
        .services {
            padding: 80px 0;
            background: white;
        }

        .section-title {
            text-align: center;
            font-size: 36px;
            margin-bottom: 20px;
            color: #0088FF;
            position: relative;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: #0088FF;
            margin: 15px auto;
            border-radius: 2px;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .service-card {
            background: #f9f9f9;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .service-icon {
            background: #0088FF;
            color: white;
            padding: 30px;
            text-align: center;
            font-size: 48px;
        }

        .service-content {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .service-content h3 {
            color: #0088FF;
            margin-bottom: 10px;
            font-size: 22px;
        }

        .service-content p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.5;
            flex-grow: 1;
        }

        .proceed-btn {
            display: inline-block;
            background: #0088FF;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s ease, transform 0.2s ease;
            border: none;
            cursor: pointer;
            text-align: center;
            align-self: flex-start;
        }

        .proceed-btn:hover {
            background: #0077DD;
            transform: scale(1.05);
        }

        /* About Section */
        .about {
            padding: 80px 0;
            background: #f9f9f9;
        }

        .about-content {
            display: flex;
            align-items: center;
            gap: 40px;
            margin-top: 40px;
        }

        .about-image {
            flex: 1;
        }

        .about-image img {
            max-width: 100%;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .about-text {
            flex: 1;
        }

        .about-text p {
            margin-bottom: 20px;
            line-height: 1.6;
            color: #555;
            font-size: 17px;
        }

        .divider {
            height: 2px;
            background: #ddd;
            margin: 25px 0;
        }

        /* Footer */
        footer {
            background: #333;
            color: white;
            padding: 60px 0 20px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-section h3 {
            margin-bottom: 20px;
            color: #0088FF;
            font-size: 22px;
        }

        .footer-section p {
            margin-bottom: 10px;
            color: #ccc;
        }

        .social-icons {
            display: flex;
            gap: 15px;
        }

        .social-icons a {
            color: white;
            font-size: 20px;
            transition: color 0.3s ease, transform 0.2s ease;
        }

        .social-icons a:hover {
            color: #0088FF;
            transform: translateY(-3px);
        }

        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #555;
            color: #ccc;
        }

        .brand-highlight {
            color: #0088FF;
            font-weight: bold;
        }

        /* Request Form */
        .request-form-container {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.3s ease;
            padding: 20px;
        }

        .request-form {
            background: white;
            width: 100%;
            max-width: 600px;
            border-radius: 15px;
            padding: 30px;
            position: relative;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #888;
            transition: color 0.3s ease;
        }

        .close-btn:hover {
            color: #333;
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-header h2 {
            color: #0088FF;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .form-header p {
            color: #666;
            margin-bottom: 5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #444;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #0088FF;
            box-shadow: 0 0 0 3px rgba(0, 136, 255, 0.1);
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background: #0088FF;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .submit-btn:hover {
            background: #0077DD;
            transform: translateY(-2px);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        /* Success Message */
        .success-message {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            background: #4CAF50;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 1001;
            animation: slideIn 0.5s ease;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-content {
                flex-direction: column;
                text-align: center;
            }
            
            .hero-text h1 {
                font-size: 36px;
            }
            
            .about-content {
                flex-direction: column;
            }
            
            nav ul {
                display: none;
            }
            
            .mobile-menu-btn {
                display: block;
            }
            
            .header-content {
                flex-direction: row;
                justify-content: space-between;
            }
            
            .services-grid {
                grid-template-columns: 1fr;
            }
            
            .footer-content {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .social-icons {
                justify-content: center;
            }
            
            .request-form {
                padding: 20px;
            }
            
            .service-card {
                flex-direction: column;
            }
            
            .service-icon {
                padding: 20px;
                font-size: 36px;
            }
        }

        @media (max-width: 480px) {
            .hero {
                padding: 60px 0;
            }
            
            .services, .about {
                padding: 60px 0;
            }
            
            .section-title {
                font-size: 28px;
            }
            
            .hero-text h1 {
                font-size: 28px;
            }
            
            .hero-text p {
                font-size: 18px;
            }
            
            .service-content h3 {
                font-size: 20px;
            }
            
            .form-header h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <!-- Success Message -->
    <div class="success-message" id="successMessage">
        <i class="fas fa-check-circle"></i> Your request has been submitted successfully!
    </div>

    <!-- Header -->
    <header>
        <div class="container header-content">
            <div class="logo-container">
                <img class="logo-img" src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' fill='%230088FF' rx='15'/><path d='M30 30 L70 30 L70 70 L30 70 Z' fill='%23FFFFFF'/><circle cx='50' cy='50' r='15' fill='%23FFFFFF'/></svg>" alt="ABSM Logo">
                <div class="logo">ABSM SYSTEM</div>
            </div>
            
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>
            
            <nav>
                <ul>
                    <li>
                        <a href="#home" class="nav-link" data-info="Return to the main page">Home</a>
                        <div class="info-box" id="home-info">
                            <h4>Home</h4>
                            <p>Return to the main page of our document retrieval system.</p>
                        </div>
                    </li>
                    <li>
                        <a href="#about" class="nav-link" data-info="Learn about our system">About</a>
                        <div class="info-box" id="about-info">
                            <h4>About Us</h4>
                            <p>Learn about our mission to simplify document retrieval for barangay residents.</p>
                        </div>
                    </li>
                    <li>
                        <a href="#services" class="nav-link" data-info="Explore our services">Services</a>
                        <div class="info-box" id="services-info">
                            <h4>Our Services</h4>
                            <p>Discover the various documents you can request through our system.</p>
                        </div>
                    </li>
                    <li>
                        <a href="#contact" class="nav-link" data-info="Get in touch with us">Contact</a>
                        <div class="info-box" id="contact-info">
                            <h4>Contact Us</h4>
                            <p>Reach out to us for support or more information about our services.</p>
                        </div>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Mobile Navigation -->
    <div class="mobile-nav" id="mobileNav">
        <button class="close-mobile-menu" id="closeMobileMenu">
            <i class="fas fa-times"></i>
        </button>
        <ul>
            <li><a href="#home">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
    </div>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container hero-content">
            <div class="hero-text">
                <h1>Welcome to Barangay<br>Document Retrieval System</h1>
                <p>Easily access official documents at the barangay level.</p>
                
                <!-- Picture Box for Title Page -->
                <div class="title-image-box">
                    <p>Add your barangay photo here</p>
                    <div class="placeholder-image">
                        <i class="fas fa-image"></i>
                        <span>Image placeholder</span>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 500 400'><defs><linearGradient id='grad1' x1='0%' y1='0%' x2='100%' y2='100%'><stop offset='0%' style='stop-color:%230088FF;stop-opacity:1' /><stop offset='100%' style='stop-color:%239CA3AF;stop-opacity:1' /></linearGradient></defs><rect width='500' height='400' fill='url(%23grad1)' rx='20'/><rect x='100' y='80' width='300' height='240' rx='10' fill='%23FFFFFF' stroke='%230088FF' stroke-width='2'/><path d='M150 130 L350 130 M150 160 L350 160 M150 190 L350 190 M150 220 L280 220' stroke='%230088FF' stroke-width='3'/><circle cx='320' cy='220' r='15' fill='%23D8C9C9'/><path d='M315 220 L325 230 M325 220 L315 230' stroke='%23000000' stroke-width='3'/><rect x='150' y='250' width='200' height='40' rx='5' fill='%230088FF' stroke='%23D8C9C9' stroke-width='2'/><text x='250' y='275' font-family='Arial' font-size='16' fill='%23FFFFFF' text-anchor='middle'>View Documents</text></svg>" alt="Document Retrieval System">
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services" id="services">
        <div class="container">
            <h2 class="section-title">Our Services</h2>
            <p style="text-align: center; margin-bottom: 2rem; color: #000000; font-size: 18px;">Access various documents with just a click.</p>
            
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div class="service-content">
                        <h3>Barangay Clearance</h3>
                        <p>Official certification of residency and good moral character for various transactions.</p>
                        <a class="proceed-btn" data-document="Barangay Clearance" data-document-id="1">PROCEED</a>
                    </div>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="service-content">
                        <h3>Certificate of Residency</h3>
                        <p>Proof of residence required for identification and government transactions.</p>
                        <a class="proceed-btn" data-document="Certificate of Residency" data-document-id="2">PROCEED</a>
                    </div>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <div class="service-content">
                        <h3>Certificate of Indigency</h3>
                        <p>Document certifying financial status for availing government assistance programs.</p>
                        <a class="proceed-btn" data-document="Certificate of Indigency" data-document-id="3">PROCEED</a>
                    </div>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="service-content">
                        <h3>Certificate of Good Moral</h3>
                        <p>Certification of good moral character required for employment, scholarships, and other purposes.</p>
                        <a class="proceed-btn" data-document="Certificate of Good Moral" data-document-id="4">PROCEED</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about" id="about">
        <div class="container">
            <h2 class="section-title">About Us</h2>
            <div class="about-content">
                <div class="about-image">
                    <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 500 350'><rect width='500' height='350' fill='%23E5E5E8' rx='20'/><rect x='50' y='40' width='400' height='270' rx='10' fill='%23FFFFFF' stroke='%230088FF' stroke-width='2'/><circle cx='250' cy='120' r='50' fill='%23D9D9D9'/><rect x='150' y='190' width='200' height='15' rx='5' fill='%23D9D9D9'/><rect x='150' y='215' width='150' height='15' rx='5' fill='%23D9D9D9'/><rect x='150' y='240' width='180' height='15' rx='5' fill='%23D9D9D9'/><rect x='350' y='240' width='80' height='40' rx='5' fill='%230088FF' stroke='%23D8C9C9' stroke-width='2'/><text x='390' y='265' font-family='Arial' font-size='14' fill='%23FFFFFF' text-anchor='middle'>APPLY</text></svg>" alt="About Barangay System">
                </div>
                <div class="about-text">
                    <p>The Barangay Document Retrieval System is an online platform that simplifies the process of requesting essential barangay documents such as Barangay Clearance, Certificate of Indigency, and Certificate of Residency. Instead of visiting the barangay hall and filling out paper forms, residents can conveniently submit their requests online at any time.</p>
                    
                    <div class="divider"></div>
                    
                    <p>The system provides quick access to request forms, real-time status tracking, and timely notifications through email or SMS. On the administrative side, barangay staff can efficiently review, validate, and update requests using a secure dashboard, reducing manual workload and ensuring faster, more organized transactions.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>ABSM SYSTEM</h3>
                    <p>Streamlining barangay services for a more efficient community.</p>
                </div>
                
                <div class="footer-section">
                    <h3>Services</h3>
                    <p>Document Retrieval</p>
                    <p>Information Update</p>
                    <p>Community Services</p>
                </div>
                
                <div class="footer-section">
                    <h3>Follow Us</h3>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="copyright">
                <p>© 2024 <span class="brand-highlight">ABSM SYSTEM</span>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Request Form (Hidden by default) -->
    <div class="request-form-container" id="requestForm">
        <div class="request-form">
            <button class="close-btn" id="closeForm">&times;</button>
            <div class="form-header">
                <h2>ABSM SYSTEM</h2>
                <p>Document Request Form</p>
                <p>Submit your document request quickly and securely</p>
            </div>
            
            <form id="documentRequestForm">
                <h3 style="color: #0088FF; margin-bottom: 1rem;">Personal Information</h3>
                
                <div class="form-group">
                    <label for="documentType">Document Type</label>
                    <input type="text" id="documentType" name="documentType" readonly>
                    <input type="hidden" id="documentTypeId" name="documentTypeId">
                </div>
                
                <div class="form-group">
                    <label for="fullName">Full Name *</label>
                    <input type="text" id="fullName" name="fullName" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="address">Address *</label>
                    <input type="text" id="address" name="address" required>
                </div>
                
                <div class="form-group">
                    <label for="contactNumber">Contact Number *</label>
                    <input type="text" id="contactNumber" name="contactNumber" required>
                </div>
                
                <div class="form-group">
                    <label for="purpose">Please describe the purpose of your document request...</label>
                    <textarea id="purpose" name="purpose" required></textarea>
                </div>
                
                <button type="submit" class="submit-btn">Submit Request</button>
            </form>
        </div>
    </div>

    <script>
        // Mobile menu functionality
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileNav = document.getElementById('mobileNav');
        const closeMobileMenu = document.getElementById('closeMobileMenu');
        
        mobileMenuBtn.addEventListener('click', function() {
            mobileNav.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });
        
        closeMobileMenu.addEventListener('click', function() {
            mobileNav.style.display = 'none';
            document.body.style.overflow = 'auto';
        });
        
        // Close mobile menu when clicking on a link
        document.querySelectorAll('.mobile-nav a').forEach(link => {
            link.addEventListener('click', function() {
                mobileNav.style.display = 'none';
                document.body.style.overflow = 'auto';
            });
        });

        // Show request form when PROCEED buttons are clicked
        document.querySelectorAll('.proceed-btn').forEach(button => {
            button.addEventListener('click', function() {
                const documentType = this.getAttribute('data-document');
                const documentTypeId = this.getAttribute('data-document-id');
                document.getElementById('documentType').value = documentType;
                document.getElementById('documentTypeId').value = documentTypeId;
                document.getElementById('requestForm').style.display = 'flex';
                document.body.style.overflow = 'hidden'; // Prevent scrolling when form is open
            });
        });

        // Close form when X button is clicked
        document.getElementById('closeForm').addEventListener('click', function() {
            document.getElementById('requestForm').style.display = 'none';
            document.body.style.overflow = 'auto'; // Re-enable scrolling
        });

        // Close form when clicking outside the form
        document.getElementById('requestForm').addEventListener('click', function(e) {
            if (e.target === this) {
                document.getElementById('requestForm').style.display = 'none';
                document.body.style.overflow = 'auto'; // Re-enable scrolling
            }
        });

        // Form submission
        document.getElementById('documentRequestForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            
            // Show loading state
            const submitBtn = this.querySelector('.submit-btn');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Submitting...';
            submitBtn.disabled = true;
            
            // Simulate form submission (replace with actual AJAX call to your server)
            setTimeout(() => {
                // Close the form
                document.getElementById('requestForm').style.display = 'none';
                document.body.style.overflow = 'auto';
                
                // Show success message
                const successMessage = document.getElementById('successMessage');
                successMessage.style.display = 'block';
                
                // Hide success message after 5 seconds
                setTimeout(() => {
                    successMessage.style.display = 'none';
                }, 5000);
                
                // Reset form and button
                this.reset();
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }, 1500);
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('nav a, .mobile-nav a').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                document.querySelector(targetId).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Show info box when nav item is clicked (desktop only)
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Hide all info boxes
                document.querySelectorAll('.info-box').forEach(box => {
                    box.style.display = 'none';
                });
                
                // Show the clicked info box
                const infoId = this.getAttribute('href').substring(1) + '-info';
                const infoBox = document.getElementById(infoId);
                if (infoBox) {
                    infoBox.style.display = 'block';
                    
                    // Hide the info box after 3 seconds
                    setTimeout(() => {
                        infoBox.style.display = 'none';
                    }, 3000);
                }
                
                // Still scroll to the section
                const targetId = this.getAttribute('href');
                document.querySelector(targetId).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Hide info boxes when clicking elsewhere
        document.addEventListener('click', function(e) {
            if (!e.target.matches('.nav-link')) {
                document.querySelectorAll('.info-box').forEach(box => {
                    box.style.display = 'none';
                });
            }
        });
    </script>
</body>
</html>