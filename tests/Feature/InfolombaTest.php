<?php

use App\Filament\Resources\InfolombaCategoryResource;
use App\Filament\Resources\InfolombaCategoryResource\Pages\CreateInfolombaCategory;
use App\Filament\Resources\InfolombaCategoryResource\Pages\EditInfolombaCategory;
use App\Filament\Resources\InfolombaResource;
use App\Filament\Resources\InfolombaResource\Pages\CreateInfolomba;
use App\Filament\Resources\InfolombaResource\Pages\EditInfolomba;
use App\Models\Infolomba;
use App\Models\InfolombaCategory;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

beforeEach(function () {
    // Always use a disposable database, even when .env points to the local MySQL database.
    config()->set('database.default', 'sqlite');
    config()->set('database.connections.sqlite.database', ':memory:');
    config()->set('database.connections.sqlite.url', null);
    app('db')->purge('sqlite');

    foreach ([
        '0001_01_01_000000_create_users_table.php',
        '2026_10_29_000001_create_infolomba_categories_table.php',
        '2026_10_29_000002_create_infolombas_table.php',
    ] as $migration) {
        (require database_path('migrations/'.$migration))->up();
    }

    config()->set('app.allowed_origin', '*');
    config()->set('admin.admin_email', 'admin@example.com');
    Storage::fake('public');
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    $this->travelTo(now()->setDate(2026, 10, 29)->startOfDay());
    $this->category = InfolombaCategory::create(['name' => 'Programming']);
    $this->attributes = [
        'image' => 'infolomba/cover.jpg',
        'title' => 'Hackathon Ilkom',
        'description' => '<p>Lomba pemrograman mahasiswa.</p>',
        'infolomba_category_id' => $this->category->id,
        'start_date' => '2026-10-01',
        'end_date' => '2026-10-31',
        'link' => 'https://example.com/daftar',
    ];
});

it('returns a paginated list with category, full image URL and period status', function () {
    $lomba = Infolomba::create($this->attributes);

    $this->getJson('/infolomba')
        ->assertOk()
        ->assertJsonPath('pagination.per_page', 6)
        ->assertJsonPath('pagination.total', 1)
        ->assertJsonPath('infolomba.0.slug', $lomba->slug)
        ->assertJsonPath('infolomba.0.image', 'infolomba/cover.jpg')
        ->assertJsonPath('infolomba.0.image_url', Storage::disk('public')->url('infolomba/cover.jpg'))
        ->assertJsonPath('infolomba.0.category.name', 'Programming')
        ->assertJsonPath('infolomba.0.start_date', '2026-10-01')
        ->assertJsonPath('infolomba.0.end_date', '2026-10-31')
        ->assertJsonPath('infolomba.0.status', 'active');
});

it('paginates the newest records first with a stable order', function () {
    $first = Infolomba::create($this->attributes);
    $last = Infolomba::create([...$this->attributes, 'title' => 'Lomba Kedua']);

    $this->getJson('/infolomba?per_page=1')->assertOk()
        ->assertJsonPath('infolomba.0.id', $last->id)
        ->assertJsonPath('pagination.last_page', 2);
    $this->getJson('/infolomba?per_page=1&page=2')->assertOk()
        ->assertJsonPath('infolomba.0.id', $first->id);
});

it('combines search and category filters without including unrelated records', function () {
    Infolomba::create($this->attributes);
    $design = InfolombaCategory::create(['name' => 'Desain']);
    Infolomba::create([...$this->attributes, 'infolomba_category_id' => $design->id]);
    Infolomba::create([...$this->attributes, 'title' => 'Competitive Programming']);

    $this->getJson('/infolomba?q=Hackathon&category_id='.$this->category->id)
        ->assertOk()->assertJsonCount(1, 'infolomba')
        ->assertJsonPath('pagination.total', 1)
        ->assertJsonCount(2, 'categories');
});

it('filters status using inclusive registration dates', function (string $start, string $end, string $status) {
    Infolomba::create([...$this->attributes, 'start_date' => $start, 'end_date' => $end]);

    $this->getJson('/infolomba?status='.$status)
        ->assertOk()->assertJsonCount(1, 'infolomba')->assertJsonPath('infolomba.0.status', $status);
    $otherStatus = $status === 'active' ? 'closed' : 'active';
    $this->getJson('/infolomba?status='.$otherStatus)->assertOk()->assertJsonCount(0, 'infolomba');
})->with([
    ['2026-10-30', '2026-11-05', 'upcoming'],
    ['2026-10-29', '2026-10-31', 'active'],
    ['2026-10-01', '2026-10-29', 'active'],
    ['2026-10-29', '2026-10-29', 'active'],
    ['2026-10-01', '2026-10-28', 'closed'],
]);

