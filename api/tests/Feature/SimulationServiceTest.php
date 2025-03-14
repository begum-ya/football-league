<?php

namespace Tests\Unit;

use App\Models\Game;
use App\Models\Standing;
use App\Models\Team;
use App\Models\Week;
use App\Services\SimulationService;
use App\Services\StandingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;

class SimulationServiceTest extends TestCase
{
    use RefreshDatabase; 
    protected SimulationService $simulationService;
    protected StandingService $standingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->standingService = new StandingService();
        $this->simulationService = new SimulationService($this->standingService);
    }

    #[Test]
    public function it_plays_a_single_week()
    {
        $homeTeam = Team::factory()->create(['power' => 80]);
        $awayTeam = Team::factory()->create(['power' => 75]);
    
        $week = Week::factory()->create();
    
        $game = Game::factory()->create([
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'week_id' => $week->id,
            'played' => false
        ]);

        $result = $this->simulationService->playWeek($week->id);
    

        $this->assertTrue($result['success']);
        $this->assertEquals("Week {$week->id} played successfully", $result['message']);
    
        $game->refresh();

        if (!$game->played) {
            dd($game->toArray());
        }
    

        $this->assertTrue((bool) $game->played);
        $this->assertNotNull($game->home_score);
        $this->assertNotNull($game->away_score);
    }

    #[Test]
    public function it_does_not_play_an_already_played_week()
    {
        $week = Week::factory()->create();
        $game = Game::factory()->create([
            'week_id' => $week->id,
            'played' => true
        ]);

        $result = $this->simulationService->playWeek($week->id);

        $this->assertFalse($result['success']);
        $this->assertEquals("Week {$week->id} has already been played.", $result['message']);
    }

    #[Test]
    public function it_plays_all_remaining_weeks()
    {

        $homeTeam = Team::factory()->create(['power' => 80]);
        $awayTeam = Team::factory()->create(['power' => 75]);

        $week1 = Week::factory()->create();
        $week2 = Week::factory()->create();

        Game::factory()->create(['home_team_id' => $homeTeam->id, 'away_team_id' => $awayTeam->id, 'week_id' => $week1->id, 'played' => false]);
        Game::factory()->create(['home_team_id' => $awayTeam->id, 'away_team_id' => $homeTeam->id, 'week_id' => $week2->id, 'played' => false]);

        $result = $this->simulationService->playAllWeeks();


        $this->assertTrue($result['success']);
        $this->assertEquals("All remaining weeks played successfully", $result['message']);

        $this->assertTrue(Game::where('played', false)->doesntExist());
    }

    #[Test]
    public function it_does_not_play_weeks_if_all_are_already_played()
    {
    
        $week = Week::factory()->create();
        $game = Game::factory()->create([
            'week_id' => $week->id,
            'played' => true
        ]);

        $result = $this->simulationService->playAllWeeks();

        $this->assertFalse($result['success']);
        $this->assertEquals("All weeks have already been played.", $result['message']);
    }

    #[Test]
    public function it_resets_league_data_except_teams()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    
        Game::query()->delete();
        Week::query()->delete();
        Standing::query()->delete();
        Team::query()->delete();
    
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    
        $this->assertDatabaseCount('teams', 0);
    
        Team::factory()->count(3)->create();
    
        $this->assertDatabaseCount('teams', 3);
    
        Week::factory()->count(2)->create();
        Game::factory()->count(4)->create();
        Standing::factory()->count(3)->create(['points' => 10]);
    
  
        $result = $this->simulationService->resetData();
    

        $this->assertTrue($result['success']);
        $this->assertEquals("League reset successfully.", $result['message']);
    
        $this->assertDatabaseCount('weeks', 0);
        $this->assertDatabaseCount('games', 0);
        $this->assertDatabaseCount('teams', 3);
        $this->assertDatabaseHas('standings', ['points' => 0, 'won' => 0, 'lose' => 0, 'draw' => 0]);
    }
    



}
