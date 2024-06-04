<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function itemBasedTrain(Request $request, RecommendationService $recommendationService)
    {
        return $recommendationService->itemBasedTrain($request->input('ids'));
    }

    public function itemContentTrain(Request $request, RecommendationService $recommendationService)
    {
        return $recommendationService->itemContentTrain($request->input('ids'));
    }

    public function datasets(Request $request, RecommendationService $recommendationService)
    {
        return $recommendationService->datasets($request->input('model'))->appends($request->query());
    }
}
