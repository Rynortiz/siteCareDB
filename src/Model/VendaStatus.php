<?php
namespace App\Model;

enum VendaStatus: string
{
    case PROCESSANDO = 'processando';
    case FINALIZADO = 'finalizado';
    case RECUSADA = 'recusada';
}
