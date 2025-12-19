<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TravelMates - Login</title>
    <link rel="icon" type="image/png" href="images/flag.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .login-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f0f23 100%);
            position: relative;
            overflow: hidden;
        }

        .login-page::before {
            content: '';
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            background: url('images/loginRegisterImg/img.jpg') center center/cover no-repeat;
            filter: blur(5px);
            z-index: 0;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .divider-vertical {
            width: 5px;
            background: linear-gradient(to bottom, transparent, rgba(255, 255, 255, 0.3), transparent);
        }

        .form-control-glass {
            background: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: #fff !important;
        }

        .form-control-glass::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-control-glass:focus {
            background: rgba(255, 255, 255, 0.15) !important;
            border-color: rgba(255, 153, 0, 0.5) !important;
            box-shadow: 0 0 15px rgba(255, 153, 0, 0.2) !important;
        }

        .btn-glass {
            background: linear-gradient(135deg, #ff9900 0%, #ff6600 100%);
        }
    </style>
</head>

<body>
    <div class="login-page d-flex align-items-center justify-content-center py-5">
        <div class="container position-relative" style="z-index: 1;">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-10">
                    <div class="glass-card border border-secondary border-opacity-25 rounded-4 shadow-lg p-4 p-lg-5">
                        <div class="row align-items-stretch" style="min-height: 500px;">
                            <!-- left Side-->
                            <div
                                class="col-12 col-md-5 d-flex flex-column justify-content-between mb-4 mb-md-0 pe-md-4">
                                <div>
                                    <p class="fs-4 fw-bold fst-italic text-white mb-4">TravelMates</p>
                                    <h1 class="display-4 fw-bold text-white mb-2">Join</h1>
                                    <h1 class="display-4 fw-bold text-white mb-4">Us Today!</h1>
                                    <p class="text-white-50 fs-6">
                                        Create your account and unlock exclusive deals,
                                        easy bookings, and personalized travel experiences.
                                        Your adventure starts here.
                                    </p>
                                </div>
                                <div class="mt-4">
                                    <a href="index.php" class="text-white-50 small mb-0 text-decoration-underline">&lt;-- Back to Home Page</a>
                                </div>
                            </div>

                            <!-- divider -->
                            <div class="col-auto d-none d-md-flex align-items-center px-4">
                                <div class="divider-vertical h-75"></div>
                            </div>

                            <!-- right Side -->
                            <div class="col-12 col-md-6">
                                <div class="d-flex flex-column h-100 justify-content-center">
                                    <h2 class="fs-3 fw-semibold text-white text-center mb-4">Register</h2>

                                    <form action="php/register_process.php" method="POST">
                                        <div class="row">
                                            <div class="col-12 col-sm-6 mb-3">
                                                <input type="text"
                                                    class="form-control form-control-lg form-control-glass rounded-3"
                                                    name="firstname" placeholder="First Name" required>
                                            </div>
                                            <div class="col-12 col-sm-6 mb-3">
                                                <input type="text"
                                                    class="form-control form-control-lg form-control-glass rounded-3"
                                                    name="lastname" placeholder="Last Name" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <input type="email"
                                                class="form-control form-control-lg form-control-glass rounded-3"
                                                name="email" placeholder="Email Address" required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="text"
                                                class="form-control form-control-lg form-control-glass rounded-3"
                                                name="username" placeholder="Username" required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="tel"
                                                class="form-control form-control-lg form-control-glass rounded-3"
                                                name="phone" placeholder="Phone Number" required>
                                        </div>
                                        <div class="mb-3 position-relative">
                                            <input type="password"
                                                class="form-control form-control-lg form-control-glass rounded-3"
                                                name="password" placeholder="Password" required id="password" style="padding-right: 2.5rem;">
                                            <i class="bi bi-eye position-absolute top-50 end-0 translate-middle-y me-3 text-white" id="togglePassword" style="cursor: pointer;"></i>
                                        </div>
                                        <div class="mb-4 position-relative">
                                            <input type="password"
                                                class="form-control form-control-lg form-control-glass rounded-3"
                                                name="confirm_password" placeholder="Confirm Password" required id="confirmPassword" style="padding-right: 2.5rem;">
                                            <i class="bi bi-eye position-absolute top-50 end-0 translate-middle-y me-3 text-white" id="toggleConfirmPassword" style="cursor: pointer;"></i>
                                        </div>

                                        <div class="d-grid mb-4">
                                            <button type="submit"
                                                class="btn btn-glass btn-lg text-white fw-semibold rounded-3 py-3">Register</button>
                                        </div>
                                    </form>

                                    <p class="text-center text-white-50 small mb-0">
                                        Already have an account?
                                        <a href="login.php"
                                            class="text-warning text-decoration-none fw-semibold">Login</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });

        const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
        const confirmPassword = document.querySelector('#confirmPassword');

        toggleConfirmPassword.addEventListener('click', function (e) {
            const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPassword.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    </script>
</body>

</html>