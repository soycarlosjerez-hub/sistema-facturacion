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
}
