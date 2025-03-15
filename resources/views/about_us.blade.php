<!-- filepath: /Users/laineljohn/Desktop/projects/php/cs2/capstone2_laravel/resources/views/about_us.blade.php -->
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ususan Elementary School</title>
    <link rel="stylesheet" href="{{ asset('css/aboutUsStyle.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbarStyle.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('icons/Logo.png') }}">
</head>
<body>

    <x-navigation />

    <div class="login-container" id="loginContainer">
        <div class="login-form" id="loginForm">
            <button class="close-btn" onclick="toggleLoginForm()"></button>
            <img src="{{ asset('icons/Logo.png') }}" alt="Large Logo" class="logo-center">
            <h2>Teacher & Admin Log In</h2>

            <form>
                <input type="email" class="input-field" placeholder="Your Email Address" required>
                <div class="password-container">
                    <input type="password" id="password" class="input-field" placeholder="Enter Your Password" required>
                    <img src="{{ asset('icons/Eye.png') }}" alt="Show Password" class="toggle-password" onclick="togglePasswordVisibility()">
                </div>
                <button type="submit">LOGIN</button>
                <div class="links">
                    <a href="#">Forgot Password?</a>
                </div>
            </form>
        </div>
    </div>

    <div class="about-us-content">
        <h2>History of the School</h2>
        <p>
            Ususan Elementary School rose from a donated 4,321.25 sq. meter lot located at Ususan, Taguig, and Rizal.
            It was on June 25, 1931 when formal schooling in Grades I and II classes started in a make-shift schoolhouse constructed on the said site.
            Miss Elpidia Carlos and Miss Ana Bunyi were the first mentors during those struggling days. Thereafter, a permanent building with two-standard classrooms was erected, housing primary classes only.
            The first principal then was Elpidia C. Tanyag, and through her initiative more classrooms were added from that period to 1963 to cater to the growing population.
        </p>
        <br />
        <p>
            The old building was later abolished and under the provincial Board Resolution 1077 during the administration of Governor Isidro Rodriguez, Mayor Monico C. Tanyag, Mrs. Caridad P. Umali, the Principal and Barrio Captain Daniel Castillo,
            the Rodriguez Building was constructed on February 5, 1969 and was finished on October 17, 1969. From that day onward the same school building has been existing on the same area. However, other buildings were constructed like the Carino Building, Multi-purpose Building and Cayetano Building replacing the old Antonio Building.
            Through the initiative of the barangay captain of Ususan, Sonny P. Marcelino, Ususan Elementary School is fortunate enough to be the beneficiary of 3-classroom building donated by Ayala Land Foundation Inc. constructed on November, 2003 and finished on June , 2004.
        </p>
        <br />
        <p>
            Another 3-storey building with 9 classrooms was erected on June 11, 2004 and was inaugurated June, 2005 through the LOGO Find project funded by the World Bank.
            Last May, 2013, Overseas Filipinos in Virginia, USA headed by Ususan Elementary School alumni Dr.Bayani Manalo and his wife Ursula donated a two-classroom kinder building. It was called Feed the Hungry Little Red School House.
            The donation was made possible also by couple John Teithof and Anita Sy in coordination with Commission on Filipinos Overseas.
        </p>
        <br />
        <p>
            Early of October 2014, a three-story building with 12 classrooms was initially constructed through the initiative of Philippine Business for Social Progress and through the sponsorship of the Australian Aid.
            This building was turned over on November 24, 2015 during the last punchlisting with the representatives from the Australian Embassy, PBSP and from the Division of Taguig Pateros.
            At present the school is still waiting for the supplies of armchairs to be used by the pupils with the hope that the recipients will benefit from it.
        </p>
        <br />
        <p> Throughout the years, Ususan Elemenatry School has been headed by dedicated school heads in the persons of Mrs. Adoracion Capistrano, Mrs Simeona Capco, Miss Melita Lammatao, Mr. Bayani Z. Pili, Miss Lydia G. Pili, Mrs. Teresita M. de Jesus, Mrs. Flerida P. Silvestre, Mrs. Felisa A. Binuya, Mrs. Josefina R. Granada, Ms. Annette P. Cristobal, Mrs. Celia A. Zuniga, Mr. Shoji G. Gerona and Mrs. Ester L. Catimon. </p>
        <br />

        <p> Ususan Elementary School as of May, 2021 has an enrolment of 1937 with 62 teaching and 19 non-teaching personnel under the supervision of the present instructional leader. </p>
        <br />

        <p>
            Despite numerous educational changes that come and go, Ususan Elementary School at present is standing tall and proud with the readiness to face the challenges of engaging all stakeholders to be able to produce quality students and generate quality life for them.
            The school is among the top schools in the Division in terms of achievement level and winnings in the different contests. At present, there is a smooth existence of the institution despite many challenges. Ususan ES remains to be consistent in serving the people and its learners.
        </p>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Ususan Elementary School. All rights reserved.</p>
        <span class="privacy-policy" onclick="openModal()">Privacy Policy</span>
    </footer>

    <div id="privacyModal" class="modal">
        <h2>Privacy Policy</h2>
        <button class="close-btn" onclick="closeModal()"></button>
        <form>
            <input type="text" placeholder="Enter information..." style="width: 100%; padding: 10px;">
        </form>
    </div>

    <script>
        // Ensure login form is hidden on page load
        document.addEventListener("DOMContentLoaded", () => {
            const loginContainer = document.getElementById("loginContainer");
            const loginForm = document.getElementById("loginForm");

            // Initially hide the login container and form
            loginContainer.style.visibility = "hidden";
            loginContainer.style.opacity = 0;
            loginForm.style.opacity = 0;
        });

        // Toggle the visibility of login form and container
        function toggleLoginForm() {
            const loginContainer = document.getElementById("loginContainer");
            const loginForm = document.getElementById("loginForm");

            // If the login form is already visible, hide it
            if (loginContainer.style.visibility === "visible") {
                // Remove the show class to start the closing transition
                loginForm.classList.remove("show");

                // Make the closing faster by reducing the delay
                setTimeout(() => {
                    // Hide the form and the background container
                    loginForm.style.opacity = 0;
                    loginContainer.style.opacity = 0;
                    loginContainer.style.visibility = "hidden";
                }, 50);  // Faster closing transition (less delay)
            } else {
                // Show the background container
                loginContainer.style.visibility = "visible";
                loginContainer.style.opacity = 1;

                // Introduce a small delay before showing the form and triggering the pop-up effect
                setTimeout(() => {
                    loginForm.style.opacity = 1;  // Make the form visible
                    setTimeout(() => {
                        loginForm.classList.add("show");  // Trigger the pop-up animation
                    }, 10);  // Small delay to ensure the form is shown before the animation
                }, 10);  // Delay before showing the form
            }
        }

        // Toggle password visibility
        function togglePasswordVisibility() {
            const passwordField = document.getElementById("password");
            const toggleIcon = document.querySelector(".toggle-password");
            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.src = "{{ asset('icons/Eye.png') }}";
            } else {
                passwordField.type = "password";
                toggleIcon.src = "{{ asset('icons/hidden.png') }}";
            }
        }

        document.querySelector(".close-btn").addEventListener("click", function () {
            document.querySelector(".input-field[type='email']").value = ""; // Clear email field
            document.querySelector(".input-field[type='password']").value = ""; // Clear password field
        });

        function openModal() {
            document.getElementById('privacyModal').classList.add('show');
        }

        function closeModal() {
            document.getElementById('privacyModal').classList.remove('show');
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const navLinks = document.querySelectorAll("nav a");

            function setActiveLink() {
                // Get current page URL
                const currentPage = window.location.pathname.split("/").pop();

                navLinks.forEach(link => {
                    // Exclude .pos-admin from being affected
                    if (!link.closest(".school-logo")) {
                        const linkPage = link.getAttribute("href");

                        // If the link matches the current page, make it active
                        if (currentPage === linkPage) {
                            link.classList.add("active");
                        } else {
                            link.classList.remove("active");
                        }
                    }
                });
            }

            // Call the function when the page loads
            setActiveLink();
        });
    </script>
</body>
</html>