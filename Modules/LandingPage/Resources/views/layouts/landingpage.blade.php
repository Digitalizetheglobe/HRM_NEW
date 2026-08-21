
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Connect360 | Innovative Business Solutions and HRM Functions  </title>

        <!-- <link rel="stylesheet" href="zohocustom.css"> -->
        <link rel="stylesheet" href="{{ Module::asset('LandingPage:Resources/views/layouts/zohocustom.css') }}">
        <!-- <link rel="stylesheet" href="{{ Module::asset('LandingPage:Resources/views/layouts/2474.css') }}"> -->
        <link rel="stylesheet" href="{{ Module::asset('LandingPage:Resources/views/layouts/style.css') }}">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="{{ Module::asset('LandingPage:Resources/views/layouts/footer.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>


        <!-- jQuery and Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

        <!-- <script src="https://cdn.emailjs.com/dist/email.min.js"></script> -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

           <!-- CSS for intl-tel-input -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
        
        
        
        
        <!-- Meta Tags for SEO -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Connect360 provides innovative business solutions and expert marketing strategies to help businesses succeed. Discover efficient HRM functions that streamline workforce management, automate payroll, organize employee documents, and ensure compliance.">
        <meta name="keywords" content="Connect360, business solutions, marketing strategies, HRM, workforce management, payroll automation, employee document organization, compliance">
        <meta name="author" content="Connect360">
        <meta name="robots" content="index, follow">
        
        <!-- Open Graph / Facebook Meta Tags -->
        <meta property="og:type" content="website">
        <meta property="og:title" content="Connect360 - Business Solutions & Expert Marketing Strategies">
        <meta property="og:description" content="Empowering businesses with innovative HRM functions and expert marketing strategies. Streamline workforce management, automate payroll, and more with Connect360.">
        <meta property="og:image" content="URL_to_image">
        <meta property="og:url" content="https://www.connect360.com">
        
        <!-- Twitter Meta Tags -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="Connect360 - Business Solutions & Expert Marketing Strategies">
        <meta name="twitter:description" content="Boost your business with Connect360's HRM and marketing solutions. Manage your workforce efficiently, automate payroll, and stay compliant.">
        <meta name="twitter:image" content="URL_to_image">
        <meta name="twitter:url" content="https://www.connect360.com">
        
        <!-- Favicon -->
        <link rel="icon" href="favicon.ico" type="image/x-icon">

        
        
        
        
        

        <!-- JavaScript for intl-tel-input -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>


       
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js">
        </script>
        <script type="text/javascript">
            (function(){
                emailjs.init({
                    publicKey: "0256Hqry936XqMD-e",
                });
            })();
        </script>



 
