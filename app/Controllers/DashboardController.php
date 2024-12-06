<?php

namespace App\Controllers;

class DashboardController
{
    public function index()
    {
        // Inicia a sessão para verificar se o usuário está autenticado
        session_start();

        // Verifica se o usuário está logado
        if (!isset($_SESSION['user'])) {
            // Redireciona para a página de login caso não esteja autenticado
            header("Location: /simple-auth/login");
            exit;
        }

        // Carrega a view da dashboard
        include_once __DIR__ . '/../Views/dashboard.php';
    }
}
