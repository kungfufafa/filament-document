# Filament Helpdesk

Aplikasi backend **panel admin** dan **API** untuk sistem **Helpdesk** yang dibangun menggunakan Laravel & Filament, dilengkapi dengan tema **Mekaya Admin Panel** dan ikon kustom.

## Tech Stack

| Paket | Versi |
|---|---|
| PHP | 8.4 |
| Laravel | v13 |
| Filament | v5 |
| Livewire | v4 |
| Laravel Sanctum | v4 |
| Mekaya Theme | @dev |
| TailwindCSS | v4 |

## Fitur

- **Panel Admin Filament v5** dengan tema Mekaya (sidebar kustom, topbar, UntitledUI icons)
- **Primary color Orange** sebagai identitas visual aplikasi
- **Custom branding** — logo, brand icon, dan favicon menggunakan `public/icon.svg`
- **Redirect otomatis** dari `/` ke `/admin`
- **Registrasi publik dinonaktifkan** — hanya admin yang dapat menambah akun
- **API-ready** menggunakan Laravel Sanctum
- **Vite** mengompilasi aset Mekaya Theme (`theme.css` & `mekaya.js`)

---

## Instalasi & Setup

### 1. Clone & Install Dependencies

```bash
composer install
npm install
```

### 2. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` sesuai kebutuhan (database, app name, dll).

### 3. Migrasi Database

```bash
php artisan migrate
```

### 4. Kompilasi Frontend Assets

```bash
npm run build
```

### 5. Jalankan Server Development

```bash
composer run dev
```

Akses panel admin di: `http://localhost:8000/admin`

---

## Konfigurasi Mekaya Theme

Mekaya Theme dikonfigurasi di [`config/mekaya.php`](config/mekaya.php):

| Key | Nilai | Keterangan |
|---|---|---|
| `admin.brand` | `icon.svg` | Logo di sidebar & halaman auth |
| `admin.brand_icon` | `icon.svg` | Ikon kompak saat sidebar collapsed |
| `admin.favicon` | `icon.svg` | Favicon browser |

Panel dikonfigurasi di [`app/Providers/Filament/AdminPanelProvider.php`](app/Providers/Filament/AdminPanelProvider.php).

---

## Struktur Penting

```
app/
├── Models/
│   └── User.php
└── Providers/
    └── Filament/
        └── AdminPanelProvider.php
config/
└── mekaya.php
public/
└── icon.svg
routes/
└── web.php         ← redirect / → /admin
```
