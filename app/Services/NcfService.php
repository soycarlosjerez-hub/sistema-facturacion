<?php

namespace App\Services;

use App\Models\NcfSequence;
use Exception;

class NcfService
{
    /**
     * Obtener el próximo NCF para un tipo específico.
     * 
     * @param string $prefijo El prefijo del NCF (B01, B02, etc.)
     * @return string
     * @throws Exception
     */
    public function getNextNcf(string $prefijo): string
    {
        $sequence = NcfSequence::where('prefijo', $prefijo)
            ->where('activo', true)
            ->where('fecha_vencimiento', '>=', now())
            ->lockForUpdate()
            ->first();

        if (!$sequence) {
            throw new Exception("No hay secuencias de NCF disponibles para el prefijo {$prefijo} o han vencido.");
        }

        if ($sequence->actual > $sequence->hasta) {
            throw new Exception("La secuencia de NCF {$prefijo} se ha agotado.");
        }

        $numero = str_pad($sequence->actual, 8, '0', STR_PAD_LEFT);
        $ncf = $prefijo . $numero;

        // Incrementar el contador actual
        $sequence->increment('actual');

        return $ncf;
    }
}
