<?php

namespace Tests\Unit;

use App\Models\EcfDocumento;
use App\Models\SecuenciaEcf;
use App\Services\Ecf\DgiiTokenManager;
use App\Services\Ecf\EcfQrGenerator;
use App\Services\Ecf\InformeDiarioService;
use App\Services\Ecf\EcfRetryService;
use App\Services\Ecf\EcfArchiveService;
use App\Models\Concerns\HasEcfStateMachine;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class DgiiTokenManagerTest extends TestCase
{
    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }

    public function test_get_token_returns_simulated_token_in_sandbox(): void
    {
        Config::set('dgii.ambiente', 'sandbox');
        Config::set('dgii.ambientes.sandbox', [
            'api_url' => 'https://sandbox.dgii.gov.do/api/v1',
            'cert_required' => false,
        ]);
        Config::set('dgii.simular_dgii', true);

        $manager = new DgiiTokenManager();
        $token1 = $manager->getToken();
        $token2 = $manager->getToken();

        $this->assertIsString($token1);
        $this->assertEquals($token1, $token2);
        $this->assertStringContainsString('SIM-TOKEN', $token1);
    }

    public function test_refresh_token_clears_cache_and_generates_new(): void
    {
        Config::set('dgii.ambiente', 'sandbox');
        Config::set('dgii.ambientes.sandbox', [
            'api_url' => 'https://sandbox.dgii.gov.do/api/v1',
            'cert_required' => false,
        ]);
        Config::set('dgii.simular_dgii', true);

        $manager = new DgiiTokenManager();
        $token1 = $manager->getToken();
        $manager->refreshToken();
        $token2 = $manager->getToken();

        $this->assertNotEquals($token1, $token2);
    }

    public function test_invalidate_token_removes_from_cache(): void
    {
        Config::set('dgii.ambiente', 'sandbox');
        Config::set('dgii.ambientes.sandbox', [
            'api_url' => 'https://sandbox.dgii.gov.do/api/v1',
            'cert_required' => false,
        ]);
        Config::set('dgii.simular_dgii', true);

        $manager = new DgiiTokenManager();
        $token1 = $manager->getToken();
        $manager->invalidateToken();
        $token2 = $manager->getToken();

        $this->assertNotEquals($token1, $token2);
    }
}

class EcfQrGeneratorTest extends TestCase
{
    public function test_build_query_string_with_mock_ecf(): void
    {
        Config::set('dgii.qr_endpoint', 'https://dgii.gov.do/app/WebApps/ConsultasWeb/ConsultasWeb/consulta');

        $generator = new EcfQrGenerator();

        $carbon = \Carbon\Carbon::parse('2026-07-15 10:30:00');
        $ecf = new class extends EcfDocumento {
            public $encf = 'E31123456789';
            public $monto_total = '15000.00';
            public $fecha_emision;
            public $codigo_seguridad = 'ABC123';

            public function __construct()
            {
                $this->fecha_emision = \Carbon\Carbon::parse('2026-07-15 10:30:00');
            }
        };

        $queryString = $generator->buildQueryString($ecf);
        $this->assertStringContainsString('rnc=', $queryString);
        $this->assertStringContainsString('encf=E31123456789', $queryString);
        $this->assertStringContainsString('monto=15000.00', $queryString);
        $this->assertStringContainsString('fecha=2026-07-15', $queryString);
    }

    public function test_generate_code_security_is_consistent(): void
    {
        Config::set('app.key', 'base64:testkey123456789012345678901234=');

        $generator = new EcfQrGenerator();

        $ecf = new class extends EcfDocumento {
            public $encf = 'E31123456789';
            public $monto_total = '1000.00';
            public $fecha_emision;

            public function __construct()
            {
                $this->fecha_emision = \Carbon\Carbon::parse('2026-07-15');
            }
        };

        $code1 = $generator->generarCodigoSeguridad($ecf);
        $code2 = $generator->generarCodigoSeguridad($ecf);

        $this->assertEquals($code1, $code2);
        $this->assertEquals(6, strlen($code1));
        $this->assertRegExp('/^[A-F0-9]{6}$/', $code1);
    }

