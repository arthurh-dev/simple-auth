<?php
require_once '../vendor/autoload.php'; // Inclusão do autoload do Composer

use App\Models\User;

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verificar se o token existe no banco de dados
    $user = User::findByToken($token);

    if ($user) {
        // Verificar se o usuário já foi confirmado
        if ($user['is_verified'] == 1) {
            echo "Este e-mail já foi confirmado!";
        } else {
            // Se o e-mail não foi confirmado, atualize o status
            if (User::updateVerificationStatus($user['email'])) {
                echo "E-mail confirmado com sucesso!";
            } else {
                echo "Erro ao confirmar o e-mail. Tente novamente mais tarde.";
            }
        }
    } else {
        echo "Este token de confirmação não é válido.";
    }
} else {
    echo "Token não fornecido.";
}