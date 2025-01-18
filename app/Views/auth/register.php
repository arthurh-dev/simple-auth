<?php include_once __DIR__ . '/../partials/css-cdns.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>

<body>

    <div class="mask d-flex align-items-center h-100">

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-5 col-md-8">

                    <form class="bg-white rounded shadow-5-strong p-5" action="/simple-auth/register" method="POST">
                        <h2 class="fw-bold mb-5 text-center">Register Now</h2>
                        <div data-mdb-input-init class="form-outline mb-4">
                            <input class="form-control" type="text" name="name" id="name" required>
                            <label class="form-label" for="name">Name</label>
                        </div>
                        <div data-mdb-input-init class="form-outline mb-4">
                            <input class="form-control" type="email" name="email" id="email" required>
                            <label class="form-label" for="email">Email</label>
                        </div>
                        <div data-mdb-input-init class="form-outline mb-4">
                            <input class="form-control" type="password" name="password" id="password" required>
                            <label class="form-label" for="password">Password</label>
                            <button type="button" class="btn btn-floating btn-outline-secondary btn-sm position-absolute toggle-password" style="right: 10px; top: 50%; transform: translateY(-50%);" data-target="password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>

                        <ul id="password-requirements" class="password-requirements">
                            <li id="lowercase-uppercase">At least one lowercase & one uppercase letter</li>
                            <li id="number">At least one number (0-9)</li>
                            <li id="special-char">At least one special character (!@#$%^&*)</li>
                            <li id="min-length">At least 8 characters</li>
                        </ul>

                        <div data-mdb-input-init class="form-outline mb-4">
                            <input class="form-control" type="password" name="password_confirm" id="password_confirm" required>
                            <label class="form-label" for="password_confirm">Confirm your password</label>
                            <button type="button" class="btn btn-floating btn-outline-secondary btn-sm position-absolute toggle-password" style="right: 10px; top: 50%; transform: translateY(-50%);" data-target="password_confirm">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="row mb-4">
                            <div class="col d-flex justify-content-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="registerForm" required />
                                    <label class="form-check-label" for="registerForm">
                                        I agree all statements in <a href="#!">Terms of service</a>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <button id="submit-button" type="submit" class="btn btn-lg btn-primary btn-block" data-mdb-ripple-init disabled>Register</button>
                        <p class="small fw-bold mt-2 pt-1 mb-0">Already have an account? <a href="http://localhost/login"
                                class="link-primary">Sign In</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include_once __DIR__ . '/../partials/js-cdns.php'; ?>
    <script src="/assets/script.js"></script>

</body>


</html>