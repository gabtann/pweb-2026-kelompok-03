<?php

use App\Models\Tim;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    // Halaman Vue akun (Pages/Panitia/Akun/*) dibuat Aji. Hapus baris ini setelah file-nya ada.
    config(['inertia.testing.ensure_pages_exist' => false]);
});

function adminAkun(array $override = []): User
{
    return User::factory()->panitia()->create($override);
}

function dataAkunValid(array $override = []): array
{
    return array_merge([
        'nama_lengkap' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'no_telepon' => '081234567890',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ], $override);
}

// ---------- Akses ----------

it('peserta mendapat 403 di semua route akun', function () {
    $target = User::factory()->create();
    $peserta = User::factory()->create();

    foreach ([
        ['get', '/panitia/akun'],
        ['get', '/panitia/akun/create'],
        ['post', '/panitia/akun'],
        ['get', "/panitia/akun/{$target->id}/edit"],
        ['put', "/panitia/akun/{$target->id}"],
        ['delete', "/panitia/akun/{$target->id}"],
    ] as [$method, $url]) {
        $this->actingAs($peserta)->{$method}($url)->assertForbidden();
    }

    expect(User::count())->toBe(2);
});

it('guest di-redirect dari semua route akun', function () {
    $target = User::factory()->create();

    foreach ([
        ['get', '/panitia/akun'],
        ['get', '/panitia/akun/create'],
        ['post', '/panitia/akun'],
        ['get', "/panitia/akun/{$target->id}/edit"],
        ['put', "/panitia/akun/{$target->id}"],
        ['delete', "/panitia/akun/{$target->id}"],
    ] as [$method, $url]) {
        $this->{$method}($url)->assertRedirect();
    }

    expect(User::count())->toBe(1);
});

// ---------- Index ----------

it('index menampilkan akun terurut nama dengan flag dapat_dihapus', function () {
    User::factory()->create(['nama_lengkap' => 'Zxq Budi']);
    User::factory()->create(['nama_lengkap' => 'Zxq Ani']);
    adminAkun(['nama_lengkap' => 'Zxq Panitia']);

    $this->actingAs(User::factory()->panitia()->create())
        ->get('/panitia/akun?q=Zxq')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Panitia/Akun/Index')
            ->has('akun.data', 3)
            ->where('akun.data.0.nama_lengkap', 'Zxq Ani')
            ->where('akun.data.0.dapat_dihapus', true)
            ->where('akun.data.1.nama_lengkap', 'Zxq Budi')
            ->where('akun.data.2.nama_lengkap', 'Zxq Panitia')
            ->where('akun.data.2.dapat_dihapus', false)
            ->where('filters.q', 'Zxq')
        );
});

it('index memakai paginasi 10 per halaman', function () {
    User::factory()->count(12)->create();

    $this->actingAs(adminAkun())
        ->get('/panitia/akun')
        ->assertInertia(fn (Assert $page) => $page
            ->has('akun.data', 10)
            ->where('akun.total', 13)
        );
});

it('pencarian mencocokkan nama, email, dan no telepon', function () {
    User::factory()->create(['nama_lengkap' => 'Orang Pertama', 'email' => 'cari.aku@example.com']);
    User::factory()->create(['nama_lengkap' => 'Orang Kedua', 'no_telepon' => '0899000111']);
    $admin = adminAkun();

    $this->actingAs($admin)->get('/panitia/akun?q=cari.aku')
        ->assertInertia(fn (Assert $page) => $page->has('akun.data', 1)->where('akun.data.0.nama_lengkap', 'Orang Pertama'));

    $this->actingAs($admin)->get('/panitia/akun?q=0899000')
        ->assertInertia(fn (Assert $page) => $page->has('akun.data', 1)->where('akun.data.0.nama_lengkap', 'Orang Kedua'));

    $this->actingAs($admin)->get('/panitia/akun?q=Orang')
        ->assertInertia(fn (Assert $page) => $page->has('akun.data', 2));
});

it('filter role bekerja dan role tidak valid diabaikan', function () {
    User::factory()->count(2)->create();
    $admin = adminAkun();

    $this->actingAs($admin)->get('/panitia/akun?role=panitia')
        ->assertInertia(fn (Assert $page) => $page->has('akun.data', 1)->where('filters.role', 'panitia'));

    $this->actingAs($admin)->get('/panitia/akun?role=peserta')
        ->assertInertia(fn (Assert $page) => $page->has('akun.data', 2));

    $this->actingAs($admin)->get('/panitia/akun?role=ngawur')
        ->assertInertia(fn (Assert $page) => $page->has('akun.data', 3)->where('filters.role', null));
});

// ---------- Create / Store ----------

it('panitia membuka form tambah akun', function () {
    $this->actingAs(adminAkun())
        ->get('/panitia/akun/create')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Panitia/Akun/Create'));
});

