# Redeploy RRCONS ke Shared Hosting (cPanel, bukan VPS)

Versi aplikasi: **Laravel 13 + PHP 8.5**. Paket rilis dibangun di komputer lokal (sudah berisi `vendor/`
produksi dan CSS yang sudah ter-compile), lalu diunggah lewat File Manager/FTP.

**Yang TIDAK dibutuhkan di hosting:** Node.js, Composer, Git, dan Terminal.
Tanpa Terminal, perintah `php artisan` dijalankan lewat **Cron Job** atau **web installer sekali pakai**
(bagian 4.7).

> Sudah diuji: gladi resik pada salinan database produksi (Docker dan MySQL XAMPP) menjalankan 11 migration baru
> (± 45–55 detik); simulasi hosting tanpa Terminal memakai web installer (struktur `rrcons/public` dan
> `public_html`) berhasil, semua halaman 200 dan komentar lama tetap tampil.

Ringkasan alur:

```
LOKAL                                         HOSTING (cPanel)
1. ambil kode main + test                     4.1 backup DB & file upload
2. gladi resik dengan dump DB produksi        4.3 upload + extract ZIP ke folder baru
3. build ZIP (scripts\build-release.ps1)  →   4.4 pindahkan .env & file upload, 4.5 tukar folder
                                              4.7 storage:link + migrate (Terminal / Cron / web installer)
                                              5.  cek website
```

---

## 0. Syarat hosting (cek sekali, sebelum mulai)

| Syarat | Di mana mengecek |
|---|---|
| **PHP 8.5** untuk domain ini | cPanel → *Select PHP Version* / *MultiPHP Manager* |
| Ekstensi PHP: `bcmath`, `ctype`, `curl`, `dom`, `exif`, `fileinfo`, `gd`, `intl`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `zip` | *Select PHP Version → Extensions* |
| Database MySQL/MariaDB + **phpMyAdmin** | *MySQL Databases*, *phpMyAdmin* |
| **File Manager** (upload & extract ZIP) atau akses FTP | *File Manager* |
| Salah satu cara menjalankan perintah: **Terminal**, **Cron Jobs**, atau fungsi PHP `symlink()` aktif (untuk web installer) | *Terminal* / *Cron Jobs* |

Bila PHP 8.5 belum tersedia, **jangan deploy**: aplikasi langsung berhenti dengan pesan
"Composer detected issues in your platform". Minta hosting mengaktifkannya terlebih dahulu.

---

## 1. Persiapan di komputer lokal — TANPA Docker

### 1.1 Yang perlu terpasang

| Program | Keterangan |
|---|---|
| PHP **8.5** (CLI) | ekstensi seperti di atas + `pdo_sqlite` & `sqlite3` (untuk test). Cek: `php -v`, `php -m` |
| Composer 2 | cek: `composer --version` |
| Git | cek: `git --version` |
| MySQL/MariaDB lokal | mis. XAMPP (`C:\xampp\mysql\bin\mysql.exe`, user `root` tanpa password) — untuk gladi resik |
| Node.js 22 (opsional) | hanya bila Anda mengubah class Tailwind di halaman publik |

### 1.2 Langkah

1. **Ambil kode terbaru**
   ```powershell
   cd "D:\PROYEK\PROYEK SOLVE IT\WEB\rrcons"
   git checkout main
   git pull
   composer install
   ```
2. **(Hanya bila tampilan publik diubah)** compile ulang CSS, lalu commit `public/front/css/app.css`:
   ```powershell
   npm ci
   npm run build:front-css
   ```
3. **Jalankan test** (SQLite in-memory, tidak menyentuh database apa pun):
   ```powershell
   php artisan test
   ```
