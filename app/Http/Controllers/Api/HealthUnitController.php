<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HealthUnitResource;
use App\Models\HealthUnit;

class HealthUnitController extends Controller
{
    public function index()
    {
        $healthUnits = HealthUnit::orderBy('name')->get();

        return HealthUnitResource::collection($healthUnits);
    }
}