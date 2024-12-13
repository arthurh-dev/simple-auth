<?php include_once __DIR__ . '/../partials/cdns.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>

<body>
    <h1>Login</h1>

    <form action="/simple-auth/login" method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="password">Senha:</label>
        <input type="password" id="password" name="password" required>
        <br></br>
        <button type="submit">Login</button>
    </form>

    <hr>

    <div>
        <h2>Ou faça login com:</h2>
        <a href="/simple-auth/google-login" style="text-decoration: none;">
            <button type="button" style="background-color: #4285F4; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
                Login com Google
            </button>
        </a>
    </div>

</body>

</html>