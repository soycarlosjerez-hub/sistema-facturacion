<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Symfony\Component\Mime\MimeTypes;

class ValidImage implements Rule
{
    protected int $maxSize = 10240;

    public function maxSize(int $maxSize): static
    {
        $this->maxSize = $maxSize;
        return $this;
    }

    public function validate(string $attribute, mixed $value, mixed $fail): void
    {
        if (! $value || ! $value->isValid()) {
            $fail("El campo {$attribute} debe ser un archivo de imagen válido.");
            return;
        }

        // Real MIME type verification (not just extension)
        $mimeType = $value->getMimeType();
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        
        if (! in_array($mimeType, $allowedTypes)) {
            $fail("El tipo MIME del archivo '{$value->getClientOriginalName()}' no está permitido. Solo se permiten JPEG, PNG, JPG, GIF y WebP.");
            return;
        }

        if ($value->getSize() > $this->maxSize * 1024) {
            $fail("El archivo no debe superar los {$this->maxSize}MB.");
        }
    }
}
