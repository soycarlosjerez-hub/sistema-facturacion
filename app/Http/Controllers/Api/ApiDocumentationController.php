<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ApiDocumentationController extends Controller
{
    public function index()
    {
        $routes = collect(Route::getRoutes())->filter(function ($route) {
            return $route->named('api.*') || str_starts_with($route->uri(), 'api/');
        })->map(function ($route) {
            return [
                'method' => strtoupper($route->methods()[0]),
                'uri' => preg_replace('/^api\//', '', $route->uri()),
                'name' => $route->getName(),
            ];
        })->sortBy('uri')->values();

        return view('api.documentation', compact('routes'));
    }
}
