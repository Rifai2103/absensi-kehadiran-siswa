# Fitur Import/Export Data Pengguna

## 📋 Deskripsi
Fitur ini memungkinkan admin untuk:
- **Import** data pengguna dalam jumlah banyak dari file Excel
- **Export** semua data pengguna ke file Excel
- **Download Template** Excel untuk memudahkan import data

## 🎯 Fitur Utama

### 1. Download Template Excel
- Klik tombol **"Download Template"** di halaman Kelola Pengguna
- Template berisi:
  - Header kolom yang sudah terformat
  - 3 baris contoh data (Admin, Guru, Kepala Sekolah)
  - Styling header (background biru, teks putih)

### 2. Import Data Pengguna
**Langkah-langkah:**
1. Download template Excel terlebih dahulu
2. Isi data pengguna sesuai format template:
   - **Nama Lengkap**: Nama lengkap pengguna (wajib)
   - **Email**: Email valid dan unique (wajib, untuk login)
   - **Password**: Password minimal 6 karakter (wajib, akan di-hash otomatis)
   - **Peran**: admin, guru, atau kepala_sekolah (wajib, case-insensitive)
   - **No. Telepon**: Nomor telepon (opsional)
3. Klik tombol **"Import Excel"**
4. Pilih file Excel yang sudah diisi
5. Klik **"Import"**

**Validasi:**
- File harus format .xlsx atau .xls
- Ukuran maksimal 2MB
- Email harus unique (tidak boleh duplikat)
- Role harus salah satu dari: admin, guru, kepala_sekolah
- Password minimal 6 karakter
- Data yang duplikat akan dilewati

**Hasil Import:**
- Menampilkan jumlah data yang berhasil diimport
- Menampilkan jumlah data yang dilewati (duplikat/error)
- Menampilkan detail error jika ada
- **Password otomatis di-hash** untuk keamanan

### 3. Export Data Pengguna
- Klik tombol **"Export Data"** di halaman Kelola Pengguna
- File Excel akan otomatis terdownload dengan nama: `data_pengguna_YYYY-MM-DD.xlsx`
- Berisi semua data pengguna yang ada di sistem (tanpa password untuk keamanan)

## 📊 Format Template Excel

| Nama Lengkap | Email | Password | Peran (admin/guru/kepala_sekolah) | No. Telepon |
|--------------|-------|----------|-----------------------------------|-------------|
| John Doe | john.doe@example.com | password123 | admin | 081234567890 |
| Jane Smith | jane.smith@example.com | password123 | guru | 081234567891 |
| Robert Johnson | robert.johnson@example.com | password123 | kepala_sekolah | 081234567892 |

## 🔐 Keamanan

1. **Password di-hash otomatis** saat import menggunakan bcrypt
2. **Export tidak menyertakan password** untuk keamanan
3. **Email harus unique** untuk mencegah duplikasi akun
4. **Validasi role** memastikan hanya role yang valid yang bisa diimport

## 🎨 Tampilan UI

**Tombol-tombol di halaman Kelola Pengguna:**
- 🟢 **Import Excel** - Membuka modal import
- 🔵 **Download Template** - Download template Excel
- ⚫ **Export Data** - Download semua data pengguna

**Modal Import:**
- Petunjuk penggunaan yang jelas
- File upload dengan custom file input
- Validasi client-side
- Loading state saat import

## 📝 Catatan Penting

1. **Email harus unique** - tidak boleh ada duplikat
2. **Password akan di-hash** - tidak perlu hash manual di Excel
3. **Role case-insensitive** - bisa tulis "Admin", "admin", atau "ADMIN"
4. **Data yang error akan dilewati**, tidak membatalkan seluruh import
5. **Template dapat diupdate** sesuai kebutuhan di `app/Exports/UserTemplateExport.php`

## 🐛 Troubleshooting

**Q: Import gagal dengan error "Email sudah ada"**
A: Email harus unique. Cek apakah email sudah terdaftar di sistem

**Q: Import gagal dengan error "Role tidak valid"**
A: Pastikan role adalah salah satu dari: admin, guru, atau kepala_sekolah

**Q: Password tidak bisa login setelah import**
A: Password di-hash otomatis saat import. Gunakan password yang Anda tulis di Excel

**Q: File Excel tidak bisa diupload**
A: Pastikan ukuran file tidak lebih dari 2MB dan format .xlsx atau .xls

## 📂 File-file Terkait

- **Controller**: `app/Http/Controllers/UserController.php`
- **Export Template**: `app/Exports/UserTemplateExport.php`
- **Export Data**: `app/Exports/UserExport.php`
- **Import**: `app/Imports/UserImport.php`
- **Routes**: `routes/web.php`
- **View**: `resources/views/crud/index.blade.php`

## 🔄 Perbedaan dengan Import Siswa

| Aspek | Import Siswa | Import Pengguna |
|-------|--------------|-----------------|
| Field Utama | NIS, NISN, Nama, Kelas | Email, Password, Role |
| Validasi Unique | NIS, NISN | Email |
| Relasi | Kelas (harus ada) | Tidak ada |
| Password | Tidak ada | Di-hash otomatis |
| Security | Biasa | Tinggi (password hashing) |

## ✅ Checklist Sebelum Import

- [ ] Template sudah didownload
- [ ] Data sudah diisi sesuai format
- [ ] Email semua unique (tidak ada duplikat)
- [ ] Role sudah benar (admin/guru/kepala_sekolah)
- [ ] Password minimal 6 karakter
- [ ] File format .xlsx atau .xls
- [ ] Ukuran file < 2MB

---

**Status**: ✅ Selesai dan siap digunakan!
