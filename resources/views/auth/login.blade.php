<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Ususan Elementary School</title>
        <link rel="icon" type="image/png" href="{{ asset('icons/Logo.png') }}" />
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />

        <style>
            body::before {
                content: "";
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 130%;
                background-color: #fef977;
                z-index: -1;
            }

            .login-container {
                display: flex;
                width: 900px;
                height: auto;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
                background-color: rgba(255, 255, 255, 0.65);
            }

            .login-image {
                width: 50%;
                background: url("{{ asset("icons/BgCover.jpeg") }}") no-repeat center center;
                background-size: 165%;
                background-position: center;
            }

            .login-form-container {
                width: 50%;
                padding: 40px;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .login-input {
                width: 100%;
                padding: 10px 10px 10px 40px;
                border: 1.5px solid #ccc;
                border-radius: 8px;
                background-color: white;
                transition: border-color 0.2s ease;
            }

            .login-input:focus {
                outline: none;
                border-color: #6da6f3;
                box-shadow: 0 0 0 3px rgba(109, 166, 243, 0.2);
            }

            .login-button {
                background-color: #ffaa01;
                color: white;
                font-weight: bold;
                padding: 15px;
                border-radius: 8px;
                width: 100%;
                transition: background-color 0.2s ease;
            }

            .login-button:hover {
                background-color: #f7d750;
            }

            .login-button:focus {
                outline: none;
                box-shadow: none;
            }

            .toggle-password {
                width: 20px;
                height: 20px;
                position: absolute;
                top: 24px;
                right: 15px;
                cursor: pointer;
                user-select: none;
            }

            /* === Para sa Tablet at Maliliit na Laptops === */
            @media (max-width: 1024px) {
                .login-container {
                    width: 90%;
                }

                .login-image {
                    display: block;
                    width: 50%;
                }

                .login-form-container {
                    width: 50%;
                }
            }

            /* === Para sa Mobile Phone === */
            @media (max-width: 768px) {
                .login-container {
                    flex-direction: column;
                    width: calc(100% - 40px);
                    margin: 0 20px;
                }

                .login-image {
                    display: none;
                }

                .login-form-container {
                    width: 100%;
                    padding: 30px 20px; 
                }
            }
        </style>
    </head>
    <body class="min-h-screen flex items-center justify-center relative">
        <div class="login-container text-black">
            <div class="login-image"></div>

            <div class="login-form-container">
                <div class="flex justify-center mb-6">
                    <img src="{{ asset('icons/logo.png') }}" alt="School Logo" class="w-28 h-28" />
                </div>

                <h2 class="text-2xl font-bold text-center mb-6">Teacher | Admin Login</h2>

                @if ($errors->any())
                <div class="w-full mb-2 flex justify-center">
                    <div class="bg-red-50 border border-red-300 text-red-600 text-xs px-4 py-2 rounded text-center max-w-xs">
                        @foreach ($errors->all() as $error)
                        <p class="mb-1">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ route('login.submit') }}">
                    @csrf

                    <div class="mb-5">
                        <label for="email" class="block text-sm font-bold mb-1">Email</label>
                        <div class="relative">
                            <img src="{{ asset('icons/email.png') }}" alt="Email Icon" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 opacity-70" />
                            <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="Enter your email" required autofocus class="login-input pl-9" />
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block text-sm font-bold mb-1">Password</label>
                        <div class="relative">
                            <img src="{{ asset('icons/padlock.png') }}" alt="Lock Icon" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 opacity-70" />
                            <input type="password" name="password" id="password" placeholder="Enter your password" required class="login-input pl-9 pr-8" />
                            <img id="togglePassword" src="{{ asset('icons/eye.png') }}" alt="Toggle Password" class="toggle-password right-2 top-1/2 transform -translate-y-1/2" />
                        </div>
                    </div>

                    <button type="submit" class="login-button">
                        LOGIN
                    </button>
                </form>
            </div>
        </div>

        <script>
            const toggleIcon = document.getElementById("togglePassword");
            const passwordInput = document.getElementById("password");
            let visible = false;

            toggleIcon.addEventListener("click", () => {
                visible = !visible;
                passwordInput.type = visible ? "text" : "password";
                toggleIcon.src = visible ? "{{ asset('icons/hidden.png') }}" : "{{ asset('icons/eye.png') }}";
            });
        </script>
    </body>
</html>
