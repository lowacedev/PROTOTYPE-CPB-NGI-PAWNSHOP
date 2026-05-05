<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\City;
use App\Models\Barangay;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    /**
     * Get provinces for a given region (AJAX)
     */
    public function provinces($regionId): JsonResponse
    {
        $provinces = Province::where('region_id', $regionId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($provinces);
    }

    /**
     * Get cities for a given province (AJAX)
     */
    public function cities($provinceId): JsonResponse
    {
        $cities = City::where('province_id', $provinceId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($cities);
    }

    /**
     * Get barangays for a given city (AJAX)
     */
    public function barangays($cityId): JsonResponse
    {
        $barangays = Barangay::where('city_id', $cityId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($barangays);
    }
}
