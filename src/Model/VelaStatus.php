<?php
namespace App\Model;

enum VelaStatus: string
{
    case DISPONIVEL = 'disponível';
    case INDISPONIVEL = 'indisponível';
}
