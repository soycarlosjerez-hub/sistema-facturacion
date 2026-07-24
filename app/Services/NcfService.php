<?php

namespace App\Services;

use App\Models\NcfSequence;
use Exception;

class NcfService
{
    /**
     * Reservar un NCF dentro de una transacción DB ya activa.
     * El increment se hace aquí DENTRO de la transacción para evitar gaps.
     * 
     * @param string $prefijo El prefijo del NCF (B01, B02, etc.)
     * @return array ['ncf' => string, 'sequence_id' => int]
     * @throws Exception
     */
    public function reservarNcfDentroTransaction(string $prefijo): array
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

        $sequence->increment('actual');

        return ['ncf' => $ncf, 'sequence_id' => $sequence->id, 'fecha_vencimiento' => $sequence->fecha_vencimiento];
    }

    /**
     * Obtener el próximo NCF para un tipo específico.
     * 
     * @param string $prefijo El prefijo del NCF (B01, B02, etc.)
     * @return string
     * @throws Exception
     */
    public function getNextNcf(string $prefijo): string
    {
        $result = $this->reservarNcfDentroTransaction($prefijo);
        return $result['ncf'];
    }
}
