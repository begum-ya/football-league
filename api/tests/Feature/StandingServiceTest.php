<?php

namespace Tests\Unit;

use App\Models\Standing;
use App\Models\Team;
use App\Models\Week;
use App\Models\Game;
use App\Services\StandingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class StandingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StandingService $standingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->standingService = new StandingService();
    }

    #[Test]
    public function it_fetches_standings_correctly()
    {
      
        $team1 = Team::factory()->create(['name' => 'Team A']);
        $team2 = Team::factory()->create(['name' => 'Team B']);

        Standing::factory()->create(['team_id' => $team1->id, 'points' => 10, 'goal_difference' => 5]);
        Standing::factory()->create(['team_id' => $team2->id, 'points' => 12, 'goal_difference' => 7]);

        $standings = $this->standingService->getStandings();

       
        $this->assertCount(2, $standings);
        $this->assertEquals('Team B', $standings->first()->team->name);
    }

    #[Test]
    public function it_updates_standings_after_a_match()
    {
        
        $team = Team::factory()->create();

        $this->assertDatabaseMissing('standings', ['team_id' => $team->id]);

        $this->standingService->updateStandings($team->id, 3, 1);

        $standing = Standing::where('team_id', $team->id)->first();


        $this->assertNotNull($standing);
        $this->assertEquals(1, $standing->won);
        $this->assertEquals(3, $standing->points);
        $this->assertEquals(3, $standing->goals_scored);
        $this->assertEquals(1, $standing->goals_conceded);
        $this->assertEquals(2, $standing->goal_difference);
    }

    #[Test]
    public function it_correctly_predicts_championship_possibilities()
    {

        $team1 = Team::factory()->create(['name' => 'Team A']);
        $team2 = Team::factory()->create(['name' => 'Team B']);

        Standing::factory()->create(['team_id' => $team1->id, 'points' => 15, 'goal_difference' => 8]);
        Standing::factory()->create(['team_id' => $team2->id, 'points' => 12, 'goal_difference' => 5]);

        Week::factory()->count(3)->create();

        $predictions = $this->standingService->getChampionshipPredictions();


        $this->assertTrue($predictions['success']);
        $this->assertEquals('Calculate championship probability successfully', $predictions['message']);
        $this->assertNotEmpty($predictions['data']);
    }

    #[Test]
    public function it_returns_no_prediction_if_more_than_3_weeks_remain()
    {
        
        $weeks = Week::factory()->count(4)->create();

        foreach ($weeks as $week) {
            Game::factory()->create([
                'week_id' => $week->id,
                'played' => false,
            ]);
        }
    
        $predictions = $this->standingService->getChampionshipPredictions();
     
        $this->assertFalse($predictions['success']);
        $this->assertEquals('Prediction available in last 3 weeks only.', $predictions['message']);
    }
}
