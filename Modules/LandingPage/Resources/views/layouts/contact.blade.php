<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Connect360 | Get in Touch for Business Solutions and HRM features</title>
    <link rel="stylesheet" href="{{ Module::asset('LandingPage:Resources/views/layouts/contact.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="{{ Module::asset('LandingPage:Resources/views/layouts/footer.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    
    
    
    
    
    
    
    
    <!-- Meta Tags for SEO -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Reach out to Connect360 for expert business solutions and HR software with features like attendance tracking, leave management, automated payroll, compliance, and employee document organization. Our team is ready to assist with your inquiries, provide support, and offer tailored strategies for business growth. Contact us today!">
    <meta name="keywords" content="Connect360, business solutions, HR software, attendance tracking, leave management, payroll automation, compliance, employee document organization, business support, tailored strategies">
    <meta name="author" content="Connect360">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph / Facebook Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Connect360 - Expert Business Solutions & HR Software Support">
    <meta property="og:description" content="Contact Connect360 for tailored business strategies and advanced HR software with attendance tracking, payroll automation, and compliance. Let us help your business grow.">
    <meta property="og:image" content="URL_to_image">
    <meta property="og:url" content="https://www.connect360.com">
    
    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Connect360 - Business Solutions & HR Software Support">
    <meta name="twitter:description" content="Reach out to Connect360 for expert solutions and support. Discover HR features like payroll automation, leave management, and document organization. Contact us today!">
    <meta name="twitter:image" content="URL_to_image">
    <meta name="twitter:url" content="https://www.connect360.com">
    
    <!-- Favicon -->
    <link rel="icon" href="favicon.ico" type="image/x-icon">



    <script type="text/javascript"
                src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js">
        </script>
        <script type="text/javascript">
        (function(){
            emailjs.init({
                publicKey: "0256Hqry936XqMD-e",
            });
        })();
        </script>

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

    <!-- Top Section: Contact Us and Social Icons -->
    <section class="top-section">
        <div class="contact-info-left">
            <h1>Contact Us</h1>
            <p>Get In Touch With Us For More Details.</p>
            <div class="social-icons">
                <a href="https://www.instagram.com/connect.360_/" target="_blank"><i class="fab fa-instagram"></i></a>
                <a href="https://www.facebook.com" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <a href="https://www.linkedin.com" target="_blank"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
        
    </section>

    <!-- Main Section: What will be next step + Contact Form -->
    <section class="contact-section">
        <div class="left-side">
            <h2>What will be next step?</h2>
            <p>You are one step closer to building your perfect product</p>
            <ul class="steps-list">
                <li>
                    <span class="step-number">1</span>
                    <p><strong style="font: size 20px;">Curious about our products or pricing?
                    </strong><br>Let us show you around with a demo or answer any questions you’ve got!</p>
                </li>
                <li>
                    <span class="step-number">2</span>
                    <p><strong style="font: size 20px;">Thinking about adding licenses, switching plans, or exploring extra services?
                    </strong><br>We’re here to guide you through every option.
                    </p>
                </li>
                <li>
                    <span class="step-number">3</span>
                    <p><strong style="font: size 20px;">Need to update or cancel your subscription?
                    </strong><br>Whether you’re renewing, tweaking, or saying goodbye, we’re ready to assist you every step of the way.</p>
                </li>
            </ul>

            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const listItems = document.querySelectorAll(".steps-list li");

                    listItems.forEach((item, index) => {
                        item.style.setProperty('--delay', ${index * 0.3}s); // Set delay for each item
                    });
                });

            </script>

        </div>
    
    
        <div class="right-side">
            <div class="form-heading">
                <h3>Write us a few words about your project, and we’ll prepare a proposal for you within <span>24 hours</span>.</h3>
            </div>
            <form class="contact-form" id="contact-form-2">
                <div class="form-row">
                    <input type="text" id="from_name" placeholder="Name *" required>
                    <input type="email" id="email" placeholder="Email Address *" required>
                </div>
                <div class="form-row">
                    <div class="phone-container">
                        <input type="tel" id="phone" placeholder="Phone Number *" required style="width:92%;">
                    </div>
                    <input type="text" id="company" placeholder="Company *" required>
                </div>
                <div class="form-row">
                    <input type="number" id="employees" placeholder="No of Employees *" required>
                </div>
                <textarea id="requirements" placeholder="Tell us about your requirements *" required></textarea>
                <div class="form-row captcha-row">
                    <label id="captcha-question">6 + 5 =</label>
                    <input type="text" id="captcha-answer" placeholder="Answer" required>
                </div>
                <button type="submit">Submit</button>
            </form>
        </div>


        <script>
            document.addEventListener("DOMContentLoaded", function () {
            const phoneInput = document.querySelector("#phone");
            const iti = window.intlTelInput(phoneInput, {
                initialCountry: "auto",
                geoIpLookup: function (callback) {
                    fetch("https://ipinfo.io/json", { headers: { Accept: "application/json" } })
                        .then((resp) => resp.json())
                        .then((resp) => callback(resp.country))
                        .catch(() => callback("US"));
                },
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                separateDialCode: true,
            });

            let captchaSolution;

            function generateCaptcha() {
                const num1 = Math.floor(Math.random() * 10);
                const num2 = Math.floor(Math.random() * 10);
                captchaSolution = num1 + num2;
                document.getElementById("captcha-question").textContent = `${num1} + ${num2} =`;
            }

            generateCaptcha();

            document.getElementById("contact-form-2").addEventListener("submit", function (event) {
                event.preventDefault();

                const errors = [];
                const captchaAnswer = parseInt(document.getElementById("captcha-answer").value.trim(), 10);
                const email = document.getElementById("email").value.trim();
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (!emailPattern.test(email)) {
                    errors.push("Invalid email address.");
                }
                if (!iti.isValidNumber()) {
                    errors.push("Invalid phone number.");
                }
                if (captchaAnswer !== captchaSolution) {
                    errors.push("Incorrect CAPTCHA answer.");
                    generateCaptcha(); // Reset CAPTCHA
                }

                if (errors.length > 0) {
                    alert(errors.join("\n"));
                    return;
                }

                const formData = {
                    from_name: document.getElementById("from_name").value.trim(),
                    email,
                    phone: iti.getNumber(),
                    company: document.getElementById("company").value.trim(),
                    employees: document.getElementById("employees").value.trim(),
                    requirements: document.getElementById("requirements").value.trim(),
                };

                // Use EmailJS for email sending
                emailjs.send("service_qtjhqpi", "template_hdzfgaa", formData)
                    .then(() => {
                        alert("Form submitted successfully!");
                        document.getElementById("contact-form-2").reset();
                        generateCaptcha();
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        alert("Failed to submit the form. Please try again.");
                    });
            });
        });


        </script>



    </section>


    
    <footer class="footer-container">
        <div class="footer-content">
            <!-- Left: Menu Links -->
            <div class="footer-links">
            <a href="{{ route('pricing') }}">Pricing</a>
            <a href="{{ route('contact') }}">Contact</a>
            <a href="{{ route('privacy') }}">Privacy Policy</a>
                
            </div>

            <!-- Center: Copyright -->
            <div class="footer-copyright">
                <p>© 2024 Connect360. All rights reserved.</p>
            </div>

            <!-- Right: Icons with hover info -->
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


    
    
    
</body>
</html>
