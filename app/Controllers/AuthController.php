<?php

namespace App\Controllers;

use App\Models\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthController
{
public function confirm($token)
{
    // Verificar se o token foi fornecido
    if (empty($token)) {
        echo "Token inválido.";
        return;
    }

    // Buscar o usuário pelo token
    $user = User::findByToken($token);

    if (!$user) {
        echo "Token inválido ou expirado.";
        return;
    }

    // Verificar se o e-mail já foi verificado
    if ($user['is_verified'] == 1) {
        echo "O e-mail já foi confirmado.";
        return;
    }

    // Atualizar o status de verificação do usuário
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
        // Sanitizar entradas
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        // Tentar verificar login
        $user = User::verifyLogin($email, $password);

        if (!$user) {
            $error = "Credenciais inválidas.";
            include_once __DIR__ . '/../Views/auth/login.php';
            echo $error;
            return;
        }

        if (!$user['is_verified']) {
            // Usuário ainda não confirmou o email
            $error = "Por favor, confirme seu e-mail antes de fazer login.";
            include_once __DIR__ . '/../Views/auth/login.php';
            echo $error;
            return;
        }

        // Login bem-sucedido, iniciar sessão
        session_start();
        $_SESSION['user'] = $user;

        // Redirecionar para a rota do dashboard
        header("Location: /simple-auth/dashboard");
        exit;
    } else {
        // Exibir o formulário de login
        include_once __DIR__ . '/../Views/auth/login.php';
    }
}

public function logout()
{
    // Inicia a sessão se ainda não estiver iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Destroi os dados da sessão
    session_unset();
    session_destroy();

    // Redireciona para a página de login
    header("Location: /simple-auth/login");
    exit;
}



public function register()
    {
        // Verificar se o formulário foi enviado
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT); // Criptografar a senha

            // Verificar se o e-mail já está cadastrado
            if (User::findByEmail($email)) {
                echo "Este e-mail já está em uso.";
                return;
            }

            // Criar o usuário no banco de dados
            $result = User::create($name, $email, $hashedPassword);

            if ($result) {
                echo "Usuário registrado com sucesso!";

                // Enviar e-mail de confirmação
                $this->sendConfirmationEmail($email);
            } else {
                echo "Erro ao registrar o usuário. Tente novamente.";
            }
        } else {
            // Se o método não for POST, apenas renderize a página de registro
            include_once __DIR__ . '/../Views/auth/register.php';
        }
    }

    // Método para enviar o email de confirmação
private function sendConfirmationEmail($email)
{
    $mail = new PHPMailer(true);
    
    try {
        // Configuração do servidor de e-mail (Exemplo para Gmail)
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host = $_ENV['MAIL_HOST'];                       // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                    // Enable SMTP authentication
        $mail->Username = $_ENV['MAIL_USERNAME'];
        $mail->Password = $_ENV['MAIL_PASSWORD'];
        $mail->SMTPSecure = $_ENV['MAIL_SMTPSECURE'];
        $mail->Port = $_ENV['MAIL_PORT'];

        // Gerar o token único para confirmação
        $token = bin2hex(random_bytes(16));  // Gera um token aleatório de 32 caracteres

        // Armazenar o token no banco de dados (você deve ter um método para associar o token ao usuário)
        User::storeVerificationToken($email, $token); // Armazenando no banco

        // Gerar o link de confirmação com o token
        $verificationLink = "http://localhost/simple-auth/confirm/$token";

        // Remetente e destinatário
        $mail->setFrom($_ENV['MAIL_USERNAME'], 'Teste');
        $mail->addAddress($email);                                    // Add a recipient

        // Conteúdo do e-mail
        $mail->isHTML(true);                                          // Set email format to HTML
        $mail->CharSet = 'UTF-8';                                     // Garantir que a codificação seja UTF-8
        $mail->Subject = 'Confirmação de Registro';
        $mail->Body    = 'Obrigado por se registrar!<br>Por favor, clique no link abaixo para confirmar seu e-mail.<br><a href="' . $verificationLink . '">Confirmar e-mail</a>';

        // Enviar o e-mail
        $mail->send();
        echo 'Mensagem de confirmação enviada.';
    } catch (Exception $e) {
        echo "Erro ao enviar o e-mail. Erro: {$mail->ErrorInfo}";
    }
}
}

