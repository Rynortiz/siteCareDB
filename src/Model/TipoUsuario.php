<?php
namespace App\Model;

enum TipoUsuario: string
{
    case CLIENTE = 'cliente';
    case ADMIN = 'admin';
}