4. **Gladi resik dengan data produksi terbaru** (sangat disarankan)
   1. Hosting → phpMyAdmin → pilih database → *Export* → *Quick*, format *SQL* → simpan, mis. `D:\backup\produksi.sql`.
   2. Atur `.env` lokal agar menunjuk MySQL XAMPP (isi database lokal ini akan **diganti**):
      ```dotenv
      DB_CONNECTION=mysql
      DB_HOST=127.0.0.1
      DB_PORT=3306
      DB_DATABASE=rrcons
      DB_USERNAME=root
      DB_PASSWORD=
      ```
   3. Impor dump dan jalankan migration persis seperti yang nanti terjadi di hosting:
      ```powershell
      powershell -ExecutionPolicy Bypass -File scripts\import-db.ps1 -File D:\backup\produksi.sql -Migrate -Native
      ```
      (`mysql.exe` di lokasi lain: tambahkan `-MysqlPath "C:\path\ke\mysql.exe"`.)
   4. Jalankan lalu cek situs:
      ```powershell
      php artisan serve
      ```
      Buka http://localhost:8000: Beranda, `?lang=en`, Blog, satu artikel, Contact, `/sitemap.xml`; lalu `/login`
      dengan akun admin produksi (passwordnya sama karena data dari produksi) → Dashboard, Blog → Articles,
      **Blog → Comments**. Agar gambar tampil, salin isi `storage/app/public` dari hosting ke folder yang sama di lokal (opsional).
5. **Bangun paket rilis**
   ```powershell
   powershell -ExecutionPolicy Bypass -File scripts\build-release.ps1
   # bila hosting TIDAK punya Terminal DAN Cron Job:
   powershell -ExecutionPolicy Bypass -File scripts\build-release.ps1 -WithWebInstaller
   ```
   Hasil: `release\rrcons-<commit>-<tanggal>.zip` (± 27 MB). Dengan `-WithWebInstaller`, URL + kunci web installer
   ditampilkan dan disimpan di `release\rrcons-<commit>-<tanggal>-installer.txt`. **Rahasiakan kunci itu.**

---

## 2. Persiapan di komputer lokal — DENGAN Docker

### 2.1 Yang perlu terpasang
Docker Desktop dan Git. PHP, Composer, MySQL, dan Node.js **tidak** perlu ada di Windows.

### 2.2 Langkah

1. **Ambil kode terbaru dan jalankan stack**
   ```powershell
   cd "D:\PROYEK\PROYEK SOLVE IT\WEB\rrcons"
   git checkout main
   git pull
   docker compose --env-file .env.docker up -d --build
   docker compose --env-file .env.docker exec app composer install
   ```
   Service `node` otomatis meng-compile ulang CSS Tailwind. Bila tampilan publik diubah, sebelum commit lakukan
   build bersih: `docker compose --env-file .env.docker restart node`.
2. **Jalankan test**
   ```powershell
   docker compose --env-file .env.docker exec app php artisan test
   ```
3. **Gladi resik dengan data produksi terbaru**
   1. Export database produksi dari phpMyAdmin (seperti 1.2 langkah 4.1).
   2. Impor ke MariaDB Docker lalu jalankan migration:
      ```powershell
      powershell -ExecutionPolicy Bypass -File scripts\import-db.ps1 -File D:\backup\produksi.sql -Migrate
      ```
   3. Buka http://localhost:8082 dan cek seperti 1.2 langkah 4.4. Email yang terkirim terlihat di http://localhost:8025.
4. **Bangun paket rilis** (PHP & Composer dijalankan di dalam container):
   ```powershell
   powershell -ExecutionPolicy Bypass -File scripts\build-release.ps1 -UseDocker
   # bila hosting TIDAK punya Terminal DAN Cron Job:
   powershell -ExecutionPolicy Bypass -File scripts\build-release.ps1 -UseDocker -WithWebInstaller
   ```

---

## 3. Siapkan `.env` produksi

Ambil `.env` yang sekarang dipakai di hosting (File Manager → klik kanan → *Download*), lalu pastikan isinya:

