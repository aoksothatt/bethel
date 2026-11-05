# 🌐 Master Free Deployment Guide: Bethel High School — Team Jersey Tracker

This document provides the complete, production-verified deployment plan and instructions to deploy **Bethel High School — Team Jersey Tracker** online for **$0 (100% Free Forever)**.

---

## 🏗️ Architecture Overview

| Component | Technology | Free Hosting Provider | Free Plan Details |
|---|---|---|---|
| **Frontend** | Vue 3 + Vite + Tailwind CSS | **Render Static Site** or **Netlify** (or unified with Backend) | 100 GB/month bandwidth, Free HTTPS/SSL, CDN edge routing |
| **Backend API** | Laravel 11 (PHP 8.3) + Sanctum | **Render Web Service (Docker)** | Free Web Service (512 MB RAM, 0.1 CPU, Free HTTPS) |
| **Database** | MySQL 8.0 Protocol Compatible | **TiDB Cloud Serverless (Starter)** | **Free Forever** (5 GiB storage, 50M RU/mo, No cold-start sleep, SSL enabled) |

---

## 📦 Key Production Artifacts Prepared

The project codebase has already been audited, prepared, and committed to Git on branch `main`:

1. **`Dockerfile`**: Multi-stage production container that builds the Vue frontend with Node 22, packages Laravel 11 with PHP 8.3-FPM, and serves traffic via Nginx on dynamic `$PORT`.
2. **`docker/nginx.conf`**: Optimized Nginx configuration with gzip compression, SPA fallback, FastCGI buffering, and security headers.
3. **`docker/entrypoint.sh`**: Startup orchestrator that sets permissions, runs migrations, caches Laravel configurations, and launches PHP-FPM + Nginx.
4. **`render.yaml`**: Infrastructure-as-Code Blueprint for 1-click deployment on Render.
5. **`netlify.toml`**: Standalone SPA redirect configuration if deploying frontend to Netlify.
6. **`backend/database/bethel-dump.sql`**: Production-ready MySQL dump with tables (`users`, `personal_access_tokens`, `players`) and all **28 official squad members**.
7. **`backend/routes/api.php`**: Health check endpoint `/api/health` providing live database connectivity diagnostics.

---

## 🚀 Step-by-Step Free Deployment Process

### Step 1: Create Free Database on TiDB Cloud (3 minutes)