it('returns empty arrays and pagination when there are no matches', function () {
    $this->getJson('/infolomba')->assertOk()->assertJsonPath('pagination.total', 0)
        ->assertJsonPath('pagination.last_page', 1)->assertJsonPath('infolomba', []);
});

it('returns categories by name and does not treat categories as a detail slug', function () {
    InfolombaCategory::create(['name' => 'Desain']);
    $this->getJson('/infolomba/categories')->assertOk()->assertJsonCount(2, 'categories')
        ->assertJsonPath('categories.0.name', 'Desain')->assertJsonPath('categories.1.name', 'Programming');
});

it('returns detail by slug and reports a missing slug', function () {
    $lomba = Infolomba::create($this->attributes);
    $this->getJson('/infolomba/'.$lomba->slug)->assertOk()
        ->assertJsonPath('infolomba.id', $lomba->id)
        ->assertJsonPath('infolomba.description', $this->attributes['description'])
        ->assertJsonPath('infolomba.category.id', $this->category->id);
    $this->getJson('/infolomba/missing')->assertNotFound()->assertJsonPath('errors', 'Info lomba not found');
});

it('validates public query parameters', function (string $query, string $field) {
    $this->getJson('/infolomba?'.$query)->assertUnprocessable()->assertJsonValidationErrors($field);
})->with([
    ['per_page=0', 'per_page'],
    ['per_page=101', 'per_page'],
    ['per_page=invalid', 'per_page'],
    ['page=-1', 'page'],
    ['status=invalid', 'status'],
    ['category_id=99999', 'category_id'],
    ['q[]=test', 'q'],
]);

it('keeps slug links stable after title changes and supports duplicate titles', function () {
    $first = Infolomba::create($this->attributes);
    $second = Infolomba::create($this->attributes);
    $slug = $first->slug;
    $first->update(['title' => 'Nama Lomba Baru']);
    expect($first->refresh()->slug)->toBe($slug)->not->toBe($second->slug);
    $this->getJson('/infolomba/'.$slug)->assertOk()->assertJsonPath('infolomba.title', 'Nama Lomba Baru');
});

it('encodes cover file names in full image URLs', function () {
    $lomba = Infolomba::create([...$this->attributes, 'image' => 'infolomba/foto lomba #1.jpg']);
    $this->getJson('/infolomba/'.$lomba->slug)->assertOk()
        ->assertJsonPath('infolomba.image_url', Storage::disk('public')->url('infolomba/foto%20lomba%20%231.jpg'));
});

it('removes replaced and deleted cover files while retaining the current cover on other edits', function () {
    Storage::disk('public')->put('infolomba/cover.jpg', 'old image');
    Storage::disk('public')->put('infolomba/new.jpg', 'new image');
    $lomba = Infolomba::create($this->attributes);
    $lomba->update(['title' => 'Judul Baru']);
    Storage::disk('public')->assertExists('infolomba/cover.jpg');
    $lomba->update(['image' => 'infolomba/new.jpg']);
    Storage::disk('public')->assertMissing('infolomba/cover.jpg');
    Storage::disk('public')->assertExists('infolomba/new.jpg');
    $lomba->delete();
    Storage::disk('public')->assertMissing('infolomba/new.jpg');
});

it('protects used categories and allows deleting unused categories', function () {
    $lomba = Infolomba::create($this->attributes);
    expect(fn () => $this->category->delete())->toThrow(ValidationException::class);
    $this->assertDatabaseHas('infolomba_categories', ['id' => $this->category->id]);
    $lomba->delete();
    $this->category->delete();
    $this->assertDatabaseMissing('infolomba_categories', ['id' => $this->category->id]);
});

it('enforces category uniqueness in the database', function () {
    expect(fn () => InfolombaCategory::create(['name' => 'Programming']))->toThrow(QueryException::class);
});

it('registers both admin resources and renders their list and form pages', function () {
    $this->actingAs(User::factory()->create(['email' => 'admin@example.com']));
    expect(Filament::getPanel('admin')->getResources())
        ->toContain(InfolombaResource::class, InfolombaCategoryResource::class);
    foreach (['/admin/infolomba', '/admin/infolomba/create', '/admin/infolomba-categories', '/admin/infolomba-categories/create'] as $url) {
        $this->get($url)->assertOk();
    }
});

