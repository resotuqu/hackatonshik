<?php

namespace Tests\Browser;

use App\Models\Hackaton;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class HackathonDiscoveryTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test hackathon discovery through search and catalog.
     */
    public function test_user_can_discover_and_view_hackathon(): void
    {
        $hackaton = Hackaton::factory()->create([
            'title' => 'Dusk Hackathon Discovery',
            'is_public' => true,
            'description' => 'A special hackathon for testing discovery.',
        ]);

        $this->browse(function (Browser $browser) use ($hackaton) {
            $browser->visit(route('hackatons.index', ['q' => 'Dusk Hackathon']))
                ->waitForText($hackaton->title, 30)
                ->assertSee($hackaton->title)
                ->visit(route('hackatons.show', $hackaton))
                ->waitForText($hackaton->title, 30)
                ->assertSee($hackaton->title)
                ->assertSee($hackaton->description);
        });
    }
}
