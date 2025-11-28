# Perubahan Sistem Pengguna: Username → Email

## 📋 Ringkasan Perubahan

Sistem pengguna telah diubah dari menggunakan **username** menjadi **email** sebagai identitas login. Perubahan ini dilakukan untuk:
1. Meningkatkan keamanan (email lebih unik dan terverifikasi)
2. Memudahkan reset password
3. Standarisasi dengan sistem modern

## 🔄 Perubahan yang Dilakukan

### 1. **Database Migration** ✅
- **File**: `2025_11_28_040028_make_email_required_in_users_table.php`
- Email field sekarang **required** (not nullable)
- Email tetap **unique** untuk setiap user

### 2. **User Controller** ✅
- **File**: `app/Http/Controllers/UserController.php`
- **Form Input**: Username diganti dengan Email (type: email)
- **Tabel Display**: 
  - Kolom: `['Nama Lengkap', 'Email', 'Peran', 'No. Telepon']`
  - Menampilkan email dan role user
- **Validasi**:
  - Create: `email` required, unique, max 255 karakter
  - Update: `email` required, unique (ignore current user), max 255 karakter

### 3. **Auth System** ✅
- **File**: `app/Http/Controllers/AuthController.php`
- Login sudah menggunakan email (tidak perlu diubah)
- Credentials: `['email' => ..., 'password' => ...]`

### 4. **Seeder Data** ✅
- **File**: `database/seeders/RoleUserSeeder.php`
- Semua user demo sudah memiliki email:
  - Admin: `admin@example.com`
  - Guru: `guru@example.com`
  - Kepala Sekolah: `kepala@example.com`

## 📊 Struktur Tabel Users

| Field | Type | Nullable | Unique | Keterangan |
|-------|------|----------|--------|------------|
| id | bigint | No | Yes | Primary key |
| nama_lengkap | varchar(255) | No | No | Nama lengkap user |
| username | varchar(100) | No | Yes | Username (deprecated, masih ada untuk backward compatibility) |
| **email** | **varchar(255)** | **No** | **Yes** | **Email untuk login** |
| password_hash | varchar(255) | No | No | Password (hashed) |
| role | enum | No | No | admin, guru, kepala_sekolah |
| no_telepon | varchar(20) | Yes | No | Nomor telepon |

## 🎯 Cara Menggunakan

### **Menambah User Baru:**
1. Buka halaman **Kelola Pengguna** (`/users`)
2. Klik **"Tambah Data"**
3. Isi form:
   - **Nama Lengkap**: Nama lengkap user
   - **Email**: Email valid dan unik (untuk login)
   - **Password**: Minimal 6 karakter
   - **Peran**: Pilih role (Admin/Guru/Kepala Sekolah)
   - **No. Telepon**: Opsional
4. Klik **"Simpan"**

### **Login:**
1. Buka halaman login (`/login`)
2. Masukkan **Email** dan **Password**
3. Klik **"Login"**

### **Akun Demo:**
| Email | Password | Role |
|-------|----------|------|
| admin@example.com | password | Admin |
| guru@example.com | password | Guru |
| kepala@example.com | password | Kepala Sekolah |

## 📝 Catatan Penting

1. ✅ **Email harus unique** - tidak boleh ada duplikat
2. ✅ **Email required** - wajib diisi saat create/update user
3. ✅ **Username masih ada** di database untuk backward compatibility
4. ✅ **Login menggunakan email**, bukan username
5. ✅ **Tabel menampilkan Email dan Peran** untuk informasi yang lebih jelas

## 🔧 File-file yang Dimodifikasi

1. ✅ `database/migrations/2025_11_28_040028_make_email_required_in_users_table.php` - Migration email required
2. ✅ `app/Http/Controllers/UserController.php` - Update CRUD untuk email
3. ✅ `database/seeders/RoleUserSeeder.php` - Sudah ada email (tidak diubah)
4. ✅ `app/Http/Controllers/AuthController.php` - Sudah menggunakan email (tidak diubah)

## 🚀 Testing

Untuk menguji perubahan:

1. **Akses halaman pengguna**: `http://127.0.0.1:8000/users`
2. **Lihat tabel** - kolom sekarang menampilkan Email dan Peran
3. **Tambah user baru** - form sekarang menggunakan Email
4. **Login** dengan email dan password
5. **Edit user** - email dapat diubah (harus tetap unique)

---

**Status**: ✅ Selesai dan siap digunakan!
