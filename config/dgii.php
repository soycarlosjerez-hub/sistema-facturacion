<?php

return [
    'ambiente' => env('DGII_AMBIENTE', 'sandbox'),

    'ambientes' => [
        'sandbox' => [
            'api_url' => env('DGII_API_URL_SANDBOX', 'https://ecf-sandbox.dgii.gov.do/api/v1'),
            'cert_required' => false,
            'descripcion' => 'Sandbox - Pruebas locales sin conexión a DGII',
        ],
        'qa' => [
            'api_url' => env('DGII_API_URL_QA', 'https://ecf-qa.dgii.gov.do/api/v1'),
            'cert_required' => true,
            'descripcion' => 'QA - Ambiente de pruebas DGII',
        ],
        'produccion' => [
            'api_url' => env('DGII_API_URL_PROD', 'https://ecf.dgii.gov.do/api/v1'),
            'cert_required' => true,
            'descripcion' => 'Producción - Ambiente real DGII',
        ],
    ],

    'cert_storage_path' => env('DGII_CERT_STORAGE_PATH', storage_path('app/certificados')),
    'xml_storage_path' => env('DGII_XML_STORAGE_PATH', storage_path('app/ecf/xml')),

    'cert_password_env' => env('DGII_CERT_PASSWORD', ''),

    'consulta_publica_url' => env('DGII_CONSULTA_URL', 'https://dgii.gov.do/ecf/consulta'),

    'tipos_ecf' => [
        'E31' => ['nombre' => 'Crédito Fiscal', 'requiere_rnc' => true],
        'E32' => ['nombre' => 'Consumo', 'requiere_rnc' => false],
        'E33' => ['nombre' => 'Nota de Débito', 'requiere_rnc' => true],
        'E34' => ['nombre' => 'Nota de Crédito', 'requiere_rnc' => true],
        'E41' => ['nombre' => 'Compras', 'requiere_rnc' => true],
        'E43' => ['nombre' => 'Gastos Menores', 'requiere_rnc' => false],
        'E44' => ['nombre' => 'Regímenes Especiales', 'requiere_rnc' => true],
        'E45' => ['nombre' => 'Gubernamentales', 'requiere_rnc' => true],
        'E46' => ['nombre' => 'Exportaciones', 'requiere_rnc' => false],
        'E47' => ['nombre' => 'Pagos al Exterior', 'requiere_rnc' => false],
    ],

    'itbis_default' => env('ITBIS_DEFAULT', 18.0),
    'moneda' => env('MONEDA_DEFAULT', 'DOP'),
    'codigo_moneda_dop' => 'DOP',

    'simular_dgii' => env('DGII_SIMULAR', false),
    'probabilidad_aprobacion_sim' => env('DGII_PROB_APROB', 0.85),

    'qr_endpoint' => env('DGII_QR_ENDPOINT', 'https://dgii.gov.do/app/WebApps/ConsultasWeb/ConsultasWeb/consulta'),
];
