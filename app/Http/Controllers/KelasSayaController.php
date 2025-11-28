<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;

class KelasSayaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        
        // Get students from classes where the logged-in teacher is the wali kelas
        $query = Siswa::with(['kelas'])
            ->whereHas('kelas', function($query) use ($user) {
                $query->where('guru', $user->id);
            });

        // Apply search filter if search term is provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_siswa', 'like', '%' . $search . '%')
                  ->orWhere('nis', 'like', '%' . $search . '%')
                  ->orWhere('nisn', 'like', '%' . $search . '%')
                  ->orWhereHas('kelas', function($kelasQuery) use ($search) {
                      $kelasQuery->where('nama_kelas', 'like', '%' . $search . '%');
                  });
            });
        }

        $items = $query->orderBy('nama_siswa')
            ->paginate(10)
            ->withQueryString(); // Preserve query string for pagination

        return view('guru.kelas_saya', [
            'title' => 'Siswa Saya',
            'page_title' => 'Daftar Siswa yang Diampu',
            'items' => $items,
            'search' => $search,
        ]);
    }
}