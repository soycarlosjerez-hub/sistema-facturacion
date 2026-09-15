<?php

namespace App\Enums;

enum OrdenTipo: string
{
    case Mostrador = 'mostrador';
    case Delivery = 'delivery';
    case Pickup = 'pickup';
}