it('store membuat akun peserta, role dipaksa peserta dan password ter-hash', function () {
    $this->actingAs(adminAkun())
        ->post('/panitia/akun', dataAkunValid(['role' => 'panitia']))
        ->assertRedirect(route('panitia.akun.index'))
        ->assertSessionHas('success');

    $user = User::where('email', 'budi@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->nama_lengkap)->toBe('Budi Santoso')
        ->and($user->role)->toBe('peserta')
        ->and(Hash::check('password123', $user->password))->toBeTrue();
});

it('store menolak data tidak valid', function () {
    $admin = adminAkun();
    User::factory()->create(['email' => 'budi@example.com']);

    $this->actingAs($admin)->post('/panitia/akun', dataAkunValid())
        ->assertSessionHasErrors('email');

    $this->actingAs($admin)->post('/panitia/akun', dataAkunValid(['email' => 'baru@example.com', 'password_confirmation' => 'beda']))
        ->assertSessionHasErrors('password');

    $this->actingAs($admin)->post('/panitia/akun', dataAkunValid(['email' => 'baru@example.com', 'password' => 'pendek', 'password_confirmation' => 'pendek']))
        ->assertSessionHasErrors('password');

    $this->actingAs($admin)->post('/panitia/akun', [])
        ->assertSessionHasErrors(['nama_lengkap', 'email', 'no_telepon', 'password']);

    expect(User::count())->toBe(2);
});

// ---------- Edit / Update ----------

it('edit menampilkan data akun tanpa password', function () {
    $target = User::factory()->create(['no_telepon' => '081200000000']);

    $this->actingAs(adminAkun())
        ->get("/panitia/akun/{$target->id}/edit")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Panitia/Akun/Edit')
            ->where('akun.id', $target->id)
            ->where('akun.email', $target->email)
            ->where('akun.no_telepon', '081200000000')
            ->missing('akun.password')
        );
});

it('update mengubah data tapi role tidak bisa diubah', function () {
    $target = User::factory()->create(['nama_lengkap' => 'Lama', 'no_telepon' => '081200000000']);

    $this->actingAs(adminAkun())
        ->put("/panitia/akun/{$target->id}", [
            'nama_lengkap' => 'Baru',
            'email' => $target->email,
            'no_telepon' => '08111',
            'role' => 'panitia',
        ])
        ->assertRedirect(route('panitia.akun.index'))
        ->assertSessionHas('success');

    $target->refresh();

    expect($target->nama_lengkap)->toBe('Baru')
        ->and($target->no_telepon)->toBe('08111')
        ->and($target->role)->toBe('peserta');
});

it('update tanpa password mempertahankan password lama dan dengan password menggantinya', function () {
    $target = User::factory()->create(['password' => 'rahasia123', 'no_telepon' => '081200000000']);
    $admin = adminAkun();
    $payload = [
        'nama_lengkap' => $target->nama_lengkap,
        'email' => $target->email,
        'no_telepon' => $target->no_telepon,
    ];

    $this->actingAs($admin)->put("/panitia/akun/{$target->id}", $payload)
        ->assertSessionHasNoErrors();
    expect(Hash::check('rahasia123', $target->fresh()->password))->toBeTrue();

    $this->actingAs($admin)->put("/panitia/akun/{$target->id}", $payload + [
        'password' => 'barulagi123',
        'password_confirmation' => 'barulagi123',
    ])->assertSessionHasNoErrors();
    expect(Hash::check('barulagi123', $target->fresh()->password))->toBeTrue();
});

it('update menolak email akun lain tapi mengizinkan email sendiri', function () {
    $lain = User::factory()->create(['email' => 'lain@example.com']);
    $target = User::factory()->create(['email' => 'target@example.com', 'no_telepon' => '081200000000']);
    $admin = adminAkun();

    $this->actingAs($admin)->put("/panitia/akun/{$target->id}", [
        'nama_lengkap' => 'Target',
        'email' => $lain->email,
        'no_telepon' => '081200000000',
    ])->assertSessionHasErrors('email');

    $this->actingAs($admin)->put("/panitia/akun/{$target->id}", [
        'nama_lengkap' => 'Target Baru',
        'email' => 'target@example.com',
        'no_telepon' => '081200000000',
    ])->assertSessionHasNoErrors();

    expect($target->fresh()->nama_lengkap)->toBe('Target Baru');
});

// ---------- Destroy ----------

it('destroy menghapus akun peserta tanpa tim', function () {
    $target = User::factory()->create();

    $this->actingAs(adminAkun())
        ->delete("/panitia/akun/{$target->id}")
        ->assertRedirect(route('panitia.akun.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('users', ['id' => $target->id]);
});

it('destroy menolak akun panitia', function () {
    $panitia = adminAkun();

    $this->actingAs(adminAkun())
        ->delete("/panitia/akun/{$panitia->id}")
        ->assertSessionHas('error');

    $this->assertDatabaseHas('users', ['id' => $panitia->id]);
});

it('destroy menolak peserta yang sudah menjadi ketua tim', function () {
    $ketua = User::factory()->create();
    Tim::factory()->create(['ketua_id' => $ketua->id]);

    $this->actingAs(adminAkun())
        ->delete("/panitia/akun/{$ketua->id}")
        ->assertSessionHas('error');

    $this->assertDatabaseHas('users', ['id' => $ketua->id]);
});
