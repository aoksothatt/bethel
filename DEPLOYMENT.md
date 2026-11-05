# 🚀 Deployment Guide: Team Jersey Tracker (Season 2026)

This document provides step-by-step instructions to deploy the **Team Jersey Tracker** full-stack application (Vue 3 + Laravel 11 API + MySQL) to publicly accessible hosting.

---

## 🏆 Recommended Hosting Solutions

### Option A: Railway.app (Recommended — Easiest & Most Cost-Effective)
- **Why:** Instant MySQL database provision, automatic GitHub deployments, free SSL/HTTPS, custom domain support, and runs both Laravel and the built Vue frontend effortlessly.
- **Estimated Cost:** ~$5/month (often covered by free starter credits).

### Option B: Render.com
- **Why:** Managed Web Service + Managed PostgreSQL or MySQL, automatic Git deployments, free SSL.
- **Estimated Cost:** Free tier or $7/month.

### Option C: Traditional VPS / Laravel Forge / Cloud (DigitalOcean, Hetzner)
- **Why:** Full control over Ubuntu server, Nginx, PHP 8.3-FPM, MySQL 8.0, and Let's Encrypt SSL.
- **Estimated Cost:** $4 - $6/month.

### Option D: Shared Hosting / cPanel (Namecheap, Hostinger)
- **Why:** Standard cPanel with PHP 8.3 & MySQL wizard.

---

## 📋 Step-by-Step Deployment Instructions (Railway / Cloud)

### 1. Build the Production Frontend
Before deploying, compile the Vue 3 frontend and bundle it into Laravel's public directory:

```bash
npm run deploy:prep
```
This builds the Vue 3 application into `/dist` and copies the assets to `/backend/public`.

---

### 2. Configure Environment Variables

Create your production environment variables in your hosting dashboard:

| Variable | Recommended Production Value | Description |
|---|---|---|
| `APP_NAME` | `Team Jersey Tracker` | Application Name |
| `APP_ENV` | `production` | Production environment |
| `APP_KEY` | *(Generate via `php artisan key:generate --show`)* | 32-character cipher key |
| `APP_DEBUG` | `false` | Disable debug stacktraces for security |
| `APP_URL` | `https://yourdomain.com` | Your public domain |
| `DB_CONNECTION` | `mysql` | MySQL Database Driver |
| `DB_HOST` | `mysql.railway.internal` or your MySQL host | Database host |
| `DB_PORT` | `3306` | MySQL port |
| `DB_DATABASE` | `team_jersey_db` | Database name |
| `DB_USERNAME` | `team_jersey_user` | Database user |
| `DB_PASSWORD` | `YOUR_SECURE_PASSWORD` | Database password |
| `ADMIN_NAME` | `Team Administrator` | Administrator Display Name |
| `ADMIN_EMAIL` | `admin@yourdomain.com` | Administrator Login Email |
| `ADMIN_PASSWORD` | `StrongAdminPassword2026!` | Administrator Login Password |
| `CORS_ALLOWED_ORIGINS` | `https://yourdomain.com` | Allowed CORS Origins |

---

### 3. Run Migrations & Safe Idempotent Seeders

In your server terminal or deployment build script, run:

```bash
php artisan migrate --force
php artisan db:seed --force
```

> **Safety Guarantee:** The `PlayerSeeder` is written with idempotent guards (`if (Player::count() > 0) return;`). Running migrations or seeders multiple times will **never duplicate or overwrite existing player records**.

---

### 4. Custom Domain Connection & HTTPS

1. In your domain registrar (GoDaddy, Namecheap, Cloudflare, etc.), add a **CNAME** or **A** record pointing to your hosting provider's DNS target.
2. Railway/Render/Cloudflare will automatically issue a **free Let's Encrypt SSL certificate** for HTTPS.
3. Verify that `https://yourdomain.com/` loads securely with a padlock.

---

## 🧪 Production Verification Checklist

- [ ] **Public Site (`https://yourdomain.com/`):**
  - [ ] Loads without login prompt.
  - [ ] Displays all 28 original players immediately.
  - [ ] Summary Statistics match: Total 28, Paid 4, Unpaid 23, Not Confirmed 1.
  - [ ] Jersey sizes breakdown matches: M=9, L=16, XL=3.
  - [ ] Search works for names and shirt numbers (including 0 and duplicate numbers).
  - [ ] Payment filter chips switch between All, Paid, Unpaid, and Not Confirmed.
  - [ ] Khmer notes (`អាវគេចូលរួម`) and `GK` render correctly.
- [ ] **Admin Dashboard (`https://yourdomain.com/admin`):**
  - [ ] Unauthenticated users are redirected to `/admin/login`.
  - [ ] Direct calls to `/api/admin/*` without Bearer token return `401 Unauthorized`.
  - [ ] Login with `admin@yourdomain.com` succeeds and stores token securely.
  - [ ] 1-Tap payment status change updates the database instantly.
  - [ ] Success toast notification appears on payment status change.
  - [ ] Public page auto-refreshes within 6 seconds to reflect the change.
  - [ ] Add player form works.
  - [ ] Edit player form works.
  - [ ] Delete player dialog prevents accidental deletion.
  - [ ] Logout invalidates token and returns to login page.
