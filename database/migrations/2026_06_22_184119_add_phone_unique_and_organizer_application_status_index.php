<?php

declare(strict_types=1);

use App\Models\User;
use App\Services\OAuth\OAuthPhoneResolver;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $resolver = app(OAuthPhoneResolver::class);

        User::query()
            ->whereNotNull('phone')
            ->orderBy('id')
            ->each(function (User $user) use ($resolver): void {
                $normalized = $resolver->normalize($user->phone);

                if ($normalized !== null && $normalized !== $user->phone) {
                    $user->forceFill(['phone' => $normalized])->saveQuietly();
                }
            });

        $duplicatePhones = User::query()
            ->whereNotNull('phone')
            ->select('phone')
            ->groupBy('phone')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('phone');

        foreach ($duplicatePhones as $phone) {
            $users = User::query()
                ->where('phone', $phone)
                ->orderBy('id')
                ->get();

            $users->slice(1)->each(function (User $user): void {
                $user->forceFill([
                    'phone' => null,
                    'phone_verified_at' => null,
                ])->saveQuietly();
            });
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('CREATE UNIQUE INDEX users_phone_unique ON users (phone) WHERE phone IS NOT NULL');
        } else {
            Schema::table('users', function (Blueprint $table): void {
                $table->unique('phone');
            });
        }

        Schema::table('organizer_applications', function (Blueprint $table): void {
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('organizer_applications', function (Blueprint $table): void {
            $table->dropIndex(['status']);
        });

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS users_phone_unique');
        } else {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropUnique(['phone']);
            });
        }
    }
};
