# Deployment (GitHub Actions build, Coolify runs the image)

On push, `.github/workflows/deploy.yml` builds `Dockerfile` on GitHub's runners, pushes it to
`ghcr.io/himalkomipb/api-landing-page-himalkom`, then calls the Coolify deploy API.
The VPS never builds; Coolify's "Docker Image" apps only pull and restart.

| Branch | Image tag | GitHub environment | Site |
|---|---|---|---|
| `main` | `:main` | `production` | https://api.himalkom-ipb.com |
| `staging` | `:staging` | `staging` | https://staging-api.himalkom-ipb.com |

Each GitHub environment holds three secrets: `COOLIFY_URL`, `COOLIFY_TOKEN` (Coolify API token
with deploy permission), `COOLIFY_APP_UUID`. Every build is also tagged `:sha-<commit>` for rollback.

- The image is built for `linux/arm64` (the VPS is ARM64) on GitHub's `ubuntu-24.04-arm` runners.
- The image is nginx + PHP-FPM 8.3 on port 8080. nginx runs only `public/index.php`, and `/storage/` never executes PHP.
- Runtime env vars (APP_KEY, DB_*, ...) are set in Coolify, never in git. Uploads are a persistent volume at `storage/app/public`.
- The container does **not** run migrations. Run `php artisan migrate --force` by hand after review.
- Test changes on `staging` first, then merge into `main`.
- Add `[skip ci]` to a commit message to skip the build and deploy.
