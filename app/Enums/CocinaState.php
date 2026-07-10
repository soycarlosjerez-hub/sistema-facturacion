<?php

namespace App\Enums;

enum CocinaState: string
{
    case Pendiente = 'pendiente';
    case EnPreparacion = 'en_preparacion';
    case Listo = 'listo';
    case Entregado = 'entregado';
}
