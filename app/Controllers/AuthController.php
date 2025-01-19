<?php

namespace App\Controllers;

use App\Models\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Google\Client as GoogleClient;

class AuthController
{
    public function confirm($token)
    {
        $message = '';
        $messageType = '';

        if (empty($token)) {
            $message = "Invalid token.";
            $messageType = 'danger';
        } else {
            $user = User::findByToken($token);

            if (!$user) {
                $message = "Invalid or expired token.";
                $messageType = 'danger';
            } elseif ($user['is_verified'] == 1) {
                $message = "Your email has already been confirmed.";
                $messageType = 'warning';
            } else {
                $result = User::verifyEmail($user['email']);

                if ($result) {
                    $message = "Email confirmed successfully!";
                    $messageType = 'success';
                } else {
                    $message = "Failed to confirm your email. Please try again.";
                    $messageType = 'danger';
                }
            }
        }

        include_once __DIR__ . '/../Views/auth/confirm_email.php';
    }


    public function checkRememberMe()
    {
        if (isset($_COOKIE['remember_me'])) {
            $token = $_COOKIE['remember_me'];

            $user = User::findByRememberMeToken($token);

            if ($user) {
                session_start();
                $_SESSION['user'] = $user;

                header("Location: /dashboard");
                exit;
            }
        }
    }

    public function login()
    {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            $user = User::verifyLogin($email, $password);
            $rememberMe = isset($_POST['remember_me']) ? true : false;

            if (!$user) {
                $errors[] = "Invalid username or password.";
            } elseif (!$user['is_verified']) {
                $errors[] = "Please confirm your email before logging in.";
            }

            if (empty($errors)) {
                session_start();
                $_SESSION['user'] = $user;

                if ($rememberMe) {
                    $rememberMeToken = bin2hex(random_bytes(32));
                    User::saveRememberMeToken($user['id'], $rememberMeToken);

                    setcookie('remember_me', $rememberMeToken, time() + 3600 * 24 * 30, "/", "", isset($_SERVER["HTTPS"]), true);
                }

                header("Location: /dashboard");
                exit;
            }
        }