it('lets the primary admin create and edit competitions with an uploaded public cover', function () {
    $this->actingAs(User::factory()->create(['email' => 'admin@example.com']));
    Livewire::test(CreateInfolomba::class)
        ->fillForm([...$this->attributes, 'image' => UploadedFile::fake()->image('cover.jpg')])
        ->call('create')->assertHasNoFormErrors();
    $lomba = Infolomba::firstOrFail();
    Storage::disk('public')->assertExists($lomba->image);
    expect($lomba->image)->toStartWith('infolomba/');

    Livewire::test(EditInfolomba::class, ['record' => $lomba->getRouteKey()])
        ->fillForm(['title' => 'Hackathon Updated'])->call('save')->assertHasNoFormErrors();
    expect($lomba->refresh()->title)->toBe('Hackathon Updated');
    Livewire::test(EditInfolomba::class, ['record' => $lomba->getRouteKey()])
        ->callAction('delete');
    $this->assertDatabaseMissing('infolombas', ['id' => $lomba->id]);
    Storage::disk('public')->assertMissing($lomba->image);
});

it('validates admin dates, required fields and HTTP links', function () {
    $this->actingAs(User::factory()->create(['email' => 'admin@example.com']));
    Livewire::test(CreateInfolomba::class)
        ->fillForm([...$this->attributes, 'image' => UploadedFile::fake()->image('cover.jpg'), 'title' => '', 'start_date' => '2026-11-01', 'link' => 'ftp://example.com/file'])
        ->call('create')->assertHasFormErrors(['title' => 'required', 'end_date' => 'after_or_equal', 'link']);
    $this->assertDatabaseCount('infolombas', 0);
});

it('lets the primary admin manage names and rejects duplicate category names', function () {
    $this->actingAs(User::factory()->create(['email' => 'admin@example.com']));
    Livewire::test(CreateInfolombaCategory::class)->fillForm(['name' => 'Desain'])->call('create')->assertHasNoFormErrors();
    Livewire::test(CreateInfolombaCategory::class)->fillForm(['name' => 'Desain'])->call('create')->assertHasFormErrors(['name' => 'unique']);
    $category = InfolombaCategory::where('name', 'Desain')->firstOrFail();
    Livewire::test(EditInfolombaCategory::class, ['record' => $category->getRouteKey()])
        ->fillForm(['name' => 'Desain Grafis'])->call('save')->assertHasNoFormErrors();
    expect($category->refresh()->name)->toBe('Desain Grafis');
    Livewire::test(EditInfolombaCategory::class, ['record' => $category->getRouteKey()])->callAction('delete');
    $this->assertDatabaseMissing('infolomba_categories', ['id' => $category->id]);
});

it('lets the admin create a category directly from the competition form', function () {
    $this->actingAs(User::factory()->create(['email' => 'admin@example.com']));
    Livewire::test(CreateInfolomba::class)
        ->callFormComponentAction('infolomba_category_id', 'createOption', data: ['name' => 'Karya Tulis'])
        ->assertHasNoFormErrors();
    $this->assertDatabaseHas('infolomba_categories', ['name' => 'Karya Tulis']);
});

it('rejects oversized covers and requires a selected category', function () {
    $this->actingAs(User::factory()->create(['email' => 'admin@example.com']));
    Livewire::test(CreateInfolomba::class)
        ->fillForm([
            ...$this->attributes,
            'image' => UploadedFile::fake()->image('cover.jpg')->size(2049),
            'infolomba_category_id' => null,
        ])
        ->call('create')->assertHasFormErrors(['image', 'infolomba_category_id' => 'required']);
    $this->assertDatabaseCount('infolombas', 0);
});

it('prevents other panel users from managing info lomba through direct admin URLs', function () {
    $lomba = Infolomba::create($this->attributes);
    $this->actingAs(User::factory()->create(['email' => 'staff@himalkom.com']));
    foreach (['/admin/infolomba', '/admin/infolomba/create', '/admin/infolomba/'.$lomba->id.'/edit', '/admin/infolomba-categories', '/admin/infolomba-categories/create', '/admin/infolomba-categories/'.$this->category->id.'/edit'] as $url) {
        $this->get($url)->assertForbidden();
    }
});

it('protects info lomba behind the existing frontend domain middleware', function () {
    config()->set('app.allowed_origin', 'https://himalkom-ipb.com');
    $this->getJson('/infolomba')->assertForbidden();
    $this->withHeader('Origin', 'https://example.com')->getJson('/infolomba/categories')->assertForbidden();
    $this->withHeader('Origin', 'https://himalkom-ipb.com')->getJson('/infolomba')->assertOk();
});
