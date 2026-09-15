<?php

namespace Tests\Unit;

use App\Services\Ecf\EcfArchiveService;
use Tests\TestCase;

class EcfArchiveRetentionTest extends TestCase
{
    public function test_retencion_es_10_anios(): void
    {
        $this->assertSame(10, EcfArchiveService::RETENCION_ANIOS);
    }

    public function test_limpieza_bloqueada_dentro_del_periodo_de_conservacion(): void
    {
        $service = new EcfArchiveService;

        $resultado = $service->limpiarArchivosAnterioresA(now()->subYears(5));

        $this->assertFalse($resultado['success']);
        $this->assertSame(0, $resultado['eliminados']);
    }
}
