<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    protected $importedCount = 0;
    protected $skippedCount = 0;

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Cek apakah user dengan email sudah ada
        $existingByEmail = !empty($row['email']) ? User::where('email', $row['email'])->first() : null;

        if ($existingByEmail) {
            $this->skippedCount++;
            Log::warning("User sudah ada dengan email: " . $row['email']);
            return null;
        }

        // Validasi role
        $role = strtolower(trim($row['peran_admingurukepalasekolah'] ?? ''));
        if (!in_array($role, ['admin', 'guru', 'kepala_sekolah'])) {
            $this->skippedCount++;
            Log::warning("Role tidak valid: " . $role);
            return null;
        }

        $this->importedCount++;

        return new User([
            'nama_lengkap' => $row['nama_lengkap'],
            'email' => $row['email'],
            'username' => explode('@', $row['email'])[0], // Generate username from email
            'password_hash' => Hash::make($row['password']), // Hash password
            'role' => $role,
            'no_telepon' => $row['no_telepon'] ?? null,
        ]);
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
            'peran_admingurukepalasekolah' => 'required|string',
            'no_telepon' => 'nullable|string|max:20',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'peran_admingurukepalasekolah.required' => 'Peran wajib diisi',
        ];
    }

    /**
     * Get imported count
     */
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    /**
     * Get skipped count
     */
    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }
}
