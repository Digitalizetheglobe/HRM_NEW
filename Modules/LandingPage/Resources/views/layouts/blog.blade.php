<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connect360</title>
    <link rel="stylesheet" href="{{ Module::asset('LandingPage:Resources/views/layouts/blog.css') }}">
    <link rel="stylesheet" href="{{ Module::asset('LandingPage:Resources/views/layouts/footer.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- CSS for intl-tel-input -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />

<!-- JavaScript for intl-tel-input -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>



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



<!-- navbar -->
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

<!-- end navbar -->
 <!-- blog section -->
        <div class="container">
                <div class="content">
                    <!-- Blog List Section -->
                     
                    <div class="blog-list">
                            <div class="blog-column">
                                <h1>LATEST POST :</h1>
                                <br><hr><br>
                            </div>

                            <a href="{{ route('blog2') }}" class="blog-link">
                                <div class="blog-post">
                                    <img src="{{ asset('Modules/LandingPage/Resources/views/layouts/images/blog1.jpg') }}" alt="Blog 2">
                                    <h2>Top 8 Must-Have Features in HR Software for 2024</h2>
                                    <p>Posted on October 4, 2024 | By Connect360</p>
                                </div>
                            </a>                     
                            <hr><br>

                            <a href="{{ route('blog1') }}" class="blog-link">
                                <div class="blog-post">
                                    <img src="{{ asset('Modules/LandingPage/Resources/views/layouts/images/blog2.jpg') }}" alt="Blog 1">
                                    <h2>How HR Software is Revolutionizing Remote Work Management</h2>
                                    <p>Posted on October 4, 2024 | By Connect360</p>
                                </div>
                            </a>                     
                            <hr><br>

                            <a href="{{ route('blog3') }}" class="blog-link">
                                <div class="blog-post">
                                    <img src="{{ asset('Modules/LandingPage/Resources/views/layouts/images/blog3.jpg') }}" alt="Blog 3">
                                    <h2>The Future of HR Analytics: Data-Driven Decision Making with HR Software</h2>
                                    <p>Posted on October 4, 2024 | By Connect360</p>
                                </div>
                            </a>                     
                            <hr><br>
                        
                        <!-- Add more blogs as needed -->
                    </div>

                    <!-- Sticky Right Section -->
                    <div class="right-sidebar">
                        <div class="sticky-box">
                        <div class="">
                            <h3>Connect with Us for Tailored Solutions!</h3>
                        </div><br>
                        <div class="form-section">
                            <form id="contactForm">
                                <input type="text" name="name" placeholder="Full Name" required>

                                <!-- Phone input field for intl-tel-input -->
                                <input type="tel" id="phone" name="phone" placeholder="Phone Number" required><br>

                                <br>  <input type="email" id="email" name="email" placeholder="Email Address" required>

                                <textarea name="message" rows="4" placeholder="Message" required></textarea>

                                <label>
                                    <input type="checkbox" name="agree" required> Yes, I agree with the privacy policy and terms and conditions.
                                </label>

                                <button class="table-button1" type="submit">Submit</button>
                            </form>

                            <p id="error-message" style="color: red;"></p> <!-- Error message display -->
                        </div>


                        <script>
                            // Initialize intl-tel-input for the phone input field
                            var phoneInput = document.querySelector("#phone");

                            var iti = window.intlTelInput(phoneInput, {
                                initialCountry: "auto",  // Automatically set the user's country
                                geoIpLookup: function(success, failure) {
                                    fetch('https://ipinfo.io/json', {headers: {'Accept': 'application/json'}})
                                    .then((resp) => resp.json())
                                    .then((resp) => { success(resp.country); })
                                    .catch(() => { success('us'); });
                                },
                                separateDialCode: true,  // Show the country code separately
                                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js", // Required for validation/formatting
                            });

                            document.getElementById('contactForm').addEventListener('submit', function(event) {
                            event.preventDefault();

                            var phoneNumber = iti.getNumber();  // Get the full international number
                            var email = document.getElementById('email').value;
                            var errorMessage = document.getElementById('error-message');

                            // Validate the phone number
                            if (!iti.isValidNumber()) {
                                errorMessage.innerText = 'Please enter a valid phone number.';
                                return;
                            }

                            // Additional validation logic for email, etc.

                            // If valid, send the form data
                            errorMessage.innerText = '';  // Clear the error message
                            alert('Form submitted successfully with phone number: ' + phoneNumber);
                                    });
                        </script>


                        </div>
                    </div>
                </div>
        </div>
<!-- end blog section -->


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
</body>
</html>
