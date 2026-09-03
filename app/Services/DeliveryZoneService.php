<?php

namespace App\Services;

use App\Models\DeliveryZone;

class DeliveryZoneService
{
    /**
     * Calcular tarifa de delivery basado en zona y distancia.
     * Incluye tarifa nocturna si aplica.
     */
    public function calcularTarifa(int $zonaId, float $distanciaKm): array
    {
        $zona = DeliveryZone::where('id', $zonaId)
            ->where('activo', true)
            ->first();

        if (!$zona) {
            return [
                'tarifa' => 50.00,
                'distancia_km' => $distanciaKm,
                'es_nocturno' => false,
                'error' => 'Zona de delivery no encontrada',
            ];
        }

        $tarifa = $zona->tarifa_base + ($distanciaKm * $zona->tarifa_por_km);
        $hora = now();
        $esNocturno = $hora->hour >= 22 || $hora->hour < 6;

        if ($esNocturno) {
            $tarifa *= 1.2;
        }

        if ($zona->minimo_para_envio_gratis > 0 && $tarifa > 0) {
            $tarifa = max(0, $tarifa);
        }

        return [
            'tarifa' => round($tarifa, 2),
            'distancia_km' => round($distanciaKm, 2),
            'es_nocturno' => $esNocturno,
            'zona' => [
                'id' => $zona->id,
                'nombre' => $zona->nombre,
                'tarifa_base' => (float) $zona->tarifa_base,
                'tarifa_por_km' => (float) $zona->tarifa_por_km,
            ],
        ];
    }

    /**
     * Obtener todas las zonas de delivery activas.
     */
    public function obtenerZonasActivas(): array
    {
        $zonas = DeliveryZone::where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'descripcion', 'tarifa_base', 'tarifa_por_km', 'radio_km'])
            ->map(function ($zona) {
                return [
                    'id' => $zona->id,
                    'nombre' => $zona->nombre,
                    'descripcion' => $zona->descripcion,
                    'tarifa_base' => (float) $zona->tarifa_base,
                    'tarifa_por_km' => (float) $zona->tarifa_por_km,
                    'radio_km' => (float) $zona->radio_km,
                ];
            });

        return ['zonas' => $zonas];
    }

    /**
     * Verificar si una coordenada (lat, lng) está dentro del radio de una zona.
     */
    public function verificarCobertura(float $lat, float $lng, int $zonaId): array
    {
        $zona = DeliveryZone::find($zonaId);

        if (!$zona) {
            return [
                'dentro_zona' => false,
                'distancia_km' => 0,
                'error' => 'Zona no encontrada',
            ];
        }

        $radioKm = $zona->radio_km ?? 10;

        // Centro por defecto (Santo Domingo)
        $centroLat = $zona->poligono_centro_lat ?? 18.4861;
        $centroLng = $zona->poligono_centro_lng ?? -69.9312;

        $distancia = $this->calcularDistanciaHaversine($lat, $lng, $centroLat, $centroLng);

        return [
            'dentro_zona' => $distancia <= $radioKm,
            'distancia_km' => round($distancia, 2),
            'radio_km' => $radioKm,
        ];
    }

    /**
     * Calcular distancia entre dos puntos usando fórmula Haversine.
     */
    private function calcularDistanciaHaversine(float $lat1, float $lng1, float $lat2, float $lng2): float
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
}
