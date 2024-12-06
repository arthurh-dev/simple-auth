<?php
require_once '../vendor/autoload.php';

use App\Models\User;

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $user = User::findByToken($token);

    if ($user) {
        if ($user['is_verified'] == 1) {
            echo "Este e-mail já foi confirmado!";
        } else {

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