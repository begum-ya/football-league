<?php
namespace App\Services;

use App\Models\Game;
use App\Models\Team;
use App\Models\Week;
use App\Models\Standing;
use Illuminate\Support\Facades\DB;

class SimulationService
{
    protected StandingService $standingService;

    public function __construct(StandingService $standingService)
    {
        $this->standingService = $standingService;
    }

    /**
    * Play a single week.
    */
    public function playWeek(int $week_id): array
    {
        $week = Week::with('games')->find($week_id);
        if (!$week) {
            return ['success' => false, 'message' => 'Week not found'];
        }

        $alreadyPlayed = $week->games->every(fn($game) => $game->played);

        if ($alreadyPlayed) {
            return [
                'success' => false,
                'message' => "Week $week_id has already been played."
            ];
        }
        $this->simulateMatches($week->games);
        return ['success' => true, 'message' => "Week $week_id played successfully"];
    }

    
    /**
    * Play all remaining weeks
    */
    public function playAllWeeks(): array
    {
        try {
            $weeks = Week::with('games')
                ->whereHas('games', function ($query) {
                    $query->where('played', false);
                })
                ->orderBy('id')
                ->get();
    
            if ($weeks->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'All weeks have already been played.'
                ];
            }
    
            foreach ($weeks as $week) {
                $this->simulateMatches($week->games);
                $week->games->each(fn($game) => $game->update(['played' => true])); 
            }
    
            return [
                'success' => true,
                'message' => 'All remaining weeks played successfully'
            ];
        } catch (\Exception $e) {
    
            return [
                'success' => false,
                'message' => 'An error occurred while playing all weeks.'
            ];
        }

    }

    /**
    * Simulate matches using team power factors.
    */
    private function simulateMatches($games)
    {
        foreach ($games as $game) {
            $homeTeam = Team::find($game->home_team_id);
            $awayTeam = Team::find($game->away_team_id);

            if (!$homeTeam || !$awayTeam) continue;

            $homePower = $homeTeam->power;
            $awayPower = $awayTeam->power;

            $homeAdvantage = 1.1;
            $goalkeeperFactor = 0.9;
            $randomFactor = rand(85, 115) / 100;

            if($homePower > $awayPower){
                $homeScore = round(($homePower * $homeAdvantage) / 10);
                $awayScore = round(($awayPower * $goalkeeperFactor * $randomFactor) / 10);
            }else if($homePower < $awayPower){
                $homeScore = round(($homePower * $homeAdvantage * $randomFactor) / 10);
                $awayScore = round(($awayPower * $goalkeeperFactor) / 10);
            }else{
                $homeScore = round(($homePower * $homeAdvantage ) / 10);
                $awayScore = round(($awayPower * $goalkeeperFactor) / 10);
            }
           

            $game->update([
                'home_score' => $homeScore,
                'away_score' => $awayScore,
                'played' => true
            ]);

            $this->standingService->updateStandings($homeTeam->id, $homeScore, $awayScore);
            $this->standingService->updateStandings($awayTeam->id, $awayScore, $homeScore);
        }
    }

    /**
    * Reset all league data except teams
    */
    public function resetData(): array
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Standing::query()->update([
            'won' => 0, 'lose' => 0, 'draw' => 0, 'points' => 0,
            'goal_difference' => 0, 'goals_scored' => 0, 'goals_conceded' => 0
        ]);

        Game::truncate();
        Week::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return ['success' => true, 'message' => 'League reset successfully.'];
    }

     


}
