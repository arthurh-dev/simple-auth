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
    <h1>Welcome to Dashboard</h1>
    <p>Hello, <?php echo htmlspecialchars($user['username']); ?>!</p>
    <p>email: <?php echo htmlspecialchars($user['email']); ?></p>

    <a href="/logout">Logout</a>
</body>

</html>