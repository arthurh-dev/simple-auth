<?php

namespace App\Controllers;

use App\Models\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

public function login()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $user = User::verifyLogin($email, $password);

        if (!$user) {
            $error = "Credenciais inválidas.";
            include_once __DIR__ . '/../Views/auth/login.php';
            echo $error;
            return;
        }

        if (!$user['is_verified']) {
            $error = "Por favor, confirme seu e-mail antes de fazer login.";
            include_once __DIR__ . '/../Views/auth/login.php';
            echo $error;
            return;
        }

        session_start();
        $_SESSION['user'] = $user;

        header("Location: /simple-auth/dashboard");
        exit;
    } else {

        include_once __DIR__ . '/../Views/auth/login.php';
    }
}

public function logout()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    session_unset();
    session_destroy();

    header("Location: /simple-auth/login");
    exit;
}



public function register()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
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
}

