<?php
namespace App\Http\Controllers;

use App\Services\StandingService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UpdateStandingDataRequest;

class StandingController extends Controller
{
    protected StandingService $standingService;

    public function __construct(StandingService $standingService)
    {
        $this->standingService = $standingService;
    }

    public function index(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $this->standingService->getStandings()], 200);
    }

    public function updateStandings(UpdateStandingDataRequest $request): JsonResponse
    {
        $this->standingService->updateStandings($request->team_id, $request->team_score, $request->opponent_score);

        return response()->json(['success' => true, 'message' => 'Standings updated successfully'], 200);
    }

    public function getChampionshipPredictions(): JsonResponse
    {
        $predictions = $this->standingService->getChampionshipPredictions();
        return response()->json($predictions, 200);
    }
}
