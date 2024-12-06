<?php include_once __DIR__ . '/../partials/cdns.php'; ?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>

<body>
    <div class="d-flex vh-100 align-items-center justify-content-center">
        <div class="container text-center max-width-500">
            <h1 class="mb-4">Sign Up</h1>
            <form action="/simple-auth/register" method="POST">
                <div class="mb-3 row align-items-center">
                    <label for="name" class="col-sm-3 col-form-label text-end">Name:</label>
                    <div class="col-sm-8">
                        <input class="form-control" type="text" name="name" id="name" required>
                    </div>
                </div>
                <div class="mb-3 row align-items-center">
                    <label for="email" class="col-sm-3 col-form-label text-end">Email:</label>
                    <div class="col-sm-8">
                        <input class="form-control" type="email" name="email" id="email" required>
                    </div>
                </div>
                <div class="mb-4 row align-items-center">
                    <label for="password" class="col-sm-3 col-form-label text-end">Password:</label>
                    <div class="col-sm-8">
                        <input class="form-control" type="password" name="password" id="password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-50 mb-20">Register</button>
            </form>
        </div>
    </div>
</body>


</html>