<?php

namespace App\Services\Ai\Tools;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class AiToolsManager
{
    public function getAvailableTools(): array
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }

        $tools = config('ai.tools', []);
        $available = [];
        $permissionMap = [
            'get_dashboard' => 'dashboard.view',
            'get_sales' => 'ventas.view',
            'get_sale' => 'ventas.view',
            'get_customers' => 'clientes.view',
            'get_customer' => 'clientes.view',
            'get_products' => 'productos.view',
            'get_product' => 'productos.view',
            'get_inventory' => 'almacenes.view',
            'get_purchases' => 'compras.view',
            'get_expenses' => 'gastos.view',
            'get_accounts_receivable' => 'ventas.view',
            'get_accounts_payable' => 'compras.view',
            'get_invoices' => 'ventas.view',
            'get_invoice' => 'ventas.view',
            'get_suppliers' => 'proveedores.view',
            'get_reports' => 'reportes.view',
        ];

        foreach ($tools as $name => $class) {
            $permission = $permissionMap[$name] ?? null;

            if ($permission && ! $user->can($permission)) {
                continue;
            }

            if (class_exists($class)) {
                /** @var AiToolInterface $toolInstance */
                $toolInstance = app($class);
                $available[] = $toolInstance;
            }
        }

        return array_map(fn($tool) => $this->buildToolDefinition($tool), $available);
    }

    public function executeTool(string $toolName, array $arguments, Authenticatable $user): array
    {
        $tools = config('ai.tools', []);

        if (!array_key_exists($toolName, $tools)) {
            throw new \RuntimeException("Tool '{$toolName}' is not registered or not available.");
        }

        $class = $tools[$toolName];
        if (!class_exists($class)) {
            throw new \RuntimeException("Tool class '{$class}' does not exist.");
        }

        /** @var AiToolInterface $toolInstance */
        $toolInstance = app($class);

        $permissionMap = [
            'get_dashboard' => 'dashboard.view',
            'get_sales' => 'ventas.view',
            'get_sale' => 'ventas.view',
            'get_customers' => 'clientes.view',
            'get_customer' => 'clientes.view',
            'get_products' => 'productos.view',
            'get_product' => 'productos.view',
            'get_inventory' => 'almacenes.view',
            'get_purchases' => 'compras.view',
            'get_expenses' => 'gastos.view',
            'get_accounts_receivable' => 'ventas.view',
            'get_accounts_payable' => 'compras.view',
            'get_invoices' => 'ventas.view',
            'get_invoice' => 'ventas.view',
            'get_suppliers' => 'proveedores.view',
            'get_reports' => 'reportes.view',
        ];

        $permission = $permissionMap[$toolName] ?? null;
        if ($permission && !$user->can($permission)) {
            throw new \RuntimeException("Tool '{$toolName}' requires permission '{$permission}' which the user does not have.");
        }

        return $toolInstance->execute($arguments, $user);
    }

    private function buildToolDefinition(AiToolInterface $tool): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => $tool->getName(),
                'description' => $tool->getDescription(),
                'parameters' => $tool->getParameters(),
            ],
        ];
    }
}
