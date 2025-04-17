
<nav>
    <ul>
        <li>
            <div class="school-logo">
                <a href="{{ url('/') }}"><img src="{{ asset('icons/Logo.png') }}" alt="School Logo">USUSAN ELEMENTARY SCHOOL</a>
            </div>
        </li>
        <li><div id="homehover"><a href="{{ url('/') }}">Home</a></div></li>
        <li><div id="aboutushover"><a href="{{ url('/about-us') }}">About Us</a></div></li>
        <li><button class="login-button" onclick="toggleLoginForm()">
            <img src="{{ asset('icons/Login.png') }}" alt="User Icon" class="login-icon">
            <h3 class="login-text">Log In</h3></button>
        </li>
    </ul>
</nav>