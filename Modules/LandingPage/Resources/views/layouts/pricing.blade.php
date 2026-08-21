<!DOCTYPE html>
<html lang="en">
<head>
   

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connect360 Pricing Plans | Affordable Business Solutions & HRM features</title>

        <!-- <link rel="stylesheet" href="zohocustom.css"> -->
        <!-- <link rel="stylesheet" href="people.css"> -->
        <link rel="stylesheet" href="2474.css">
    <link rel="stylesheet" href="{{ Module::asset('LandingPage:Resources/views/layouts/style1.css') }}">
    <!-- <link rel="stylesheet" href="{{ Module::asset('LandingPage:Resources/views/layouts/style.css') }}"> -->
    <link rel="stylesheet" href="{{ Module::asset('LandingPage:Resources/views/layouts/footer.css') }}">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- <script src="https://cdn.emailjs.com/dist/email.min.js"></script> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<!-- CSS for intl-tel-input -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>


        <!-- jQuery and Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    
    
    
    
    <!-- Meta Tags for SEO -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Explore Connect360's transparent and competitive pricing plans for business solutions. Find the ideal package to drive growth with cost-effective strategies. Access preferred HR software features like streamlined workforce management, simplified attendance tracking, organized employee documents, compliance assurance, and automated payroll processing.">
    <meta name="keywords" content="Connect360, business solutions, pricing plans, HR software, workforce management, attendance tracking, payroll processing, employee document management, compliance">
    <meta name="author" content="Connect360">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph / Facebook Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Connect360 - Competitive Pricing for Business Solutions & HR Software">
    <meta property="og:description" content="Find the right Connect360 package for your business with transparent pricing and essential HR software features like workforce management, attendance tracking, and payroll automation.">
    <meta property="og:image" content="URL_to_image">
    <meta property="og:url" content="https://www.connect360.com">
    
    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Connect360 - Competitive Pricing for Business Solutions & HR Software">
    <meta name="twitter:description" content="Accelerate growth with Connect360's cost-effective packages, including essential HR features like payroll processing, compliance, and employee document management.">
    <meta name="twitter:image" content="URL_to_image">
    <meta name="twitter:url" content="https://www.connect360.com">
    
    <!-- Favicon -->
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    
    
    
    
    
    

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

        <!-- <script src="https://cdn.emailjs.com/dist/email.min.js"></script> -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


    <script src="https://cdn.emailjs.com/dist/email.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Owl Carousel JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

