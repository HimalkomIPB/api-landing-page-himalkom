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

#### Get syntaxes

```http
  GET /syntaxes
```

### Research

#### Get research

```http
  GET /research
```

### IGallery

#### Get IGalleries

```http
  GET /igalleries
```

#### Get IGallery Subjects

```http
  GET /igalleries/subjects
```
### Division and Staff

#### Get divisions

```http
  GET /divisions
```

#### Get division with its staff(s)

```http
  GET /divisions/{slug}
```

| Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `slug`      | `string` | **Required**. Slug of division to fetch with it staff |

#### Get megaprokers with its image(s)

```http
  GET /megaprokres
```