<?php

use App\Filament\Resources\InfoBeasiswaResource\Pages\CreateInfoBeasiswa;
use App\Filament\Resources\InfoBeasiswaResource\Pages\EditInfoBeasiswa;
use App\Filament\Resources\InfoBeasiswaResource\Pages\ListInfoBeasiswa;
use App\Filament\Resources\InfoMagangResource\Pages\CreateInfoMagang;
use App\Filament\Resources\InfoMagangResource\Pages\EditInfoMagang;
use App\Filament\Resources\InfoMagangResource\Pages\ListInfoMagang;
use App\Models\InfoBeasiswa;
use App\Models\InfoMagang;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    config()->set('database.default', 'sqlite');
    config()->set('database.connections.sqlite.database', ':memory:');
    config()->set('database.connections.sqlite.url', null);
    app('db')->purge('sqlite');

    foreach ([
        '0001_01_01_000000_create_users_table.php',
        '2026_11_20_000001_create_info_beasiswa_table.php',
        '2026_11_20_000002_create_info_magang_table.php',
    ] as $migration) {
        (require database_path('migrations/'.$migration))->up();
    }

    config()->set('app.allowed_origin', '*');
    config()->set('admin.admin_email', 'admin@example.com');
    $this->travelTo(today(config('app.timezone'))->setDate(2026, 11, 20));
    $this->attributes = [
        'image' => 'covers/foto #1.jpg',
        'title' => 'Program Ilkom',
        'description' => '<p>Pendaftaran mahasiswa.</p>',
        'start_date' => '2026-11-20',
        'end_date' => '2026-11-20',
        'link' => 'https://example.com/daftar',
    ];
});

dataset('registration info APIs', [
    'beasiswa' => [InfoBeasiswa::class, InfoMagang::class, '/info-beasiswa', 'infobeasiswa', 'Info beasiswa not found'],
    'magang' => [InfoMagang::class, InfoBeasiswa::class, '/info-magang', 'infomagang', 'Info magang not found'],
]);

it('paginates records of its own type and combines title and status filters', function (
    string $model, string $otherModel, string $path, string $key,
) {
    $this->getJson($path)->assertOk()->assertExactJson([
        'pagination' => ['current_page' => 1, 'per_page' => 6, 'total' => 0, 'last_page' => 1],
        $key => [],
    ]);

    $first = $model::create($this->attributes);
    $last = $model::create($this->attributes);
    $model::create([...$this->attributes, 'title' => 'Kegiatan lain']);
    $upcoming = $model::create([...$this->attributes, 'start_date' => '2026-11-21', 'end_date' => '2026-11-30']);
    $closed = $model::create([...$this->attributes, 'start_date' => '2026-11-01', 'end_date' => '2026-11-19']);
    $otherModel::create($this->attributes);

    $active = $path.'?q=Ilkom&status=active&per_page=1';
    $this->getJson($active)->assertOk()
        ->assertJsonPath($key.'.0.id', $last->id)
        ->assertJsonPath($key.'.0.status', 'active')
        ->assertJsonPath('pagination.total', 2)
        ->assertJsonPath('pagination.last_page', 2)
        ->assertJsonMissingPath('categories')
        ->assertJsonMissingPath($key.'.0.category');
    $this->getJson($active.'&page=2')->assertOk()
        ->assertJsonPath('pagination.current_page', 2)
        ->assertJsonPath($key.'.0.id', $first->id);

    foreach (['upcoming' => $upcoming, 'closed' => $closed] as $status => $info) {
        $this->getJson($path.'?status='.$status)->assertOk()
            ->assertJsonCount(1, $key)
            ->assertJsonPath($key.'.0.id', $info->id)
            ->assertJsonPath($key.'.0.status', $status);
    }
})->with('registration info APIs');

it('serves the detail contract with a stable slug and an encoded cover URL', function (
    string $model, string $otherModel, string $path, string $key, string $missingMessage,
) {
    $info = $model::create($this->attributes);
    $duplicate = $model::create($this->attributes);
    $other = $otherModel::create($this->attributes);
    $slug = $info->slug;
    $info->update(['title' => 'Program Ilkom Baru']);

    expect($info->refresh()->slug)->toBe($slug)->not->toBe($duplicate->slug);
    $this->getJson($path.'/'.$slug)->assertOk()->assertExactJson([
        $key => [
            'id' => $info->id,
            'image' => $this->attributes['image'],
            'image_url' => Storage::disk('public')->url('covers/foto%20%231.jpg'),
            'title' => 'Program Ilkom Baru',
            'slug' => $slug,
            'description' => $this->attributes['description'],
            'start_date' => '2026-11-20',
            'end_date' => '2026-11-20',
            'status' => 'active',
            'link' => $this->attributes['link'],
            'created_at' => $info->created_at->toDateTimeString(),
            'updated_at' => $info->updated_at->toDateTimeString(),
        ],
    ]);
    $this->getJson($path.'/'.$other->slug)->assertNotFound()->assertExactJson(['errors' => $missingMessage]);
})->with('registration info APIs');

