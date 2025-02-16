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

## API Reference

### Komnews

#### Get all Komnews

```http
  GET /komnews
```

#### Get komnews by slug

```http
  GET /komnews/{slug}
```

| Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `slug`      | `string` | **Required**. Slug of komnews to fetch |

### Syntaxes

#### Get all syntaxes

```http
  GET /syntaxes
```

### Research

#### Get all research

```http
  GET /research
```

### IGallery

#### Get all IGallery

```http
  GET /igalleries
```

#### Get all IGallery Subjects

```http
  GET /igalleries/subjects
```
