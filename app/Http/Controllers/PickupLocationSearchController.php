<?php

namespace App\Http\Controllers;

use App\Models\PickupLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PickupLocationSearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $q = trim($request->get('q', ''));

        if ($q === '') {
            return response()->json([]);
        }

        // Split into terms so "Whole Foods Destin" matches name="Whole Foods" city="Destin"
        $terms = array_filter(explode(' ', $q));

        $locations = PickupLocation::where('active', true)
            ->where(function ($query) use ($terms) {
                foreach ($terms as $term) {
                    $query->where(function ($q) use ($term) {
                        $q->where('name', 'like', "%{$term}%")
                          ->orWhere('city', 'like', "%{$term}%")
                          ->orWhere('address', 'like', "%{$term}%");
                    });
                }
            })
            ->orderBy('name')
            ->orderBy('city')
            ->limit(10)
            ->get(['id', 'name', 'address', 'city', 'state', 'zip']);

        return response()->json($locations);
    }
}
