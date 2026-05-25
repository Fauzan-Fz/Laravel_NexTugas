# NexTugas – Dokumentasi Teknis

## 1. Deskripsi Proyek

**NexTugas** adalah Sistem Manajemen Tugas Pintar yang dibangun dengan arsitektur modern:

- **Framework**: Laravel 13 dengan kemampuan AI Native
- **Database**: Generasi ID Snowflake 64-bit untuk keunikan terdistribusi
- **Integrasi AI**: OpenRouter API untuk pemecahan tugas cerdas
- **Desain UI**: Antarmuka premium minimalist matte dengan dukungan dark mode
- **Arsitektur**: Pemrosesan antrean asinkron untuk UX yang mulus

### Fitur Utama

- **Snowflake IDs**: Pengidentifikasi unik 64-bit terdistribusi (timestamp + worker + sequence)
- **Pemecahan Tugas AI**: Secara otomatis menghasilkan sub-tugas menggunakan OpenRouter AI
- **UI Real-time**: Alpine.js + Tailwind CSS untuk komponen reaktif
- **Pemrosesan Antrean**: Worker latar belakang mencegah UI frontend membeku
- **Multi-tema**: Mode terang/gelap dengan sistem desain matte

---

## 2. Konfigurasi API AI

### Pengaturan API Key OpenRouter

Untuk mengaktifkan fitur AI, tambahkan API key OpenRouter ke file `.env`:

```env
OPENROUTER_API_KEY=isi_api_key_openrouter_kamu_di_sini
```

**Cara mendapatkan API key:**
1. Kunjungi [openrouter.ai](https://openrouter.ai)
2. Buat akun atau masuk
3. Navigasi ke bagian **Keys**
4. Hasilkan API key baru
5. Salin dan tempel ke file `.env`

**Catatan keamanan**: Jangan pernah commit API key ke version control. File `.env` sudah termasuk dalam `.gitignore`.

---

## 3. Panduan Menjalankan Perintah Utama

### Perintah 1: Pengaturan Database

```bash
php artisan migrate:fresh --seed
```

**Apa yang dilakukan perintah ini:**

1. **Menghapus semua tabel yang ada** – Membersihkan database sepenuhnya
2. **Menjalankan migrasi** – Membuat tabel dengan skema Snowflake ID:
   - `users`: Primary key 64-bit unsigned (tanpa auto-increment)
   - `tasks`: Primary key 64-bit unsigned + foreign key ke users
   - `task_steps`: Primary key 64-bit unsigned + foreign key ke tasks
3. **Mengisi data uji** – Membuat akun pengguna uji coba:
   - **User**: `Fauzan Firdaus`
   - **Email**: `fauzan@rpl.com`
   - **Password**: `password123`
   - **Tugas**: 6 tugas sekolah RPL contoh

**Mengapa menggunakan Snowflake IDs?**
- **Integer 64-bit** – Mendukung 69 tahun ID unik
- **Generasi terdistribusi** – Tidak perlu koordinasi database
- **Berurutan waktu** – ID diurutkan secara kronologis
- **Tanpa tabrakan** – Unik di seluruh server/worker

### Perintah 2: Worker Antrean

```bash
php artisan queue:work
```

**Apa yang dilakukan perintah ini:**

Menjalankan proses worker latar belakang yang mendengarkan dan mengeksekusi pekerjaan dalam antrean secara asinkron.

**Mengapa perintah ini WAJIB:**

| Fitur | Tanpa Queue Worker | Dengan Queue Worker |
|-------|---------------------|---------------------|
| **Pemecahan Tugas AI** | Frontend membeku saat API call | Respon instan, AI diproses di latar belakang |
| **Pengalaman User** | Lag 3-5 detik | Feedback langsung |
| **Penanganan Error** | Kegagalan memblokir user | Retry otomatis |
| **Skalabilitas** | Single-threaded | Banyak worker memungkinkan |

**Bagaimana AI Center bekerja:**

1. User membuat tugas di UI
2. Tugas disimpan ke database segera
3. `GenerateTaskStepsJob` dikirim ke antrean
4. Queue worker mengambil job di latar belakang
5. OpenRouter API menghasilkan sub-tugas
6. Langkah tugas disimpan ke tabel `task_steps`
7. User melihat hasil saat refresh halaman

**Menjalankan worker:**

Buka **terminal terpisah** dan biarkan perintah ini berjalan:

```bash
php artisan queue:work --timeout=60
```

Untuk lingkungan produksi, gunakan Supervisor atau systemd untuk menjaga worker tetap berjalan.

---

## 4. Arsitektur Sistem

```
┌─────────────────┐     ┌──────────────┐     ┌─────────────────┐
│ Browser User    │────▶│  Laravel App │────▶│   MySQL DB      │
│  (Alpine.js)    │     │   (PHP 8.4)  │     │ (Snowflake IDs) │
└─────────────────┘     └──────────────┘     └─────────────────┘
                               │
                               ▼
                        ┌──────────────┐
                        │  Queue Worker│
                        │  (Latar      │
                        │  Belakang)   │
                        └──────────────┘
                               │
                               ▼
                        ┌──────────────┐
                        │  OpenRouter  │
                        │   AI API     │
                        └──────────────┘
```

---

## 5. Struktur File

```
laravel_new_my_kisah/
├── app/
│   ├── Traits/
│   │   └── HasSnowflake.php          # Generator ID 64-bit
│   ├── Models/
│   │   ├── User.php                  # Trait HasSnowflake
│   │   ├── Task.php                  # Trait HasSnowflake
│   │   └── TaskStep.php              # Trait HasSnowflake
│   ├── Jobs/
│   │   └── GenerateTaskStepsJob.php  # Job pemrosesan AI
│   └── Services/
│       └── OpenRouterService.php     # Client API AI
├── database/
│   ├── migrations/                   # Skema Snowflake
│   └── seeders/
│       └── TaskSeeder.php            # Data uji
└── docs/
    ├── README_EN.md                  # Versi Inggris
    └── README_ID.md                  # File ini
```

---

## 6. Checklist Memulai Cepat

- [ ] Salin `.env.example` ke `.env`
- [ ] Tambahkan `OPENROUTER_API_KEY` ke `.env`
- [ ] Jalankan `php artisan migrate:fresh --seed`
- [ ] Buka terminal 1: `php artisan serve`
- [ ] Buka terminal 2: `php artisan queue:work`
- [ ] Akses `http://localhost:8000`
- [ ] Login dengan `fauzan@rpl.com` / `password123`

---

## 7. Troubleshooting (Pemecahan Masalah)

### Masalah: AI tidak menghasilkan langkah
**Solusi**: Pastikan `php artisan queue:work` berjalan di terminal terpisah.

### Masalah: Error Snowflake ID
**Solusi**: Jalankan `php artisan migrate:fresh --seed` untuk rebuild dengan skema benar.

### Masalah: API key tidak bekerja
**Solusi**: Verifikasi `OPENROUTER_API_KEY` di `.env` dan tidak tercache. Jalankan `php artisan config:clear`.

---

**Versi**: 1.0.0  
**Terakhir Diperbarui**: Mei 2026  
**Penulis**: XI RPL
