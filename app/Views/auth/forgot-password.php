<?php include_once __DIR__ . '/../partials/css-cdns.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esqueceu a Senha</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>

<body>

    <div class="mask d-flex align-items-center h-100">

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-5 col-md-8">
                    <form class="bg-white rounded shadow-5-strong p-5" action="/simple-auth/forgot-password" method="POST">
                        <h2 class="fw-bold mb-4 text-center">Forgot your password?</h2>
                        <p class="fw-bold mb-4 text-center">Enter your email address to reset your password</p>
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($successMessage)): ?>
                            <div class="alert alert-success text-center">
                                <?= htmlspecialchars($successMessage) ?>
                            </div>
                        <?php endif; ?>
                        <div data-mdb-input-init class="form-outline mb-4">
                            <input class="form-control" type="email" name="email" id="email" required>
                            <label class="form-label" for="email">Email</label>
                        </div>
                        <button type="submit" class="btn btn-lg btn-primary btn-block">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include_once __DIR__ . '/../partials/js-cdns.php'; ?>

</body>

</html>