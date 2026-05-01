# NNHS Student Grades Management System (SGMS)

A centralized, high-performance portal for managing, submitting, and tracking student grades at NNHS. Built with Laravel and Supabase, featuring a stark "Refined Editorial" user interface.

## Key Features

- **Teacher Grade Entry**: Streamlined, spreadsheet-like interface for entering Written Work, Performance Tasks, and Quarterly Exams.
- **Admin Heatmap Dashboard**: Real-time visualization for the principal and administrators to track grade submission progress across all sections and subjects.
- **Role-Based Access Control**: Strict access boundaries between Admin, Teacher, and Adviser roles using Spatie Permissions.
- **High-Performance Architecture**: Custom Eloquent caching and file-based session handling to eliminate remote Supabase latency during routine lookups.
- **Refined Editorial UI**: A distinctive, brutalist-inspired interface optimized for high data density and professional utility.

## Technology Stack

- **Framework**: Laravel 11
- **Database**: Supabase (PostgreSQL)
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js
- **Assets**: Vite
- **Typography**: Google Fonts (Outfit, DM Sans)

## Local Development Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com/Aedwon/nnhs-tracking.git
   cd nnhs-tracking
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install and compile frontend assets:**
   ```bash
   npm install
   npm run build
   ```

4. **Environment Configuration:**
   Copy `.env.example` to `.env` and configure your database and caching:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Important settings for Supabase + Local Cache:*
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=aws-0-[region].pooler.supabase.com
   DB_PORT=6543
   DB_DATABASE=postgres
   DB_USERNAME=postgres.[project_ref]
   DB_PASSWORD=[your_password]

   CACHE_STORE=file
   SESSION_DRIVER=file
   ```

5. **Run Migrations and Seeders:**
   ```bash
   php artisan migrate --seed
   ```

6. **Serve the application:**
   ```bash
   php artisan serve
   ```
   Navigate to `http://localhost:8000`.

## Caching Note
If you experience stale data after modifying roles or users directly in the database, clear the local file cache:
```bash
php artisan cache:clear
```
