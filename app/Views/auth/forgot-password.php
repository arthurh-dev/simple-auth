<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esqueceu a Senha</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>

<body>
    <div class="container">
        <h1>Esqueceu a Senha?</h1>
        <p>Digite seu email para receber um link de redefinição de senha.</p>

        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form action="/simple-auth/forgot-password" method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required placeholder="Digite seu email">
            </div>

            <button type="submit" class="btn">Enviar Link</button>
        </form>
    </div>
</body>

</html>