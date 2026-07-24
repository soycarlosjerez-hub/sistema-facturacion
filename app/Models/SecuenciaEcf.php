<?php

namespace App\Models;

use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;

class SecuenciaEcf extends Model
{
    use Auditable;
    use TenantScope;
    protected $table = 'secuencias_ecf';

    protected $fillable = [
        'nombre',
        'tipo_ecf',
        'desde',
        'hasta',
        'actual',
        'fecha_vencimiento',
        'activo',
        'descripcion',
        'tenant_id',
    ];

    protected $casts = [
        'desde' => 'integer',
        'hasta' => 'integer',
        'actual' => 'integer',
        'activo' => 'boolean',
        'fecha_vencimiento' => 'date',
    ];

    public const TIPOS = [
        'E31' => 'Crédito Fiscal',
        'E32' => 'Consumo',
        'E33' => 'Nota de Débito',
        'E34' => 'Nota de Crédito',
        'E41' => 'Compras',
        'E43' => 'Gastos Menores',
        'E44' => 'Regímenes Especiales',
        'E45' => 'Gubernamentales',
        'E46' => 'Exportaciones',
        'E47' => 'Pagos al Exterior',
    ];

    public static function tiposParaCliente(string $tipoCliente): ?string
    {
        return match (strtolower($tipoCliente)) {
            'credito_fiscal' => 'E31',
            'gubernamental' => 'E45',
            'especial' => 'E31',
            'zona_franc' => 'E44',
            'consumo' => 'E32',
            default => 'E32',
        };
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(EcfDocumento::class, 'secuencia_ecf_id');
    }

    public function disponibles(): int
    {
        return max(0, $this->hasta - $this->actual);
    }

    public function porcentajeUso(): float
    {
        if ($this->hasta <= 0) return 0;
        return round(($this->actual / $this->hasta) * 100, 2);
    }

    public function vencida(): bool
    {
        return $this->fecha_vencimiento->isPast();
    }

    public function agotada(): bool
    {
        return $this->actual >= $this->hasta;
    }

    public function disponibleParaUso(): bool
    {
        return $this->activo && !$this->vencida() && !$this->agotada();
    }

    public function getNextNumero(): int
    {
        if ($this->agotada()) {
            throw new \RuntimeException("Secuencia {$this->tipo_ecf} agotada");
        }
        $this->increment('actual');
        $this->refresh();
        return $this->actual;
    }
}
