<?php

namespace App\Services;

use App\Models\ClimatizacionFactura;
use App\Models\Mantenimiento;
use App\Models\ContratoMantenimiento;
use App\Models\OrdenEmergencia;
use App\Models\Instalacion;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;

class ClimatizacionFacturaService
{
    public function generarDesdeMantenimiento(Mantenimiento $mantenimiento): ClimatizacionFactura
    {
        if ($mantenimiento->estado !== 'completado') {
            throw new \InvalidArgumentException('Solo se puede facturar un mantenimiento completado.');
        }

        if ($mantenimiento->total <= 0) {
            throw new \InvalidArgumentException('El mantenimiento no tiene montos para facturar.');
        }

        $this->verificarDuplicado('mantenimiento', $mantenimiento->id);

        $detalle = $this->construirDetalleMantenimiento($mantenimiento);
        [$subtotal, $itbis, $total] = $this->calcularConITBIS($mantenimiento->costo_repuestos + $mantenimiento->mano_de_obra);

        return DB::transaction(function () use ($mantenimiento, $detalle, $subtotal, $itbis, $total) {
            return ClimatizacionFactura::create([
                'business_instance_id' => $mantenimiento->business_instance_id,
                'cliente_id' => $mantenimiento->cliente_id,
                'created_by' => auth()->id(),
                'origen' => 'mantenimiento',
                'origen_id' => $mantenimiento->id,
                'referencia' => $mantenimiento->numero,
                'subtotal' => $subtotal,
                'itbis' => $itbis,
                'descuento' => 0,
                'total' => $total,
                'estado' => 'borrador',
                'detalle' => $detalle,
            ]);
        });
    }

    public function generarDesdeContrato(ContratoMantenimiento $contrato): ClimatizacionFactura
    {
        if ($contrato->estado !== 'activo') {
            throw new \InvalidArgumentException('Solo se puede facturar cuotas de contratos activos.');
        }

        $this->verificarDuplicadoContrato($contrato);

        $valor = $contrato->valor_mensual;
        [$subtotal, $itbis, $total] = $this->calcularConITBIS($valor);

        return DB::transaction(function () use ($contrato, $valor, $subtotal, $itbis, $total) {
            return ClimatizacionFactura::create([
                'business_instance_id' => $contrato->business_instance_id,
                'cliente_id' => $contrato->cliente_id,
                'created_by' => auth()->id(),
                'origen' => 'contrato_cuota',
                'origen_id' => $contrato->id,
                'referencia' => $contrato->codigo,
                'subtotal' => $subtotal,
                'itbis' => $itbis,
                'descuento' => 0,
                'total' => $total,
                'estado' => 'borrador',
                'detalle' => [[
                    'descripcion' => 'Cuota ' . ucfirst($contrato->tipo_periodicidad) . ' - Contrato ' . $contrato->codigo,
                    'cantidad' => 1,
                    'precio_unitario' => $valor,
                    'subtotal' => $valor,
                ]],
            ]);
        });
    }

    public function generarDesdeEmergencia(OrdenEmergencia $orden): ClimatizacionFactura
    {
        if (!in_array($orden->estado, ['resuelta', 'cerrada'])) {
            throw new \InvalidArgumentException('Solo se puede facturar una emergencia resuelta o cerrada.');
        }

        $costoFinal = $orden->costo_final ?? 0;
        if ($costoFinal <= 0) {
            throw new \InvalidArgumentException('La emergencia no tiene costo final definido.');
        }

        $this->verificarDuplicado('emergencia', $orden->id);

        [$subtotal, $itbis, $total] = $this->calcularConITBIS($costoFinal);

        return DB::transaction(function () use ($orden, $costoFinal, $subtotal, $itbis, $total) {
            return ClimatizacionFactura::create([
                'business_instance_id' => $orden->business_instance_id,
                'cliente_id' => $orden->cliente_id,
                'created_by' => auth()->id(),
                'origen' => 'emergencia',
                'origen_id' => $orden->id,
                'referencia' => $orden->codigo,
                'subtotal' => $subtotal,
                'itbis' => $itbis,
                'descuento' => 0,
                'total' => $total,
                'estado' => 'borrador',
                'detalle' => [[
                    'descripcion' => 'Servicio de Emergencia - ' . ($orden->tipo_falla ?? 'Sin especificar'),
                    'cantidad' => 1,
                    'precio_unitario' => $costoFinal,
                    'subtotal' => $costoFinal,
                ]],
            ]);
        });
    }

