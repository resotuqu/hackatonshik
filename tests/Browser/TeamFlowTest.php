<?php

namespace Tests\Browser;

use App\Models\Hackaton;
use App\Models\Role;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TeamFlowTest extends DuskTestCase
{
    public function test_user_can_create_team_and_apply_to_hackaton(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $hackaton = Hackaton::factory()->create([
            'title' => 'Dusk Hackathon',
            'is_public' => true,
        ]);

        $role = Role::factory()->create(['name' => 'Developer']);

        $this->browse(function (Browser $browser) use ($user, $hackaton, $role) {
            $browser->loginAs($user)
                ->visit('/profile/teams')
                ->assertSee('Мои команды')
                ->clickLink('Создать команду')
                ->waitForRoute('teams.create')

                    // Step 1: Основное
                ->type('input[placeholder*="Phoenix"]', 'Dusk Team')
                ->pause(500)
                ->tap(fn ($b) => $b->script([
                    "let lw = Livewire.find(document.querySelector('[wire\\\\:id]').getAttribute('wire:id'));",
                    "lw.set('description', 'A team created by Dusk E2E test');",
                ]))
                ->pause(500)
                ->press('Далее')
                ->waitForText('Обложка и хакатон', 15)

                    // Step 2: Обложка
                ->attach('photo', storage_path('app/tmp/test.jpg'))
                ->pause(500)
                ->select('hackaton_id', (string) $hackaton->id)
                ->pause(500)
                ->press('Далее')
                ->waitForText('Социальные ссылки', 15)

                    // Step 3: Ссылки
                ->type('input[placeholder="Например, Telegram"]', 'Telegram')
                ->pause(200)
                ->type('input[placeholder="https://..."]', 'https://t.me/dusk')
                ->pause(500)
                ->press('Далее')
                ->waitForText('Роли в команде', 15)

                    // Step 4: Роли
                ->type('input[wire\:model*="roles.0.title"]', 'Developer')
                ->pause(200)
                ->tap(fn ($b) => $b->script([
                    "let lw = Livewire.find(document.querySelector('[wire\\\\:id]').getAttribute('wire:id'));",
                    "lw.set('roles.0.description', 'Coding things');",
                ]))
                ->pause(200)
                ->select('roles.0.role', (string) $role->id)
                ->pause(500)
                ->press('Создать команду')
                ->waitForLocation('/profile/teams', 20)
                ->assertSee('Dusk Team');

            $browser->visit(route('hackatons.show', $hackaton))
                ->waitForText('Подать заявку')
                ->press('Подать заявку')
                ->waitForText('Заявка команды на хакатон подана')
                ->assertSee('Заявка команды на хакатон подана');
        });
    }
}
