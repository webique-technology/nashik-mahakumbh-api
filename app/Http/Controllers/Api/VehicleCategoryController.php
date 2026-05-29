<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VehicleCategory;

class VehicleCategoryController extends Controller
{
    public function index()
    {
        $categories = VehicleCategory::orderBy('id', 'asc')->get();

        return response()->json([
            'status' => true,
            'data' => $categories
        ]);
    }

    
}
