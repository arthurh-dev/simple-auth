<?php

namespace App\Controllers;

class HomeController
{
    public function index()
    {
        include_once __DIR__ . '/../Views/home.php';
    }
}
