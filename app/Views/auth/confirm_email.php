<?php include_once __DIR__ . '/../partials/css-cdns.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Confirmation</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body text-center">
                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?= htmlspecialchars($messageType) ?>" role="alert">
                                <?= htmlspecialchars($message) ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($messageType === 'success'): ?>
                            <a href="/login" class="btn btn-primary">Go to Login</a>
                        <?php elseif ($messageType === 'danger' || $messageType === 'warning'): ?>
                            <a href="/" class="btn btn-secondary">Return to Home</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php include_once __DIR__ . '/../partials/js-cdns.php'; ?>
    <script src="/assets/script.js"></script>
</body>

</html>