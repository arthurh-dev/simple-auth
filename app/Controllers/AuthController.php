<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\User;

class AuthController extends Controller {
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Validação básica
            if (empty($username) || empty($email) || empty($password)) {
                die('All fields are required!');
            }

            // Verificar se o email já está registrado
            $user = User::findByEmail($email);
            if ($user) {
                die('Email already registered!');
            }

            // Criação do usuário
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            if (User::create($username, $email, $passwordHash)) {
                echo "User registered successfully!";
            } else {
                echo "Error registering user.";
            }
        } else {
            $this->view('auth/register');
        }
    }
}
