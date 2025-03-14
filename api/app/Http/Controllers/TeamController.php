<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTeamRequest;
use App\Services\TeamService;
use Illuminate\Http\JsonResponse;

class TeamController extends Controller
{
    protected TeamService $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function index(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $this->teamService->getTeams()], 200);
    }

    public function store(CreateTeamRequest $request): JsonResponse
    {
        $team = $this->teamService->createTeam($request->validated());
        return response()->json(['success' => true, 'message' => 'Team created successfully', 'data' => $team], 201);
    }
}
