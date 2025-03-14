<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Team;
use App\Models\Week;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class FixtureService
{
    /**
     * Fetch fixtures for the league
     */
    public function getFixtures(): Collection
    {
       
       return Week::with([
            'games' => function ($query) {
                $query->with(['homeTeam', 'awayTeam']);
            }
        ])
        ->orderBy('week')
        ->get();
    }

    /**
     * Generate fixtures for the league.
     */
    public function generateFixtures(): array
    {
        $teams = Team::all();
    
        if ($teams->count() < 2) {
            return ['success' => false, 'message' => 'Not enough teams to generate fixtures'];
        }
    
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Game::truncate();
        Week::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    
        DB::beginTransaction();
    
        try {
                $matchups = $this->generateMatchups($teams->shuffle()->values());
                $weeks = [];
                foreach ($matchups as $match) {

                    if (!isset($weeks[$match['week']])) {
                        $weeks[$match['week']] = Week::create(['week' => $match['week']]);
                    }
                    Game::create([
                        'home_team_id' => $match['home'],
                        'away_team_id' => $match['away'],
                        'week_id' => $match['week'],
                        'played' => false
                    ]);
                }
    
            DB::commit();
            return ['success' => true, 'message' => 'Fixtures generated successfully'];

        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => 'Error generating fixtures: ' . $e->getMessage()];
        }
    }
    
    /**
     * Generate matchups for a week.
    */
    private function generateMatchups($teams)
    {
        $matchups = [];
    
        if ($teams->count() % 2 !== 0) {
            $teams->push((object) ['id' => null, 'name' => 'BYE']);
        }
    
        $totalTeams = $teams->count();
        $totalRounds = $totalTeams - 1; 
        $halfSize = $totalTeams / 2;
    
        $teamList = $teams->slice(1)->values();
        $fixedTeam = $teams->first(); 
    
        $roundRobinMatches = [];
    
        for ($round = 1; $round <= $totalRounds; $round++) {
            $weekMatchups = [];
    
            $homeTeam = ($round % 2 === 0) ? $fixedTeam : $teamList[$halfSize - 1];
            $awayTeam = ($round % 2 === 0) ? $teamList[$halfSize - 1] : $fixedTeam;
    
            if ($homeTeam->id !== null && $awayTeam->id !== null) {
       
                $weekMatchups[] = [
                    'home' => $homeTeam->id,
                    'away' => $awayTeam->id,
                    'week' => $round
                ];
            }
    
            // Remaining matches
            for ($i = 0; $i < $halfSize - 1; $i++) {
                $teamA = $teamList[$i];
                $teamB = $teamList[$totalTeams - 2 - $i];
    
                if ($teamA->id !== null && $teamB->id !== null) {
                    $weekMatchups[] = [
                        'home' => $teamA->id,
                        'away' => $teamB->id,
                        'week' => $round
                    ];
                }
            }
    
            // Store the first round matchups
            $roundRobinMatches[] = $weekMatchups;
    
            // Rotate teams (except the fixed team)
            $teamList->push($teamList->shift());
        }
    
        // Second round: Reverse home and away matches, assign correct week numbers
        foreach ($roundRobinMatches as $index => $weekMatchups) {
            $newWeek = $index + 1 + $totalRounds; 
            $reversedMatchups = [];
    
            foreach ($weekMatchups as $match) {
                $reversedMatchups[] = [
                    'home' => $match['away'], 
                    'away' => $match['home'],
                    'week' => $newWeek 
                ];
            }
    
            // Add both rounds to final matchups
            $matchups = array_merge($matchups, $weekMatchups, $reversedMatchups);
        }
    
        return $matchups;
    }
    
    
    /**
    * Get the current match week.
    */
    public function getCurrentWeek(): array
    {
        $currentWeek = Game::where('played', false)
        ->orderBy('week_id')
        ->first();
        
        
        if (!$currentWeek) {
            return [
                'success' => false,
                'message' => 'No Weeks Remaining'
            ];
        }

        return [
            'success' => true,
            'current_week' => $currentWeek ? $currentWeek->week_id : null,
            'week_title' => $currentWeek ? "Week " . $currentWeek->week_id : 'No Weeks Remaining'
        ];
    }
}
