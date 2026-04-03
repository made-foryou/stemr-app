# Vota — Stemtool MVP

Een stemtool waarmee een organisator opties aanmaakt, deelnemers per e-mail uitnodigt, en iedereen aangeeft welke opties ze leuk vinden. De optie met de meeste likes wint.

## Techstack

| Laag | Technologie |
|------|-------------|
| Framework | Laravel 13 (PHP 8.5) |
| Authenticatie | Laravel Fortify + Socialite |
| Frontend | Livewire 4 + Flux UI v2 |
| Styling | Tailwind CSS v4 (Aubergine thema) |
| Typografie | Outfit (via Bunny Fonts) |
| Database | SQLite (MySQL/PostgreSQL in productie) |
| Testing | Pest 4 |
| Bundler | Vite |

## Features

### Authenticatie
- Registratie en login via e-mail/wachtwoord
- Google OAuth (Socialite)
- Apple Sign In (Socialite community provider)
- E-mailverificatie (MustVerifyEmail)
- Tweestapsverificatie (TOTP)
- Wachtwoord resetten

### Account instellingen (`/account`)
- Wachtwoord instellen (SSO-only users) of wijzigen
- Google/Apple koppelen en loskoppelen
- Veiligheidscheck: provider pas loskoppelen als er een alternatieve inlogmethode is

### Poll dashboard (`/dashboard`)
- Overzicht van alle polls van de ingelogde gebruiker
- Empty state met geanimeerde orbital graphic
- Poll cards met titel, status badge (open/gesloten), aanmaakdatum
- Gesorteerd op nieuwste eerst

### Datamodel
- **Users** — naam, e-mail, wachtwoord (nullable), google_id, apple_id, 2FA
- **Polls** — uuid, slug, titel, beschrijving, status (open/closed), user_id

## Installatie

```bash
git clone <repository-url>
cd vota
composer run setup
```

Dit draait `composer install`, `npm install`, migraties en `npm run build`.

### Omgevingsvariabelen

Kopieer `.env.example` naar `.env` en configureer:

```env
APP_NAME=Vota
APP_URL=https://vota.test

GOOGLE_CLIENT_ID=       # Google Cloud Console
GOOGLE_CLIENT_SECRET=

APPLE_CLIENT_ID=        # Apple Developer Console
APPLE_CLIENT_SECRET=
```

## Development

```bash
composer run dev
```

Dit start de Herd PHP server, queue worker, log viewer (Pail) en Vite dev server tegelijk.

## Tests

```bash
vendor/bin/pest              # Alle tests
vendor/bin/pest --coverage   # Met coverage rapport
vendor/bin/pest --compact    # Compact overzicht
```

Huidige status: **81 tests, 207 assertions, 100% coverage**.

## Code style

```bash
vendor/bin/pint              # Fix alle bestanden
vendor/bin/pint --dirty      # Alleen gewijzigde bestanden
```

## Projectstructuur

```
app/
├── Enums/PollStatus.php
├── Http/Controllers/Auth/
│   ├── GoogleController.php
│   └── AppleController.php
├── Livewire/
│   ├── Dashboard.php
│   └── AccountSettings.php
├── Models/
│   ├── User.php
│   └── Poll.php
├── Concerns/
│   ├── PasswordValidationRules.php
│   └── ProfileValidationRules.php
├── Actions/Fortify/
│   ├── CreateNewUser.php
│   └── ResetUserPassword.php
└── Providers/
    ├── AppServiceProvider.php
    └── FortifyServiceProvider.php

resources/views/
├── layouts/
│   ├── app.blade.php
│   └── auth/
│       ├── split.blade.php
│       ├── simple.blade.php
│       └── card.blade.php
├── livewire/
│   ├── dashboard.blade.php
│   └── account-settings.blade.php
├── pages/auth/
│   ├── login.blade.php
│   ├── register.blade.php
│   ├── forgot-password.blade.php
│   ├── reset-password.blade.php
│   ├── verify-email.blade.php
│   ├── confirm-password.blade.php
│   └── two-factor-challenge.blade.php
└── components/
    ├── social-login-buttons.blade.php
    ├── auth-header.blade.php
    └── app-logo-icon.blade.php
```

## Thema

Vota gebruikt het **Aubergine** kleurenthema:

| Token | Hex | Gebruik |
|-------|-----|---------|
| Primary | `#6B3568` | Knoppen, logo, actieve states |
| Primary dark | `#522A4F` | Auth linker paneel |
| Primary light | `#F5E8F4` | Badge achtergronden |
| Accent | `#C4703F` | Terracotta highlights |
| Background | `#FAF7F2` | Warme roomwitte achtergrond |
| Text | `#2A1A28` | Tekst |

## Licentie

Eigendom van Made.
