<?php

declare(strict_types=1);

namespace Tests\Browser\Concerns;

use App\Models\Hackaton;
use Laravel\Dusk\Browser;

trait InteractsWithHackatonShow
{
    protected function visitHackatonShowAndSubmitApplication(Browser $browser, Hackaton $hackaton, int $teamId): void
    {
        $browser->visit(route('hackatons.show', $hackaton))
            ->waitFor('@application-modal-trigger-hackaton-'.$hackaton->id, 30)
            ->click('@application-modal-trigger-hackaton-'.$hackaton->id)
            ->waitFor('select[name="team_id"]', 15)
            ->select('team_id', (string) $teamId)
            ->press('Отправить заявку');
    }
}
