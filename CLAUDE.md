# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Environment

This project runs inside Docker via Laravel Sail (WSL/Linux). All `artisan`, `npm`, and `composer` commands should be prefixed with `sail`:

```bash
sail up -d                                        # start the Docker environment
sail npm run dev                                  # watch and rebuild frontend files
sail npm run build                                # production JS build
sail artisan migrate                              # run pending migrations
sail artisan migrate --seed                       # migrate and seed (fresh setup)
sail artisan route:clear && sail artisan route:cache  # required after any route changes
sail artisan test                                 # run all tests
sail artisan test --filter=TestClassName          # run a single test class
sail artisan test --filter=test_method_name       # run a single test method
```

## Architecture Overview

**Stack:** Laravel + Jetstream (auth scaffolding) + Inertia.js + Vue 3 + Tailwind CSS. Controllers do not render Blade views — they return `Inertia::render('Page/Name', [...])` responses that load Vue SPA pages from `resources/js/Pages/`.

**Request flow:** HTTP request → Laravel route → Controller (auth/gate checks, data shaping) → `Inertia::render()` → Vue component in `resources/js/Pages/`.

**Frontend structure:**
- `resources/js/Pages/` — full-page Vue components (one per route)
- `resources/js/Components/` — reusable UI components
- `resources/js/Layouts/AppLayout.vue` — the main authenticated shell
- `resources/js/utils.js` — shared JS helpers

## Domain Model

The core domain revolves around **Courses → Lessons → Participants**:

- **Course** — a recurring class (e.g. "Beginner Taiko"). Has `capacity`, `signout_limit` (hours before lesson that free cancellation closes), `teacher_payment`, and `assist_payment`. Belongs to one or more **Teams**.
- **Lesson** — a single session within a Course. Has `start`/`finish` timestamps (stored UTC, displayed in `Europe/Zurich` via `config('app.timezone_default')`).
- **LessonUser** (pivot) — links users to lessons with a `participation` enum, `message`, and `remind_at` for push notification scheduling.
- **User** — has a `role` (admin/teacher/student) and a `karma` counter. Karma is decremented on sign-in and incremented on timely sign-out.

**Compensations:** A course can designate other courses as "compensation courses" (stored in the `compensations` table). This allows members to attend a lesson from a different course when making up for a missed one.

## Authorization

Three Gates defined in [app/Providers/AuthServiceProvider.php](app/Providers/AuthServiceProvider.php):

| Gate | Who |
|---|---|
| `edit-users` | admin only |
| `edit-courses` | admin, teacher |
| `assist-lessons` | admin, teacher |

All authenticated routes use `auth:sanctum` + `verified` middleware. The three public non-auth routes (`/scheduler`, `/remindUsers`, `/cancelWaitlist`) trigger Artisan commands directly via HTTP — this is how the hosting server's cron calls them.

## Lesson Participation Lifecycle

`LessonParticipationEnum` values: `signed_in`, `signed_out`, `teacher`, `assistance`, `late`, `no_show`, `waitlist`.

- **Sign-in**: if `karma !== 0` and capacity has room → `SIGNED_IN`; otherwise → `WAITLIST`.
- **Sign-out**: if cancellation is within `signout_limit` hours of start (and not on waitlist), karma is **not** refunded. Otherwise karma is refunded.
- **Waitlist promotion**: when a `SIGNED_IN` participant signs out, the oldest `WAITLIST` entry is promoted to `SIGNED_IN` and sent a `LessonConfirmed` push notification.
- **Karma = null**: means infinite karma (user can always sign in, but karma is never modified).

## Timezone Handling

Dates are stored in UTC in the database. In controllers, user-submitted datetime strings are parsed as `Europe/Zurich` and converted to UTC before saving:

```php
Carbon::parse($validated['start'], config('app.timezone_default'))->setTimezone('UTC')
```

When passing dates to the frontend, they are converted back:
```php
$lesson->start->startOfMinute()->inApplicationTz()->toDateTimeLocalString()
```

## Notifications

Push notifications use `NotificationChannels\WebPush` (via `HasPushSubscriptions` on User). Notification classes live in `app/Notifications/`. The `remind_at` field on `lesson_user` is set automatically via `LessonUser::booted()` and `setReminder()` based on the user's `settings.lessonNotificationTime` preference.

## Testing

Tests are almost entirely Jetstream boilerplate in `tests/Feature/`. The test database is `testing` (set in `phpunit.xml`). There are no application-specific tests yet.
