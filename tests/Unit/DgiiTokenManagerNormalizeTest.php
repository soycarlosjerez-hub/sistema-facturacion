<?php

namespace Tests\Unit;

use App\Services\Ecf\DgiiTokenManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class DgiiTokenManagerNormalizeTest extends TestCase
{
    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }

    public function test_normaliza_alias_prod_a_produccion(): void
    {
        $this->assertSame('produccion', DgiiTokenManager::normalizarAmbiente('prod'));
        $this->assertSame('produccion', DgiiTokenManager::normalizarAmbiente('PROD'));
        $this->assertSame('produccion', DgiiTokenManager::normalizarAmbiente('produccion'));
        $this->assertSame('qa', DgiiTokenManager::normalizarAmbiente('qa'));
        $this->assertSame('sandbox', DgiiTokenManager::normalizarAmbiente('sandbox'));
        $this->assertSame('sandbox', DgiiTokenManager::normalizarAmbiente('desconocido'));
    }

    public function test_no_cachea_nulls_ante_fallo_mtls(): void
    {
        Config::set('dgii.ambiente', 'qa');
        Config::set('dgii.ambientes.qa', [
            'api_url' => 'https://ecf-qa.dgii.gov.do/api/v1',
            'cert_required' => true,
        ]);
        Config::set('dgii.certificates.qa', [
            'client_cert_path' => '/inexistente/cert.crt',
            'client_key_path' => '/inexistente/key.key',
            'client_key_pass' => '',
        ]);

        $manager = new DgiiTokenManager;

        $this->assertNull($manager->getToken('qa'));
        // Sin envenenar el caché: sigue null y el caché sigue vacío.
        $this->assertNull(Cache::get('dgii_token_'.md5('qa')));
        $this->assertNull($manager->getToken('qa'));
    }

    public function test_acepta_alias_prod_como_ambiente_valido(): void
    {
        Config::set('dgii.ambientes.produccion', [
            'api_url' => 'https://ecf.dgii.gov.do/api/v1',
            'cert_required' => true,
        ]);
        Config::set('dgii.certificates.produccion', [
            'client_cert_path' => '/inexistente/cert.crt',
            'client_key_path' => '/inexistente/key.key',
            'client_key_pass' => '',
        ]);

        $manager = new DgiiTokenManager;

        // 'prod' se normaliza a 'produccion' (antes retornaba null siempre).
        $this->assertNull($manager->getToken('prod'));
    }
}
