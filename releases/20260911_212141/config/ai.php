<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Assistant Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains all the configuration settings for the AI chatbot
    | integrated into the ERP system. The API key is stored exclusively
    | in the backend and never exposed to the frontend.
    |
    */

    'api_url' => env('AI_API_URL'),

    'api_key' => env('AI_API_KEY'),

    'model' => env('AI_MODEL', 'gpt-4o'),

    'stream' => (bool) env('AI_STREAM', true),

    'temperature' => (float) env('AI_TEMPERATURE', 0.3),

    'max_tokens' => (int) env('AI_MAX_TOKENS', 2000),

    'timeout' => (int) env('AI_TIMEOUT', 60),

    /*
    |--------------------------------------------------------------------------
    | Disable Thinking (vLLM/Qwen3)
    |--------------------------------------------------------------------------
    |
    | Los modelos de razonamiento (Qwen3) consumen gran parte del presupuesto
    | de tokens "pensando" antes de responder, lo que agota max_tokens y
    | produce respuestas vacias. Al desactivar el razonamiento las respuestas
    | son directas y confiables. Solo aplica a backends vLLM compatibles.
    |
    */
    'disable_thinking' => (bool) env('AI_DISABLE_THINKING', true),

    /*
    |--------------------------------------------------------------------------
    | SSL Verification
    |--------------------------------------------------------------------------
    |
    | Verify the LLM endpoint TLS certificate. Disable only if the endpoint
    | uses a private/internal certificate (e.g. WAMP without CA bundle) and
    | you trust the network. Optionally point to a CA bundle file.
    |
    */
    'ssl_verify' => (bool) env('AI_SSL_VERIFY', true),

    'ca_bundle' => env('AI_CA_BUNDLE'),

    /*
    |--------------------------------------------------------------------------
    | System Prompt
    |--------------------------------------------------------------------------
    |
    | This prompt is sent to the LLM in every conversation. It defines
    | the AI's behavior and enforces the read-only policy.
    |
    */
    'system_prompt' => "Eres un asistente de solo lectura del sistema ERP Facturacion.\n\nTu unico rol es consultar INFORMACION y ayudar al usuario a comprender los datos de SU negocio. Tu funcion es mostrar datos reales del sistema, nunca inventarlos.\n\nREGLAS ABSOLUTAS:\n1. SOLO LECTURA: No puedes crear, modificar ni eliminar ningun dato. Si el usuario te pide realizar una accion de escritura, debes responder que NO puedes hacerlo.\n2. Usa EXCLUSIVAMENTE las herramientas disponibles para consultar datos. NUNCA inventes informacion.\n3. Si no encuentras informacion solicitada, indicalo claramente.\n4. Responde siempre en español.\n5. Cuando proporciones datos numericos, siempre especifica la moneda (RD$).\n6. No proporciones datos que no hayas obtenido a tr de las herramientas disponibles.\n7. Las herramientas disponibles son herramientas de consulta SOLO. Nunca intentes realizar acciones de escritura.\n\nInformacion del usuario:\n- Empresa: {business_name}\n- Usuario: {user_name}\n- Rol: {role}\n\nSi el usuario solicita eliminar, modificar, crear o cualquier accion de escritura, responde: \"No puedo modificar ni eliminar informacion. Mi funcion es unicamente consultar y mostrar informacion de tu sistema.\"",

    /*
    |--------------------------------------------------------------------------
    | Maximum Conversation Messages Kept in Context
    |--------------------------------------------------------------------------
    |
    | How many recent messages to include in the conversation history
    | sent to the LLM. Older messages are stored in the database but
    | not included in the context window to optimize API usage.
    |
    */
    'max_context_messages' => 20,

    /*
    |--------------------------------------------------------------------------
    | Maximum Results Per Tool
    |--------------------------------------------------------------------------
    |
    | Limits how many records each tool returns to prevent excessive
    | data from being sent to the LLM.
    |
    */
    'max_tool_results' => 50,

    /*
    |--------------------------------------------------------------------------
    | Allowed Tools (Read-Only)
    |--------------------------------------------------------------------------
    |
    | This is the definitive list of tools that can be called. Tools are
    | dynamically registered based on user permissions, so the actual
    | available tools depend on what the authenticated user can access.
    |
    */
    'tools' => [
        'get_dashboard' => \App\Services\Ai\Tools\GetDashboardTool::class,
        'get_sales' => \App\Services\Ai\Tools\GetSalesTool::class,
        'get_sale' => \App\Services\Ai\Tools\GetSaleTool::class,
        'get_customers' => \App\Services\Ai\Tools\GetCustomersTool::class,
        'get_customer' => \App\Services\Ai\Tools\GetCustomerTool::class,
        'get_products' => \App\Services\Ai\Tools\GetProductsTool::class,
        'get_product' => \App\Services\Ai\Tools\GetProductTool::class,
        'get_inventory' => \App\Services\Ai\Tools\GetInventoryTool::class,
        'get_purchases' => \App\Services\Ai\Tools\GetPurchasesTool::class,
        'get_expenses' => \App\Services\Ai\Tools\GetExpensesTool::class,
        'get_accounts_receivable' => \App\Services\Ai\Tools\GetAccountsReceivableTool::class,
        'get_accounts_payable' => \App\Services\Ai\Tools\GetAccountsPayableTool::class,
        'get_invoices' => \App\Services\Ai\Tools\GetInvoicesTool::class,
        'get_invoice' => \App\Services\Ai\Tools\GetInvoiceTool::class,
        'get_suppliers' => \App\Services\Ai\Tools\GetSuppliersTool::class,
        'get_reports' => \App\Services\Ai\Tools\GetReportsTool::class,
    ],
];
