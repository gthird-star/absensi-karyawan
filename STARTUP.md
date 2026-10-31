# STARTUP — Panduan Cepat Menjalankan Aplikasi Lokal

Dokumentasi singkat untuk menjalankan aplikasi **Absensi Karyawan Soerabaja45** di mesin lokal tanpa Docker.

---

## Prasyarat Awal (Instalasi Satu Kali Saja)

Pastikan sudah terinstall di mesin Anda:

1. **PHP 8.5+** — verifikasi dengan: `php --version`
2. **Composer** — verifikasi dengan: `composer --version`
3. **Node.js 20+** atau **Bun** — verifikasi dengan: `node --version`
4. **MySQL 8.0+** di port `3306` — user: `root`, password: kosong (default)

Jika belum, download dari:
- PHP: https://www.php.net/downloads
- Composer: https://getcomposer.org/download/
- Node.js: https://nodejs.org/
- MySQL: https://dev.mysql.com/downloads/mysql/

---

## Setup Awal (Pertama Kali Saja)

Jalankan perintah ini **satu kali saja** saat setup awal:

```sh
# 1. Clone/extract repository (jika belum)
cd "D:\Website Ujicoba\Absensi Karyawan"

# 2. Install dependency PHP
composer install

# 3. Install dependency JavaScript
npm install

# 4. Buat file .env (jika belum ada)
cp .env.example .env

# 5. Generate APP_KEY
php artisan key:generate --force

# 6. Link storage ke public
php artisan storage:link

# 7. Buat database
# Buka MySQL dan jalankan:
# CREATE DATABASE IF NOT EXISTS absensi_karyawan_db;

# 8. Setup database (migrate) + seeder demo
php artisan migrate:fresh --seed

# 9. Build asset CSS/JS
npm run build
```

Setelah ini, database terisi dengan:
- **Master data:** Divisi, Pendidikan, Jabatan, Shift, Cabang, Hari Libur
- **50 Karyawan demo** dengan profile photos (Picsum)
- **Admin + Superadmin accounts**
- **3000+ attendance records** (Okt–Des 2026)

---

## Startup Harian (Setiap Kali Ingin Menjalankan)

```sh
cd "D:\Website Ujicoba\Absensi Karyawan"

# 1. Build asset (jika ada perubahan code JavaScript/CSS)
npm run build

# 2. Jalankan server
php artisan serve --host=0.0.0.0 --port=8000
```

Server akan berjalan di: **http://localhost:8000**

Tekan `Ctrl+C` untuk stop server.

---

## Akun Demo

Login dengan akun berikut (password sama dengan username):

| Role       | Email                    | Password     | Akses                                |
| ---------- | ------------------------ | ------------ | ------------------------------------ |
| Superadmin | `superadmin@example.com` | `superadmin` | Semua menu admin                     |
| Admin      | `admin@example.com`      | `admin`      | Admin dashboard, master data, lapor |
| Karyawan   | `user@example.com`       | `password`   | Scan presensi, riwayat, pengajuan   |

Atau lihat di sidebar bawah (test account list).

---

## Port & URL

- **Aplikasi:** http://localhost:8000
- **MySQL:** localhost:3306 (user: `root`, password: kosong)
- **Database:** `absensi_karyawan_db`

---

## Troubleshooting

### ❌ "Port 8000 sudah dipakai"
Gunakan port lain:
```sh
php artisan serve --host=0.0.0.0 --port=8001
```

### ❌ "Database connection error"
1. Pastikan MySQL running (Services → MySQL80 atau equivalent)
2. Cek `.env` → `DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_USERNAME=root`, `DB_PASSWORD=` (kosong)
3. Buat database jika belum:
   ```sql
   CREATE DATABASE IF NOT EXISTS absensi_karyawan_db;
   ```

### ❌ "Asset not loading (CSS/JS blank)"
```sh
npm run build
php artisan optimize:clear
```

### ❌ "Storage link error"
```sh
php artisan storage:link
```

### ❌ "Ingin reset database ke data fresh"
```sh
php artisan migrate:fresh --seed
```
⚠️ **Ini akan hapus semua data & buat ulang dari seeder**.

### ❌ "Login stuck / session error"
```sh
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## Struktur File Penting

```
├── .env                          # Config lokal (DB, app key, dll)
├── app/                          # Logic aplikasi (Model, Controller, Service)
├── database/seeders/             # Data demo (DemoSeeder, AdminSeeder)
├── resources/views/              # Template Blade
├── routes/web.php                # URL routes
├── public/build/                 # Build asset (CSS/JS)
└── storage/app/public/           # File upload (photos, documents)
```

---

## Info Teknis

- **Framework:** Laravel 12
- **UI:** Livewire 3 + Tailwind + Alpine.js
- **Database:** MySQL
- **QR Code:** Leaflet.js (maps) + html5-qrcode (scanner)
- **Test DB:** `db_absensi_karyawan_test` (port 3306, untuk `php artisan test`)

---

## Catatan

- **Development Mode:** Jangan pakai production di `.env APP_ENV`
- **Browser:** Chrome/Edge recommended (GPS + QR scanner support)
- **Mobile:** Aplikasi responsif, buka via `http://<your-ip>:8000` dari phone
- **Demo Data:** Fixture Okt–Des 2026, update perlu manual atau re-seed

---

Butuh bantuan? Baca [README.md](./README.md) untuk informasi lebih detail tentang Docker, production setup, atau fitur-fitur lain.
