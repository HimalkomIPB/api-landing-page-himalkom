<?php

use App\Models\InfoBeasiswa;
use App\Models\InfoMagang;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    config()->set('database.default', 'sqlite');
    config()->set('database.connections.sqlite.database', ':memory:');
    config()->set('database.connections.sqlite.url', null);
    app('db')->purge('sqlite');

    foreach ([
        '2026_11_20_000001_create_info_beasiswa_table.php',
        '2026_11_20_000002_create_info_magang_table.php',
    ] as $migration) {
        (require database_path('migrations/'.$migration))->up();
    }

    config()->set('app.allowed_origin', '*');
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
