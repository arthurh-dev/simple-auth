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

        if (empty($token)) {
            echo "Token inválido.";
            return;
        }


        $user = User::findByToken($token);

        if (!$user) {
            echo "Token inválido ou expirado.";
            return;
        }


        if ($user['is_verified'] == 1) {
            echo "O e-mail já foi confirmado.";
            return;
        }


        $result = User::verifyEmail($user['email']);

        if ($result) {
            echo "E-mail confirmado com sucesso!";
        } else {
            echo "Erro ao confirmar o e-mail. Tente novamente.";
        }
    }



    public function checkRememberMe()
    {
        // Verifica se o cookie "remember_me" está presente
        if (isset($_COOKIE['remember_me'])) {
            $token = $_COOKIE['remember_me'];

            // Busca o usuário com o token de "Remember Me" no banco de dados
            $user = User::findByRememberMeToken($token);

            if ($user) {
                // Inicia a sessão se o token for válido
                session_start();
                $_SESSION['user'] = $user;

                // Redireciona para o dashboard ou outra página protegida
                header("Location: /simple-auth/dashboard");
                exit;
            }
        }
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            // Verifica as credenciais de login
            $user = User::verifyLogin($email, $password);
            $rememberMe = isset($_POST['remember_me']) ? true : false;

            // Se as credenciais forem inválidas
            if (!$user) {
                $error = "Credenciais inválidas.";
                include_once __DIR__ . '/../Views/auth/login.php';
                echo $error;
                return;
            }

            // Verifica se o e-mail foi confirmado
            if (!$user['is_verified']) {
                $error = "Por favor, confirme seu e-mail antes de fazer login.";
                include_once __DIR__ . '/../Views/auth/login.php';
                echo $error;
                return;
            }

            // Inicia a sessão
            session_start();
            $_SESSION['user'] = $user;

            // Se "Remember Me" for marcado
            if ($rememberMe) {
                // Gera um token aleatório para "Remember Me"
                $rememberMeToken = bin2hex(random_bytes(32)); // Token seguro

                // Armazena o token no banco de dados
                User::saveRememberMeToken($user['id'], $rememberMeToken);

                // Define o cookie "remember_me" com segurança (adicionando HttpOnly e Secure)
                setcookie('remember_me', $rememberMeToken, time() + 3600 * 24 * 30, "/", "", isset($_SERVER["HTTPS"]), true);
            }

            // Redireciona para o dashboard após o login
            header("Location: /simple-auth/dashboard");
            exit;
        } else {
            // Se o método não for POST, exibe a tela de login
            include_once __DIR__ . '/../Views/auth/login.php';
        }
    }


    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Verificar se o ID do usuário está presente na sessão
        if (isset($_SESSION['user']['id'])) {
            $userId = $_SESSION['user']['id'];

            // Limpar o token de "Remember Me" no banco de dados
            User::clearRememberMeToken($userId);
        }

        session_unset();
        session_destroy();

        // Limpar o cookie "Remember Me"
        setcookie('remember_me', '', time() - 3600, "/");

        header("Location: /simple-auth/login");
        exit;
    }


    public function register()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $passwordConfirm = $_POST['password_confirm'];

            // Verifique se as senhas coincidem
            if ($password !== $passwordConfirm) {
                echo "As senhas não coincidem.";
                return;
            }

            // Verifique a força da senha
            if (!isStrongPassword($password)) {
                echo "A senha não atende aos requisitos de segurança.";
                return;
            }

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            if (User::findByEmail($email)) {
                echo "Este e-mail já está em uso.";
                return;
            }

            $result = User::create($name, $email, $hashedPassword);

            if ($result) {
                echo "Usuário registrado com sucesso!";

                $this->sendConfirmationEmail($email);
            } else {
                echo "Erro ao registrar o usuário. Tente novamente.";
            }
        } else {
            include_once __DIR__ . '/../Views/auth/register.php';
        }
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

            $verificationLink = "http://localhost/simple-auth/confirm/$token";

            $mail->setFrom($_ENV['MAIL_USERNAME'], 'Teste');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Confirmação de Registro';
            $mail->Body    = 'Obrigado por se registrar!<br>Por favor, clique no link abaixo para confirmar seu e-mail.<br><a href="' . $verificationLink . '">Confirmar e-mail</a>';

            $mail->send();
            echo 'Mensagem de confirmação enviada.';
        } catch (Exception $e) {
            echo "Erro ao enviar o e-mail. Erro: {$mail->ErrorInfo}";
        }
    }

    public function googleLogin()
    {
        $client = new GoogleClient();
        $client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
        $client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
        $client->setRedirectUri('http://localhost/simple-auth/google-callback');
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
        $client->setRedirectUri('http://localhost/simple-auth/google-callback');

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
                header("Location: /simple-auth/dashboard");
                exit;
            } else {
                session_start();
                $_SESSION['user'] = $user;
                header("Location: /simple-auth/dashboard");
                exit;
            }
        }

        User::createSocialUser($googleUser->name, $googleUser->email);

        $user = User::findByEmail($googleUser->email);

        session_start();
        $_SESSION['user'] = $user;
        header("Location: /simple-auth/dashboard");
        exit;
    }

    public function forgotPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $user = User::findByEmail($email);

            if (!$user || $user['user_created_by'] !== 'Sign Up') {
                echo "If the email you specified exists in our system, we've sent a password reset link to it.";
                return;
            }
            $resetToken = bin2hex(random_bytes(32));
            User::saveResetToken($email, $resetToken);

            $this->sendResetPasswordEmail($email, $resetToken);
        } else {
            include_once __DIR__ . '/../Views/auth/forgot-password.php';
        }
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

            $resetLink = $_ENV['BASE_URL'] . "/reset-password?token=$resetToken";

            $mail->setFrom($_ENV['MAIL_USERNAME'], 'Seu Sistema');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Redefinição de Senha';
            $mail->Body    = 'Você solicitou uma redefinição de senha.<br>'
                . 'Clique no link abaixo para redefinir sua senha:<br>'
                . '<a href="' . $resetLink . '">Redefinir Senha</a>';

            $mail->send();
            echo "If the email you specified exists in our system, we've sent a password reset link to it.";
        } catch (Exception $e) {
            echo "Erro ao enviar o e-mail. Erro: {$mail->ErrorInfo}";
        }
    }

    public function resetPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Processamento do formulário de redefinição de senha
            $token = $_POST['token'];
            $password = $_POST['password'];
            $passwordConfirm = $_POST['password_confirm'];

            // Verifique se as senhas coincidem
            if ($password !== $passwordConfirm) {
                echo "As senhas não coincidem.";
                return;
            }

            // Verifique a força da senha
            if (!isStrongPassword($password)) {
                echo "A senha não atende aos requisitos de segurança.";
                return;
            }

            // Buscar o usuário com base no token
            $user = User::findByResetToken($token);

            if (!$user) {
                echo "Token inválido ou expirado.";
                return;
            }

            // Atualizar a senha no banco de dados
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            User::updatePassword($user['id'], $hashedPassword);

            echo "Senha redefinida com sucesso.";
        } else {
            // Se a requisição for GET, exiba o formulário de redefinição de senha
            $token = $_GET['token'];
            $this->resetPasswordPage($token);
        }
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
    // Verifica se a senha tem pelo menos uma letra minúscula e uma maiúscula
    if (!preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password)) {
        return false;
    }

    // Verifica se a senha tem pelo menos um número
    if (!preg_match('/\d/', $password)) {
        return false;
    }

    // Verifica se a senha tem pelo menos um caractere especial
    if (!preg_match('/[!@#$%^&*]/', $password)) {
        return false;
    }

    // Verifica se a senha tem pelo menos 8 caracteres
    if (strlen($password) < 8) {
        return false;
    }

    return true;
}
