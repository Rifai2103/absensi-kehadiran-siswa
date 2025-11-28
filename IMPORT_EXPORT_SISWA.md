# Fitur Import/Export Data Siswa

## 📋 Deskripsi
Fitur ini memungkinkan admin untuk:
- **Import** data siswa dalam jumlah banyak dari file Excel
- **Export** semua data siswa ke file Excel
- **Download Template** Excel untuk memudahkan import data

## 🎯 Fitur Utama

### 1. Download Template Excel
- Klik tombol **"Download Template"** di halaman Data Siswa
- Template berisi:
  - Header kolom yang sudah terformat
  - 3 baris contoh data
  - Styling header (background biru, teks putih)

### 2. Import Data Siswa
**Langkah-langkah:**
1. Download template Excel terlebih dahulu
2. Isi data siswa sesuai format template:
   - **Nama Siswa**: Nama lengkap siswa (wajib)
   - **NIS**: Nomor Induk Siswa, max 20 karakter (opsional)
   - **NISN**: Nomor Induk Siswa Nasional, harus 10 digit (opsional)
   - **Jenis Kelamin**: L atau P (wajib)
   - **Nama Kelas**: Harus sesuai dengan nama kelas yang ada di sistem (wajib)
   - **Nama Orang Tua**: Nama orang tua/wali (opsional)
   - **No. Telepon Orang Tua**: Nomor telepon orang tua (opsional)
3. Klik tombol **"Import Excel"**
4. Pilih file Excel yang sudah diisi
5. Klik **"Import"**

**Validasi:**
- File harus format .xlsx atau .xls
- Ukuran maksimal 2MB
- Nama kelas harus sudah ada di sistem
- NIS dan NISN harus unique (tidak boleh duplikat)
- Data yang duplikat akan dilewati

**Hasil Import:**
- Menampilkan jumlah data yang berhasil diimport
- Menampilkan jumlah data yang dilewati (duplikat/error)
- Menampilkan detail error jika ada

### 3. Export Data Siswa
- Klik tombol **"Export Data"** di halaman Data Siswa
- File Excel akan otomatis terdownload dengan nama: `data_siswa_YYYY-MM-DD.xlsx`
- Berisi semua data siswa yang ada di sistem

## 📊 Format Template Excel

| Nama Siswa | NIS | NISN | Jenis Kelamin (L/P) | Nama Kelas | Nama Orang Tua | No. Telepon Orang Tua |
|------------|-----|------|---------------------|------------|----------------|-----------------------|
| Rizky Maulana | 20244A001 | 0034567801 | L | 4A - SDN 03 Kebayoran | Agus Maulana | 081234567890 |
| Dewi Lestari | 20244A002 | 0034567802 | P | 4A - SDN 03 Kebayoran | Rina Lestari | 081234567891 |

## 🔧 Teknologi yang Digunakan

- **Laravel Excel (Maatwebsite)** v3.1
- **PhpSpreadsheet** untuk styling Excel
- **Bootstrap 4 Modal** untuk UI import
- **Laravel Validation** untuk validasi data

## 📝 Catatan Penting

1. **Nama Kelas harus exact match** dengan data di sistem
2. **NIS dan NISN bersifat unique** - tidak boleh ada duplikat
3. **Data yang error akan dilewati**, tidak membatalkan seluruh import
4. **Pastikan kelas sudah dibuat** sebelum import siswa
5. **Template dapat diupdate** sesuai kebutuhan di `app/Exports/SiswaTemplateExport.php`

## 🐛 Troubleshooting

**Q: Import gagal dengan error "Kelas tidak ditemukan"**
A: Pastikan nama kelas di Excel sama persis dengan nama kelas di sistem (case-sensitive)

**Q: Data tidak terimport meskipun tidak ada error**
A: Cek apakah NIS/NISN sudah ada di database (duplikat akan dilewati)

**Q: File Excel tidak bisa diupload**
A: Pastikan ukuran file tidak lebih dari 2MB dan format .xlsx atau .xls

## 📂 File-file Terkait

- **Controller**: `app/Http/Controllers/SiswaController.php`
- **Export Template**: `app/Exports/SiswaTemplateExport.php`
- **Export Data**: `app/Exports/SiswaExport.php`
- **Import**: `app/Imports/SiswaImport.php`
- **Routes**: `routes/web.php`
- **View**: `resources/views/crud/index.blade.php`
