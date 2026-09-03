<?php

namespace App\Http\Controllers;

use App\Models\BusinessInstance;
use App\Models\InstanceErrorLog;
use App\Models\User;
use App\Services\TenantCleanupService;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class OwnerInstanceConfigController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:owner']);
    }

    private function logOwnerAction(string $action, string $description, ?array $oldValues = null, ?array $newValues = null, ?Model $model = null): void
    {
        try {
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'model_type' => $model ? get_class($model) : null,
                'model_id' => $model?->id,
                'description' => $description,
                'old_values' => $oldValues ? json_encode($oldValues) : null,
                'new_values' => $newValues ? json_encode($newValues) : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'tenant_id' => null,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to log owner action: ' . $e->getMessage());
        }
    }

    /**
     * Vista de configuración de una instancia.
     */
    public function instancesConfig($id): \Illuminate\View\View
    {
        $instance = BusinessInstance::withTrashed()->with('businessType')->findOrFail($id);
        $globalSettings = [
            'nombre_empresa' => \App\Models\SystemSetting::get('nombre_empresa', ''),
            'slogan' => \App\Models\SystemSetting::get('slogan', ''),
            'moneda_simbolo' => \App\Models\SystemSetting::get('moneda_simbolo', 'RD$'),
            'itbis_porcentaje' => \App\Models\SystemSetting::get('itbis_porcentaje', 18),
            'prefijo_factura' => \App\Models\SystemSetting::get('prefijo_factura', 'FAC-'),
            'prefijo_ncf' => \App\Models\SystemSetting::get('prefijo_ncf', ''),
            'dias_credito' => \App\Models\SystemSetting::get('dias_credito', 30),
            'impresora_papel_default' => \App\Models\SystemSetting::get('impresora_papel_default', '80mm'),
        ];
        $instanceConfig = $instance->configuracion ?? [];
        $instanceNotifSettings = \App\Models\InstanceNotificationSetting::forInstance($instance);

        return view('owner.instances.config', compact('instance', 'globalSettings', 'instanceConfig', 'instanceNotifSettings'));
    }

    /**
     * Actualiza la configuración de una instancia.
     */
    public function instancesConfigUpdate(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $instance = BusinessInstance::findOrFail($id);

        $data = $request->validate([
            'nombre_empresa' => 'nullable|string|max:255',
            'slogan' => 'nullable|string|max:500',
            'moneda_simbolo' => 'nullable|string|max:10',
            'itbis_porcentaje' => 'nullable|numeric|min:0|max:100',
            'prefijo_factura' => 'nullable|string|max:20',
            'prefijo_ncf' => 'nullable|string|max:10',
            'dias_credito' => 'nullable|integer|min:0|max:365',
            'impresora_papel_default' => 'nullable|in:58mm,80mm',
            'restaurante_valida_stock' => 'nullable|string',
            'enabled' => 'nullable|boolean',
            'sale_created' => 'nullable|boolean',
            'sale_paid' => 'nullable|boolean',
            'sale_cancelled' => 'nullable|boolean',
            'order_confirmed' => 'nullable|boolean',
            'order_ready' => 'nullable|boolean',
            'order_shipped' => 'nullable|boolean',
            'payment_received' => 'nullable|boolean',
            'credit_overdue' => 'nullable|boolean',
            'credit_abono' => 'nullable|boolean',
            'stock_critical' => 'nullable|boolean',
            'stock_restocked' => 'nullable|boolean',
            'product_created' => 'nullable|boolean',
            'shift_opened' => 'nullable|boolean',
            'shift_closed' => 'nullable|boolean',
            'cash_shortage' => 'nullable|boolean',
            'daily_report' => 'nullable|boolean',
            'ncff_expiring' => 'nullable|boolean',
            'ecf_certificate_expiring' => 'nullable|boolean',
            'backup_completed' => 'nullable|boolean',
            'backup_failed' => 'nullable|boolean',
            'user_registered' => 'nullable|boolean',
            'subscription_expiring' => 'nullable|boolean',
            'subscription_suspended' => 'nullable|boolean',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'delete_logo' => 'nullable|boolean',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($instance->logo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($instance->logo);
            }
            // Store new logo
            $logoPath = $request->file('logo')->store('logos', 'public');
            $instance->update(['logo' => $logoPath]);
        }

        // Handle logo delete
        if ($request->has('delete_logo') && $request->delete_logo == '1') {
            if ($instance->logo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($instance->logo);
                $instance->update(['logo' => null]);
            }
        }

        $data['restaurante_valida_stock'] = $request->has('restaurante_valida_stock') ? '1' : '0';

        $existingConfig = $instance->configuracion ?? [];
        $mergedConfig = array_merge($existingConfig, array_filter($data, fn($v) => !is_null($v)));

        $instance->update(['configuracion' => $mergedConfig]);

        $notifData = collect($data)->only([
            'enabled',
            'sale_created', 'sale_paid', 'sale_cancelled',
            'order_confirmed', 'order_ready', 'order_shipped',
            'payment_received', 'credit_overdue', 'credit_abono',
            'stock_critical', 'stock_restocked', 'product_created',
            'shift_opened', 'shift_closed', 'cash_shortage', 'daily_report',
            'ncff_expiring', 'ecf_certificate_expiring',
            'backup_completed', 'backup_failed', 'user_registered',
            'subscription_expiring', 'subscription_suspended',
        ])->toArray();

        \App\Models\InstanceNotificationSetting::updateOrCreate(
            ['business_instance_id' => $instance->id],
            $notifData
        );

        return redirect()->route('owner.instances.show', $instance)
            ->with('success', 'Configuración de instancia actualizada correctamente.');
    }

    /**
     * Lista errores de una instancia específica.
     */
    public function instanceErrors($id): \Illuminate\View\View
    {
        $instance = BusinessInstance::findOrFail($id);

        $query = InstanceErrorLog::where('tenant_id', $id)->with('user', 'resolvedBy', 'tenant');

        if ($level = request('level')) {
            $query->ofLevel($level);
        }
        if ($source = request('source')) {
            $query->ofSource($source);
        }
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }
        if ($desde = request('desde')) {
            $query->whereDate('created_at', '>=', $desde);
        }
        if ($hasta = request('hasta')) {
            $query->whereDate('created_at', '<=', $hasta);
        }
        if (request()->has('resolved') && request('resolved') !== '') {
            $query->where('resolved', request('resolved'));
        }

        $errorLogs = $query->latest()->paginate(30)->withQueryString();

        $stats = [
            'total' => InstanceErrorLog::where('tenant_id', $id)->count(),
            'last_7d' => InstanceErrorLog::where('tenant_id', $id)->recent(7)->count(),
            'errors' => InstanceErrorLog::where('tenant_id', $id)->ofLevel('error')->count(),
            'warnings' => InstanceErrorLog::where('tenant_id', $id)->ofLevel('warning')->count(),
            'criticals' => InstanceErrorLog::where('tenant_id', $id)->ofLevel('critical')->count(),
        ];

        return view('owner.instances.errors', compact('instance', 'errorLogs', 'stats'));
    }

    /**
     * Borra errores antiguos de una instancia.
     */
    public function clearErrors($id): \Illuminate\Http\RedirectResponse
    {
        $instance = BusinessInstance::findOrFail($id);

        $deleted = InstanceErrorLog::where('tenant_id', $id)
            ->where('created_at', '<', now()->subDays(30))
            ->delete();

        return back()->with('success', "Se eliminaron {$deleted} errores antiguos.");
    }

    /**
     * Marca un error como resuelto o lo reabre.
     */
    public function resolveError($instanceId, InstanceErrorLog $errorLog): \Illuminate\Http\RedirectResponse
    {
        $instance = BusinessInstance::findOrFail($instanceId);

        if ($errorLog->tenant_id !== (int) $instanceId) {
            abort(404);
        }

        $errorLog->update([
            'resolved' => !$errorLog->resolved,
            'resolved_at' => $errorLog->resolved ? null : now(),
            'resolved_by' => $errorLog->resolved ? null : auth()->id(),
        ]);

        $msg = $errorLog->resolved ? 'Error marcado como resuelto.' : 'Error reabierto.';

        return back()->with('success', $msg);
    }
}
