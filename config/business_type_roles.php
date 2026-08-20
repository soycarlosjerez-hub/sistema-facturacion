<?php

/**
 * Roles disponibles por tipo de negocio.
 *
 * Cada tipo tiene 3 niveles de roles (owner, root, admin-business) y
 * una lista de roles específicos del tipo. Cuando un tipo no tiene
 * módulo especializado (ej. embutidos), se usan los roles genéricos.
 *
 * Uso: config('business_type_roles.restaurante')
 */
return [
    'restaurante' => [
        'owner' => ['owner', 'root', 'admin', 'gerente', 'vendedor', 'almacen', 'contador', 'admin-business', 'supervisor', 'administrativo', 'mesero', 'cocinero', 'delivery', 'bartender', 'cajero', 'instance-admin'],
        'root' => ['admin', 'gerente', 'vendedor', 'almacen', 'contador', 'supervisor', 'administrativo', 'mesero', 'cocinero', 'delivery', 'bartender', 'cajero', 'instance-admin'],
        'admin-business' => ['gerente', 'supervisor', 'administrativo', 'mesero', 'cocinero', 'delivery', 'bartender', 'cajero', 'instance-admin'],
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
    'climatizacion' => [
        'owner' => ['owner', 'root', 'admin', 'gerente', 'vendedor', 'almacen', 'contador', 'admin-business', 'supervisor', 'administrativo', 'tecnico', 'vendedor-tecnico', 'cotizador', 'instance-admin'],
        'root' => ['admin', 'gerente', 'vendedor', 'almacen', 'contador', 'supervisor', 'administrativo', 'tecnico', 'vendedor-tecnico', 'cotizador', 'instance-admin'],
        'admin-business' => ['gerente', 'supervisor', 'administrativo', 'tecnico', 'vendedor-tecnico', 'cotizador', 'instance-admin'],
    ],
    'tecnologia' => [
        'owner' => ['owner', 'root', 'admin', 'gerente', 'vendedor', 'almacen', 'contador', 'admin-business', 'supervisor', 'administrativo', 'technico', 'soporte', 'vendedor-tecnico', 'soporte-n1', 'soporte-n2', 'redes', 'almacen-tech', 'instance-admin'],
        'root' => ['admin', 'gerente', 'vendedor', 'almacen', 'contador', 'supervisor', 'administrativo', 'technico', 'soporte', 'vendedor-tecnico', 'soporte-n1', 'soporte-n2', 'redes', 'almacen-tech', 'instance-admin'],
        'admin-business' => ['gerente', 'supervisor', 'administrativo', 'technico', 'soporte', 'vendedor-tecnico', 'soporte-n1', 'soporte-n2', 'redes', 'almacen-tech', 'instance-admin'],
    ],
    'mecanica' => [
        'owner' => ['owner', 'root', 'admin', 'gerente', 'vendedor', 'almacen', 'contador', 'admin-business', 'supervisor', 'administrativo', 'cajero', 'mecanico', 'instance-admin'],
        'root' => ['admin', 'gerente', 'vendedor', 'almacen', 'contador', 'supervisor', 'administrativo', 'cajero', 'mecanico', 'instance-admin'],
        'admin-business' => ['gerente', 'supervisor', 'administrativo', 'cajero', 'mecanico', 'instance-admin'],
    ],
    'arte_escultura' => [
        'owner' => ['owner', 'root', 'admin', 'gerente', 'vendedor', 'almacen', 'contador', 'admin-business', 'supervisor', 'administrativo', 'curador', 'artista', 'logista', 'instance-admin'],
        'root' => ['admin', 'gerente', 'vendedor', 'almacen', 'contador', 'supervisor', 'administrativo', 'curador', 'artista', 'logista', 'instance-admin'],
        'admin-business' => ['gerente', 'supervisor', 'administrativo', 'curador', 'artista', 'logista', 'instance-admin'],
    ],
    'embutidos' => [
        'owner' => ['owner', 'root', 'admin', 'gerente', 'vendedor', 'almacen', 'contador', 'admin-business', 'supervisor', 'administrativo', 'cajero', 'reponedor', 'instance-admin'],
        'root' => ['admin', 'gerente', 'vendedor', 'almacen', 'contador', 'supervisor', 'administrativo', 'cajero', 'reponedor', 'instance-admin'],
        'admin-business' => ['gerente', 'supervisor', 'administrativo', 'cajero', 'reponedor', 'instance-admin'],
    ],
];