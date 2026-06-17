<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\Team;
use Illuminate\Console\Command;

class EnsureCaptainRoles extends Command
{
    protected $signature = 'teams:ensure-captain-roles';

    protected $description = 'Create captain team roles for teams that are missing one.';

    public function handle(): int
    {
        $defaultRoleId = Role::query()->value('id');

        if ($defaultRoleId === null) {
            $this->error('No role categories found. Seed roles before running this command.');

            return self::FAILURE;
        }

        $created = 0;

        Team::query()
            ->whereNotNull('user_id')
            ->with('user')
            ->chunkById(100, function ($teams) use ($defaultRoleId, &$created): void {
                foreach ($teams as $team) {
                    if ($team->captainRole() !== null) {
                        continue;
                    }

                    $team->ensureCaptainHasRole([
                        'title' => 'Капитан',
                        'description' => filled($team->description)
                            ? (string) $team->description
                            : 'Руководитель команды',
                        'role' => $defaultRoleId,
                        'skills' => [],
                    ]);

                    $created++;
                }
            });

        $this->info("Created {$created} captain role(s).");

        return self::SUCCESS;
    }
}
