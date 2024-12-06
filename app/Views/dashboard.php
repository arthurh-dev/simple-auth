<?php
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Bem-vindo ao Dashboard</h1>
    <p>Olá, <?php echo htmlspecialchars($user['username']); ?>!</p>
    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>

    <a href="/simple-auth/logout">Sair</a>
</body>
</html>
