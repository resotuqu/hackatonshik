<?php

declare(strict_types=1);

test('teams catalog page renders for guests', function () {
    $this->get('/teams?tab=all')
        ->assertOk()
        ->assertSee('Каталог команд', false)
        ->assertSee('Открытые', false);
});
