<?php

namespace App\Controllers;

use App\Models\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthController
{
    // Método para exibir o formulário de registro e processar o envio
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
            $mail->Host       = 'smtp.gmail.com';                        // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                    // Enable SMTP authentication
            $mail->Username   = 'arthurhenrique010702@gmail.com';                  // SMTP username
            $mail->Password   = 'xkos nytg dwbh czdb';                      // SMTP password (se você estiver usando o Gmail, crie uma senha de app)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;          // Enable TLS encryption
            $mail->Port       = 587;                                     // TCP port to connect to

            // Remetente e destinatário
            $mail->setFrom('arthurhenrique010702@gmail.com', 'Teste');
            $mail->addAddress($email);                                    // Add a recipient

            // Conteúdo do e-mail
            $mail->isHTML(true);                                          // Set email format to HTML
            $mail->Subject = 'Confirmação de Registro';
            $mail->Body    = 'Obrigado por se registrar!<br>Por favor, clique no link abaixo para confirmar seu e-mail.<br><a href="localhost/simple-auth/confirm-email.php?email=' . urlencode($email) . '">Confirmar e-mail</a>';

            // Enviar o e-mail
            $mail->send();
            echo 'Mensagem de confirmação enviada.';
        } catch (Exception $e) {
            echo "Erro ao enviar o e-mail. Erro: {$mail->ErrorInfo}";
        }
    }
}