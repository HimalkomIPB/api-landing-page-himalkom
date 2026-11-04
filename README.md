## Main API for HIMALKOM IPB landing page

## Installation
```bash
composer install
```
<br>

```bash
cp .env.example .env
```
<br>

```bash
php artisan key:generate
```
<br>

```bash
php artisan migrate
```
<br>

```bash
php artisan db:seed
```
<br>

```bash
php artisan serve
```
<br>

```plaintext
http://localhost:8000/admin
```
<br>

## What to improve
### Implementing pagination (backend and frontend) 

## API Reference

See api_reference_latest.postman_collection.json

### Info Lomba

API publik: `GET /infolomba`, `GET /infolomba/categories`, dan `GET /infolomba/{slug}`.
Pengelolaan tersedia pada grup **Info Lomba** di admin panel untuk akun `ADMIN_EMAIL`.

Lihat [handoff frontend Info Lomba](docs/infolomba-frontend-handoff.md) untuk kontrak API, contoh respons, filter, flow admin, dan panduan integrasi dengan frontend yang sudah ada.
