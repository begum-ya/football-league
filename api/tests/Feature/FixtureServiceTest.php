<?php

namespace Tests\Unit;

use App\Models\Game;
use App\Models\Team;
use App\Models\Week;
use App\Services\FixtureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class FixtureServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FixtureService $fixtureService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fixtureService = new FixtureService();
    }


    #[Test]
    public function it_fetches_fixtures_correctly()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Game::truncate();
        Week::truncate();
        Team::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $teams = Team::factory()->count(4)->create();

        $this->fixtureService->generateFixtures();
        $fixtures = $this->fixtureService->getFixtures();
        $expectedWeeks = ($teams->count() - 1) * 2; 

        $this->assertCount($expectedWeeks, $fixtures, "Expected $expectedWeeks weeks, but found " . count($fixtures));

        foreach ($fixtures as $week) {
            $this->assertGreaterThan(0, $week->games->count(), "Week {$week->week} has no games.");
        }

        foreach ($teams as $team) {
            $gamesAsHome = Game::where('home_team_id', $team->id)->count();
            $gamesAsAway = Game::where('away_team_id', $team->id)->count();
            $totalGames = $gamesAsHome + $gamesAsAway;

            $expectedGames = ($teams->count() - 1) * 2; 

            $this->assertEquals($expectedGames, $totalGames, "Team {$team->id} played $totalGames games, expected $expectedGames.");
        }
    }


    #[Test]
    public function it_generates_fixtures_successfully()
    {
        Team::factory()->count(4)->create(); 

        $result = $this->fixtureService->generateFixtures();

        $this->assertTrue($result['success']);
        $this->assertEquals('Fixtures generated successfully', $result['message']);
        $this->assertDatabaseCount('weeks', 6); 
        $this->assertDatabaseHas('games', ['played' => false]);
    }

    #[Test]
    public function it_does_not_generate_fixtures_if_not_enough_teams()
    {
        Team::factory()->count(1)->create(); 

        $result = $this->fixtureService->generateFixtures();

        $this->assertFalse($result['success']);
        $this->assertEquals('Not enough teams to generate fixtures', $result['message']);
    }

    #[Test]
    public function it_fetches_current_week_correctly()
    {
        $week = Week::factory()->create();
        Game::factory()->create([
            'week_id' => $week->id,
            'played' => false
        ]);

        $currentWeek = $this->fixtureService->getCurrentWeek();

        $this->assertTrue($currentWeek['success']);
        $this->assertEquals($week->week, $currentWeek['current_week']);
    }

    #[Test]
    public function it_returns_no_weeks_remaining_if_all_games_are_played()
    {
        $week = Week::factory()->create();
        Game::factory()->create([
            'week_id' => $week->id,
            'played' => true
        ]);

        $currentWeek = $this->fixtureService->getCurrentWeek();

        $this->assertFalse($currentWeek['success']);
        $this->assertEquals('No Weeks Remaining', $currentWeek['message']);
    }
}