it('validates list parameters and applies the frontend domain middleware to both routes', function (
    string $model, string $otherModel, string $path,
) {
    $info = $model::create($this->attributes);

    $this->getJson($path.'?q[]=test&status=unknown&per_page=101&page=0')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['q', 'status', 'per_page', 'page']);

    config()->set('app.allowed_origin', 'https://himalkom-ipb.com');
    $this->getJson($path)->assertForbidden();
    $this->withHeader('Origin', 'https://example.com')->getJson($path.'/'.$info->slug)->assertForbidden();
    $this->withHeader('Origin', 'https://himalkom-ipb.com')->getJson($path.'/'.$info->slug)->assertOk();
})->with('registration info APIs');

dataset('registration info admin pages', [
    'beasiswa' => [InfoBeasiswa::class, 'info-beasiswa', CreateInfoBeasiswa::class, EditInfoBeasiswa::class, ListInfoBeasiswa::class],
    'magang' => [InfoMagang::class, 'info-magang', CreateInfoMagang::class, EditInfoMagang::class, ListInfoMagang::class],
]);

it('lets the primary admin create, edit and delete from Filament and serves the changes through the API', function (
    string $model, string $section, string $createPage, string $editPage,
) {
    $this->actingAs(User::factory()->create(['email' => 'admin@example.com']));
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    Storage::fake('public');

    $indexUrl = '/admin/'.$section;
    $this->get($indexUrl)->assertOk();
    $this->get($indexUrl.'/create')->assertOk();

    Livewire::test($createPage)
        ->fillForm([...$this->attributes, 'image' => UploadedFile::fake()->image('cover.jpg')])
        ->call('create')->assertHasNoFormErrors()->assertRedirect($indexUrl);
    $info = $model::firstOrFail();
    $oldCover = $info->image;
    expect($oldCover)->toStartWith($section.'/');
    Storage::disk('public')->assertExists($oldCover);
    $this->get($indexUrl.'/'.$info->id.'/edit')->assertOk();

    Livewire::test($editPage, ['record' => $info->getRouteKey()])
        ->set('data.image', [])
        ->fillForm(['title' => 'Judul Baru', 'image' => UploadedFile::fake()->image('new.jpg')])
        ->call('save')->assertHasNoFormErrors()->assertRedirect($indexUrl);
    $info->refresh();
    expect($info->title)->toBe('Judul Baru');
    expect($info->image)->not->toBe($oldCover);
    Storage::disk('public')->assertMissing($oldCover)->assertExists($info->image);

    $detailUrl = '/'.$section.'/'.$info->slug;
    $this->getJson($detailUrl)->assertOk()->assertJsonFragment(['title' => 'Judul Baru', 'image' => $info->image]);

    Livewire::test($editPage, ['record' => $info->getRouteKey()])->callAction('delete');
    $this->assertModelMissing($info);
    Storage::disk('public')->assertMissing($info->image);
    $this->getJson($detailUrl)->assertNotFound();
})->with('registration info admin pages');

it('filters the admin list by registration status and removes covers on bulk deletion', function (
    string $model, string $section, string $createPage, string $editPage, string $listPage,
) {
    $this->actingAs(User::factory()->create(['email' => 'admin@example.com']));
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    Storage::fake('public');

    $active = $model::create($this->attributes);
    $upcoming = $model::create([
        ...$this->attributes,
        'image' => $section.'/upcoming.jpg',
        'start_date' => '2026-11-21',
        'end_date' => '2026-11-23',
    ]);
    Storage::disk('public')->put($active->image, 'active cover');
    Storage::disk('public')->put($upcoming->image, 'upcoming cover');

    Livewire::test($listPage)->filterTable('status', 'upcoming')
        ->assertCanSeeTableRecords([$upcoming])->assertCanNotSeeTableRecords([$active])
        ->callTableBulkAction('delete', [$upcoming]);

    $this->assertModelMissing($upcoming);
    $this->assertModelExists($active);
    Storage::disk('public')->assertMissing($upcoming->image)->assertExists($active->image);
})->with('registration info admin pages');

it('validates required fields, cover size, date order and link in Filament', function (
    string $model, string $section, string $createPage,
) {
    $this->actingAs(User::factory()->create(['email' => 'admin@example.com']));
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    Storage::fake('public');

    Livewire::test($createPage)
        ->fillForm([
            ...$this->attributes,
            'title' => '',
            'image' => UploadedFile::fake()->image('cover.jpg')->size(2049),
            'start_date' => '2026-11-21',
            'link' => 'ftp://example.com/daftar',
        ])
        ->call('create')->assertHasFormErrors(['title' => 'required', 'image', 'end_date' => 'after_or_equal', 'link']);

    $this->assertDatabaseCount((new $model)->getTable(), 0);
})->with('registration info admin pages');

it('denies access to another panel user at the registration info admin URLs', function (string $model, string $section) {
    $info = $model::create($this->attributes);
    $this->actingAs(User::factory()->create(['email' => 'staff@himalkom.com']));

    $indexUrl = '/admin/'.$section;
    $this->get($indexUrl)->assertForbidden();
    $this->get($indexUrl.'/create')->assertForbidden();
    $this->get($indexUrl.'/'.$info->id.'/edit')->assertForbidden();
})->with('registration info admin pages');
