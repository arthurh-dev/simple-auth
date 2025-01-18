<?php include_once __DIR__ . '/../partials/css-cdns.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>

<body>

    <div class="mask d-flex align-items-center h-100">

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-5 col-md-8">

                    <form class="bg-white rounded shadow-5-strong p-5" action="/simple-auth/login" method="POST">
                        <h2 class="fw-bold mb-5 text-center">Login now</h2>
                        <div data-mdb-input-init class="form-outline mb-4">
                            <input class="form-control" type="email" name="email" id="email" required>
                            <label class="form-label" for="email">Email</label>
                        </div>
                        <div data-mdb-input-init class="form-outline mb-4">
                            <input class="form-control" type="password" name="password" id="password" required>
                            <label class="form-label" for="password">Password</label>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Checkbox -->
                            <div class="form-check mb-4">
                                <input class="form-check-input me-2" type="checkbox" value="" id="loginForms" />
                                <label class="form-check-label" for="loginForms">
                                    Remember me
                                </label>
                            </div>
                            <a href="/simple-auth/forgot-password" class="text-body mb-4">Forgot password?</a>
                        </div>
                        <button type="submit" class="btn btn-lg btn-primary btn-block" data-mdb-ripple-init>Login</button>
                        <p class="small fw-bold mt-2 pt-1 mb-0">Don't have an account? <a href="http://localhost/register"
                                class="link-primary">Register</a></p>

                        <div class="divider d-flex align-items-center my-4">
                            <p class="text-center fw-bold mx-3 mb-0">Or</p>
                        </div>
                        <div class="d-flex flex-row align-items-center justify-content-center">
                            <a href="/simple-auth/google-login" class="text-center">
                                <button type="button" class="btn btn-lg btn-floating mx-1 btn-google">
                                    <i class="fab fa-google text-white"></i>
                                </button>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include_once __DIR__ . '/../partials/js-cdns.php'; ?>




</body>

</html>