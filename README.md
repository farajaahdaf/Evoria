# Evoria — Event Ticketing Platform

Aplikasi pembelian tiket event berbasis web (Laravel 11) dan mobile (Flutter).  
Dilengkapi Virtual Waiting Room, integrasi pembayaran Midtrans, dan AI Chatbot.

---

## Prasyarat

| Kebutuhan | Versi minimum |
|-----------|--------------|
| PHP | 8.2+ |
| Composer | 2.x |
| Node.js | 18+ |
| MySQL | 8.0+ |
| Redis | 6.x+ |

> **macOS**: Install via Homebrew — `brew install php composer node mysql redis`  
> **Windows**: Gunakan [Laragon](https://laragon.org) (sudah include PHP, MySQL, Redis)

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

### 5. Migrasi & isi data awal

```bash
php artisan migrate --seed
```

### 6. Build asset frontend

```bash
npm run build
```

### 7. Buat symlink storage

```bash
php artisan storage:link
```

---

## Menjalankan Aplikasi

Jalankan **3 perintah** di terminal terpisah:

```bash
# Terminal 1 — Redis
redis-server

# Terminal 2 — Laravel
php artisan serve

# Terminal 3 — Vite (hot reload CSS/JS)
npm run dev
```

Akses di browser: **http://localhost:8000**

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

- **Virtual Waiting Room** — antrian berbasis Redis, mencegah server collapse saat banyak user beli tiket serentak
- **Pembayaran Midtrans** — Snap payment (sandbox), mendukung berbagai metode bayar
- **AI Chatbot** — asisten event berbasis OpenAI GPT-4o-mini
- **E-Ticket + QR Code** — tiket digital otomatis dikirim setelah pembayaran berhasil
- **Google Maps** — lokasi event ditampilkan di halaman detail

---