</head>
<body></body>
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

    
    
    
    <!-- Banner -->
    
    <section id="banner">
        <div class="container-banner">
            <div class="text-section">
                <h2 class="hone">STREAMLINE SUCCESS THROUGH.</h2>
                <h1 class="htwo">HR Management</h1>
                <p>
                    Boost your HR efficiency and employee satisfaction with our cutting-edge HRM software, tailored to meet the unique needs of your organization.
                </p>
            </div>
            <div class="form-section">
                <form id="contactForm">
                    <input type="text" name="name" id="name" placeholder="Full Name" required>

                    <!-- Phone input field for intl-tel-input -->
                    <input type="tel" id="phone" name="phone" placeholder="Phone Number" required><br>

                    <br>  
                    <input type="email" id="email" name="email" placeholder="Email Address" required>

                    <textarea name="message" id="message" rows="4" placeholder="Message" required></textarea>

                    
                    <div id="captcha-section" class="captcha-container">
                        <div class="captcha-box">
                            <span id="captcha-equation"></span>
                        </div>
                        <input type="number" id="captcha-answer" placeholder="Answer" required class="captcha-input">
                        </div>


                    <button class="table-button1" type="submit">Submit</button>
                </form>

                <p id="error-message" style="color: red;"></p> <!-- Error message display -->
            </div>


        </div>


        
            
        <script>
                    // Initialize intl-tel-input for the phone input field
            var phoneInput = document.querySelector("#phone");

            var iti = window.intlTelInput(phoneInput, {
                initialCountry: "auto", // Automatically set the user's country
                geoIpLookup: function (success, failure) {
                    fetch('https://ipinfo.io/json', { headers: { 'Accept': 'application/json' } })
                        .then((resp) => resp.json())
                        .then((resp) => { success(resp.country); })
                        .catch(() => { success('us'); });
                },
                separateDialCode: true, // Show the country code separately
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js", // Required for validation/formatting
            });

            // Initialize EmailJS
            emailjs.init('0256Hqry936XqMD-e'); // Replace with your user ID

            // CAPTCHA Logic
            function generateCaptcha() {
                const num1 = Math.floor(Math.random() * 10) + 1; // Random number 1
                const num2 = Math.floor(Math.random() * 10) + 1; // Random number 2
                const operators = ['+', '-', '*']; // Supported operators
                const operator = operators[Math.floor(Math.random() * operators.length)]; // Randomly select operator

                let captchaAnswer;
                let captchaEquation;

                // Calculate answer based on operator
                if (operator === '+') {
                    captchaAnswer = num1 + num2;
                    captchaEquation = `${num1} + ${num2}`;
                } else if (operator === '-') {
                    captchaAnswer = num1 - num2;
                    captchaEquation = `${num1} - ${num2}`;
                } else if (operator === '*') {
                    captchaAnswer = num1 * num2;
                    captchaEquation = `${num1} * ${num2}`;
                }

                // Display CAPTCHA equation
                document.getElementById('captcha-equation').innerText = `${captchaEquation} = `;

                // Return the correct answer for validation
                return captchaAnswer;
            }

            // Generate the initial CAPTCHA question
            let correctAnswer = generateCaptcha();

            // Handle form submission
            document.getElementById('contactForm').addEventListener('submit', function (event) {
                event.preventDefault();

                var phoneNumber = iti.getNumber(); // Get the full international number
                var email = document.getElementById('email').value;
                var name = document.getElementById('name').value;
                var message = document.getElementById('message').value;
                var captchaInput = parseInt(document.getElementById('captcha-answer').value);
                var errorMessage = document.getElementById('error-message');

                // Validate the phone number
                if (!iti.isValidNumber()) {
                    errorMessage.innerText = 'Please enter a valid phone number.';
                    return;
                }

                // Validate CAPTCHA
                if (captchaInput !== correctAnswer) {
                    errorMessage.innerText = 'Incorrect CAPTCHA. Please try again.';
                    correctAnswer = generateCaptcha(); // Reset CAPTCHA
                    document.getElementById('captcha-answer').value = ''; // Clear answer field
                    return;
                }

                // Prepare EmailJS template parameters
                var templateParams = {
                    to_name: 'EmailJS Team', // Replace with recipient's name if needed
                    from_name: name,
                    email: email,
                    phone: phoneNumber,
                    message: message
                };

                // Send the email
                emailjs.send('service_qtjhqpi', 'template_0bb4t27', templateParams)
                    .then(function (response) {
                        alert('Message sent successfully!');
                        errorMessage.innerText = ''; // Clear the error message
                        correctAnswer = generateCaptcha(); // Reset CAPTCHA
                        document.getElementById('contactForm').reset(); // Reset form fields
                    }, function (error) {
                        errorMessage.innerText = 'Failed to send the message. Please try again later.';
                        console.error('EmailJS error:', error);
                    });
            });

        </script>


    </section>
    <!-- Banner End -->
     <!-- Feature Section -->
     <section class="feature-section">
        <div class="feature-text">
            <h2>Our Amazing Features Helpful for Your HR</h2>
            <p>
                Streamline your HR tasks with our powerful features. From payroll processing to project tracking, our tools simplify management, boost productivity, and enhance employee satisfaction.
            </p>
        </div>

        <div class="feature-image">
            <img src="{{ asset('Modules/LandingPage/Resources/views/layouts/images/HRM.gif') }}" alt="Description of image">
        </div>
    </section>

    <!-- End Feture Section -->
    <!-- Feture SHow -->
        <section id="feture-show">
            <div class="unique-container">
                <h2 class="unique-heading">Transforming<span style="color: #005b33; font-weight: bold;"> HR Management</span> for Success</h2>
                <div class="unique-buttons">
                    <a href="#" class="unique-btn-demo">Book A Demo!</a>
                    <a href="#function-section" class="unique-btn-features">See All Features</a>
                </div>
            </div>
            <div class="image-container">
                <img src="{{ asset('Modules/LandingPage/Resources/views/layouts/images/HRMFE.gif') }}" alt="HR Management" class="big-image">
            </div>  
        </section>

    
    
     
     <!-- End Feture Show -->

     <!-- Function SHow -->
     <section id="function-section">
        <h2 class="function-heading">Discover Our Comprehensive <span style="color: #005b33;">HRM Functions</span></h2>
        <p class="function-paragraph">
            Explore our range of powerful HRM functions designed to streamline your processes and enhance efficiency across every aspect of human resource management.
        </p>

        <!-- First Card Section -->
        <div class="card-section">
            <div class="container">
                <div class="card">
                    <img src="{{ asset('Modules/LandingPage/Resources/views/layouts/images/meeting.gif') }}" alt="Meeting Icon">
                    <h3>Streamline Workforce Management</h3>
                    <p>Effortlessly manage onboarding, development, and offboarding. Keep your team organized and productive.</p>
                </div>
                <div class="card">
                    <img src="{{ asset('Modules/LandingPage/Resources/views/layouts/images/clock.gif') }}" alt="Clock Icon">
                    <h3>Simplify Attendance Tracking</h3>
                    <p>Effortlessly monitor employee attendance, ensuring accountability and boosting overall productivity.</p>
                </div>
                <div class="card">
                    <img src="{{ asset('Modules/LandingPage/Resources/views/layouts/images/leave.gif') }}" alt="Leave Icon">
                    <h3>Streamlined Leave Requests</h3>
                    <p>Enable easy leave requests and approvals, promoting a healthy work-life balance for your team.</p>
                </div>
            </div>
        </div>

        <!-- Second Card Section with Background -->
        <div class="card-section second-card">
            <div class="container">
                <div class="card">
                    <img src="{{ asset('Modules/LandingPage/Resources/views/layouts/images/salary.gif') }}" alt="Salary Icon">
                    <h3>Automate Payroll Processing</h3>
                    <p>Quickly and accurately calculate payroll, reducing errors and ensuring timely payments.</p>
                </div>
                <div class="card">
                    <img src="{{ asset('Modules/LandingPage/Resources/views/layouts/images/document.gif') }}" alt="Document Icon">
                    <h3>Organize Employee Documents</h3>
                    <p>Manage employee documentation with ease, ensuring everything is secure and organized.</p>
                </div>
                <div class="card last-crad">
                    <img src="{{ asset('Modules/LandingPage/Resources/views/layouts/images/tick.gif') }}" alt="Tick Icon">
                    <h3>Ensure Compliance</h3>
                    <p>Stay compliant with HR regulations by managing employee data and processes efficiently.</p>
                </div>
            </div>
        </div>
    </section>


    <script>
         document.addEventListener("DOMContentLoaded", function() {
            const cards = document.querySelectorAll(".card");
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("show"); // Add class to show the card
                        observer.unobserve(entry.target); // Stop observing once it's shown
                    }
                });
            }, {
                threshold: 0.5 // Trigger when 10% of the card is visible
            });

            cards.forEach(card => {
                observer.observe(card); // Observe each card
            });
        });


    </script>



      
     
     <!-- End Function Show -->

     

      <!-- Why choose us -->
       

    <section class="special-features-section">
        <h2 class="slider-heading" style="font-size: 2.5rem;">Special Features</h2>
        <div class="icon-line"></div>
        <p class="slider-paragraph" style="font-size: 1.2rem;">Connect360 is launched with everything you need. We've got a lot of amazing and cool features, so here we go, with unlimited features, go and check out!</p>
    </section>

    <section>
        <div class="main-container">
            <!-- First Div - Gradient Cards -->
            <div class="section first-section">
                <div class="gradient-card fetaure-box">
                    <h5>Comprehensive HRM solutions tailored for your business.</h5>
                </div>
                <div class="gradient-card">
                    <h5>Intuitive interface designed for easy everyday use.</h5>
                </div>
                <div class="gradient-card">
                    <h5>Scalable system suitable for all business sizes.</h5>
                </div>
            </div>

            <!-- Second Div - Mobile Preview with Video -->
            <div class="section middle-section">
                <div class="phone-container">
                    <video autoplay loop muted playsinline>
                        <source src="{{ asset('Modules/LandingPage/Resources/views/layouts/images/mobile.mp4') }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>

            <!-- Third Div - Gradient Cards -->
            <div class="section third-section">
                <div class="gradient-card">
                    <h5>Expert support available around the clock, 24/7.</h5>
                </div>
                <div class="gradient-card">
                    <h5>Advanced reporting to drive informed business decisions.</h5>
                </div>
                <div class="gradient-card">
                    <h5>Secure platform ensuring compliance and data protection.</h5>
                </div>
            </div>
        </div>
    </section>

       <!-- End Why choose us  -->

    <!-- contact form -->
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
		
		</section>
    <!-- end whatsapp button  -->



    <!-- main popup -->
    <div id="mainMiddelPopup" class="main-middel-popup">
        <div class="main-middel-popup-content">
            <!-- Left side content -->
            <div class="main-left-content">
                <h2>Level up your HR game</h2>
                <p>Explore how Connect360 can help you save time on HR tasks.</p>
                <div class="main-button-container">
                    <button class="main-btn main-red-btn">
                        <a href="{{ route('contact') }}" class="main-btn main-red-btn">REQUEST A DEMO</a>
                    </button>
                </div>
            </div>
            <!-- Right side image -->
            <div class="main-right-image">
                <img src="{{ asset('Modules/LandingPage/Resources/views/layouts/images/middel.png') }}" alt="Person with Laptop">
            </div>
            <!-- Close button -->
            <span class="main-close-btn">&times;</span>
        </div>
    </div>

