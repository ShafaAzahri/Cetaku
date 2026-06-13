# Cetaku

Aplikasi berbasis web yang dibangun menggunakan framework [Laravel](https://laravel.com/) dan Node.js.

## Persyaratan Sistem

Sebelum melakukan instalasi, pastikan sistem/komputer Anda telah memenuhi persyaratan berikut:
- PHP >= 8.1 (atau sesuai versi Laravel yang Anda gunakan)
- [Composer](https://getcomposer.org/)
- [Node.js & NPM](https://nodejs.org/)
- MySQL / MariaDB (bisa menggunakan XAMPP, Laragon, dll)

## Panduan Instalasi (Lokal)

Ikuti langkah-langkah di bawah ini untuk menginstal dan menjalankan project secara lokal:

### 1. Buka Terminal di Folder Project
Buka terminal (Command Prompt, PowerShell, atau Git Bash) dan arahkan ke direktori project Anda:
```bash
cd c:\laragon\www\cetakufinal
```

### 2. Install Dependensi Backend (PHP)
Jalankan perintah Composer untuk menginstal seluruh library PHP yang dibutuhkan Laravel:
```bash
composer install
```

### 3. Install Dependensi Frontend (Node.js)
Jalankan NPM untuk menginstal library JavaScript (seperti Vite, CSS preprocessors, atau UI framework):
```bash
npm install
```

### 4. Buat Database dan Import File SQL
Sebelum mengonfigurasi environment, pastikan Anda sudah membuat dan meng-import database terlebih dahulu:
1. Buka aplikasi manajemen database Anda (seperti **phpMyAdmin**, **HeidiSQL**, **DBeaver**, atau via **Laragon**).
2. Buat database kosong baru dengan nama sesuai keinginan Anda (contoh: `db_cetaku`).
3. Pilih database tersebut, kemudian gunakan fitur **Import**.
4. Pilih file database berformat `*.sql` yang Anda miliki (biasanya disertakan bersama source code).
5. Proses import file tersebut hingga sukses (muncul tabel-tabel database).

### 5. Konfigurasi File Environment
Gandakan file `.env.example` dan ubah namanya menjadi `.env`.
Jika menggunakan Windows (Command Prompt / PowerShell):
```bash
copy .env.example .env
```
Atau Anda bisa meng-copy dan me-rename file tersebut secara manual melalui File Explorer.

Buka file `.env` yang baru dibuat di code editor Anda (misal: VS Code), lalu sesuaikan konfigurasi koneksi database Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_cetaku           # Ubah sesuai nama database yang Anda buat pada langkah 4
DB_USERNAME=root                # Biasanya root jika menggunakan Laragon/XAMPP
DB_PASSWORD=                    # Biasanya kosong jika menggunakan Laragon/XAMPP
```

### 6. Generate Application Key
Jalankan perintah ini untuk men-generate kunci keamanan aplikasi Laravel:
```bash
php artisan key:generate
```

### 7. Migrasi Database (Opsional / Lewati)
> **Catatan Penting:** Karena Anda sudah meng-import file `.sql` secara manual pada Langkah 4, Anda **TIDAK PERLU** menjalankan perintah migrasi di bawah ini. Abaikan langkah ini.

Namun, jika sewaktu-waktu Anda ingin membuat ulang seluruh struktur database dari nol menggunakan sistem migrasi bawaan Laravel, jalankan:
```bash
php artisan migrate
```

### 8. Build Aset (CSS / JS)
Untuk me-compile file CSS dan JavaScript (Vite), jalankan:

Untuk tahap **development** (rekomendasi, akan terus berjalan untuk memantau perubahan file):
```bash
npm run dev
```
Untuk tahap **production** (build final sekali jalan):
```bash
npm run build
```

### 9. Jalankan Local Server
Jika Anda menggunakan **Laragon**, Anda mungkin sudah bisa langsung mengaksesnya melalui browser di alamat:
**http://cetakufinal.test**

Namun, jika Anda ingin menjalankan server bawaan Laravel, buka tab terminal baru (biarkan `npm run dev` di tab lain tetap berjalan), lalu ketik:
```bash
php artisan serve
```
Aplikasi kini dapat diakses di **http://localhost:8000**.

---

## E2E Testing menggunakan Playwright (Opsional)
Project ini dilengkapi dengan konfigurasi [Playwright](https://playwright.dev/) untuk pengujian End-to-End.

Jika Anda ingin menjalankan test:
1. Install browser yang dibutuhkan Playwright (hanya perlu dijalankan sekali):
   ```bash
   npx playwright install
   ```
2. Jalankan proses testing:
   ```bash
   npx playwright test
   ```
3. Untuk melihat laporan (report) hasil testing:
   ```bash
   npx playwright show-report
   ```