```dotenv
APP_NAME=RRCONS
APP_ENV=production
APP_KEY=base64:...            # SAMA dengan .env produksi lama, JANGAN generate baru
APP_DEBUG=false
APP_URL=https://rrconsindonesia.com
APP_TIMEZONE=Asia/Jakarta      # WAJIB ada (dipakai migration koreksi waktu)
APP_LOCALE=id                  # bahasa default pengunjung: id atau en

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=...                # dari cPanel → MySQL Databases
DB_USERNAME=...
DB_PASSWORD=...

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public

MAIL_MAILER=smtp               # sesuai email hosting
MAIL_HOST=...
MAIL_PORT=465
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=...
MAIL_FROM_NAME="${APP_NAME}"

ANALYTICS_ENABLED=true
ANALYTICS_EXCLUDE_AUTHENTICATED=true
TRUSTED_PROXIES=               # isi * bila memakai Cloudflare
ANALYTICS_COUNTRY_HEADER=      # CF-IPCountry bila memakai Cloudflare
OPTIMIZE_UPLOADED_IMAGES=true
SEO_GOOGLE_VERIFICATION=
```

---

## 4. Di hosting

> Contoh path: aplikasi di `/home/USER/rrcons`, domain menunjuk ke `/home/USER/rrcons/public`. Sesuaikan dengan
> struktur yang sekarang dipakai. Kerjakan saat pengunjung sepi: sejak langkah 4.5 sampai migration selesai
> (± 1–2 menit) website bisa menampilkan error.

### 4.1 Backup (wajib)
1. phpMyAdmin → database → *Export* → *Quick* → SQL. Simpan di komputer.
2. File Manager → folder aplikasi → `storage/app/public` → *Compress* (ZIP) → *Download* (semua gambar/video
   yang diunggah admin). Download juga file `.env`.

### 4.2 PHP 8.5
*Select PHP Version* → pilih **8.5** → centang ekstensi di bagian 0 → *Save*. Bila tersedia tab *Options*, set
`upload_max_filesize = 64M`, `post_max_size = 72M`, `max_execution_time = 300`, `memory_limit = 256M`.

### 4.3 Upload dan extract
1. File Manager → folder home (`/home/USER`) → *Upload* → pilih `rrcons-<commit>-<tanggal>.zip`.
   Bila upload lewat browser gagal karena ukuran, pakai FTP (mis. FileZilla).
2. Klik kanan ZIP → *Extract* → ke folder baru `/home/USER/rrcons_new`. Setelah selesai, hapus file ZIP.

### 4.4 Pindahkan data yang tidak ada di paket
1. Upload `.env` produksi (bagian 3) ke `/home/USER/rrcons_new/.env`.
2. Salin seluruh isi `/home/USER/rrcons/storage/app/public/` ke `/home/USER/rrcons_new/storage/app/public/`
   (File Manager → pilih semua → *Copy*). Folder `back/` berisi semua gambar; `photos/` (bila ada) berisi gambar
   yang disisipkan di isi artikel lewat editor.

### 4.5 Tukar folder (memudahkan rollback)
*Rename* `rrcons` → `rrcons_old`, lalu `rrcons_new` → `rrcons`.

### 4.6 Document root
- **Domain menunjuk ke `rrcons/public`**: tidak perlu apa-apa.
- **Web root `public_html`** berisi salinan folder `public/`: salin isi `rrcons/public/` ke `public_html/` (timpa),
  **kecuali `index.php`**. `index.php` di `public_html` sudah berisi path ke folder aplikasi
  (`__DIR__.'/../rrcons/vendor/autoload.php'` dan `__DIR__.'/../rrcons/bootstrap/app.php'`), jadi biarkan.
  Pastikan `.htaccess` baru ikut tersalin. Bila memakai web installer, file `install-*.php` ikut tersalin juga.

### 4.7 Jalankan perintah deploy — pilih SATU metode

