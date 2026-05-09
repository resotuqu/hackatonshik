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
            $browser->visit('/')
                    ->waitForText('Хакатоны', 30)
                    ->clickLink('Хакатоны')
                    ->waitForRoute('hackatons.index')
                    ->assertSee('Хакатоны')
                    // Search for our hackathon
                    ->type('input[placeholder*="Название"]', 'Dusk Hackathon')
                    ->pause(1000) // Wait for Livewire search debounce
                    ->assertSee($hackaton->title)
                    ->pause(500)
                    ->press('Подробнее') // Click the card button
                    ->waitForRoute('hackatons.show', ['hackaton' => $hackaton->id])
                    ->assertSee($hackaton->title)
                    ->assertSee($hackaton->description);
        });
    }
}
