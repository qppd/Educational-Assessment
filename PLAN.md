# 🎓 TOPCIT Educational Assessment — Modernization Plan

> **Status:** Planning Phase  
> **Target:** Production-ready, modernized Laravel 12 + Tailwind + Livewire app  
> **Brand:** TOPCIT (Tech-based Online Platform for Collaborative and Interactive Testing)

---

## Table of Contents

1. [Current State Assessment](#1-current-state-assessment)
2. [Dependency Upgrades](#2-dependency-upgrades)
3. [UI/UX Redesign — 2026 Modern Futuristic](#3-uiux-redesign)
4. [Security Fixes](#4-security-fixes)
5. [Bug Fixes & Flow Improvements](#5-bug-fixes)
6. [Database Migration Plan](#6-database-migration-plan)
7. [Phase Breakdown](#7-phase-breakdown)
8. [Architecture Improvements](#8-architecture-improvements)
9. [File Change Manifest](#9-file-change-manifest)

---

## 1. Current State Assessment

### What Works

| Feature | Status | Notes |
|---------|--------|-------|
| Admin CRUD (users, exams, students, profs) | ✅ | Working with session auth |
| Student registration + photos for face rec | ✅ | Saves to `storage/images/students/` |
| Exam attempt + auto-scoring | ✅ | Multiple choice, enumeration, fill-in-blank |
| Faculty question submission + approval workflow | ✅ | Faculty submits → Admin approves |
| Reviewer file upload/download | ✅ | PDF, DOC, PPT, XLS support |
| Leaderboard/rankings | ✅ | Basic percentage score ranking |
| Face API integration | ⚠️ | Present but browser-based only, limited |

### Current Tech Stack (Outdated)

| Library | Current Version | Latest Stable |
|---------|----------------|---------------|
| Laravel | **10.22.0** | **12.x** |
| PHP | ^8.1 | ^8.3/8.4 |
| AdminLTE | **3.x** (Bootstrap 4) | AdminLTE 4 (Bootstrap 5) or replace |
| Bootstrap | **4.1.1** | **5.3.x** |
| jQuery | **3.2.1** | 3.7.x (legacy) |
| Intervention Image | **dev-master** | 3.x |
| PhpSpreadsheet | **1.29.0** | 3.x+ |
| Vite | **4.x** | **6.x** |
| face-api.js | face-api.min.js | face-api.js v0.22+ |

### Code Quality Issues

- **3 duplicate include directories** (`includes/`, `includes2/`, `includes3/`) — ~90% identical, massive maintenance burden
- **Hardcoded CSS** in blade views (inline `<style>` blocks everywhere)
- **Mixed PHP/Blade** — some views use `.php` instead of `.blade.php`
- **Dead code** — commented-out queries litter controllers
- **No Form Requests** — validation inline in controllers
- **No Service layer** — business logic mixed with controllers
- **Plain SQL dump committed** (`database/u750085622_topcit.sql`) — contains real student data
- **Two public directories** (`public/` and `public_html/`) — duplicated assets

---

## 2. Dependency Upgrades

### PHP/Laravel (Phase 1)

```bash
composer require laravel/framework:^12.0
composer require php:^8.3
composer require laravel/sanctum:^4.0
composer require intervention/image:^3.0
composer require maatwebsite/excel:^3.2
```

### Frontend (Phase 2)

```bash
# Replace AdminLTE + Bootstrap 4 with modern stack
npm remove vite laravel-vite-plugin
npm install -D vite@^6 laravel-vite-plugin@^1

# New stack
npm install tailwindcss@^4 @tailwindcss/vite
npm install alpinejs@^3                    # Lightweight reactive framework
npm install aos@^2                         # Scroll animations
npm install @fontsource/inter              # Modern font

# Keep for exam functionality (until Livewire migration)
npm install jquery@^3.7
npm install datatables.net-bs5
```

**Tailwind CSS v4 replaces:**
- ✅ Bootstrap 4 CSS framework
- ✅ AdminLTE theme
- ✅ All custom inline CSS
- ✅ All CDN-loaded stylesheets

**Alpine.js replaces:**
- ✅ jQuery for UI interactions (modals, toggles, notifications)
- ✅ Bootstrap JS (via `@alpinejs/collapse`, `@alpinejs/focus`)
- ✅ Custom JS for sidebar, navbar, preloader

---

## 3. UI/UX Redesign

### 3.1 Design System

**Color Palette** — Cyber-academic theme:

```
Primary:    #6366F1 (Indigo-500)   — trust, intelligence
Secondary:  #06B6D4 (Cyan-500)     — technology, clarity
Accent:     #F59E0B (Amber-500)    — achievement, rank
Success:    #10B981 (Emerald-500)  — correct, pass
Danger:     #EF4444 (Red-500)      — wrong, fail
Dark:       #0F172A (Slate-900)    — backgrounds
Surface:    #1E293B (Slate-800)    — cards, panels
```

**Typography:**
- Headings: `Inter` (sans-serif) — weights 700, 600, 500
- Body: `Inter` — weight 400, size 14-16px
- Mono: `JetBrains Mono` — for code/reviewer content
- Scale: `text-xs` (12) → `text-sm` (14) → `text-base` (16) → `text-lg` (18) → `text-xl` (20) → `text-2xl` (24) → `text-3xl` (30) → `text-4xl` (36)

**Spacing:** Tailwind default scale (4px base)

**Border radius:** `rounded-lg` (8px) for cards, `rounded-xl` (12px) for modals, `rounded-full` for avatars

**Shadows:** `shadow-sm` → `shadow-lg` → `shadow-2xl` with colored variants

### 3.2 Key Pages — Visual Design

#### Landing Page (Portal) — `/portal`
```
┌──────────────────────────────────────────────────────────┐
│ [TOPCIT Logo]                    [Login] [Register]      │  ← Glassmorphism navbar
├──────────────────────────────────────────────────────────┤
│                                                          │
│   ┌────────────────────────────────────────────────┐     │
│   │  🎓  "Learn. Compete. Succeed."               │     │  ← Gradient text, AOS fade-in
│   │  [Get Started] [Learn More]                    │     │
│   └────────────────────────────────────────────────┘     │
│                                                          │
│   ┌────┐ ┌────┐ ┌────┐ ┌────┐                          │
│   │📝  │ │📸  │ │🏆  │ │📊  │                          │  ← Feature cards, staggered entrance
│   │Quiz│ │Face│ │Rank│ │Anal│                          │
│   └────┘ └────┘ └────┘ └────┘                          │
│                                                          │
│   "Trusted by X students at [University Name]"          │  ← Social proof
│                                                          │
└──────────────────────────────────────────────────────────┘
```

- Dark gradient background with animated particle wave (CSS only, no heavy libs)
- Glassmorphism cards (`bg-white/10 backdrop-blur-lg`)
- Animated gradient text for tagline
- Feature cards with hover-tilt effect (Alpine.js)
- Face recognition badge with camera icon animation

#### Student Dashboard — `/portal/dashboard`
```
┌──────────────────────────────────────────────────────────┐
│  ← Dashboard                    [👤 Student Name]       │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  ┌────────────┐ ┌────────────┐ ┌────────────┐           │
│  │ 📝 Exams   │ │ 📊 Avg     │ │ 🏆 Rank    │           │  ← Metric cards with animated
│  │   5 Avail  │ │   78%      │ │   #3 / 50  │           │     counters (countUp-style)
│  └────────────┘ └────────────┘ └────────────┘           │
│                                                          │
│  ┌──────────────────────────────────────────────────┐    │
│  │  Available Examinations                          │    │  ← Table with search/sort
│  │  ┌────┬────────────┬────────┬──────┬────────┐   │    │
│  │  │ #  │ Title      │ Items  │ Time │ Action │   │    │
│  │  ├────┼────────────┼────────┼──────┼────────┤   │    │
│  │  │ 1  │ ELEC 4     │ 10     │ 30m  │ [Take] │   │    │
│  │  └────┴────────────┴────────┴──────┴────────┘   │    │
│  └──────────────────────────────────────────────────┘    │
│                                                          │
│  ┌──────────────────────────────────────────────────┐    │
│  │  🏆 Leaderboard                                  │    │  ← Top 10, animated trophy icons
│  │  🥇 Juan D. — 95%                               │    │
│  │  🥈 Maria S. — 92%                               │    │
│  └──────────────────────────────────────────────────┘    │
│                                                          │
└──────────────────────────────────────────────────────────┘
```

#### Exam Attempt — Immersive Mode
```
┌──────────────────────────────────────────────────────────┐
│  ELEC 4 — Graphics and Visual Computing   ⏱ 12:34       │  ← Sticky timer, turns red <5min
├──────────────────────────────────────────────────────────┤
│                                                          │
│  Question 3 of 10                                        │
│  ┌──────────────────────────────────────────────────┐    │
│  │  _______ are also based on text, but typically    │    │
│  │  only use initials or an abbreviation.            │    │
│  │                                                   │    │
│  │  ○ A. Lettermarks                                 │    │  ← Large clickable radio cards
│  │  ○ B. Emblems                                     │    │     with hover scale effect
│  │  ○ C. Brand Marks                                 │    │
│  │  ○ D. Combination marks                           │    │
│  └──────────────────────────────────────────────────┘    │
│                                                          │
│  [◄ Previous]                    [Next ►]                │
│                                                          │
│  ○ ○ ● ○ ○ ○ ○ ○ ○ ○   (progress bar)                   │  ← Step indicator
│                                                          │
└──────────────────────────────────────────────────────────┘
```

- Full-screen minimal mode (hide navbar during exam)
- Large, readable question cards
- Keyboard shortcuts (1/2/3/4 for MCQ, arrows for navigation)
- Auto-save on each answer (AJAX)
- Warn before submit if unanswered
- Face verification snapshot (optional) — take photo before exam starts

#### Admin Panel — `/admin/`
```
┌──────────────────────────────────────────────────────────┐
│  ☰          🎓 TOPCIT Admin            👤 Admin         │
├──────────┬───────────────────────────────────────────────┤
│          │                                               │
│  📊 Dash │  ┌────┐ ┌────┐ ┌────┐ ┌────┐                │
│  👥 Users│  │12  │ │45  │ │8   │ │230 │                │  ← Animated stat cards
│  📝 Exams│  │Adms│ │Prof│ │Exm │ │Std │                │
│  ❓ Quest│  └────┘ └────┘ └────┘ └────┘                │
│  📁 Rev. │                                               │
│  📸 Face │  Recent Activity Feed                         │
│  ⚙️ Setti│  ┌──────────────────────────────────────┐    │
│          │  │ ● New professor registered           │    │
│          │  │ ● Exam "ELEC 4" activated            │    │
│          │  │ ● 5 questions pending approval        │    │
│          │  └──────────────────────────────────────┘    │
│          │                                               │
│          │  🏆 Leaderboard Preview (Top 5)                │
└──────────┴───────────────────────────────────────────────┘
```

- Dark sidebar with icons + tooltips
- Collapsible sidebar (icon-only mode)
- Breadcrumb navigation
- Quick-action buttons (Add Exam, Approve Questions)

### 3.3 Animations & Transitions

| Element | Animation | Implementation |
|---------|-----------|---------------|
| Page load | Content fade-in-up | AOS `data-aos="fade-up"` |
| Card hover | Scale 1.02 + lift shadow | Tailwind `hover:scale-[1.02]` |
| Modal open | Scale + fade | Alpine.js `x-transition` |
| Stat counter | Count-up 0→N | Alpine.js `x-intersect` |
| Sidebar | Smooth slide | Tailwind `transition-all` |
| Button click | Ripple effect | CSS `::after` pseudo |
| Exam timer | Pulse when <5min | CSS `animate-pulse` |
| Success/wrong | Check/X icon pop | Tailwind `animate-bounce` |
| Page transition | Quick crossfade | Tailwind `transition-opacity` |
| Particle bg | Floating dots | CSS-only (no heavy lib) |

### 3.4 Component Library

All reusable UI components will be built as **Blade components** (app/View/Components/):

| Component | Description |
|-----------|-------------|
| `<x-card>` | Glassmorphism card with slot |
| `<x-stat-card>` | Metric card with icon, number, label |
| `<x-modal>` | Reusable modal with Alpine.js |
| `<x-table>` | Styled table with DataTables integration |
| `<x-button>` | Primary/secondary/danger variants |
| `<x-badge>` | Status badge (pending/active/finished) |
| `<x-avatar>` | User avatar with fallback initials |
| `<x-alert>` | Success/error/warning alert |
| `<x-input>` | Styled input with label + error |
| `<x-select>` | Styled select with options |
| `<x-loading>` | Skeleton loader |
| `<x-progress>` | Progress bar (exam timer, completion) |
| `<x-radio-card>` | Large clickable radio for exam options |
| `<x-header>` | Page header with breadcrumb |
| `<x-sidebar>` | Navigation sidebar |
| `<x-navbar>` | Top navigation bar |

### 3.5 Responsive Behavior

| Page | Mobile | Tablet | Desktop |
|------|--------|--------|---------|
| Landing | Stack vertically | 2-col features | Full layout |
| Dashboard | Single col, cards stack | 2-col stat cards | 3-col stats + table + rank |
| Exam attempt | Full-width, larger tap targets | Comfortable | Centered card |
| Admin panel | Bottom nav, drawers | Collapsed sidebar | Full sidebar |
| Tables | Horizontal scroll | Responsive | Full width |
| Modals | Full-screen drawer | Centered 90% | Centered 60% |

---

## 4. Security Fixes

### 🔴 Critical

| Issue | File | Fix |
|-------|------|-----|
| **No CSRF on some POST routes** | `routes/web.php` (several) | Verify `@csrf` in all forms |
| **XSS in question display** | `examination_attempt.blade.php` — `{{ $question->question }}` may contain HTML | Use `{!! $question->question !!}` only after `strip_tags` or Blade's `{{ }}` for raw text |
| **SQL dump with real data in repo** | `database/u750085622_topcit.sql` | Remove from repo, add to `.gitignore`, regenerate with factory seeders |
| **Plain "default" password check** | `LoginController.php`, `FacultyController.php` | Remove; use `Hash::needsRehash()` or password expiry tracking instead |

### 🟠 High

| Issue | File | Fix |
|-------|------|-----|
| **No rate limiting on login** | All login routes | Add `RateLimiter` middleware to `/admin/login`, `/faculty/login`, `/portal/login` |
| **No email verification** | `User.php` | Enable `MustVerifyEmail` interface for student accounts |
| **File upload MIME bypass** | `ReviewerController` | Add file content validation (not just extension) |
| **Weak password policy** | `register.blade.php` | Enforce min 8 chars, require uppercase + number |
| **Session fixation** | `LoginController.php` | Call `$request->session()->regenerate()` after login |
| **No HTTPS enforcement** | `.env.example` | Set `FORCE_HTTPS=true`, add `TrustProxies` middleware |

### 🟡 Medium

| Issue | Fix |
|-------|-----|
| **CORS misconfiguration** | Review `config/cors.php` |
| **Storage symlink public access** | Verify `php artisan storage:link` is in setup |
| **Debug mode** | Ensure `APP_DEBUG=false` in production |
| **No failed login tracking** | Add throttle: 5 attempts → 15min lockout |
| **Default passwords** | Password expiry: force change on first login |

---

## 5. Bug Fixes & Flow Improvements

### Known Bugs

| Bug | Location | Fix |
|-----|----------|-----|
| `addStudent` uses `editStudentt` in route | `routes/web.php:66` | Fix typo → `editStudent` |
| `portal.blade.php` loads HTTP CDN resources over HTTPS site = mixed content | `portal.blade.php:1-3` | Switch to `https://` or use npm/bundled |
| `$validation` returns array, `!$validated` never true | `StudentController.php:78-79` | `$request->validate()` returns array, use `$validated` proper |
| Image Intervention `Image::make()` returns object, null check wrong | Several controllers | Check `$thumbnail` result properly |
| `!$isValidated` never triggers on null | `FacultyController.php:84` | `$request->validate()` returns array, not boolean |
| `saveNewPassword` — no `ppassword` column | `PortalController.php:498` | Fix field name or add validation |
| `administratorupdatePassword` — may allow any admin to change any password | `LoginController.php` | Add current password check |
| Duplicate session key checks in login views redirect to same | Multiple routes | Consolidate |
| `QuestionController` sets `choice_2` twice (overwrites choice_b on line 40) | `QuestionController.php:35-39` | Remove redundant set |

### Flow Improvements

| Improvement | Details |
|-------------|---------|
| **Exam auto-save** | Save answers by AJAX every 30s during exam |
| **Submit confirmation** | Modal: "You have X unanswered questions. Submit anyway?" |
| **Password reset flow** | Add proper token-based forgot password instead of weak "forgot" page |
| **Bulk import UX** | Drag-and-drop zone for CSV/Excel student/professor upload |
| **Email notifications** | Notify students when exam is available (optional) |
| **Exam scheduling** | Auto-activate/deactivate exams based on `examination_at` |
| **Face verification flow** | Smooth camera permission flow + retry if face not detected |
| **Exam result breakdown** | Show which questions wrong + correct answer after submission |
| **Progress saving** | Resume incomplete exams (don't lose answers if browser crashes) |

---

## 6. Database Migration Plan

### Consolidate Schema

Currently the app has **2 schema sources**:
1. Laravel migrations (4 stock tables — users, password_resets, failed_jobs, personal_access_tokens)
2. Raw SQL dump (8 custom tables — answers, examinations, professors, questions, results, reviewers, students, users)

**Action:** Create proper migrations for the 8 custom tables, then remove the raw SQL dump.

### New migrations needed:

| # | Migration | Purpose |
|---|-----------|---------|
| 1 | `create_students_table` | student_no, lastname, firstname, middlename, photo, status |
| 2 | `create_professors_table` | (kept alongside users for role tracking) |
| 3 | `create_examinations_table` | title, duration, limit, status, description, examination_at, administrator_id |
| 4 | `create_questions_table` | examination_id, question text, type, choices, answer, status, professor_id |
| 5 | `create_answers_table` | student_id, examination_id, question_id, student_answer, status |
| 6 | `create_results_table` | user_id, examination_id, score, remarks, status |
| 7 | `create_reviewers_table` | examination_id, professor_id, file, status |
| 8 | `update_users_table` | Add role, surname, middlename, photo, contact, status columns |

### Seeders:

| Seeder | Data |
|--------|------|
| `AdministratorSeeder` | 1 super admin (with proper hashed password) |
| `ExaminationSeeder` | Sample exam data for testing |
| `QuestionSeeder` | Sample questions per exam |

---

## 7. Phase Breakdown

### Phase 0: Cleanup & Foundation (1 session)
- [ ] Remove SQL dump, add to `.gitignore`
- [ ] Consolidate `includes/`, `includes2/`, `includes3/` → single `includes/`
- [ ] Remove dead commented code from controllers
- [ ] Remove duplicate `public_html/` (consolidate to `public/`)
- [ ] Add `.editorconfig`, proper `.gitignore`
- [ ] Update `composer.json` requirements (Laravel 12, PHP 8.3)
- [ ] Run `composer update`

### Phase 1: Security Hardening (1 session)
- [ ] Add CSRF tokens to all forms
- [ ] Add rate limiting to all login routes
- [ ] Fix all validation bugs (`!$validated`, `!$isValidated`)
- [ ] Fix `QuestionController` duplicate choice_2 bug
- [ ] Fix `addStudent` typo route
- [ ] Add `session()->regenerate()` after login
- [ ] Remove "default" password string detection
- [ ] Add password strength requirements
- [ ] Fix `portal.blade.php` HTTP→HTTPS resources
- [ ] Enable `MustVerifyEmail` on User model

### Phase 2: Frontend Migration (2 sessions)
- [ ] Install Tailwind v4 + Vite 6 + Alpine.js
- [ ] Remove AdminLTE/CDN assets
- [ ] Create Blade component library (`<x-card>`, `<x-modal>`, etc.)
- [ ] Rebuild **Portal landing page** — dark theme, glassmorphism, AOS animations
- [ ] Rebuild **Login/Register** — animated form with face rec badge
- [ ] Rebuild **Student Dashboard** — stat cards, tables, leaderboard
- [ ] Rebuild **Exam Attempt** — immersive full-screen mode
- [ ] Rebuild **Exam Results** — animated score reveal
- [ ] Rebuild **Admin Panel** — collapsible sidebar, dark mode toggle
- [ ] Rebuild **Faculty Portal** — consistent with admin layout
- [ ] Add AOS scroll animations to all pages
- [ ] Add particle background on landing page (CSS only)

### Phase 3: Bug Fixes & Flow (1 session)
- [ ] Fix Image Intervention null checks
- [ ] Fix password reset flow (token-based)
- [ ] Add exam auto-save via AJAX
- [ ] Add submit confirmation modal
- [ ] Add unanswered question warning
- [ ] Fix session management (regenerate on role change)
- [ ] Add keyboard shortcuts in exam mode

### Phase 4: Features & Polish (1 session)
- [ ] Add dark mode toggle (Alpine.js + Tailwind `dark:`)
- [ ] Add email notifications (new exam, grades available)
- [ ] Add exam scheduling daemon (auto-activate at `examination_at`)
- [ ] Add bulk import drag-and-drop UI
- [ ] Add comprehensive error pages (403, 404, 500)
- [ ] Add loading skeletons everywhere
- [ ] Add Toast notifications (success/error)
- [ ] Add responsive mobile layout

### Phase 5: Testing & Deploy (1 session)
- [ ] Add feature tests for all CRUD flows
- [ ] Add exam attempt + scoring test
- [ ] Security scan (SQL injection, XSS, CSRF)
- [ ] Performance audit + optimize queries
- [ ] Final cleanup (remove dead files, console.logs)

---

## 8. Architecture Improvements

### Current:
```
Blade + AdminLTE + jQuery + Bootstrap 4 → Controller → Eloquent → MySQL
                                                                   ↓
                                                              Raw SQL dump
```

### Target:
```
Blade + Tailwind v4 + Alpine.js + Vite 6 → Controller (FormRequest) → Service → Eloquent → MySQL
                                                          ↓
                                            View/Components (Blade)
                                                          ↓
                                            Feature Tests (PHPUnit)
```

### File structure cleanup:
```
resources/views/
├── components/          # Blade components
│   ├── card.blade.php
│   ├── modal.blade.php
│   ├── table.blade.php
│   └── ...
├── admin/               # Admin portal layouts
│   ├── dashboard.blade.php
│   ├── examinations.blade.php
│   └── ...
├── faculty/
│   └── dashboard.blade.php
├── portal/              # Student portal
│   ├── landing.blade.php
│   ├── dashboard.blade.php
│   ├── exam/
│   │   ├── attempt.blade.php
│   │   └── result.blade.php
│   └── ...
├── layouts/
│   ├── admin.blade.php
│   ├── faculty.blade.php
│   ├── portal.blade.php
│   └── exam.blade.php
├── includes/
│   ├── navbar.blade.php
│   ├── sidebar.blade.php
│   ├── footer.blade.php
│   └── scripts.blade.php
├── auth/
│   ├── login.blade.php
│   ├── register.blade.php
│   └── forgot-password.blade.php
└── errors/
    ├── 403.blade.php
    ├── 404.blade.php
    └── 500.blade.php
```

---

## 9. File Change Manifest

### Files to DELETE:
```
database/u750085622_topcit.sql     # Raw dump with real data → .gitignore
resources/views/includes2/         # Consolidate into includes/
resources/views/includes3/         # Consolidate into includes/
resources/views/available.php      # Dead file
resources/views/exams.blade.php    # Dead file
resources/views/test.blade.php     # Dead file
resources/views/examination_student_result.php  # Duplicate of .blade.php
public_html/                       # Duplicate of public/
public/faceapi/                    # Move to resources/js/vendor/
node_modules/                      # Regenerated by npm install
```

### Files to MODIFY (~20 controllers/views):

| File | Change |
|------|--------|
| `routes/web.php` | Fix `editStudentt` typo, add rate limiting, consolidate routes |
| `app/Http/Controllers/*` | Remove dead code, fix validation bugs, add FormRequests |
| `app/Models/User.php` | Add `MustVerifyEmail`, update fillable/casts |
| `app/Models/*.php` | Add `HasFactory`, proper relationships |
| `config/database.php` | Clean config |
| `config/app.php` | Update timezone, locale |
| `config/cors.php` | Tighten origin restrictions |
| `composer.json` | Bump all versions |
| `package.json` | Replace AdminLTE with Tailwind + Alpine |
| `vite.config.js` | Update for Vite 6 |
| `resources/views/portal.blade.php` | Full UI rebuild |
| `resources/views/login.blade.php` | Full UI rebuild |
| `resources/views/faculty.blade.php` | Full UI rebuild |
| `resources/views/dash.blade.php` | Full UI rebuild |
| `resources/views/*` | Replace Bootstrap/Tailwind mix → pure Tailwind |
| `resources/views/includes/header.blade.php` | Vite config |
| `resources/views/includes/scripts.blade.php` | Vite config |
| `.env.example` | Update with new vars |
| `.gitignore` | Add `database/*.sql`, `public_html/`, etc. |

### Files to CREATE (~25 new files):

| File | Purpose |
|------|---------|
| `app/View/Components/Card.php` | Blade component |
| `app/View/Components/StatCard.php` | Blade component |
| `app/View/Components/Modal.php` | Blade component |
| `app/View/Components/Table.php` | Blade component |
| `app/View/Components/Button.php` | Blade component |
| `app/View/Components/Badge.php` | Blade component |
| `app/View/Components/Alert.php` | Blade component |
| `app/View/Components/Avatar.php` | Blade component |
| `app/View/Components/Input.php` | Blade component |
| `app/View/Components/RadioCard.php` | Blade component |
| `app/View/Components/Loading.php` | Skeleton loader |
| `app/View/Components/Select.php` | Form select |
| `app/View/Components/Progress.php` | Progress bar |
| `app/Http/Requests/*.php` | ~8 FormRequest classes |
| `app/Services/ExamService.php` | Business logic |
| `app/Services/FaceRecognitionService.php` | Face API logic |
| `app/Http/Middleware/ExamAccess.php` | Exam-specific middleware |
| `database/migrations/*.php` | ~8 new migrations |
| `database/seeders/*.php` | ~4 seeders |
| `resources/views/layouts/*.blade.php` | ~4 layout files |
| `resources/views/components/*.blade.php` | ~13 component views |
| `resources/css/app.css` | Tailwind entry point |
| `resources/js/app.js` | Alpine.js + custom JS entry |
| `resources/js/exam.js` | Exam-specific JS (timer, auto-save) |
| `resources/js/face-recognition.js` | face-api.js wrapper |

---

## Implementation Order

```
Week 1:  Phase 0 (Cleanup) + Phase 1 (Security) → deploy to staging
Week 2:  Phase 2a (Tailwind migration + landing page + auth pages)
Week 3:  Phase 2b (Dashboard + Exam + Admin rebuild)
Week 4:  Phase 3 (Bug fixes + Flow improvements)
Week 5:  Phase 4 (Features + Polish)
Week 6:  Phase 5 (Testing + Deploy)
```

**Total: ~6 weeks, ~45 files changed, ~25 new files, ~15 files deleted**

---

*Plan last updated: May 2026*