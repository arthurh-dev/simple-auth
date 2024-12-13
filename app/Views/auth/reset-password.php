<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>

<body>
    <h1>Redefinir Senha</h1>
    <form action="/simple-auth/reset-password" method="POST">
        <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token']) ?>">
        <label for="password">Nova Senha:</label>
        <input type="password" name="password" id="password" required>
        <label for="password_confirm">Confirme a Senha:</label>
        <input type="password" name="password_confirm" id="password_confirm" required>
        <button type="submit">Redefinir Senha</button>
    </form>
</body>

</html>