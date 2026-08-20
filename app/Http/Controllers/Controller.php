<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function dtSearch(Request $request): string
    {
        $search = $request->input('search.value') ?? $request->input('search');

        if (is_array($search)) {
            $search = $search['value'] ?? '';
        }

        return trim((string) $search);
    }
}
