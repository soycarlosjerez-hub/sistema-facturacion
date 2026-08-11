<?php

namespace App\Http\Controllers;

use App\Models\ArteObra;
use App\Models\ArteArtista;
use App\Models\ArteColeccion;
use App\Models\ArteExhibicion;
use App\Models\ArteConsignment;
use Illuminate\Http\Request;

class ArteController extends Controller
{
    public function index()
    {
        $totalObras   = ArteObra::count();
        $disponibles  = ArteObra::disponibles()->count();
        $vendidas     = ArteObra::vendidas()->count();
        $enExhibicion = ArteObra::enExhibicion()->count();

        $totalArtistas = ArteArtista::count();
        $totalColecciones = ArteColeccion::count();
        $exhibicionesActivas = ArteExhibicion::activas()->count();
        $consignacionesActivas = ArteConsignment::activas()->count();

        $valorInventario = ArteObra::whereIn('estado', ['disponible', 'en_consulta'])->sum('precio_venta');
        $ultimasObras = ArteObra::with('artista')->orderByDesc('created_at')->limit(8)->get();

        return view('arte.index', compact(
            'totalObras', 'disponibles', 'vendidas', 'enExhibicion',
            'totalArtistas', 'totalColecciones', 'exhibicionesActivas', 'consignacionesActivas',
            'valorInventario', 'ultimasObras'
        ));
    }
}