        include_once __DIR__ . '/../Views/auth/login.php';
    }


    public function terms()
    {
        include_once __DIR__ . '/../Views/auth/terms.php';
    }


    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user']['id'])) {
            $userId = $_SESSION['user']['id'];

            User::clearRememberMeToken($userId);
        }

        session_unset();
        session_destroy();

        setcookie('remember_me', '', time() - 3600, "/");

        header("Location: /login");
        exit;
    }


    public function register()
    {
        $errors = [];
        $successMessage = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $passwordConfirm = $_POST['password_confirm'];

            // Validate passwords match
            if ($password !== $passwordConfirm) {
                $errors[] = "Passwords do not match.";
            }

            // Validate password strength
            if (!isStrongPassword($password)) {
                $errors[] = "The password does not meet security requirements.";
            }

            // Check if email already exists
            if (User::findByEmail($email)) {
                $errors[] = "This email is already in use.";
            }

            if (empty($errors)) {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                $result = User::create($name, $email, $hashedPassword);

                if ($result) {
                    $emailStatus = $this->sendConfirmationEmail($email);

                    if ($emailStatus === true) {
                        $successMessage = "User registered successfully! Please check your email for confirmation.";
                    } else {
                        $errors[] = $emailStatus;
                    }
                } else {
                    $errors[] = "Error registering the user. Please try again.";
                }
            }
        }

        include_once __DIR__ . '/../Views/auth/register.php';
    }

    private function sendConfirmationEmail($email)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username = $_ENV['MAIL_USERNAME'];
            $mail->Password = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure = $_ENV['MAIL_SMTPSECURE'];
            $mail->Port = $_ENV['MAIL_PORT'];

            $token = bin2hex(random_bytes(16));

            User::storeVerificationToken($email, $token);

            $verificationLink = $_ENV['BASE_URL'] . "confirm/$token";

            $mail->setFrom($_ENV['MAIL_USERNAME'], 'Teste');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Registration Confirmation';
            $mail->Body    = 'Thank you for registering!<br>Please click the link below to confirm your email.<br><a href="' . $verificationLink . '">Confirm Email</a>';

            $mail->send();
            return true; // Indica sucesso
        } catch (Exception $e) {
            return "Error sending confirmation email: {$mail->ErrorInfo}"; // Retorna o erro
        }
    }

    public function googleLogin()
    {
        $client = new GoogleClient();
        $client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
        $client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
        $client->setRedirectUri($_ENV['BASE_URL'] . 'simple-auth/google-callback');
        $client->addScope('email');
        $client->addScope('profile');

        $authUrl = $client->createAuthUrl();

        header('Location: ' . $authUrl);
        exit;
    }
    public function googleCallback()
    {
        $client = new GoogleClient();
        $client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
        $client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
        $client->setRedirectUri($_ENV['BASE_URL'] . 'simple-auth/google-callback');

        if (!isset($_GET['code'])) {
            echo "Código de autorização não fornecido.";
            return;
        }

        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $client->setAccessToken($token);

        $googleService = new \Google\Service\Oauth2($client);
        $googleUser = $googleService->userinfo->get();
        $user = User::findByEmail($googleUser->email);

        if ($user) {
            if ($user['user_created_by'] == 'Sign Up') {
                session_start();
                $_SESSION['user'] = $user;
                header("Location: /dashboard");
                exit;
            } else {
                session_start();
                $_SESSION['user'] = $user;
                header("Location: /dashboard");
                exit;
            }
        }

        User::createSocialUser($googleUser->name, $googleUser->email);

        $user = User::findByEmail($googleUser->email);

        session_start();
        $_SESSION['user'] = $user;
        header("Location: /dashboard");
        exit;
    }

    public function forgotPassword()
    {
        $errors = [];
        $successMessage = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $user = User::findByEmail($email);

            $resetToken = bin2hex(random_bytes(32));

            $saveTokenResult = User::saveResetToken($email, $resetToken);

            if ($saveTokenResult) {
                if ($this->sendResetPasswordEmail($email, $resetToken)) {
                    $successMessage = "If the email you specified exists in our system, we've sent a password reset link to it.";
                } else {
                    $errors[] = "Failed to send the password reset email. Please try again later.";
                }
            } else {
                $errors[] = "An error occurred while processing your request. Please try again.";
            }
        }

        include_once __DIR__ . '/../Views/auth/forgot-password.php';
    }



    private function sendResetPasswordEmail($email, $resetToken)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USERNAME'];
            $mail->Password = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure = $_ENV['MAIL_SMTPSECURE'];
            $mail->Port = $_ENV['MAIL_PORT'];

            $resetLink = $_ENV['BASE_URL'] . "reset-password?token=$resetToken";

            $mail->setFrom($_ENV['MAIL_USERNAME'], 'Your System');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Password Reset';
            $mail->Body    = 'You requested a password reset.<br>'
                . 'Click the link below to reset your password:<br>'
                . '<a href="' . $resetLink . '">Reset Password</a>';

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Error sending password reset email: {$mail->ErrorInfo}");
            return false;
        }
    }


    public function resetPassword()
    {
        $errors = [];
        $successMessage = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'];
            $password = $_POST['password'];
            $passwordConfirm = $_POST['password_confirm'];

            if ($password !== $passwordConfirm) {
                $errors[] = "The passwords do not match.";
            }

            if (!isStrongPassword($password)) {
                $errors[] = "The password does not meet the security requirements.";
            }
            $user = User::findByResetToken($token);

            if (!$user) {
                $errors[] = "Invalid or expired token.";
            }

            if (empty($errors)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $updateResult = User::updatePassword($user['id'], $hashedPassword);

                if ($updateResult) {
                    $successMessage = "Password successfully reset.";
                } else {
                    $errors[] = "An error occurred while resetting the password. Please try again.";
                }
            }
        }

        include_once __DIR__ . '/../Views/auth/reset-password.php';
    }



    public function resetPasswordPage($token)
    {
        $user = User::findByResetToken($token);

        if (!$user) {
            echo "Invalid or expired token.";
            exit;
        }

        include_once __DIR__ . '/../Views/auth/reset-password.php';
    }
}

function isStrongPassword($password)
{
    if (!preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password)) {
        return false;
    }

    if (!preg_match('/\d/', $password)) {
        return false;
    }

    if (!preg_match('/[!@#$%^&*]/', $password)) {
        return false;
    }

    if (strlen($password) < 8) {
        return false;
    }

    return true;
}
