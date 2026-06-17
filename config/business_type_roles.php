<?php

return [
    'restaurante' => [
        'owner' => ['owner', 'root', 'admin', 'gerente', 'vendedor', 'almacen', 'contador', 'admin-business', 'supervisor', 'administrativo', 'mesero', 'cocinero', 'delivery', 'bartender', 'instance-admin'],
        'root' => ['admin', 'gerente', 'vendedor', 'almacen', 'contador', 'supervisor', 'administrativo', 'mesero', 'cocinero', 'delivery', 'bartender', 'instance-admin'],
        'admin-business' => ['gerente', 'supervisor', 'administrativo', 'mesero', 'cocinero', 'delivery', 'bartender', 'instance-admin'],
    ],
    'retail' => [
        'owner' => ['owner', 'root', 'admin', 'gerente', 'vendedor', 'almacen', 'contador', 'admin-business', 'supervisor', 'administrativo', 'cajero', 'reponedor', 'instance-admin'],
        'root' => ['admin', 'gerente', 'vendedor', 'almacen', 'contador', 'supervisor', 'administrativo', 'cajero', 'reponedor', 'instance-admin'],
        'admin-business' => ['gerente', 'supervisor', 'administrativo', 'cajero', 'reponedor', 'instance-admin'],
    ],
    'mayorista' => [
        'owner' => ['owner', 'root', 'admin', 'gerente', 'vendedor', 'almacen', 'contador', 'admin-business', 'supervisor', 'administrativo', 'despachador', 'vendedor-mayorista', 'instance-admin'],
        'root' => ['admin', 'gerente', 'vendedor', 'almacen', 'contador', 'supervisor', 'administrativo', 'despachador', 'vendedor-mayorista', 'instance-admin'],
        'admin-business' => ['gerente', 'supervisor', 'administrativo', 'despachador', 'vendedor-mayorista', 'instance-admin'],
    ],
    'servicios' => [
        'owner' => ['owner', 'root', 'admin', 'gerente', 'vendedor', 'almacen', 'contador', 'admin-business', 'supervisor', 'administrativo', 'consultor', 'facturador', 'instance-admin'],
        'root' => ['admin', 'gerente', 'vendedor', 'almacen', 'contador', 'supervisor', 'administrativo', 'consultor', 'facturador', 'instance-admin'],
        'admin-business' => ['gerente', 'supervisor', 'administrativo', 'consultor', 'facturador', 'instance-admin'],
    ],
    'lavadero' => [
        'owner' => ['owner', 'root', 'admin', 'gerente', 'vendedor', 'almacen', 'contador', 'admin-business', 'supervisor', 'administrativo', 'lavador', 'recepcionista', 'inspector', 'instance-admin'],
        'root' => ['admin', 'gerente', 'vendedor', 'almacen', 'contador', 'supervisor', 'administrativo', 'lavador', 'recepcionista', 'inspector', 'instance-admin'],
        'admin-business' => ['gerente', 'supervisor', 'administrativo', 'lavador', 'recepcionista', 'inspector', 'instance-admin'],
    ],
    'mixto' => [
        'owner' => ['owner', 'root', 'admin', 'gerente', 'vendedor', 'almacen', 'contador', 'admin-business', 'supervisor', 'administrativo', 'mesero', 'cocinero', 'delivery', 'bartender', 'cajero', 'reponedor', 'instance-admin'],
        'root' => ['admin', 'gerente', 'vendedor', 'almacen', 'contador', 'supervisor', 'administrativo', 'mesero', 'cocinero', 'delivery', 'bartender', 'cajero', 'reponedor', 'instance-admin'],
        'admin-business' => ['gerente', 'supervisor', 'administrativo', 'mesero', 'cocinero', 'delivery', 'bartender', 'cajero', 'reponedor', 'instance-admin'],
    ],
    'while-pone-el-restaurante' => [
        'owner' => ['owner', 'root', 'admin', 'gerente', 'vendedor', 'almacen', 'contador', 'admin-business', 'supervisor', 'administrativo', 'mesero', 'cocinero', 'delivery', 'bartender', 'instance-admin'],
        'root' => ['admin', 'gerente', 'vendedor', 'almacen', 'contador', 'supervisor', 'administrativo', 'mesero', 'cocinero', 'delivery', 'bartender', 'instance-admin'],
        'admin-business' => ['gerente', 'supervisor', 'administrativo', 'mesero', 'cocinero', 'delivery', 'bartender', 'instance-admin'],
    ],
];