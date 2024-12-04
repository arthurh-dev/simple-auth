<?php
require_once '../vendor/autoload.php'; // Inclusão do autoload do Composer

use App\Models\User;

if (isset($_GET['email'])) {
    $email = $_GET['email'];

    // Verificar se o e-mail existe no banco de dados
    $user = User::findByEmail($email);

    if ($user) {
        // Verificar se o usuário já foi confirmado
        if ($user['is_verified'] == 1) {
            echo "Este e-mail já foi confirmado!";
        } else {
            // Se o e-mail não foi confirmado, atualize o status
            if (User::updateVerificationStatus($email)) {
                echo "E-mail confirmado com sucesso!";
            } else {
                echo "Erro ao confirmar o e-mail. Tente novamente mais tarde.";
            }
        }
    } else {
        echo "Este e-mail não existe ou não foi registrado.";
    }
} else {
    echo "E-mail não fornecido.";
}
