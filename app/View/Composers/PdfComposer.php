<?php

namespace App\View\Composers;

use App\Models\BusinessInstance;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

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
