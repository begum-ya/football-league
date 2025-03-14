<?php

namespace App\Http\Controllers;

use App\Services\FixtureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FixtureController extends Controller
{
    protected FixtureService $fixtureService;

    public function __construct(FixtureService $fixtureService)
    {
        $this->fixtureService = $fixtureService;
    }

    public function index(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $this->fixtureService->getFixtures()], 200);
    }

    public function generateFixtures(): JsonResponse
    {
        $result = $this->fixtureService->generateFixtures();
        return response()->json($result, $result['success'] ? 201 : 400);
    }
       
    public function getCurrentWeek(): JsonResponse
    {
        return response()->json($this->fixtureService->getCurrentWeek(), 200);
    }
}
