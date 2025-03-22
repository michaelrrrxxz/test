<?php
// app/Http/Controllers/LocationController.php
namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    public function index(): JsonResponse
    {
        $regions = Region::with('provinces.cities')->get();
        return response()->json($regions);
    }
    

    // public function index(): JsonResponse
    // {
    //     $path = public_path('js/regions.json');

    //     $jsonContents = File::get($path);
    //     $regions = json_decode($jsonContents, true);

    //     return response()->json($regions);
    // }
}