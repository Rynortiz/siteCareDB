<?php
namespace App\Model;

enum VendaStatus: string
{
    case PROCESSANDO = 'processando';
    case FINALIZADA = 'finalizada';
    case RECUSADA = 'recusada';
}