Perintah yang harus dijalankan (urutan penting; `migrate` dijalankan sekaligus, jangan dipecah):
```
php artisan optimize:clear
php artisan storage:link
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Metode A: Terminal** (bila tersedia). cPanel → *Terminal*:
```bash
cd ~/rrcons
php -v        # harus 8.5.x
php artisan optimize:clear && php artisan storage:link && php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

**Metode B: Cron Job sekali jalan** (tanpa Terminal)
1. Cari path PHP 8.5 di server, umumnya salah satu dari: `/usr/local/bin/ea-php85` (cPanel EasyApache),
   `/opt/cpanel/ea-php85/root/usr/bin/php`, atau `/opt/alt/php85/usr/bin/php` (CloudLinux). Bila ragu, tanyakan ke support hosting.
2. cPanel → *Cron Jobs* → *Add New Cron Job* → *Common Settings*: **Once Per Minute** (`* * * * *`). Command
   (ganti `USER` dan path PHP):
   ```bash
   cd /home/USER/rrcons && P=/usr/local/bin/ea-php85 && $P artisan optimize:clear > storage/logs/deploy.log 2>&1 && $P artisan storage:link >> storage/logs/deploy.log 2>&1 && $P artisan migrate --force >> storage/logs/deploy.log 2>&1 && $P artisan config:cache >> storage/logs/deploy.log 2>&1 && $P artisan route:cache >> storage/logs/deploy.log 2>&1 && $P artisan view:cache >> storage/logs/deploy.log 2>&1
   ```
3. Tunggu 1–2 menit, buka `rrcons/storage/logs/deploy.log` di File Manager. Harus ada daftar migration `DONE`
   (atau `Nothing to migrate` bila sudah pernah jalan) dan diakhiri `Blade templates cached successfully`.
   Baris `ERROR The [public/storage] link already exists.` itu **normal** (link sudah ada); error lain berarti gagal.
4. **Hapus cron job tersebut.** Menjalankannya ulang aman karena migration hanya jalan sekali, tetapi tidak diperlukan.
5. Bila `deploy.log` kosong atau tidak ada, path PHP-nya salah. Buat cron `php -v > /home/USER/phpver.txt 2>&1` untuk
   melihat versi `php` default, atau tanyakan path PHP 8.5 ke support.
6. Struktur `public_html`: link storage harus dibuat di `public_html`, jadi ganti `$P artisan storage:link` dengan
   `ln -s /home/USER/rrcons/storage/app/public /home/USER/public_html/storage` (lewati bila `public_html/storage` sudah ada).

**Metode C: Web installer** (tanpa Terminal **dan** tanpa Cron Job)
1. Paket harus dibuat dengan `-WithWebInstaller` (1.2 langkah 5 / 2.2 langkah 4).
2. Buka URL dari `release\...-installer.txt`, mis. `https://rrconsindonesia.com/install-1a2b3c4d5e6f7a8b.php?key=...`.
   Tanpa kunci yang benar halaman ini menjawab 404.
3. Halaman menampilkan pemeriksaan server: versi PHP, ekstensi, `.env`, izin folder, `symlink()`, koneksi database,
   dan daftar migration yang akan dijalankan. Semua harus ✔.
4. Klik **Jalankan deploy** dan tunggu sampai selesai (jangan tutup halaman). Semua langkah harus ✔.
5. File installer **menghapus dirinya sendiri** setelah berhasil. Pastikan `install-*.php` sudah tidak ada di folder
   `public` (atau `public_html`); bila masih ada, hapus manual.

**Metode D: tanpa menjalankan PHP di server** (cadangan bila B dan C tidak memungkinkan)
1. Saat website sepi, export database produksi terbaru dan lakukan gladi resik (bagian 1 atau 2).
2. Export database lokal yang sudah dimigrasi: phpMyAdmin XAMPP (`http://localhost/phpmyadmin`) → *Export*, atau
   (Docker, dari Git Bash) `docker compose --env-file .env.docker exec -T db sh -c 'mariadb-dump -u$MARIADB_USER -p$MARIADB_PASSWORD $MARIADB_DATABASE' > hasil-migrasi.sql`.