</head>
<body>


    
    <div class="navbar-connect">
        <div class="navbar-logo">
            <a href="https://connect360.in/">
                <img src="<?php echo asset('Modules/LandingPage/Resources/views/layouts/images/logo.png'); ?>" alt="Connect360">
            </a>
        </div>

        <ul class="navbar-menu" id="nav-links">
            <li><a href="{{ route('pricing') }}">Pricing</a></li>
            <li><a href="{{ route('blog') }}">Blog</a></li>
            <li><a href="{{ route('contact') }}">Contact</a></li>
            
        </ul>

        <div class="navbar-login">
            <a href="{{ route('login') }}">Login</a>
        </div>

        <div class="navbar-toggle" id="mobile-menu">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
    </div>

    <script>
        // JavaScript to toggle mobile menu
        document.getElementById('mobile-menu').addEventListener('click', function() {
            const navLinks = document.getElementById('nav-links');
            navLinks.classList.toggle('active');
        });


        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar-connect');
            if (window.scrollY > 0) {
                navbar.classList.add('scrolled'); // Add shadow when scrolling
            } else {
                navbar.classList.remove('scrolled'); // Remove shadow when at the top
            }
        });


    </script>

    <!-- banner -->
     <section style="background-color:#fafafa; padding-top:20px;">
        <div class="banner">
            <div class="banner-content">
                <h1>Take Our Pricing Challenge!</h1>
                <p>Connect360 for exceptional software, support, and service. Starting at INR 30/employee – the lowest license costs in its category.</p>
                <a href="{{ route('contact') }}" class="banner-btn">Discuss Pricing</a>
            </div>
            <div class="banner-image">
                <img src="{{ asset('Modules/LandingPage/Resources/views/layouts/images/p-banner.png') }}" alt="Banner Image"> <!-- Replace with correct image path -->
            </div>
        </div>
        
        </section>
     <!-- end banner -->

     <!-- prizing -->
    <section id="prizing">
            <h2 class="slider-heading">Explore Our <span style="color: #005b33;">HRM</span> in Action</h2>
            <p class="slider-paragraph">
                Browse through our gallery to see how our HRM software streamlines processes, enhances productivity, and transforms HR management. Discover real-world examples and visual insights into our comprehensive features and benefits.
            </p>
            <div class="pricing-table">
                <table>
                    <thead>
                        <tr>
                            <th class="table-th">Modules and Features</th>
                            <th style="text-align:center">Starter<br>₹ <span style="color: #62965d;">3499</span> /month<br><h6>(70 Employees)</h6>
                                <button class="table-button">Choose Starter</button>
                            </th >
                            <th style="text-align:center">Essential<br>₹<span style="color: #62965d;"> 4599 </span> /month<br><h6>(150 Employees)</h6>
                                <button class="table-button">Choose Essential</button>
                            </th>
                            <th style="text-align:center">Growth<br>₹<span style="color: #62965d;"> 5999 </span> /month<br><h6>(250 Employees)</h6>
                                <button class="table-button">Choose Growth</button>
                            </th>
                            <th style="text-align:center">Enterprise<br>₹<span style="color: #62965d;"> 6599 </span> /month<br><h6>(300 Employees)</h6>
                                <button class="table-button">Choose Enterprise</button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Number of Employees</td>
                            <td>Limited</td>
                            <td>Unlimited</td>
                            <td>Unlimited</td>
                            <td>Unlimited</td>
                        </tr>
                        <tr>
                            <td>Cost Per Additional Employee</td>
                            <td>Not Applicable</td>
                            <td>₹30/month</td>
                            <td>₹50/month</td>
                            <td>₹80/month</td>
                        </tr>
                        <tr>
                            <td>Income & Expense Dashboard</td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#E04D44" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#E04D44"/>
                                <line x1="15" y1="9" x2="9" y2="15" stroke="#FFFFFF" stroke-width="2" />
                                <line x1="9" y1="9" x2="15" y2="15" stroke="#FFFFFF" stroke-width="2" />
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                        </tr>
                        <tr>
                            <td>Monthly Attendance</td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                        </tr>
                        <tr>
                            <td>Account Statement</td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                        </tr>
                        <tr>
                            <td>Payroll Management</td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#E04D44"/>
                                <line x1="15" y1="9" x2="9" y2="15" stroke="#FFFFFF" stroke-width="2" />
                                <line x1="9" y1="9" x2="15" y2="15" stroke="#FFFFFF" stroke-width="2" />
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                        </tr>
                        <tr>
                            <td>Performance Management</td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#E04D44"/>
                                <line x1="15" y1="9" x2="9" y2="15" stroke="#FFFFFF" stroke-width="2" />
                                <line x1="9" y1="9" x2="15" y2="15" stroke="#FFFFFF" stroke-width="2" />
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                        </tr>
                        <tr>
                            <td>job Management</td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#E04D44"/>
                                <line x1="15" y1="9" x2="9" y2="15" stroke="#FFFFFF" stroke-width="2" />
                                <line x1="9" y1="9" x2="15" y2="15" stroke="#FFFFFF" stroke-width="2" />
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#E04D44"/>
                                <line x1="15" y1="9" x2="9" y2="15" stroke="#FFFFFF" stroke-width="2" />
                                <line x1="9" y1="9" x2="15" y2="15" stroke="#FFFFFF" stroke-width="2" />
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                        </tr>
                        <tr>
                            <td>Meeting</td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>

                        </tr>
                        <tr>
                            <td>Zoom Meeting</td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#E04D44"/>
                                <line x1="15" y1="9" x2="9" y2="15" stroke="#FFFFFF" stroke-width="2" />
                                <line x1="9" y1="9" x2="15" y2="15" stroke="#FFFFFF" stroke-width="2" />
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#E04D44"/>
                                <line x1="15" y1="9" x2="9" y2="15" stroke="#FFFFFF" stroke-width="2" />
                                <line x1="9" y1="9" x2="15" y2="15" stroke="#FFFFFF" stroke-width="2" />
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>

                        </tr>
                        <tr>
                            <td>notification Management</td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                        </tr>
                        <tr>
                            <td>Asset Management</td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#E04D44"/>
                                <line x1="15" y1="9" x2="9" y2="15" stroke="#FFFFFF" stroke-width="2" />
                                <line x1="9" y1="9" x2="15" y2="15" stroke="#FFFFFF" stroke-width="2" />
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#E04D44"/>
                                <line x1="15" y1="9" x2="9" y2="15" stroke="#FFFFFF" stroke-width="2" />
                                <line x1="9" y1="9" x2="15" y2="15" stroke="#FFFFFF" stroke-width="2" />
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#E04D44"/>
                                <line x1="15" y1="9" x2="9" y2="15" stroke="#FFFFFF" stroke-width="2" />
                                <line x1="9" y1="9" x2="15" y2="15" stroke="#FFFFFF" stroke-width="2" />
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                        </tr>
                        <tr>
                            <td>Chat Messanger</td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#E04D44"/>
                                <line x1="15" y1="9" x2="9" y2="15" stroke="#FFFFFF" stroke-width="2" />
                                <line x1="9" y1="9" x2="15" y2="15" stroke="#FFFFFF" stroke-width="2" />
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#E04D44"/>
                                <line x1="15" y1="9" x2="9" y2="15" stroke="#FFFFFF" stroke-width="2" />
                                <line x1="9" y1="9" x2="15" y2="15" stroke="#FFFFFF" stroke-width="2" />
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#E04D44"/>
                                <line x1="15" y1="9" x2="9" y2="15" stroke="#FFFFFF" stroke-width="2" />
                                <line x1="9" y1="9" x2="15" y2="15" stroke="#FFFFFF" stroke-width="2" />
                            </svg></td>
                            <td> <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#4CAF50" stroke-width="2" viewBox="0 0 24 24" width="28px" height="28px">
                                <circle cx="12" cy="12" r="10" stroke="none" fill="#4CAF50"/>
                                <path d="M9 12l2 2 4-4" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                            </svg></td>
                        </tr>   
                        <!-- Additional rows omitted for brevity -->
                    </tbody>
                </table>
            </div>
    </section>
    <!-- end prizing -->

       <!-- whatsapp button -->
     

       <section>

            <!-- WhatsApp Button -->
            <div id="whatsapp-button" 
                style="position: fixed; z-index: 100;
                        bottom: 20px; 
                        right: 20px; 
                        background-color: #1fb355; 
                        color: white; 
                        border-radius: 50px; 
                        padding: 10px 20px; 
                        font-size: 18px; 
                        display: flex; 
                        align-items: center; 
                        text-decoration: none; 
                        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); 
                        cursor: pointer;">
                <img src="{{ asset('Modules/LandingPage/Resources/views/layouts/images/whatsapp.png') }}" 
                    alt="WhatsApp" 
                    style="width: 24px; z-index: 100;
                            height: 24px; 
                            margin-right: 10px;">
                Let's Chat 
            
            </div>

            <!-- Chatbox -->
            <div id="chatbox" 
                style="position: fixed; z-index: 100;
                        bottom: 80px; 
                        right: 20px; 
                        width: 350px; 
                        height: 450px; 
                        border: 1px solid #ddd; 
                        border-radius: 10px; 
                        background-color: #fff; 
                        display: none; 
                        flex-direction: column; 
                        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);">
                <!-- Chatbox Header -->
                <div style="background-color: #075E54; z-index: 100;
                            color: white; 
                            padding: 15px; 
                            border-top-left-radius: 10px; 
                            border-top-right-radius: 10px; 
                            font-size: 16px; 
                            text-align: center;">
                    Connect360
                    <div style="font-size: 12px; z-index: 100;
                                color: #C2C2C2; 
                                margin-top: 5px;">
                        Replies within few hours
                    </div>
                </div>

                <!-- Chatbox Body -->
                <div style="flex: 1; 
                z-index: 100;
                            padding: 10px; 
                            overflow-y: auto; 
                            background-color: #ECE5DD;">
                    <p style="background-color: white; 
                    z-index: 100;
                            padding: 10px; 
                            border-radius: 8px; 
                            width: fit-content; 
                            margin: 10px 0;">
                        Hey! 👋 How can we help you?
                    </p>
                    <div id="user-message" 
                        style="background-color: #DCF8C6; 
                        z-index: 100;
                                padding: 10px; 
                                border-radius: 8px; 
                                width: fit-content; 
                                margin: 10px 0;">
                        <!-- User messages will appear here -->
                    </div>
                </div>

                <!-- Chatbox Footer -->
                <div style="padding: 10px; 
                            border-top: 1px solid #ddd;">
                    <input id="chat-input" 
                        type="text" 
                        placeholder="Type a message..." 
                        style="width: 100%;
                        z-index: 100; 
                                padding: 10px; 
                                border: 1px solid #ddd; 
                                border-radius: 5px;">
                    <button id="send-button" 
                            style="margin-top: 10px; 
                                z-index: 100;
                                width: 100%; 
                                padding: 10px; 
                                background-color: #25D366; 
                                color: white; 
                                border: none; 
                                border-radius: 5px; 
                                cursor: pointer;">
                        Start chat
                    </button>
                </div>
            </div>

            <script>
                // Toggle chatbox visibility
                document.getElementById('whatsapp-button').addEventListener('click', function() {
                    var chatbox = document.getElementById('chatbox');
                    if (chatbox.style.display === 'none') {
                        chatbox.style.display = 'flex';
                    } else {
                        chatbox.style.display = 'none';
                    }
                });

                // Send message and open WhatsApp
                document.getElementById('send-button').addEventListener('click', function() {
                    var message = document.getElementById('chat-input').value;
                    if (message.trim() !== "") {
                        var whatsappUrl = "https://wa.me/9834969704?text=" + encodeURIComponent(message);
                        document.getElementById('user-message').textContent = message;
                        window.open(whatsappUrl, '_blank');
                    }
                });
            </script>

        </section>`

<!-- end whatsapp button -->



    <!-- faq -->
    <div class="faq-container">
        <h2 class="faq-title"> <span class="highlight">FAQs</span></h2>
        <div class="faq-item">
            <button class="faq-question"><strong>What occurs when my free trial period concludes?</strong></button>
            <div class="faq-answer">
                <p>After the trial ends, your account will automatically switch to a paid plan unless you cancel.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question"><strong>Is it possible to change my subscription plan?</strong></button>
            <div class="faq-answer">
                <p>YYes, you can easily change your subscription plan at any time.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question"><strong>Are there any long-term contracts required?</strong></button>
            <div class="faq-answer">
                <p>No long-term contracts are required; you can choose a monthly or annual plan.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question"><strong>Do you offer discounts for annual subscriptions?</strong></button>
            <div class="faq-answer">
                <p>Yes, we provide a discounted rate for annual subscriptions.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question"><strong>Will I incur any fees if I cancel my subscription?</strong></button>
            <div class="faq-answer">
                <p>No, there are no cancellation fees; you can cancel without penalties.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question"><strong>What payment methods are available?</strong></button>
            <div class="faq-answer">
                <p>We accept various payment options, including credit cards and online payment services.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question"><strong>Am I the owner of my data?</strong></button>
            <div class="faq-answer">
                <p>Yes, you own all the data stored in Connect360.</p>
            </div>
        </div>
        <div class="faq-item">
            <button class="faq-question"><strong>What measures are in place to protect my data?</strong></button>
            <div class="faq-answer">
                <p>We implement robust security measures, including encryption, to ensure your data is safe.</p>
            </div>
        </div>
    </div>
    

    
    <script>
     const faqQuestions = document.querySelectorAll('.faq-question');

faqQuestions.forEach(question => {
    question.addEventListener('click', () => {
        question.classList.toggle('active');
        const answer = question.nextElementSibling;
        if (answer.style.display === 'block') {
            answer.style.display = 'none';
        } else {
            answer.style.display = 'block';
        }
    });
});

    </script>

    <!-- end faq -->

    <!-- why best -->
    <section class="features-section">
        <div class="">
            <h2 class="main-heading">Discover Why Connect360 is the preferred <span class="highlightOne">HR Software</span></h2>
            <p class="subtitle">Know what to expect <span class="highlight1">from Connect360</span></p>
        </div>
        

        <div class="container1" style="margin-top: 78px;">

            <div class="features-grid">
                <div class="feature-box">
                    <h3 class="feature-title ">Reliable & Robust Product</h3>
                    <p>Tailored to your unique needs for precision and efficiency</p>
                </div>
                <div class="feature-box">
                    <h3 class="feature-title">Excellent Support & After Sales Service</h3>
                    <p>Dedicated assistance and support for a smooth experience</p>
                </div>
                <div class="feature-box">
                    <h3 class="feature-title">Integration & Customization</h3>
                    <p>On Demand Customization and Integration</p>
                </div>
            </div>
        </div>
        <div class="container2">

            <div class="features-grid1">
                <div class="feature-box">
                    <h3 class="feature-title ">Reliable & Robust Product</h3>
                    <p>Tailored to your unique needs for precision and efficiency</p>
                </div>
                <div class="feature-box">
                    <h3 class="feature-title">Excellent Support & After Sales Service</h3>
                    <p>Dedicated assistance and support for a smooth experience</p>
                </div>
                <div class="feature-box">
                    <h3 class="feature-title">Integration & Customization</h3>
                    <p>On Demand Customization and Integration</p>
                </div>
            </div>
        </div>
                

        
    </section>
    
    
    
     <!-- end why best -->


     <section class="app-section">
        <div class="container-call call-to-action">
            <div class="app-content">
                <h2>Ready for a Game Changer? Book Your Demo Today!</h2>
                <p>Discover how our solution fits your needs. Book a demo today for a quick, personalized tour of the features that matter most to you.</p>
                <div class="app-buttons">
                    <a href="{{ route('contact') }}" class="google-play-button">
                        Schedule A Demo
                    </a>
                </div>
            </div>
            <div class="app-image">
                <img src="{{ asset('Modules/LandingPage/Resources/views/layouts/images/call-to.gif') }}" alt="Demo Image">
            </div>
        </div>
    </section>


        
     <!-- End contact form -->


      <!-- popup -->

    <div id="popup-modal" class="popup-overlay">
        <div class="popup-content">
            <!-- Left Side: Heading, Paragraph, and Subpoints -->
            <div class="popup-left">
                <h2><span style="color: #299dc6;">Connect360</span> for Your Business!</h2>
                <p>Join 23,000+ companies that endorse Connect360 for its exceptional software, support, and services.</p>
                <h4 style="margin-top: 50px;">Why Choose Us?</h4>
                <ul>
                    <li>Most affordable plans in the industry</li>
                    <li>Powerful, mobile-friendly employee self-service</li>
                    <li>Access to demo data for easy onboarding</li>
                    <li>Comprehensive guided admin tours</li>
                </ul>
            </div>

            <!-- Right Side: Name, Phone, Email Form -->
            <div class="popup-right">
            <form id="popup-form">
                <label for="name">Name*</label>
                <input type="text" id="name" name="name" placeholder="Enter your name" required />

                <label for="number">Phone Number*</label>
                <input type="tel" id="number" name="number" placeholder="Enter your phone number" required />

                <label for="email">Email Address*</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required />

                <label for="service">Select Service*</label>
                <select id="service" name="service" required>
                    <option value="" disabled selected>Select a service</option>
                    <option value="consulting">Starter</option>
                    <option value="development">Essential</option>
                    <option value="design">Growth</option>
                    <option value="marketing">Enterprise</option>
                </select>

                <button type="submit">Submit</button>
                <p id="form-message" style="color: green;"></p> <!-- Form message will appear here -->
            </form>

        <script src="https://cdn.jsdelivr.net/npm/emailjs-com@2/dist/email.min.js"></script>
        <script>
            (function() {
                emailjs.init("svxs7uxhEgtb84WK7"); // Your EmailJS user ID
            })();

            document.getElementById('popup-form').addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent default form submission

                // Fetch the form values
                var formData = {
                    name: document.getElementById('name').value,
                    number: document.getElementById('number').value,
                    email: document.getElementById('email').value,
                    service: document.getElementById('service').value
                };

                // Send the email using EmailJS
                emailjs.send("service_3zuxsnk", "template_h4irwlh", formData)
                    .then(function(response) {
                        console.log("SUCCESS!", response.status, response.text);
                        document.getElementById('form-message').textContent = "Your message has been sent!";
                    }, function(error) {
                        console.log("FAILED...", error);
                        document.getElementById('form-message').textContent = "Failed to send the message. Please try again.";
                    });
            });
        </script>

            </div>

            <!-- Close Button -->
            <span class="close-popup">&times;</span>
        </div>
    </div>



        <script>
            // JavaScript for Popup and Form handling

            const popupModal = document.getElementById("popup-modal");
            // const buttons = document.querySelectorAll(".table-button");
            const closeBtn = document.querySelector(".close-popup");

            // Open popup on button click
            buttons.forEach(button => {
                button.addEventListener("click", function () {
                    popupModal.style.display = "block";
                });
            });

            // Close popup on close button click
            closeBtn.addEventListener("click", function () {
                popupModal.style.display = "none";
            });

            // Close popup when clicking outside the modal
            window.addEventListener("click", function (event) {
                if (event.target === popupModal) {
                    popupModal.style.display = "none";
                }
            });

            // Handle form submission
            const form = document.getElementById('popup-form');
            form.addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent page refresh

                const name = document.getElementById('name').value;
                const number = document.getElementById('number').value;
                const email = document.getElementById('email').value;

                alert(`Name: ${name}\nPhone: ${number}\nEmail: ${email}`);

                // Close the popup
                popupModal.style.display = "none";
            });
        </script>  
    <!-- End popup -->

    <!-- Sticky Button -->
   <div class="sticky-demo-btn" onclick="openPopup()">
                <span>Book A Demo</span>
            </div>

            <!-- Popup -->
            <div id="demo-popup" class="demo-popup-overlay">
                <div class="demo-popup-container">
                    <div class="demo-popup-image">
                        <img src="{{ asset('Modules/LandingPage/Resources/views/layouts/images/popup1.png') }}" alt="Demo Image">
                    </div>
                    <div class="demo-popup-form">
                        <span class="close-popup-btn" onclick="closePopup()">&times;</span> <!-- Close Button -->
                        <form id="demo-form-1">
                            <div class="demo-input-group">
                                <input type="text" id="name1" name="name" placeholder="Full Name" required>
                            </div>
                            <div class="demo-input-group">
                                <input type="tel" id="phone1" name="phone" placeholder="Phone Number" required style="width:150%;"> 
                            </div>
                            <div class="demo-input-group">
                                <input type="email" id="email1" name="email" placeholder="Email Address" required>
                            </div>
                            <div class="demo-input-group">
                                <textarea id="requirement1" name="requirement" rows="4" placeholder="Message" required></textarea>
                            </div>
                            <div id="captcha-section" class="captcha-container">
                                <div class="captcha-box1">
                                    <span id="captcha-equation1"></span>
                                </div>
                                <input type="number" id="captcha-answer1" placeholder="Answer" required class="captcha-input">
                            </div>
                            <button type="submit" class="submit-demo-btn">Submit</button>
                            <p id="error-message1" style="color: red;"></p>
                        </form>

                    </div>

                </div>
            </div>

            <script>
                function openPopup() {
                    document.getElementById('demo-popup').style.display = 'flex';
                }

                function closePopup() {
                    document.getElementById('demo-popup').style.display = 'none';
                }

                window.onclick = function(event) {
                    var popup = document.getElementById('demo-popup');
                    if (event.target === popup) {
                        closePopup();
                    }
                };
            </script>
            <script>
                (function () {
                    // Initialize EmailJS
                    emailjs.init("0256Hqry936XqMD-e");

                    // Initialize intl-tel-input for phone fields
                    function initializeIntlTelInput(phoneInputField) {
                        return window.intlTelInput(phoneInputField, {
                            initialCountry: "auto",
                            geoIpLookup: function (callback) {
                                fetch("https://ipinfo.io/json", { headers: { Accept: "application/json" } })
                                    .then((resp) => resp.json())
                                    .then((resp) => {
                                        callback(resp.country);
                                    })
                                    .catch(() => {
                                        callback("US");
                                    });
                            },
                            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js", // Formatting utils
                            separateDialCode: true, // Display the country code separately
                        });
                    }

                    const phoneInputField1 = document.querySelector("#phone1");
                    const iti1 = initializeIntlTelInput(phoneInputField1);

                    // CAPTCHA Logic
                    function generateCaptcha() {
                        const num1 = Math.floor(Math.random() * 10) + 1;
                        const num2 = Math.floor(Math.random() * 10) + 1;
                        const operators = ["+", "-", "*"];
                        const operator = operators[Math.floor(Math.random() * operators.length)];

                        let answer;
                        if (operator === "+") {
                            answer = num1 + num2;
                        } else if (operator === "-") {
                            answer = num1 - num2;
                        } else if (operator === "*") {
                            answer = num1 * num2;
                        }

                        // Update CAPTCHA equation text
                        document.getElementById("captcha-equation1").innerText = ` ${num1} ${operator} ${num2}`;
                        return answer;
                    }

                    let correctCaptchaAnswer = generateCaptcha();

                    // Form validation and submission
                    document.getElementById("demo-form-1").addEventListener("submit", function (e) {
                        e.preventDefault(); // Prevent default form submission

                        const name = document.getElementById("name1").value;
                        const email = document.getElementById("email1").value;
                        const requirement = document.getElementById("requirement1").value;
                        const captchaInput = parseInt(document.getElementById("captcha-answer1").value, 10);
                        const errorMessage = document.getElementById("error-message1");

                        // Validate phone number
                        if (!iti1.isValidNumber()) {
                            errorMessage.innerText = "Please enter a valid phone number.";
                            return;
                        }

                        // Validate email
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(email)) {
                            errorMessage.innerText = "Please enter a valid email address.";
                            return;
                        }

                        // Validate CAPTCHA
                        if (captchaInput !== correctCaptchaAnswer) {
                            errorMessage.innerText = "Incorrect CAPTCHA. Please try again.";
                            correctCaptchaAnswer = generateCaptcha(); // Regenerate CAPTCHA
                            document.getElementById("captcha-answer1").value = ""; // Clear CAPTCHA input
                            return;
                        }

                        // Clear error message
                        errorMessage.innerText = "";

                        // Prepare data for EmailJS
                        const templateParams = {
                            to_name: name,
                            from_name: name,
                            email: email,
                            phone: iti1.getNumber(), // Full international phone number
                            message: requirement,
                        };

                        // Send email using EmailJS
                        emailjs.send("service_qtjhqpi", "template_0bb4t27", templateParams)
                            .then(function () {
                                alert("Email sent successfully!");
                                correctCaptchaAnswer = generateCaptcha(); // Reset CAPTCHA
                                document.getElementById("demo-form-1").reset(); // Reset form fields
                            })
                            .catch(function (error) {
                                alert("Failed to send email. Please try again.");
                                console.error("EmailJS error:", error);
                            });
                    });
                })();
            </script>


    <!-- End popup -->



     <!-- footer -->

    <footer class="footer-container">
        <div class="footer-content">
            <div class="footer-links">
            <a href="{{ route('pricing') }}">Pricing</a>
            <a href="{{ route('contact') }}">Contact</a>
            <a href="{{ route('privacy') }}">Privacy Policy</a>
                
            </div>

            <div class="footer-copyright">
                <p>© 2024 Connect360. All rights reserved.</p>
            </div>

            <div class="footer-icons">
                <div class="icon">
                    <i class="fa fa-phone"></i>
                    <div class="icon-info">Call: +91 98349 69704</div>
                </div>
                <div class="icon">
                    <i class="fa fa-envelope"></i>
                    <div class="icon-info">Email: info@connect360.in</div>
                </div>
                <div class="icon">
                    <i class="fa fa-map-marker"></i>
                    <div class="icon-info">Address: Office U2, First Floor, Runwal Platinum,
                    Bavdhan</div>
                </div>
            </div>
        </div>
    </footer>

    

     <!-- end footer -->

   


    <!-- Sticky Button -->
    
   
    
</body>
</html>