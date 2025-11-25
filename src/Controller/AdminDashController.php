<?php

namespace App\Controller;

use App\Core\Database;

class AdminDashController
{
    public function dashboard(): void
    {
        if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'ADMIN') {
            header("Location: /home");
            exit;
        }

        $page = 'dashboard';
        require __DIR__ . '/../View/page.phtml';
    }
}