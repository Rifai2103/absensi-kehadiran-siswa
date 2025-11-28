<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Exports\UserTemplateExport;
use App\Exports\UserExport;
use App\Imports\UserImport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    private function title(): string { return 'Pengguna'; }
    private function routePrefix(): string { return 'users'; }

    private function fields(): array
    {
        return [
            'nama_lengkap' => ['label' => 'Nama Lengkap', 'type' => 'text'],
            'email' => ['label' => 'Email', 'type' => 'email'],
            'password' => ['label' => 'Password', 'type' => 'password'],
            'role' => ['label' => 'Peran', 'type' => 'select', 'options' => 'roles'],
            'no_telepon' => ['label' => 'No. Telepon', 'type' => 'text'],
        ];
    }

    private function columns(): array
    {
        return ['Nama Lengkap', 'Email', 'Peran', 'No. Telepon'];
    }

    private function options(string $key): array
    {
        return match ($key) {
            'roles' => [
                ['value' => 'admin', 'label' => 'Admin'],
                ['value' => 'guru', 'label' => 'Guru'],
                ['value' => 'kepala_sekolah', 'label' => 'Kepala Sekolah'],
            ],
            default => [],
        };
    }

    private function buildFields(array $fields, $item = null, array $overrides = []): array
    {
        $out = [];
        foreach ($fields as $name => $def) {
            $field = $def;
            $field['name'] = $name;
            // Jangan pernah prefill password
            if ($name === 'password') {
                $field['value'] = '';
            } else {
                $field['value'] = old($name, $item->{$name} ?? null);
            }
            if (($def['type'] ?? null) === 'select') {
                $key = $def['options'] ?? null;
                $field['options'] = $key ? $this->options($key) : [];
            }
            if (isset($overrides[$name]) && is_array($overrides[$name])) {
                $field = array_merge($field, $overrides[$name]);
            }
            $out[] = $field;
        }
        return $out;
    }

    public function index()
    {
        $items = User::latest()->paginate(10);
        $rows = $items->getCollection()->map(function($item){
            return [
                'id' => $item->id,
                'name' => $item->nama_lengkap,
                'cols' => [
                    $item->nama_lengkap,
                    $item->email ?? '-',
                    ucfirst(str_replace('_',' ', $item->role)),
                    $item->no_telepon ?? '-',
                ],
            ];
        });

        return view('crud.index', [
            'title' => 'Kelola ' . $this->title(),
            'page_title' => 'Kelola ' . $this->title(),
            'routePrefix' => $this->routePrefix(),
            'paramKey' => 'user',
            'headers' => $this->columns(),
            'items' => $items,
            'rows' => $rows,
        ]);
    }

    public function create()
    {
        $fields = $this->buildFields($this->fields());
        return view('crud.form', [
            'title' => 'Tambah ' . $this->title(),
            'page_title' => 'Tambah ' . $this->title(),
            'routePrefix' => $this->routePrefix(),
            'mode' => 'create',
            'fields' => $fields,
            'action' => route($this->routePrefix() . '.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_lengkap' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:6'],
            'role' => ['required', Rule::in(['admin','guru','kepala_sekolah'])],
            'no_telepon' => ['nullable','string','max:20'],
        ]);

        $user = new User();
        $user->nama_lengkap = $data['nama_lengkap'];
        $user->email = $data['email'];
        // cast 'hashed' pada model akan meng-hash otomatis
        $user->password_hash = $data['password'];
        $user->role = $data['role'];
        $user->no_telepon = $data['no_telepon'] ?? null;
        $user->save();

        return redirect()->route($this->routePrefix().'.index')->with('success', $this->title().' berhasil ditambahkan');
    }

    public function show(User $user)
    {
        // Sembunyikan field password pada halaman detail
        $fields = $this->fields();
        unset($fields['password']);
        return view('crud.show', [
            'title' => 'Detail ' . $this->title(),
            'page_title' => 'Detail ' . $this->title(),
            'routePrefix' => $this->routePrefix(),
            'paramKey' => 'user',
            'item' => $user,
            'fields' => $this->buildFields($fields, $user),
        ]);
    }

    public function edit(User $user)
    {
        $fields = $this->buildFields($this->fields(), $user, [
            'password' => ['label' => 'Password (kosongkan jika tidak diubah)']
        ]);
        return view('crud.form', [
            'title' => 'Ubah ' . $this->title(),
            'page_title' => 'Ubah ' . $this->title(),
            'routePrefix' => $this->routePrefix(),
            'mode' => 'edit',
            'fields' => $fields,
            'action' => route($this->routePrefix() . '.update', $user->id),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'nama_lengkap' => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'password' => ['nullable','string','min:6'],
            'role' => ['required', Rule::in(['admin','guru','kepala_sekolah'])],
            'no_telepon' => ['nullable','string','max:20'],
        ]);

        $user->nama_lengkap = $data['nama_lengkap'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password_hash = $data['password'];
        }
        $user->role = $data['role'];
        $user->no_telepon = $data['no_telepon'] ?? null;
        $user->save();

        return redirect()->route($this->routePrefix().'.index')->with('success', $this->title().' berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        // Cegah penghapusan jika menjadi wali kelas (FK kelas.guru restrict)
        if (Kelas::where('guru', $user->id)->exists()) {
            return redirect()->route($this->routePrefix().'.index')
                ->with('error', 'Tidak dapat menghapus pengguna karena menjadi wali kelas di salah satu kelas. Pindahkan wali kelas terlebih dahulu.');
        }

        $user->delete();
        return redirect()->route($this->routePrefix().'.index')->with('success', $this->title().' berhasil dihapus');
    }

    /**
     * Download template Excel untuk import pengguna
     */
    public function downloadTemplate()
    {
        return Excel::download(new UserTemplateExport, 'template_import_pengguna.xlsx');
    }

    /**
     * Import pengguna dari file Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048'
        ], [
            'file.required' => 'File Excel wajib dipilih',
            'file.mimes' => 'File harus berformat Excel (.xlsx atau .xls)',
            'file.max' => 'Ukuran file maksimal 2MB'
        ]);

        try {
            $import = new UserImport();
            Excel::import($import, $request->file('file'));

            $imported = $import->getImportedCount();
            $skipped = $import->getSkippedCount();
            $failures = $import->failures();

            $message = "Import selesai! {$imported} pengguna berhasil diimport.";

            if ($skipped > 0) {
                $message .= " {$skipped} data dilewati (duplikat atau role tidak valid).";
            }

            if ($failures->count() > 0) {
                $errorMessages = [];
                foreach ($failures as $failure) {
                    $errorMessages[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
                }
                return redirect()->route($this->routePrefix().'.index')
                    ->with('warning', $message . ' Namun ada ' . $failures->count() . ' baris dengan error.')
                    ->with('errors', $errorMessages);
            }

            return redirect()->route($this->routePrefix().'.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route($this->routePrefix().'.index')
                ->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    /**
     * Export semua data pengguna ke Excel
     */
    public function export()
    {
        return Excel::download(new UserExport, 'data_pengguna_' . date('Y-m-d') . '.xlsx');
    }
}
