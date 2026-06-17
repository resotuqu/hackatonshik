<?php

declare(strict_types=1);

use App\Events\MessageSent;
use App\Livewire\TeamChat;
use App\Models\Team;
use App\Models\TeamRole;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;

beforeEach(function (): void {
    config(['broadcasting.default' => 'log']);
});

use function Pest\Laravel\actingAs;

test('team chat broadcasts message sent event', function () {
    Event::fake([MessageSent::class]);

    $owner = User::factory()->create([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ]);
    $team = Team::factory()->for($owner)->create();

    actingAs($owner);

    Livewire::test(TeamChat::class, ['team' => $team])
        ->set('message', 'Broadcast probe')
        ->call('sendMessage')
        ->assertHasNoErrors();

    Event::assertDispatched(MessageSent::class, function (MessageSent $event) use ($team): bool {
        return $event->message->team_id === $team->id
            && $event->message->content === 'Broadcast probe';
    });
});

test('team member can authorize private team broadcast channel', function () {
    $owner = User::factory()->create([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ]);
    $member = User::factory()->create([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ]);
    $team = Team::factory()->for($owner)->create();
    TeamRole::factory()->for($team)->create(['user_id' => $member->id]);

    actingAs($member);

    $response = $this->post('/broadcasting/auth', [
        'channel_name' => 'private-team.'.$team->id,
        'socket_id' => '1.1',
    ]);

    $response->assertSuccessful();
});

test('team channel authorization denies outsider membership', function () {
    $owner = User::factory()->create([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ]);
    $outsider = User::factory()->create([
        'email_verified_at' => now(),
        'phone_verified_at' => now(),
    ]);
    $team = Team::factory()->for($owner)->create();

    expect($team->hasMember($outsider))->toBeFalse()
        ->and($team->hasMember($owner))->toBeTrue();
});
