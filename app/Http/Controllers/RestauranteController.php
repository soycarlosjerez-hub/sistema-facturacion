<?php

namespace App\Http\Controllers;

use App\Services\RestaurantOrderService;

class RestauranteController extends Controller
{
    public function __construct(
        protected RestaurantOrderService $orderService
    ) {}

    public function index()
    {
        return view('restaurante.index', $this->orderService->getIndexData());
    }
}
