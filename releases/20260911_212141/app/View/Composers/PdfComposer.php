<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PdfComposer
{
    public function compose(View $view): void
    {
        $logoUrl = null;
        $user = Auth::user();

        if ($user && $user->businessInstance) {
            $logoUrl = $user->businessInstance->logo_url;
        }

        $view->with('pdfLogoUrl', $logoUrl);
    }
}
