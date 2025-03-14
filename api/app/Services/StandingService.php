<?php
namespace App\Services;

use App\Models\Standing;
use App\Models\Week;

class StandingService
{
    /**
    * Fetch standings sorted by points.
    */
    public function getStandings()
    {
        return Standing::with('team')->orderByDesc('points')->orderByDesc('goal_difference')->get();
    }

    /**
    * Update standings based on match results.
    */
    public function updateStandings(int $team_id, int $team_score, int $opponent_score): void
    {
        $standing = Standing::firstOrCreate(['team_id' => $team_id]);

        if ($team_score > $opponent_score) {
            $standing->increment('won');
            $standing->increment('points', 3);
        } elseif ($team_score == $opponent_score) {
            $standing->increment('draw');
            $standing->increment('points');
        } else {
            $standing->increment('lose');
        }

        $standing->increment('goals_scored', $team_score);
        $standing->increment('goals_conceded', $opponent_score);
        $standing->goal_difference = $standing->goals_scored - $standing->goals_conceded;
        $standing->save();
    }

    /**
    * Predict championship chances.
    */
    public function getChampionshipPredictions(): array
    {
        $standings = Standing::with('team')->orderByDesc('points')->get();
        $remainingMatches = Week::whereHas('games', function ($query) {
            $query->where('played', false);
        })->count();

        if ($remainingMatches <= 3) {
            return [
                'success' => true,
                'message' => 'Calculate championship probability successfully',
                'data'=>
                $this->calculateChampionshipProbability($standings, $remainingMatches)
            ];
        }

        return ['success' => false, 'message' => 'Prediction available in last 3 weeks only.'];
    }

    /**
    * Calculate championship probability based on various factors.
    */
    private function calculateChampionshipProbability($standings, $remainingMatches)
    {
        $maxPoints = $standings->max('points') ?: 1; // Get the highest points
        $topTeams = $standings->where('points', $maxPoints);
    
        $totalTopTeams = $topTeams->count();

        // If the season has ended 
        if ($remainingMatches === 0 ) {
        
            //one team has the highest points
            if($totalTopTeams === 1){
                return $standings->map(fn($team) => [
                    'team' => $team->team->name,
                    'championship_probability' => in_array($team->team_id, $topTeams->pluck('team_id')->toArray()) ? 100 : 0
                ])->toArray();
            }
            
            // multiple teams have the highest points, check goal difference
            $maxGoalDifference = $topTeams->max('goal_difference');
            $topTeams = $topTeams->where('goal_difference', $maxGoalDifference);

            if(count($topTeams) === 1){
                return $standings->map(fn($team) => [
                    'team' => $team->team->name,
                    'championship_probability' => in_array($team->team_id, $topTeams->pluck('team_id')->toArray()) ? 100 : 0
                ])->toArray();
            }
            // multiple teams have the highest points, check goal scores.
            $maxGoalScore = $topTeams->max('goals_scored');
            $topTeams = $topTeams->where('goals_scored', $maxGoalScore);

            if(count($topTeams) === 1){
                return $standings->map(fn($team) => [
                    'team' => $team->team->name,
                    'championship_probability' => in_array($team->team_id, $topTeams->pluck('team_id')->toArray()) ? 100 : 0
                ])->toArray();
            }
            // multiple teams have the highest points, check goal conceded.
            $minGoalConceded = $topTeams->min('goals_conceded');
            $topTeams = $topTeams->where('goals_conceded', $minGoalConceded);
    
            if(count($topTeams) === 1){
                return $standings->map(fn($team) => [
                    'team' => $team->team->name,
                    'championship_probability' => in_array($team->team_id, $topTeams->pluck('team_id')->toArray()) ? 100 : 0
                ])->toArray();
            }

            if ($topTeams->count() > 1) {
                return $standings->map(fn($team) => [
                    'team' => $team->team->name,
                    'championship_probability' => in_array($team->team_id, $topTeams->pluck('team_id')->toArray()) ? round(100/count($topTeams),2) : 0
                ])->toArray();
            }
        }


    // If there is 1 week left and more than one team or one team is ahead by more than 4 points
        if ($remainingMatches === 1) {
        
            $secondHighestPoints = $standings->where('points', '<', $maxPoints)->max('points') ?? 0;

            $leadingTeams = $standings->filter(fn($team) => $team->points - $secondHighestPoints >= 4);
            $totalLeadingTeams = $leadingTeams->count();

            if ($totalLeadingTeams > 0) {
                return $standings->map(fn($team) => [
                    'team' => $team->team->name,
                    'championship_probability' => $leadingTeams->contains($team) ? round(100 / $totalLeadingTeams, 2) : 0
                ])->toArray();
            }
            $teamsWithNoChance = $standings->filter(fn($team) => $maxPoints - $team->points >= 4);

            return $standings->map(fn($team) => [
                'team' => $team->team->name,
                'championship_probability' =>  $teamsWithNoChance->contains($team) ? 0 : round(min(($team->points / ($maxPoints + ($remainingMatches * 3))) * 100, 100), 2)
            ])->toArray();

        }

        // If there is 1 week left and 2 teams are tied for the lead, and they play against each other in the last match, assign 60%-40% probability.
        if ($remainingMatches === 1 && $totalTopTeams === 2) {
            $finalMatch = Week::whereHas('games', function ($query) use ($topTeams) {
                $query->whereIn('home_team_id', $topTeams->pluck('team_id'))
                    ->whereIn('away_team_id', $topTeams->pluck('team_id'))
                    ->where('played', false);
            })->exists();

            if ($finalMatch) {
                return $standings->map(fn($team) => [
                    'team' => $team->team->name,
                    'championship_probability' => in_array($team->team_id, $topTeams->pluck('team_id')->toArray()) ? ($team->goals_scored > $topTeams->first()->goals_scored ? 60 : 40) : 0
                ])->toArray();
            }
        }

        // If there are 2 weeks left and more than one team or one team is ahead by 7+ points
        if ($remainingMatches <= 2) {
            
            $secondHighestPoints = $standings->where('points', '<', $maxPoints)->max('points') ?? 0;
            $leadingTeams = $standings->filter(fn($team) => $team->points - $secondHighestPoints >= 7);
            $totalLeadingTeams = $leadingTeams->count();

            if ($totalLeadingTeams > 0) {
                return $standings->map(fn($team) => [
                    'team' => $team->team->name,
                    'championship_probability' => $leadingTeams->contains($team) ? round(100 / $totalLeadingTeams, 2) : 0
                ])->toArray();
            }

            $teamsWithNoChance = $standings->filter(fn($team) => $maxPoints - $team->points >= 7);

            return $standings->map(fn($team) => [
                'team' => $team->team->name,
                'championship_probability' =>  $teamsWithNoChance->contains($team) ? 0 : round(min(($team->points / ($maxPoints + ($remainingMatches * 3))) * 100, 100), 2)
            ])->toArray();

        }

        // Default case
        return $standings->map(fn($team) => [
            'team' => $team->team->name,
            'championship_probability' => round(min(($team->points / ($maxPoints + ($remainingMatches * 3))) * 100, 100), 2)
        ])->toArray();
    }

}
