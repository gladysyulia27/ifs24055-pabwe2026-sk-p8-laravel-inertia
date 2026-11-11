# BlogVerse Pro — P8 Laravel + Inertia

Aplikasi publikasi artikel multi-tier sesuai studi kasus P8. Struktur backend Feature-First dan pemisahan tanggung jawab menggunakan Controller, Service, Repository, Policy, Action, DTO, dan Middleware.

## Teknologi
- Laravel 13 / PHP 8.3+
- Inertia.js 2 + React 19 + Vite
- PostgreSQL
- PhpSpreadsheet (XLSX import/export)
- OpenAI Chat Completions API (opsional, model dapat diatur)
- TOAST UI Editor + React Markdown Viewer

## Persyaratan
PHP >= 8.3 dengan ekstensi `pdo_pgsql`, `mbstring`, `openssl`, `fileinfo`, `xml`, `zip`, `gd`; Composer; Node.js >= 20 dan npm; PostgreSQL.

## Instalasi (Windows PowerShell / macOS / Linux)
1. Ekstrak folder proyek, lalu buka terminal di folder `ifs24055-pabwe2026-sk-p8-laravel-inertia`.
2. Install dependency: `composer install` lalu `npm install`.
3. Salin `.env.example` menjadi `.env`.
4. Buat database PostgreSQL:
   ```sql
   CREATE DATABASE pabwe_p8_blog;
   ```
5. Sesuaikan konfigurasi `DB_*` di `.env`. Nilai default sesuai lembar tugas (`postgres` / `postgres`).
6. Buat application key: `php artisan key:generate`.
7. Migrasi dan data contoh: `php artisan migrate --seed`.
8. Buat symbolic link storage: `php artisan storage:link`.
9. Jalankan dua terminal:
   - `php artisan serve`
   - `npm run dev`
10. Buka `http://127.0.0.1:8000`.

## Akun demo
| Role | Username | Password |
|---|---|---|
| Admin | `admin` | `password123` |
| Penulis | `writer` | `password123` |

Seeder hanya membuat akun contoh jika email belum ada. Ubah password demo sebelum deploy.

## OpenAI (opsional)
Atur `OPENAI_API_KEY` dan `OPENAI_MODEL=gpt-4o-mini` pada `.env`. Tanpa API key, semua fitur inti blog tetap berjalan; AI Generator menampilkan pesan konfigurasi yang jelas. Batasi jumlah generate 1–5 artikel per permintaan.

## Fitur
### Publik
- Homepage, galeri artikel, pencarian judul/konten/ringkasan dan filter kategori.
- Halaman baca artikel Markdown, cover, sticky navbar, footer, Login/Register.
### Penulis
- Login/register, dashboard list view, statistik total/published/draft.
- CRUD artikel, status draft/published, upload dan hapus cover.
- Import/export Excel artikel pribadi.
- AI generator dan review hasil sebelum menyimpan sebagai draft.
- Pengaturan profil.
### Admin
- Daftar pengguna dan jumlah artikel.
- Monitoring artikel per pengguna (read-only), ekspor per pengguna.
- Monitoring seluruh artikel dan ekspor seluruh sistem.

## Rute penting
- Publik: `/`, `/articles`, `/articles/{slug}`
- Auth: `/login`, `/register`, `/logout`
- Penulis: `/dashboard/posts`, `/dashboard/posts/export`, `/dashboard/posts/import`, `/dashboard/posts/ai-generate`, `/dashboard/posts/ai-save`, `/profile`
- Admin: `/admin/users`, `/admin/users/{user}/posts`, `/admin/posts`

## Excel
Header ekspor: ID, Penulis, Judul, Slug, Kategori, Ringkasan, Konten, Status, Diterbitkan, Dibuat. Impor membaca kolom Judul (C), Kategori (E), Ringkasan (F), Konten (G), dan Status (H), mengikuti format hasil ekspor.

## Catatan keamanan & produksi
- Gunakan HTTPS, `APP_DEBUG=false`, APP_KEY unik, password database kuat, dan API key OpenAI hanya di server.
- Jangan commit `.env` atau kredensial.
- Batasi ukuran file cover 5 MB dan file Excel 10 MB.
- Jalankan `composer audit`, `php artisan test`, dan `npm run build` setelah memasang dependency.

## Pengujian
Jalankan `php artisan test`. Test unit/feature tambahan dapat diletakkan di `tests/`. Pengujian coverage 100% memerlukan konfigurasi Xdebug/PCOV dan cakupan test komprehensif di environment lokal; tidak diklaim sudah tercapai hanya dari paket sumber ini.
