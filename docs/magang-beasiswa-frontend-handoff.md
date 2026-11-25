# Panduan integrasi Info Beasiswa dan Info Magang

Kedua fitur memakai pola Info Lomba untuk daftar, detail, pencarian judul, paginasi, dan status pendaftaran. Daftar dan detail tersedia lewat API publik. Admin mengelola konten lewat Filament.

## Endpoint

Gunakan base URL API landing page yang sudah dipakai untuk Info Lomba. Tambahkan path langsung ke base URL tanpa prefiks `/api` dan kirim `Accept: application/json`.

| Fitur | Daftar | Detail | Kunci JSON |
| --- | --- | --- | --- |
| Info Beasiswa | `GET /info-beasiswa` | `GET /info-beasiswa/{slug}` | `infobeasiswa` |
| Info Magang | `GET /info-magang` | `GET /info-magang/{slug}` | `infomagang` |

Kedua fitur tanpa kategori. Saat menggunakan ulang komponen Info Lomba, hilangkan pemilih kategori dan pengiriman `category_id` untuk kedua fitur. Respons hanya memuat data pendaftaran dan metadata paginasi pada daftar.

Endpoint memakai aturan origin yang sama dengan Info Lomba. Saat `ALLOWED_ORIGIN` dibatasi, request harus membawa `Origin` atau `Referer` dari domain frontend yang diizinkan. Untuk request lintas origin dari browser, gunakan domain frontend yang sudah terdaftar di CORS. Untuk request server-side yang diizinkan, sertakan `Origin` frontend sesuai konfigurasi deployment.

## Daftar dan filter

Semua parameter bersifat opsional dan dapat digabungkan. Contoh request daftar adalah `GET /info-magang?q=Data&status=active&page=1&per_page=6`.

| Parameter | Nilai | Perilaku |
| --- | --- | --- |
| `q` | String, maksimal 255 karakter | Mencari berdasarkan judul. |
| `status` | `upcoming`, `active`, `closed` | Menyaring berdasarkan periode pendaftaran. Tanpa parameter ini, daftar memuat semua status. |
| `page` | Integer mulai dari 1 | Default `1`. |
| `per_page` | Integer 1–100 | Default `6`. |

Daftar diurutkan dari waktu pembuatan terbaru, lalu ID terbesar jika waktunya sama. Gunakan `pagination.last_page` untuk menentukan tombol halaman berikutnya dan kembali ke `page=1` saat filter berubah. Jika tidak ada hasil, kunci daftar berisi `[]`, dengan `total: 0` dan `last_page: 1`.

### Contoh respons daftar beasiswa

`GET /info-beasiswa?page=1&per_page=6`

```json
{
  "pagination": {
    "current_page": 1,
    "per_page": 6,
    "total": 1,
    "last_page": 1
  },
  "infobeasiswa": [
    {
      "id": 12,
      "image": "info-beasiswa/cover.jpg",
      "image_url": "https://api.example.com/storage/info-beasiswa/cover.jpg",
      "title": "Beasiswa Prestasi",
      "slug": "beasiswa-prestasi-01k9zz00000000000000000000",
      "description": "<p>Pendaftaran terbuka untuk mahasiswa aktif.</p>",
      "start_date": "2026-11-20",
      "end_date": "2026-12-15",
      "status": "active",
      "link": "https://example.com/daftar-beasiswa",
      "created_at": "2026-11-20 09:00:00",
      "updated_at": "2026-11-20 09:00:00"
    }
  ]
}
```

## Detail dan pemetaan field

Ambil `slug` dari daftar, lalu gunakan pada path detail. Slug tetap sama setelah judul diedit. Detail mengembalikan objek pendaftaran di bawah kunci fitur.

### Contoh respons detail magang

`GET /info-magang/magang-data-analyst-01k9zz00000000000000000001`

```json
{
  "infomagang": {
    "id": 5,
    "image": "info-magang/cover.jpg",
    "image_url": "https://api.example.com/storage/info-magang/cover.jpg",
    "title": "Magang Data Analyst",
    "slug": "magang-data-analyst-01k9zz00000000000000000001",
    "description": "<p>Program magang untuk mahasiswa tingkat akhir.</p>",
    "start_date": "2026-11-25",
    "end_date": "2026-12-10",
    "status": "upcoming",
    "link": "https://example.com/daftar-magang",
    "created_at": "2026-11-20 10:00:00",
    "updated_at": "2026-11-20 10:00:00"
  }
}
```

- Gunakan `image_url` langsung untuk gambar cover. Field `image` berisi path penyimpanan.
- `description` berisi HTML dari rich-text editor. Sanitasi HTML sebelum dirender.
- `start_date` dan `end_date` berformat `YYYY-MM-DD`. `created_at` dan `updated_at` berformat `YYYY-MM-DD HH:mm:ss` atau `null`.
- Tampilkan tombol pendaftaran dengan URL dari `link`. Gunakan `status` dari API untuk menentukan label dan ketersediaan tombol.

Status dihitung saat request memakai tanggal hari ini di zona waktu backend (`APP_TIMEZONE`).

| Status | Label | Kondisi |
| --- | --- | --- |
| `upcoming` | Akan Datang | Hari ini sebelum `start_date`. |
| `active` | Dibuka | Hari ini antara `start_date` dan `end_date`, termasuk keduanya. |
| `closed` | Ditutup | Hari ini setelah `end_date`. |

## Respons error

| HTTP | Kondisi | Format |
| --- | --- | --- |
| `403` | Origin request ditolak | `{"errors":"Forbidden"}` |
| `404` | Slug tidak ditemukan pada fitur yang diminta | `{"errors":"Info beasiswa not found"}` atau `{"errors":"Info magang not found"}` |
| `422` | Parameter daftar tidak valid | Objek `message` dan `errors`, dengan daftar pesan per field di `errors`. |

## Data untuk pengujian integrasi

Jalankan `php artisan migrate` pada database lingkungan pengujian sebelum memanggil endpoint. Pastikan `APP_URL` mengarah ke base URL API dan `php artisan storage:link` sudah dijalankan agar cover dapat diakses.

Masuk ke Filament dengan akun `ADMIN_EMAIL`, lalu buka `/admin/info-beasiswa` atau `/admin/info-magang` di grup **Ilkomerz Hub**. Admin mengisi cover (maksimal 2 MB), judul, deskripsi, periode, dan tautan HTTP/HTTPS. Konten yang disimpan langsung muncul di API. Gunakan slug dari respons daftar untuk menguji detail.

Empat request juga tersedia untuk diimpor dari [koleksi Postman](../api_reference_latest.postman_collection.json) di folder **Info Beasiswa** dan **Info Magang**.
