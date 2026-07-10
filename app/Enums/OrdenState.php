<?php

namespace App\Enums;

enum OrdenState: string
{
    case Pendiente = 'pendiente';
    case Confirmada = 'confirmada';
    case EnProceso = 'en_proceso';
    case EnCamino = 'en_camino';
    case Lista = 'lista';
    case Recogida = 'recogida';
    case Entregado = 'entregado';
    case Completada = 'completada';
    case Anulada = 'anulada';
}
