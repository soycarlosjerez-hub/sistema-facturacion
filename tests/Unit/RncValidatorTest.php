<?php

namespace Tests\Unit;

use App\Support\RncValidator;
use PHPUnit\Framework\TestCase;

class RncValidatorTest extends TestCase
{
    // ──────────── tipoDocumentoDgii ────────────

    public function test_tipo_documento_dgii_returns_1_for_rnc(): void
    {
        $this->assertSame('1', RncValidator::tipoDocumentoDgii('rnc'));
        $this->assertSame('1', RncValidator::tipoDocumentoDgii('RNC'));
        $this->assertSame('1', RncValidator::tipoDocumentoDgii('1'));
    }

    public function test_tipo_documento_dgii_returns_2_for_cedula(): void
    {
        $this->assertSame('2', RncValidator::tipoDocumentoDgii('cedula'));
        $this->assertSame('2', RncValidator::tipoDocumentoDgii('cédula'));
        $this->assertSame('2', RncValidator::tipoDocumentoDgii('2'));
    }

    public function test_tipo_documento_dgii_returns_3_for_pasaporte(): void
    {
        $this->assertSame('3', RncValidator::tipoDocumentoDgii('pasaporte'));
        $this->assertSame('3', RncValidator::tipoDocumentoDgii('Pasaporte'));
        $this->assertSame('3', RncValidator::tipoDocumentoDgii('3'));
    }

    public function test_tipo_documento_dgii_defaults_to_2(): void
    {
        $this->assertSame('2', RncValidator::tipoDocumentoDgii(null));
        $this->assertSame('2', RncValidator::tipoDocumentoDgii(''));
        $this->assertSame('2', RncValidator::tipoDocumentoDgii('unknown'));
    }

    // ──────────── formato ────────────

    public function test_formato_9_digit_rnc(): void
    {
        $this->assertSame('123-45678-9', RncValidator::formato('123456789'));
    }

    public function test_formato_11_digit_cedula(): void
    {
        $this->assertSame('001-0000000-0', RncValidator::formato('00100000000'));
    }

    public function test_formato_strips_dashes_already_present(): void
    {
        $this->assertSame('001-0000000-0', RncValidator::formato('001-0000000-0'));
    }

    public function test_formato_strips_non_alphanumeric(): void
    {
        $this->assertSame('123-45678-9', RncValidator::formato('123-45678-9 ABC'));
    }

    public function test_formato_returns_empty_for_null_or_empty(): void
    {
        $this->assertSame('', RncValidator::formato(null));
        $this->assertSame('', RncValidator::formato(''));
    }

    public function test_formato_returns_passthrough_for_unexpected_length(): void
    {
        $this->assertSame('12', RncValidator::formato('12'));
        $this->assertSame('1234', RncValidator::formato('AB-1234'));
    }

    public function test_formato_accepts_tipo_parameter(): void
    {
        $this->assertSame('123-45678-9', RncValidator::formato('123456789', 'rnc'));
        $this->assertSame('001-0000000-0', RncValidator::formato('00100000000', 'cedula'));
    }

    // ──────────── validar / inferirTipo ────────────

    public function test_inferir_tipo_por_longitud(): void
    {
        $this->assertSame('rnc', RncValidator::inferirTipo('123456789'));
        $this->assertSame('cedula', RncValidator::inferirTipo('00100000000'));
        $this->assertSame('cedula', RncValidator::inferirTipo('001-0000000-0'));
    }

    public function test_validar_acepta_rnc_9_digitos_valido(): void
    {
        // 101-01453-9 es un RNC con dígito verificador válido (módulo 11 DGII).
        $this->assertTrue(RncValidator::validar('101014539', 'rnc'));
        $this->assertTrue(RncValidator::validar('101-01453-9', 'auto'));
    }

    public function test_validar_rechaza_rnc_con_digito_malo(): void
    {
        $this->assertFalse(RncValidator::validar('101014538', 'rnc'));
    }

    public function test_validar_acepta_cedula_11_digitos_valida(): void
    {
        // Cédula 001-0000000-9: ponderación 1,2,... da verificador 9.
        $this->assertTrue(RncValidator::validar('00100000009', 'cedula'));
        $this->assertTrue(RncValidator::validar('001-0000000-9', 'auto'));
    }

    public function test_validar_rechaza_cedula_con_digito_malo_y_longitudes_cortas(): void
    {
        $this->assertFalse(RncValidator::validar('00100000001', 'cedula'));
        $this->assertFalse(RncValidator::validar('12345678', 'auto'));
        $this->assertFalse(RncValidator::validar('', 'auto'));
    }
}
