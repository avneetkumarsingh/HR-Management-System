# AttendMS - Attendance Management System

A complete Laravel 10 Keka-like Attendance Management System built with pure CSS.

## Features
- **Authentication**: Login, Logout, Profile Management (with Avatar upload).
- **Dashboard**: Live Clock, Status Cards, Stat Counters, Mini-tables.
- **Attendance**:
  - Web Check-in / Check-out with IP tracking.
  - Interactive Daily and Monthly Reports.
  - Visual 7-day Attendance Calendar.
  - Team Attendance & Admin Reports.
  - Regularization workflow for missed check-ins.
- **Leaves**:
  - Centralized Leave Type configurations.
  - Yearly balance carry forwards logic.
  - Multi-level Leave Approvals & Rejections.
- **Employees**:
  - Directory listing & advanced search.
  - Comprehensive multi-section Employee Profile CRUD.
- **Design System**: Fully bespoke teal/emerald pure CSS framework without Tailwind or Bootstrap constraints.
- **Reports**: KPI Dashboards, Analytics summaries, Attendance metrics.

## Requirements
- PHP 8.1+
- MySQL 8.0+
- Composer

## Installation
1. Clone the repository and `cd` into it.
2. Run `composer install`
3. Prepare `.env`: `cp .env.example .env` and update your DB credentials.
4. Run `php artisan key:generate`
5. Map storage symlink: `php artisan storage:link`
6. Migrate and Seed Database: `php artisan migrate:fresh --seed`
7. Start Server: `php artisan serve`

## Default Credentials
| Role | Email | Password |
|---|---|---|
| Super Admin | admin@company.com | password |
| Manager | alex@company.com | password |
| Employee | employee4@company.com | password |
