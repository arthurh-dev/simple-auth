<?php

namespace App\Controllers;

class DashboardController
{
    public function index()
    {
        session_start();
        if (!isset($_SESSION['user'])) {
            header("Location: /simple-auth/login");
            exit;
        }
        include_once __DIR__ . '/../Views/dashboard.php';
    }
}
