<?php

namespace Tests\Unit;

use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class TeamServiceTest extends TestCase
{
    use RefreshDatabase; 

    protected TeamService $teamService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamService = new TeamService();
    }

    #[Test]
    public function it_fetches_all_teams()
    {

        Team::factory()->count(3)->create();

        $teams = $this->teamService->getTeams();

        $this->assertCount(3, $teams, "Expected 3 teams, but found " . count($teams));
    }

    #[Test]
    public function it_creates_a_new_team()
    {

        $teamData = ['name' => 'Test Team'];
        $team = $this->teamService->createTeam($teamData);

        $this->assertDatabaseHas('teams', ['id' => $team->id, 'name' => 'Test Team']);
        $this->assertInstanceOf(Team::class, $team);
    }
}
