<?php

namespace App\Http\Controllers;

use App\Models\AbsensiHarian;
use App\Models\Siswa;
use App\Models\Perangkat;
use Illuminate\Http\Request;

class AbsensiHarianController extends Controller
{
    private function title(): string
    {
        return 'Absensi Harian';
    }
    private function routePrefix(): string
    {
        return 'absensi-harian';
    }

    private function fields(): array
    {
        return [
            'siswa_id' => ['label' => 'Siswa', 'type' => 'select', 'options' => 'siswa_list', 'rules' => 'required|exists:siswa,id'],
            'perangkat_masuk_id' => ['label' => 'Perangkat Masuk', 'type' => 'select', 'options' => 'perangkat_list', 'rules' => 'required|exists:perangkat,id'],
            'perangkat_pulang_id' => ['label' => 'Perangkat Pulang', 'type' => 'select', 'options' => 'perangkat_list', 'rules' => 'nullable|exists:perangkat,id'],
            'waktu_masuk' => ['label' => 'Waktu Masuk', 'type' => 'datetime-local', 'rules' => 'nullable|date'],
            'waktu_pulang' => [
                'label' => 'Waktu Pulang',
                'type' => 'datetime-local',
                'rules' => 'nullable|date'
            ],

            'status_kehadiran' => [
                'label' => 'Status Kehadiran',
                'type' => 'select',
                'options' => 'status_list',
                'rules' => 'required|in:hadir,izin,sakit,alpa,terlambat'
            ]
        ];
    }

    private function columns(): array
    {
        return ['Siswa', 'Perangkat', 'Masuk', 'Pulang'];
    }

    private function options(string $key): array
    {
        return match ($key) {
            'siswa_list' => Siswa::orderBy('nama_siswa')->get(['id', 'nama_siswa'])->map(fn($s) => ['value' => $s->id, 'label' => $s->nama_siswa])->toArray(),
            'perangkat_list' => Perangkat::orderBy('nama_perangkat')->get(['id', 'nama_perangkat'])->map(fn($p) => ['value' => $p->id, 'label' => $p->nama_perangkat])->toArray(),
            'status_list' => [
                ['value' => 'hadir', 'label' => 'Hadir'],
                ['value' => 'izin', 'label' => 'Izin'],
                ['value' => 'sakit', 'label' => 'Sakit'],
                ['value' => 'alpa', 'label' => 'Alpa'],
                ['value' => 'terlambat', 'label' => 'Terlambat'],
            ],

            default => [],
        };
    }



    private function buildFields(array $fields, $item = null): array
    {
        $out = [];
        foreach ($fields as $name => $def) {
            $field = $def;
            $field['name'] = $name;
            $value = old($name, $item->{$name} ?? null);
            // normalize datetime-local value to HTML format: Y-m-dTH:i
            if (in_array(($def['type'] ?? ''), ['datetime-local']) && $value) {
                $value = \Carbon\Carbon::parse($value)->format('Y-m-d\\TH:i');
            }
            $field['value'] = $value;
            if (($def['type'] ?? null) === 'select') {
                $key = $def['options'] ?? null;
                $field['options'] = $key ? $this->options($key) : [];
            }
            $out[] = $field;
        }
        return $out;
    }

    private function convertDateTime($val)
    {
        if (!$val)
            return null;
        return str_replace('T', ' ', $val) . ':00';
    }


    public function index()
    {
        $items = AbsensiHarian::with(['siswa', 'perangkat'])->latest()->paginate(10);
        $rows = $items->getCollection()->map(function ($item) {
            $masuk = $item->waktu_masuk ? \Carbon\Carbon::parse($item->waktu_masuk)->format('d-m-Y H:i') : '-';
            $pulang = $item->waktu_pulang ? \Carbon\Carbon::parse($item->waktu_pulang)->format('d-m-Y H:i') : '-';
            return [
                'id' => $item->id,
                'cols' => [
                    $item->siswa->nama_siswa ?? '-',
                    $item->perangkat->nama_perangkat ?? '-',
                    $masuk,
                    $pulang,
                ],
            ];
        });

        return view('crud.index', [
            'title' => 'Kelola ' . $this->title(),
            'page_title' => 'Kelola ' . $this->title(),
            'routePrefix' => $this->routePrefix(),
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
        $rules = collect($this->fields())
            ->mapWithKeys(fn($v, $k) => [$k => $v['rules'] ?? ''])
            ->filter()
            ->toArray();

        $data = $request->validate($rules);

        $model = new AbsensiHarian();

        foreach ($this->fields() as $name => $_) {

            if (in_array($name, ['waktu_masuk', 'waktu_pulang'])) {
                $model->{$name} = $this->convertDateTime($data[$name] ?? null);
            } else {
                $model->{$name} = $data[$name] ?? null;
            }
        }

        // Jika tanggal kosong → ambil dari waktu_masuk
        if (!$model->tanggal && $model->waktu_masuk) {
            $model->tanggal = substr($model->waktu_masuk, 0, 10);
        }

        $model->save();

        return redirect()->route($this->routePrefix() . '.index')
            ->with('success', $this->title() . ' berhasil ditambahkan');
    }


    public function show(AbsensiHarian $absensi_harian)
    {
        return view('crud.show', [
            'title' => 'Detail ' . $this->title(),
            'page_title' => 'Detail ' . $this->title(),
            'routePrefix' => $this->routePrefix(),
            'item' => $absensi_harian,
            'fields' => $this->buildFields($this->fields(), $absensi_harian),
        ]);
    }

    public function edit(AbsensiHarian $absensi_harian)
    {
        $fields = $this->buildFields($this->fields(), $absensi_harian);
        return view('crud.form', [
            'title' => 'Ubah ' . $this->title(),
            'page_title' => 'Ubah ' . $this->title(),
            'routePrefix' => $this->routePrefix(),
            'mode' => 'edit',
            'fields' => $fields,
            'action' => route($this->routePrefix() . '.update', $absensi_harian->id),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, AbsensiHarian $absensi_harian)
    {
        $rules = collect($this->fields())
            ->mapWithKeys(fn($v, $k) => [$k => $v['rules'] ?? ''])
            ->filter()
            ->toArray();

        $data = $request->validate($rules);

        foreach ($this->fields() as $name => $_) {

            if (in_array($name, ['waktu_masuk', 'waktu_pulang'])) {
                $absensi_harian->{$name} = $this->convertDateTime($data[$name] ?? null);
            } else {
                $absensi_harian->{$name} = $data[$name] ?? null;
            }
        }

        // Auto set tanggal
        if (!$absensi_harian->tanggal && $absensi_harian->waktu_masuk) {
            $absensi_harian->tanggal = substr($absensi_harian->waktu_masuk, 0, 10);
        }

        $absensi_harian->save();

        return redirect()->route($this->routePrefix() . '.index')
            ->with('success', $this->title() . ' berhasil diperbarui');
    }


    public function destroy(AbsensiHarian $absensi_harian)
    {
        $absensi_harian->delete();
        return redirect()->route($this->routePrefix() . '.index')->with('success', $this->title() . ' berhasil dihapus');
    }
}

