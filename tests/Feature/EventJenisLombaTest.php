<?php

use App\Models\Event;
use App\Models\Pengumpulan;
use App\Models\Pengumuman;
use App\Models\TahapLomba;
use App\Models\Tim;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

/**
 * Event dengan 1 tahap (jadi tahap terakhir) berisi 3 pengumpulan peringkat 1-3.
 */
function buatEventDenganJuara(Event $event, bool $dipublikasikan = true): Event
{
    $tahap = TahapLomba::factory()->pdf()->create(['event_id' => $event->id]);

    Pengumuman::factory()->lengkap()->create([
        'tahap_lomba_id' => $tahap->id,
        'dipublikasikan_at' => $dipublikasikan ? now() : null,
    ]);

    foreach ([1, 2, 3] as $peringkat) {
        $tim = Tim::factory()->disetujui()->create(['event_id' => $event->id]);

        Pengumpulan::factory()->dinilai()->create([
            'tim_id' => $tim->id,
            'tahap_lomba_id' => $tahap->id,
            'peringkat' => $peringkat,
            'status_kelulusan' => 'lolos',
        ]);
    }

    return $event;
}

it('label_jenis_lomba mengambil dari konstanta', function () {
    expect(Event::factory()->uiux()->make()->label_jenis_lomba)->toBe('UI/UX');
    expect(Event::factory()->ict()->make()->label_jenis_lomba)->toBe('ICT');
    expect(Event::factory()->softwareDevelopment()->make()->label_jenis_lomba)->toBe('Software Development');
});

it('scopeJenis memfilter jika jenis diberikan dan mengabaikan jika null', function () {
    Event::factory()->uiux()->count(2)->create();
    Event::factory()->ict()->create();

    expect(Event::jenis('uiux')->count())->toBe(2);
    expect(Event::jenis(null)->count())->toBe(3);
});

it('scopeUrutJenis mengurutkan uiux, ict, software_development lalu nama_event', function () {
    Event::factory()->softwareDevelopment()->create(['nama_event' => 'SD B']);
    Event::factory()->ict()->create(['nama_event' => 'ICT A']);
    Event::factory()->softwareDevelopment()->create(['nama_event' => 'SD A']);
    Event::factory()->uiux()->create(['nama_event' => 'UIUX A']);

    $urut = Event::urutJenis()->pluck('nama_event')->all();

    expect($urut)->toBe(['UIUX A', 'ICT A', 'SD A', 'SD B']);
});

it('juaraPerJenis mengembalikan 3 kunci berurutan dengan top 3 hanya untuk jenis yang sudah diumumkan', function () {
    $uiux = buatEventDenganJuara(Event::factory()->uiux()->create());
    buatEventDenganJuara(Event::factory()->ict()->create(), dipublikasikan: false);
    // software_development: tidak ada event sama sekali.

    $hasil = Event::juaraPerJenis();

    expect(array_keys($hasil))->toBe(['uiux', 'ict', 'software_development']);

    expect($hasil['uiux'])->toHaveCount(3);
    expect($hasil['uiux']->pluck('peringkat')->all())->toBe([1, 2, 3]);
    expect($hasil['uiux']->first()->relationLoaded('tim'))->toBeTrue();

    expect($hasil['ict'])->toBeEmpty();
    expect($hasil['software_development'])->toBeEmpty();
});

it('juaraPerJenis memakai event dengan waktu_mulai terbaru', function () {
    $lama = buatEventDenganJuara(Event::factory()->uiux()->create(['waktu_mulai' => now()->subYear()]));
    $baru = buatEventDenganJuara(Event::factory()->uiux()->create(['waktu_mulai' => now()->addMonth()]));

    $timBaru = Tim::where('event_id', $baru->id)->pluck('id')->all();

    $hasil = Event::juaraPerJenis()['uiux'];

    expect($hasil)->toHaveCount(3);
    expect($hasil->pluck('tim_id')->every(fn ($id) => in_array($id, $timBaru)))->toBeTrue();
});

it('Tim::filterJenis hanya mengembalikan tim dari jenis itu', function () {
    $eventUiux = Event::factory()->uiux()->create();
    $eventIct = Event::factory()->ict()->create();

    $timUiux = Tim::factory()->count(2)->create(['event_id' => $eventUiux->id]);
    Tim::factory()->create(['event_id' => $eventIct->id]);

    $hasil = Tim::filterJenis('uiux')->pluck('id')->sort()->values()->all();

    expect($hasil)->toBe($timUiux->pluck('id')->sort()->values()->all());
    expect(Tim::filterJenis(null)->count())->toBe(3);
});