    public function test_qr_api_url_format(): void
    {
        Config::set('dgii.qr_endpoint', 'https://dgii.gov.do/app/WebApps/ConsultasWeb/ConsultasWeb/consulta');

        $generator = new EcfQrGenerator();

        $ecf = new class extends EcfDocumento {
            public $encf = 'E31123456789';
            public $monto_total = '15000.00';
            public $fecha_emision;
            public $codigo_seguridad = 'ABC123';

            public function __construct()
            {
                $this->fecha_emision = \Carbon\Carbon::parse('2026-07-15 10:30:00');
            }
        };

        $url = $generator->toQrApiUrl($ecf, 200);

        $this->assertStringStartsWith('https://api.qrserver.com/v1/create-qr-code/', $url);
        $this->assertStringContainsString('size=200x200', $url);
        $this->assertStringContainsString('data=', $url);
    }
}

class InformeDiarioServiceTest extends TestCase
{
    public function test_obtener_resumen_diario_structure(): void
    {
        $service = new InformeDiarioService();
        $resumen = $service->obtenerResumenDiario(\Carbon\Carbon::today());

        $this->assertArrayHasKey('fecha', $resumen);
        $this->assertArrayHasKey('total_aprobados', $resumen);
        $this->assertArrayHasKey('total_monto_aprobados', $resumen);
        $this->assertArrayHasKey('total_rechazados', $resumen);
        $this->assertArrayHasKey('por_tipo', $resumen);
        $this->assertArrayHasKey('informado', $resumen);

        $this->assertIsInt($resumen['total_aprobados']);
        $this->assertIsFloat($resumen['total_monto_aprobados']);
        $this->assertIsBool($resumen['informado']);
    }

    public function test_verificar_informes_pendientes_structure(): void
    {
        $service = new InformeDiarioService();
        $resultado = $service->verificarInformesPendientes();

        $this->assertArrayHasKey('informes_pendientes', $resultado);
        $this->assertArrayHasKey('total_no_informados', $resultado);
        $this->assertIsArray($resultado['informes_pendientes']);
        $this->assertIsInt($resultado['total_no_informados']);
    }
}

class EcfRetryServiceTest extends TestCase
{
    public function test_max_retries_constant(): void
    {
        $this->assertEquals(5, EcfRetryService::MAX_RETRIES);
    }

    public function test_backoff_base_constant(): void
    {
        $this->assertEquals(60, EcfRetryService::BACKOFF_BASE_SECONDS);
    }

    public function test_backoff_multiplier_constant(): void
    {
        $this->assertEquals(2, EcfRetryService::BACKOFF_MULTIPLIER);
    }
}

class EcfArchiveServiceTest extends TestCase
{
    public function test_retention_years_constant(): void
    {
        $this->assertEquals(5, EcfArchiveService::RETENCION_ANIOS);
    }

    public function test_obtener_estadisticas_almacenamiento_structure(): void
    {
        $service = new EcfArchiveService();
        $stats = $service->obtenerEstadisticasAlmacenamiento();

        $this->assertArrayHasKey('total_documentos', $stats);
        $this->assertArrayHasKey('archivados', $stats);
        $this->assertArrayHasKey('no_archivados', $stats);
        $this->assertArrayHasKey('tamano_estimado_bytes', $stats);
        $this->assertArrayHasKey('tamano_estimado_mb', $stats);
        $this->assertArrayHasKey('por_anio', $stats);
        $this->assertArrayHasKey('proximo_archivado_sugerido', $stats);

        $this->assertIsInt($stats['total_documentos']);
        $this->assertIsInt($stats['archivados']);
        $this->assertIsInt($stats['no_archivados']);
        $this->assertIsFloat($stats['tamano_estimado_mb']);
        $this->assertIsString($stats['proximo_archivado_sugerido']);
    }
}

class SecuenciaEcfTest extends TestCase
{
    public function test_tipos_const_contains_all_dgii_types(): void
    {
        $tipos = SecuenciaEcf::TIPOS;

        $this->assertArrayHasKey('E31', $tipos);
        $this->assertArrayHasKey('E32', $tipos);
        $this->assertArrayHasKey('E33', $tipos);
        $this->assertArrayHasKey('E34', $tipos);
        $this->assertArrayHasKey('E41', $tipos);
        $this->assertArrayHasKey('E43', $tipos);
        $this->assertArrayHasKey('E44', $tipos);
        $this->assertArrayHasKey('E45', $tipos);
        $this->assertArrayHasKey('E46', $tipos);
        $this->assertArrayHasKey('E47', $tipos);

        $this->assertCount(10, $tipos);
    }

