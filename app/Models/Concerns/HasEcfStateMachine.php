<?php

namespace App\Models\Concerns;

use App\Models\EcfDocumento;

trait HasEcfStateMachine
{
    public static array $allowedTransitions = [
        'borrador' => ['generado', 'firmado'],
        'generado' => ['firmado'],
        'firmado' => ['enviado'],
        'enviado' => ['aprobado', 'rechazado'],
        'aprobado' => ['anulado'],
        'rechazado' => ['firmado', 'enviado'],
        'anulado' => [],
        'expirado' => [],
    ];

    public static array $terminalStates = ['anulado', 'expirado'];

    public static array $pendingStates = ['borrador', 'generado', 'firmado', 'enviado'];

    public function puedeTransicionarA(string $destino): bool
    {
        return in_array($destino, self::$allowedTransitions[$this->estado] ?? [], true);
    }

    public function transicionarA(string $destino, ?\Closure $before = null): static
    {
        if (!$this->puedeTransicionarA($destino)) {
            $from = $this->estado;
            throw new \RuntimeException(
                "Transición inválida: {$from} → {$destino}. " .
                "Transiciones permitidas desde {$from}: " .
                implode(', ', self::$allowedTransitions[$from] ?? ['ninguna'])
            );
        }

        if ($before) {
            $before($this);
        }

        $this->estado = $destino;
        $this->save();

        return $this;
    }

    public function esTerminal(): bool
    {
        return in_array($this->estado, self::$terminalStates, true);
    }

    public function esPendiente(): bool
    {
        return in_array($this->estado, self::$pendingStates, true);
    }

    public function puedeAnular(): bool
    {
        return $this->estado === 'aprobado';
    }

    public function requiereNotaCredito(): bool
    {
        return $this->estado === 'aprobado';
    }
}
