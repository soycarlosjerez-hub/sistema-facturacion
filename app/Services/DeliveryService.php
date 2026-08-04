<?php

namespace App\Services;

use App\Models\DeliveryTracking;
use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\Orden;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class DeliveryService
{
    public function calcularTarifaDelivery($distanciaKm, $zonaId, $hora = null)
    {
        $hora = $hora ?? now();
        $esNocturno = $hora->hour >= 22 || $hora->hour < 6;

        if (!$zonaId) {
            $tarifa = 50;
            return [
                'tarifa' => round($tarifa, 2),
                'distancia' => round($distanciaKm, 2),
                'es_nocturno' => $esNocturno,
            ];
        }

        $zona = DeliveryZone::where('id', $zonaId)
            ->where('activo', true)
            ->first();

        if (!$zona) {
            $tarifa = 50;
            return [
                'tarifa' => round($tarifa, 2),
                'distancia' => round($distanciaKm, 2),
                'es_nocturno' => $esNocturno,
            ];
        }

        $tarifa = $zona->tarifa_base + ($distanciaKm * $zona->tarifa_por_km);

        if ($esNocturno) {
            $tarifa *= 1.2;
        }

        if ($zona->minimo_para_envio_gratis > 0) {
            $tarifa = max(0, $tarifa);
        }

        return [
            'tarifa' => round($tarifa, 2),
            'distancia' => round($distanciaKm, 2),
            'es_nocturno' => $esNocturno,
        ];
    }

    public function verificarZonaCobertura($lat, $lng, $zonaId)
    {
        $zona = DeliveryZone::where('id', $zonaId)->first();

        if (!$zona) {
            return ['dentro_zona' => false, 'distancia_km' => 0];
        }

        if ($zona->zona_poligono && is_array($zona->zona_poligono)) {
            return $this->verificarPoligono($lat, $lng, $zona->zona_poligono);
        }

        $radioKm = $zona->radio_km ?? 10;

        $distancia = $this->calcularDistanciaHaversine(
            $lat,
            $lng,
            $zona->poligono_centro_lat ?? 18.4861,
            $zona->poligono_centro_lng ?? -69.9312
        );

        return [
            'dentro_zona' => $distancia <= $radioKm,
            'distancia_km' => round($distancia, 2),
        ];
    }

    private function verificarPoligono($lat, $lng, $poligono)
    {
        $rayCast = false;
        $points = $poligono;

        for ($i = 0, $j = count($points) - 1; $i < count($points); $j = $i++) {
            $yi = $points[$i][0];
            $xi = $points[$i][1];
            $yj = $points[$j][0];
            $xj = $points[$j][1];

            if ((($yi > $lat) !== ($yj > $lat))
                && ($lng < ($xj - $xi) * ($lat - $yi) / ($yj - $yi) + $xi)
            ) {
                $rayCast = !$rayCast;
            }
        }

        return ['dentro_zona' => $rayCast, 'distancia_km' => 0];
    }

    private function calcularDistanciaHaversine($lat1, $lng1, $lat2, $lng2)
    {
        $radioTierra = 6371;
        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLng = deg2rad($lng2 - $lng1);

        $a = sin($deltaLat / 2) * sin($deltaLat / 2)
            + cos($lat1Rad) * cos($lat2Rad)
            * sin($deltaLng / 2) * sin($deltaLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $radioTierra * $c;
    }

    public function estimarTiempoEntrega($zonaId, $distanciaKm)
    {
        $zona = DeliveryZone::where('id', $zonaId)->first();

        if (!$zona) {
            return ['minutos_estimados' => 30];
        }

        $tiempoBase = $zona->tiempo_estimado_minutos ?? 20;
        $tiempoExtra = floor($distanciaKm * 2);

        return [
            'minutos_estimados' => $tiempoBase + $tiempoExtra,
        ];
    }

    public function enviarNotificacionEntrega($orden, $status)
    {
        $notificationService = app(OrdenNotificationService::class);

        switch ($status) {
            case 'en_camino':
                $notificationService->sendOnTheWay($orden);
                break;
            case 'entregado':
                break;
            case 'fallido':
                break;
        }
    }

    public function crearSeguimiento($ordenId, $datos)
    {
        $orden = Orden::with('deliveryCompany')->findOrFail($ordenId);

        $tracking = DeliveryTracking::create([
            'tenant_id' => Auth::user()->business_instance_id,
            'orden_id' => $orden->id,
            'driver_id' => $datos['driver_id'] ?? null,
            'status' => $datos['status'] ?? 'creado',
            'notas' => $datos['notas'] ?? null,
            'latitud' => $datos['latitud'] ?? null,
            'longitud' => $datos['longitud'] ?? null,
            'creado_por' => Auth::id(),
        ]);

        $orden->update([
            'tracking_status' => $datos['status'] ?? 'creado',
        ]);

        return $tracking;
    }
}
