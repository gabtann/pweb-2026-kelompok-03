<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Http\Requests\Panitia\StoreAkunRequest;
use App\Http\Requests\Panitia\UpdateAkunRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AkunController extends Controller
{
    private const ROLE = [
        'panitia' => 'Panitia',
        'peserta' => 'Peserta',
    ];

    public function index(Request $request): Response
    {
        $q = $request->query('q');
        $q = is_string($q) && trim($q) !== '' ? trim($q) : null;

        $role = $request->query('role');
        $role = is_string($role) && array_key_exists($role, self::ROLE) ? $role : null;

        $akun = User::query()
            ->when($q, fn ($query) => $query->where(function ($w) use ($q) {
                $like = '%'.$q.'%';

                $w->where('nama_lengkap', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('no_telepon', 'like', $like);
            }))
            ->when($role, fn ($query) => $query->where('role', $role))
            ->withCount('timDiketuai')
            ->orderBy('nama_lengkap')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (User $user) => [
                'id' => $user->id,
                'nama_lengkap' => $user->nama_lengkap,
                'email' => $user->email,
                'no_telepon' => $user->no_telepon,
                'role' => $user->role,
                'label_role' => self::ROLE[$user->role] ?? $user->role,
                // Hanya akun peserta tanpa tim yang boleh dihapus (lihat destroy()).
                'dapat_dihapus' => $user->isPeserta() && $user->tim_diketuai_count === 0,
            ]);

        return Inertia::render('Panitia/Akun/Index', [
            'akun' => $akun,
            'filters' => ['q' => $q, 'role' => $role],
            'roles' => $this->opsiRole(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Panitia/Akun/Create');
    }

    public function store(StoreAkunRequest $request): RedirectResponse
    {
        // role TIDAK dari input dan tidak ada di fillable: akun baru selalu peserta.
        $user = new User($request->validated());
        $user->role = 'peserta';
        $user->save();

        return redirect()->route('panitia.akun.index')->with('success', 'Akun berhasil ditambahkan.');
    }

    public function edit(User $akun): Response
    {
        return Inertia::render('Panitia/Akun/Edit', [
            'akun' => [
                'id' => $akun->id,
                'nama_lengkap' => $akun->nama_lengkap,
                'email' => $akun->email,
                'no_telepon' => $akun->no_telepon,
                'role' => $akun->role,
                'label_role' => self::ROLE[$akun->role] ?? $akun->role,
            ],
        ]);
    }

    public function update(UpdateAkunRequest $request, User $akun): RedirectResponse
    {
        $data = $request->validated();

        // Password kosong berarti tidak diganti.
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $akun->update($data); // role tidak di fillable, jadi tidak bisa berubah lewat sini.

        return redirect()->route('panitia.akun.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(User $akun): RedirectResponse
    {
        if ($akun->isPanitia()) {
            return back()->with('error', 'Akun panitia tidak bisa dihapus.');
        }

        if ($akun->timDiketuai()->exists()) {
            return back()->with('error', 'Akun tidak bisa dihapus karena sudah menjadi ketua tim terdaftar.');
        }

        $akun->delete();

        return redirect()->route('panitia.akun.index')->with('success', 'Akun berhasil dihapus.');
    }

    /** @return list<array{value: string, label: string}> */
    private function opsiRole(): array
    {
        return collect(self::ROLE)
            ->map(fn (string $label, string $value) => ['value' => $value, 'label' => $label])
            ->values()
            ->all();
    }
}
