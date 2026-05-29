# Evoria — Event Ticketing Platform

Aplikasi pembelian tiket event berbasis web (Laravel 11) dan mobile (Flutter).
Dilengkapi integrasi pembayaran Midtrans dan AI Chatbot.

---

## Prasyarat

| Kebutuhan | Versi minimum |
|-----------|--------------|
| PHP | 8.2+ |
| Composer | 2.x |
| Node.js | 18+ |
| MySQL | 8.0+ |

> **macOS**: Install via Homebrew — `brew install php composer node mysql`
> **Windows**: Gunakan [Laragon](https://laragon.org) (sudah include PHP, MySQL)

---

## Instalasi

### 1. Clone & masuk folder

```bash
git clone https://github.com/farajaahdaf/Evoria.git
cd Evoria
```

### 2. Install dependencies

```bash
composer install
npm install
```

> Untuk file `.env`, saya sudah kumpulkan di LMS untuk file env nya

### 3. Migrasi & isi data awal

```bash
php artisan migrate --seed
```

### 4. Build asset frontend

```bash
npm run build
```

### 5. Buat symlink storage

```bash
php artisan storage:link
```

---

## Menjalankan Aplikasi

Jalankan **2 perintah** di terminal terpisah:

```bash
# Terminal 1 — Laravel
php artisan serve

# Terminal 2 — Vite (hot reload CSS/JS)
npm run dev
```

Akses di browser: **http://localhost:8000**

> Cache, session, dan queue sudah menggunakan database driver — tidak perlu Redis.

---

## Akun Demo

Semua akun menggunakan password: **`password`**

| Role | Email | Akses |
|------|-------|-------|
| Admin | `admin@example.com` | Dashboard admin — kelola semua event & user |
| Organizer | `organizer@example.com` | Dashboard organizer — buat & kelola event |
| Attendee | `attendee@example.com` | Beli tiket, lihat pesanan, chatbot |

> Organizer tambahan: `harmony.stage@example.com`, `creative.hub@example.com`, `edu.summit@example.com`

---

## Fitur Utama

- **Pembayaran Midtrans** — Snap payment embedded (sandbox), mendukung berbagai metode bayar
- **Race-condition safe** — booking pakai `DB::lockForUpdate()` sehingga stok tiket tidak pernah oversold
- **AI Chatbot** — asisten event berbasis OpenAI GPT-4o-mini
- **E-Ticket + QR Code** — tiket digital otomatis dikirim setelah pembayaran berhasil
- **Google Maps** — lokasi event ditampilkan di halaman detail

---