    public function test_tipos_para_cliente_mapping(): void
    {
        $this->assertEquals('E31', SecuenciaEcf::tiposParaCliente('credito_fiscal'));
        $this->assertEquals('E45', SecuenciaEcf::tiposParaCliente('gubernamental'));
        $this->assertEquals('E31', SecuenciaEcf::tiposParaCliente('especial'));
        $this->assertEquals('E44', SecuenciaEcf::tiposParaCliente('zona_franc'));
        $this->assertEquals('E32', SecuenciaEcf::tiposParaCliente('consumo'));
        $this->assertEquals('E32', SecuenciaEcf::tiposParaCliente('desconocido'));
    }

    public function test_disponibles_returns_correct_count(): void
    {
        $secuencia = new SecuenciaEcf();
        $secuencia->actual = 50;
        $secuencia->hasta = 100;

        $this->assertEquals(50, $secuencia->disponibles());
    }

    public function test_porcentaje_uso_calculation(): void
    {
        $secuencia = new SecuenciaEcf();
        $secuencia->actual = 50;
        $secuencia->hasta = 100;

        $this->assertEquals(50.0, $secuencia->porcentajeUso());
    }

    public function test_agotada_returns_true_when_actual_equals_hasta(): void
    {
        $secuencia = new SecuenciaEcf();
        $secuencia->actual = 100;
        $secuencia->hasta = 100;

        $this->assertTrue($secuencia->agotada());
    }

    public function test_agotada_returns_false_when_actual_less_than_hasta(): void
    {
        $secuencia = new SecuenciaEcf();
        $secuencia->actual = 50;
        $secuencia->hasta = 100;

        $this->assertFalse($secuencia->agotada());
    }

    public function test_disponible_para_uso_requires_all_conditions(): void
    {
        $secuencia = new SecuenciaEcf();
        $secuencia->activo = true;
        $secuencia->fecha_vencimiento = \Carbon\Carbon::now()->addDays(30);
        $secuencia->actual = 50;
        $secuencia->hasta = 100;

        $this->assertTrue($secuencia->disponibleParaUso());
    }

    public function test_disponible_para_uso_false_when_inactive(): void
    {
        $secuencia = new SecuenciaEcf();
        $secuencia->activo = false;
        $secuencia->fecha_vencimiento = \Carbon\Carbon::now()->addDays(30);
        $secuencia->actual = 50;
        $secuencia->hasta = 100;

        $this->assertFalse($secuencia->disponibleParaUso());
    }

    public function test_disponible_para_uso_false_when_expired(): void
    {
        $secuencia = new SecuenciaEcf();
        $secuencia->activo = true;
        $secuencia->fecha_vencimiento = \Carbon\Carbon::now()->subDays(1);
        $secuencia->actual = 50;
        $secuencia->hasta = 100;

        $this->assertFalse($secuencia->disponibleParaUso());
    }

    public function test_vencida_returns_true_for_past_date(): void
    {
        $secuencia = new SecuenciaEcf();
        $secuencia->fecha_vencimiento = \Carbon\Carbon::yesterday();
        $this->assertTrue($secuencia->vencida());
    }

    public function test_vencida_returns_false_for_future_date(): void
    {
        $secuencia = new SecuenciaEcf();
        $secuencia->fecha_vencimiento = \Carbon\Carbon::tomorrow();
        $this->assertFalse($secuencia->vencida());
    }
}

class HasEcfStateMachineTest extends TestCase
{
    public function test_allowed_transitions_are_valid(): void
    {
        $transitions = [
            'borrador' => ['generado', 'firmado'],
            'generado' => ['firmado'],
            'firmado' => ['enviado'],
            'enviado' => ['aprobado', 'rechazado'],
            'aprobado' => ['anulado'],
            'rechazado' => ['firmado', 'enviado'],
            'anulado' => [],
            'expirado' => [],
        ];

        $this->assertEquals(HasEcfStateMachine::$allowedTransitions, $transitions);
    }

    public function test_terminal_states_defined(): void
    {
        $this->assertEquals(['anulado', 'expirado'], HasEcfStateMachine::$terminalStates);
    }

    public function test_pending_states_defined(): void
    {
        $this->assertEquals(['borrador', 'generado', 'firmado', 'enviado'], HasEcfStateMachine::$pendingStates);
    }
}
