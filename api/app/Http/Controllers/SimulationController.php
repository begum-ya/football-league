<?php
namespace App\Http\Controllers;

use App\Http\Requests\PlayWeekRequest;
use App\Services\SimulationService;
use Illuminate\Http\JsonResponse;

class SimulationController extends Controller
{
    protected SimulationService $simulationService;

    public function __construct(SimulationService $simulationService)
    {
        $this->simulationService = $simulationService;
    }

    public function playWeek(PlayWeekRequest $request): JsonResponse
    {
        $weekId = $request->input('week_id');
        $result = $this->simulationService->playWeek($weekId);
        return response()->json($result, $result['success'] ? 200 : 404);
    }

    public function playAllWeeks(): JsonResponse
    {
        $result = $this->simulationService->playAllWeeks();
        return response()->json($result, $result['success'] ? 200 : 404);
    }

    public function resetData(): JsonResponse
    {
        return response()->json($this->simulationService->resetData(), 200);
    }
}