<script>
    // Wait for the DOM to load
    document.addEventListener('DOMContentLoaded', function () {
        const mainMiddelPopup = document.getElementById("mainMiddelPopup");
        const closeBtn = document.querySelector(".main-close-btn");

        // Function to check screen width and toggle popup display
        function checkScreenWidth() {
            if (window.innerWidth < 1024) {
                mainMiddelPopup.style.display = "none"; // Hide on small devices
            } else if (!localStorage.getItem('popupSeen')) {
                mainMiddelPopup.style.display = "flex"; // Show on large screens if not seen
            }
        }

        // Show the popup with a delay if screen width is 1024px or more
        setTimeout(function () {
            if (window.innerWidth >= 1024 && !localStorage.getItem('popupSeen')) {
                mainMiddelPopup.style.display = "flex";
            }
        }, 5000); // Show popup after 5 seconds

        // Close the popup when clicking on the close button
        closeBtn.onclick = function () {
            mainMiddelPopup.style.display = "none";
            localStorage.setItem('popupSeen', 'true'); // Mark popup as seen
        };

        // Close the popup when clicking outside the popup content
        window.onclick = function (event) {
            if (event.target == mainMiddelPopup) {
                mainMiddelPopup.style.display = "none";
                localStorage.setItem('popupSeen', 'true'); // Mark popup as seen
            }
        };

        // Check on window resize to adjust popup visibility
        window.addEventListener("resize", checkScreenWidth);

        // Initial check when the page loads
        checkScreenWidth();
    });
</script>


     <!-- end main popup -->


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
    



   

     <!-- Sticky Button -->
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
                                <input type="tel" id="phone1" name="phone" placeholder="Phone Number" required>
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

</body>
</html>