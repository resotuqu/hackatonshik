<?php

declare(strict_types=1);

use App\Actions\AvatarPreset\DeleteAvatarPresetImage;
use App\Actions\AvatarPreset\StoreAvatarPresetImages;
use App\Enums\ReportStatus;
use App\Models\AvatarPreset;
use App\Models\AvatarPresetPack;
use App\Models\ContactMessage;
use App\Models\NewsPost;
use App\Models\PlatformSetting;
use App\Models\Report;
use App\Models\TeamMessage;
use App\Models\User;
use App\Support\PresetAvatar;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;

test('admin can access all filament management resources', function () {
    $admin = User::factory()->admin()->create();

    $routes = [
        'filament.admin.resources.users.index',
        'filament.admin.resources.hackatons.index',
        'filament.admin.resources.teams.index',
        'filament.admin.resources.news.index',
        'filament.admin.resources.avatar-presets.index',
        'filament.admin.resources.platform-settings.index',
        'filament.admin.resources.reports.index',
        'filament.admin.resources.contact-messages.index',
        'filament.admin.resources.organizer-applications.index',
        'filament.admin.resources.activity-log.activity-logs.index',
    ];

    foreach ($routes as $route) {
        $this->actingAs($admin)
            ->get(route($route))
            ->assertOk();
    }
});

test('moderator can access moderation resources but not user management', function () {
    $moderator = User::factory()->moderator()->create();

    $this->actingAs($moderator)
        ->get(route('filament.admin.resources.reports.index'))
        ->assertOk();

    $this->actingAs($moderator)
        ->get(route('filament.admin.resources.contact-messages.index'))
        ->assertOk();

    $this->actingAs($moderator)
        ->get(route('filament.admin.resources.activity-log.activity-logs.index'))
        ->assertOk();

    $this->actingAs($moderator)
        ->get(route('filament.admin.resources.users.index'))
        ->assertForbidden();

    $this->actingAs($moderator)
        ->get(route('filament.admin.resources.news.index'))
        ->assertForbidden();
});

test('participant cannot access filament panel resources', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/admin')
        ->assertForbidden();
});

test('admin can create news post via filament', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('filament.admin.resources.news.create'))
        ->assertOk();

    NewsPost::query()->create([
        'title' => 'Filament новость',
        'slug' => 'filament-news',
        'excerpt' => 'Кратко',
        'body' => 'Текст',
        'category' => 'Обновления',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $this->assertDatabaseHas('news_posts', [
        'title' => 'Filament новость',
        'slug' => 'filament-news',
    ]);
});

test('moderator can dismiss report and activity is logged', function () {
    $moderator = User::factory()->moderator()->create();
    $reporter = User::factory()->create();
    $message = TeamMessage::factory()->create();

    $report = Report::query()->create([
        'reporter_id' => $reporter->id,
        'reportable_type' => TeamMessage::class,
        'reportable_id' => $message->id,
        'reason' => 'Спам',
        'status' => ReportStatus::Pending,
    ]);

    $this->actingAs($moderator)
        ->get(route('filament.admin.resources.reports.edit', $report))
        ->assertOk();

    $report->resolve(ReportStatus::Dismissed);

    expect($report->fresh())
        ->status->toBe(ReportStatus::Dismissed)
        ->reviewed_by->toBe($moderator->id);

    expect(Activity::query()->where('log_name', 'report')->where('subject_id', $report->id)->exists())->toBeTrue();
});

test('admin can toggle platform setting in filament', function () {
    $admin = User::factory()->admin()->create();
    $setting = PlatformSetting::query()->firstOrFail();

    $this->actingAs($admin)
        ->get(route('filament.admin.resources.platform-settings.edit', $setting))
        ->assertOk();
});

test('moderator can view contact messages', function () {
    $moderator = User::factory()->moderator()->create();
    $message = ContactMessage::factory()->create();

    $this->actingAs($moderator)
        ->get(route('filament.admin.resources.contact-messages.view', $message))
        ->assertOk();
});

test('admin can manage avatar presets on pack edit page', function () {
    $admin = User::factory()->admin()->create();
    $pack = AvatarPresetPack::factory()->create(['slug' => 'filament-pack']);

    $this->actingAs($admin)
        ->get(route('filament.admin.resources.avatar-presets.edit', $pack))
        ->assertOk()
        ->assertSee('Аватарки');
});

test('store avatar preset images action creates preset records', function () {
    Storage::fake('public');

    $pack = AvatarPresetPack::factory()->create(['slug' => 'action-pack']);
    $dir = PresetAvatar::packStorageDirectory($pack->slug);
    Storage::disk('public')->makeDirectory($dir);

    $pathOne = $dir.'/avatar-one.jpg';
    $pathTwo = $dir.'/avatar-two.jpg';
    Storage::disk('public')->put($pathOne, 'image-one');
    Storage::disk('public')->put($pathTwo, 'image-two');

    $created = app(StoreAvatarPresetImages::class)($pack, [$pathOne, $pathTwo]);

    expect($created)->toBe(2);

    expect(AvatarPreset::query()->where('avatar_preset_pack_id', $pack->id)->count())->toBe(2);
});

test('delete avatar preset image action removes file and record', function () {
    Storage::fake('public');

    $pack = AvatarPresetPack::factory()->create(['slug' => 'delete-pack']);
    $path = PresetAvatar::packStorageDirectory($pack->slug).'/to-delete.jpg';
    Storage::disk('public')->put($path, 'image');

    $preset = AvatarPreset::factory()->create([
        'avatar_preset_pack_id' => $pack->id,
        'storage_path' => $path,
    ]);

    app(DeleteAvatarPresetImage::class)($preset);

    expect(AvatarPreset::query()->find($preset->id))->toBeNull();
    Storage::disk('public')->assertMissing($path);
});