    public function generarDesdeInstalacion(Instalacion $instalacion): ClimatizacionFactura
    {
        if ($instalacion->estado !== 'completada') {
            throw new \InvalidArgumentException('Solo se puede facturar una instalación completada.');
        }

        if ($instalacion->total <= 0) {
            throw new \InvalidArgumentException('La instalación no tiene montos para facturar.');
        }

        $this->verificarDuplicado('instalacion', $instalacion->id);

        $detalle = $this->construirDetalleInstalacion($instalacion);
        [$subtotal, $itbis, $total] = $this->calcularConITBIS($instalacion->total);

        return DB::transaction(function () use ($instalacion, $detalle, $subtotal, $itbis, $total) {
            return ClimatizacionFactura::create([
                'business_instance_id' => $instalacion->business_instance_id,
                'cliente_id' => $instalacion->cliente_id,
                'created_by' => auth()->id(),
                'origen' => 'instalacion',
                'origen_id' => $instalacion->id,
                'referencia' => $instalacion->numero,
                'subtotal' => $subtotal,
                'itbis' => $itbis,
                'descuento' => 0,
                'total' => $total,
                'estado' => 'borrador',
                'detalle' => $detalle,
            ]);
        });
    }

    public function anular(ClimatizacionFactura $factura): void
    {
        if ($factura->estado === 'anulada') {
            throw new \InvalidArgumentException('Esta factura ya está anulada.');
        }

        DB::transaction(fn () => $factura->update(['estado' => 'anulada']));
    }

    public function generar(ClimatizacionFactura $factura): void
    {
        if ($factura->estado !== 'borrador') {
            throw new \InvalidArgumentException('Solo se pueden generar facturas en estado borrador.');
        }

        DB::transaction(fn () => $factura->update(['estado' => 'generada']));
    }

    private function verificarDuplicado(string $origen, int $origenId): void
    {
        $existe = ClimatizacionFactura::where('origen', $origen)
            ->where('origen_id', $origenId)
            ->first();

        if ($existe) {
            throw new \InvalidArgumentException("Ya existe una factura para este {$origen}.");
        }
    }

    private function verificarDuplicadoContrato(ContratoMantenimiento $contrato): void
    {
        $existe = ClimatizacionFactura::where('origen', 'contrato_cuota')
            ->where('origen_id', $contrato->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->first();

        if ($existe) {
            throw new \InvalidArgumentException('Ya existe una factura de cuota para este contrato este mes.');
        }
    }

    private function construirDetalleMantenimiento(Mantenimiento $mtto): array
    {
        $detalle = [];

        if ($mtto->costo_repuestos > 0) {
            if ($mtto->repuestos_usados && is_array($mtto->repuestos_usados) && count($mtto->repuestos_usados) > 0) {
                foreach ($mtto->repuestos_usados as $repuesto) {
                    $detalle[] = [
                        'descripcion' => $repuesto['nombre'] ?? 'Repuesto',
                        'cantidad' => $repuesto['cantidad'] ?? 1,
                        'precio_unitario' => $repuesto['precio'] ?? 0,
                        'subtotal' => ($repuesto['cantidad'] ?? 1) * ($repuesto['precio'] ?? 0),
                    ];
                }
            } else {
                $detalle[] = [
                    'descripcion' => 'Repuestos - Mantenimiento ' . $mtto->numero,
                    'cantidad' => 1,
                    'precio_unitario' => $mtto->costo_repuestos,
                    'subtotal' => $mtto->costo_repuestos,
                ];
            }
        }

        if ($mtto->mano_de_obra > 0) {
            $detalle[] = [
                'descripcion' => 'Mano de Obra - Mantenimiento ' . $mtto->numero,
                'cantidad' => 1,
                'precio_unitario' => $mtto->mano_de_obra,
                'subtotal' => $mtto->mano_de_obra,
            ];
        }

        return $detalle;
    }

    private function construirDetalleInstalacion(Instalacion $inst): array
    {
        $detalle = [];

        if ($inst->productos && $inst->productos->count() > 0) {
            foreach ($inst->productos as $producto) {
                $cantidad = $producto->pivot->cantidad ?? 1;
                $precio = $producto->pivot->precio_unitario ?? 0;
                $detalle[] = [
                    'descripcion' => $producto->nombre,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio,
                    'subtotal' => $cantidad * $precio,
                ];
            }
        } else {
            $detalle[] = [
                'descripcion' => 'Instalación de equipos - ' . $inst->numero,
                'cantidad' => 1,
                'precio_unitario' => $inst->total,
                'subtotal' => $inst->total,
            ];
        }

        return $detalle;
    }

    private function calcularConITBIS(float $subtotal): array
    {
        $itbis = round($subtotal * (SystemSetting::itbisDefault() / 100), 2);
        $total = round($subtotal + $itbis, 2);
        return [$subtotal, $itbis, $total];
    }
}