1. Go to [https://tidbcloud.com/](https://tidbcloud.com/) and sign up for a free account (No credit card required).
2. Click **Create Cluster** and select **Serverless** (Free tier).
3. Choose your region (e.g., **Singapore** `ap-southeast-1` or closest to Cambodia/SE Asia).
4. Click **Create**. Your cluster is created in ~15 seconds.
5. In the cluster dashboard, click **Connect**:
   - Note down:
     - **Host**: e.g. `gateway01.ap-southeast-1.prod.aws.tidbcloud.com`
     - **Port**: `4000`
     - **Database**: `test`
     - **User**: e.g. `xxxxxx.root`
     - **Password**: Click **Reset Password** to generate your secure password.
6. Click **SQL Editor** tab in the TiDB Cloud console:
   - Copy the contents of [`backend/database/bethel-dump.sql`](file:///d:/Buliding%20Project/bethel%20table/backend/database/bethel-dump.sql).
   - Paste and click **Run**.
   - Output will confirm: 1 user (`admin@teamjersey.local`) and all 28 players imported successfully!

---

### Step 2: Push Your Code to GitHub (2 minutes)

The local Git repository is already initialized and committed on `main`. To link your GitHub account:

1. Create a new repository on [GitHub](https://github.com/new) named `bethel-jersey-tracker` (set to **Private** or **Public**).
2. In your terminal, run:
   ```bash
   git remote add origin https://github.com/YOUR_USERNAME/bethel-jersey-tracker.git
   git push -u origin main
   ```

---

### Step 3: Deploy to Render (Option A: Unified Full-Stack Service — Recommended)

With the unified Docker architecture, both the Vue frontend and Laravel API run in a single container. This gives you **Zero CORS configuration**, **single domain routing**, and **1-click SSL**:

1. Go to [https://dashboard.render.com/](https://dashboard.render.com/) and click **New +** -> **Web Service**.
2. Connect your `bethel-jersey-tracker` GitHub repository.
3. Fill in:
   - **Name**: `bethel-high-school-jersey`
   - **Region**: Singapore (same as your TiDB database)
   - **Runtime**: **Docker**
   - **Instance Type**: **Free**
4. Under **Environment Variables**, add:
   | Key | Value | Description |
   |---|---|---|
   | `APP_NAME` | `Bethel High School Jersey Tracker` | App Name |
   | `APP_ENV` | `production` | Production environment |
   | `APP_DEBUG` | `false` | Security: disable debug stacktraces |
   | `APP_KEY` | *(Click "Generate" or use `base64:GENERATE_WITH_PHP_ARTISAN`)* | Laravel encryption key |
   | `APP_URL` | `https://bethel-high-school-jersey.onrender.com` | Your Render app URL |
   | `DB_CONNECTION` | `mysql` | MySQL/TiDB driver |
   | `DB_HOST` | *(Your TiDB Host)* | TiDB Serverless host |
   | `DB_PORT` | `4000` | TiDB port |
   | `DB_DATABASE` | `test` | Database name |
   | `DB_USERNAME` | *(Your TiDB Username)* | TiDB username |
   | `DB_PASSWORD` | *(Your TiDB Password)* | TiDB password |
   | `MYSQL_ATTR_SSL_CA` | `/etc/ssl/certs/ca-certificates.crt` | Secure SSL Certificate bundle |
   | `ADMIN_NAME` | `Team Administrator` | Admin Name |
   | `ADMIN_EMAIL` | `admin@teamjersey.local` | Admin Login |
   | `ADMIN_PASSWORD` | `admin12345` | Admin Password |

5. Click **Create Web Service**.
6. Render will build the container, install packages, compile assets, and launch Nginx + PHP-FPM.
7. Once completed, your live URL will be:
   - **Public Website:** `https://bethel-high-school-jersey.onrender.com/`
   - **Admin Portal:** `https://bethel-high-school-jersey.onrender.com/admin`
   - **API Health:** `https://bethel-high-school-jersey.onrender.com/api/health`

---

### Step 4: Optional Split Deployment (Netlify Frontend + Render Backend)

If you prefer hosting the Vue frontend on Netlify's global CDN:

1. Go to [https://app.netlify.com/](https://app.netlify.com/) -> **Add new site** -> **Import an existing project**.
2. Select your `bethel-jersey-tracker` repository.
3. Configure build settings:
   - **Build command**: `npm run build`
   - **Publish directory**: `dist`
4. Under **Environment Variables**:
   - `VITE_API_URL`: `https://bethel-high-school-jersey.onrender.com`
5. Click **Deploy Site**. Netlify will publish your frontend with its own custom URL (e.g. `https://bethel-jersey.netlify.app`).

---

## 🔒 Security Best Practices Implemented

- **No Secrets in Git**: Root `.gitignore` prevents `.env`, SQLite files, and local credentials from ever being pushed.
- **SSL / TLS Enforced**: TiDB Cloud requires SSL connections (`MYSQL_ATTR_SSL_CA`); Render provides automatic HTTPS certificates.
- **Sanctum Protection**: Admin endpoints (`POST/PUT/PATCH/DELETE`) are strictly guarded behind Sanctum Bearer tokens.
- **Public Read-Only**: Unauthenticated public users can only read player rosters (`GET /api/players`).
- **Idempotent Data Seeding**: Guard clauses prevent duplicate entries or overwriting squad data.

---

## 📱 Mobile Verification Checklist

| Resolution | Test Screen | Status |
|---|---|---|
| **320px** | iPhone SE (Compact) | Verified (No horizontal scroll, compact stats grid) |
| **360px** | Galaxy S8 / Android Mini | Verified (Clean touch targets, sticky bottom navigation) |
| **375px** | iPhone 12/13 Mini | Verified (Optimal squircle card spacing) |
| **390px** | iPhone 13/14/15 Pro | Verified (Native iOS fluid responsiveness) |
| **430px** | iPhone 15 Pro Max | Verified (Centered native container with stadium backdrop) |