3. phpMyAdmin hosting → database → centang semua tabel → *Drop* → *Import* `hasil-migrasi.sql`.
   Data yang masuk ke produksi setelah dump diambil (komentar/kunjungan baru) hilang, jadi kerjakan cepat.
4. Link `public/storage`: minta support hosting membuatnya
   (`ln -s /home/USER/rrcons/storage/app/public /home/USER/rrcons/public/storage`).

### 4.8 Izin folder
`storage/` dan `bootstrap/cache/` harus bisa ditulis web server (755 atau 775): File Manager → klik kanan →
*Change Permissions*.

### 4.9 Rollback (bila ada masalah)
1. *Rename* `rrcons` → `rrcons_gagal`, lalu `rrcons_old` → `rrcons`.
2. phpMyAdmin → *Drop* semua tabel → *Import* backup SQL dari 4.1 (migration baru mengubah struktur & data).
3. Kembalikan versi PHP sebelumnya.

---

## 5. Checklist setelah deploy

- [ ] `/`, `/?lang=en`, `/rrfuture`, `/service`, `/blog`, satu artikel, `/contact` tampil normal (cek juga di HP).
- [ ] `/sitemap.xml`, `/feed`, `/robots.txt`, `/llms.txt` terbuka.
- [ ] Gambar lama tampil (artinya `storage/app/public` dan link `public/storage` benar).
- [ ] Login admin → Dashboard (grafik analytics), Blog → Articles, edit artikel, unggah gambar di editor (File Manager).
- [ ] Blog → Comments terbuka; komentar lama ada di tab *Approved* dan tetap tampil di artikelnya.
- [ ] Kirim satu komentar uji dari halaman artikel → muncul di tab *Pending* (badge di sidebar) → setujui → tampil → hapus.
- [ ] Unggah satu gambar poster/galeri lalu hapus lagi.
- [ ] Tidak ada error baru di `storage/logs/laravel.log`.
- [ ] Metode B: cron job sudah dihapus. Metode C: file `install-*.php` sudah tidak ada.
- [ ] Setelah beberapa hari normal: hapus folder `rrcons_old`.
- Tidak perlu cron permanen maupun queue worker. Artikel terjadwal tayang otomatis.

## 6. Masalah yang sering muncul

| Gejala | Penyebab & solusi |
|---|---|
| Halaman putih / "Composer detected issues in your platform" | PHP di hosting belum 8.5 (4.2) |
| **500 Internal Server Error** setelah ganti versi PHP | Handler PHP-FPM tidak menerima `php_value` di `.htaccess`: hapus blok "Tambahan setting PHP untuk upload" di `public/.htaccess` (atau `public_html/.htaccess`), lalu atur nilainya lewat *Select PHP Version → Options* / *MultiPHP INI Editor* |
| Error 500 lain | Baca `storage/logs/laravel.log`; pastikan `.env` lengkap dan folder bisa ditulis (4.8) |
| Gambar tidak tampil | Link `public/storage` belum ada (ulangi `storage:link` / Metode B langkah 6) atau isi `storage/app/public` belum disalin (4.4) |
| Web installer: ✘ `symlink()` | Fungsi dinonaktifkan hosting: pakai Metode B, atau minta support membuat link (Metode D langkah 4) |
| Web installer berhenti di tengah | Batas waktu PHP terlalu kecil: naikkan `max_execution_time` (4.2) lalu muat ulang halaman; migration yang sudah selesai tidak diulang |
| "419 Page Expired" saat login | `APP_KEY` berubah atau cookie lama: pastikan `APP_KEY` sama, lalu hapus cookie browser |
| Upload video besar gagal | Naikkan `upload_max_filesize`/`post_max_size` (4.2) |
| Perubahan tidak terlihat | Ulangi `optimize:clear`, lalu `config:cache`, `route:cache`, `view:cache` |
