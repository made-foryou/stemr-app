# Changelog

Alle wijzigingen op de `made-136` branch ten opzichte van `main`.

## Epic 0 — Authenticatie & accounts

### MADE-136: Account aanmaken via e-mail
- Registratie met naam, e-mailadres en wachtwoord (min. 8 tekens)
- E-mailverificatie via `MustVerifyEmail`
- Redirect naar dashboard na verificatie
- Wachtwoord minimum 8 tekens in alle omgevingen
- Aubergine kleurenthema (Outfit font, warme roomwitte achtergrond)
- Custom auth layout (split-screen met geanimeerde orbital graphic)
- Alle auth views vertaald naar Nederlands
- Flux UI componenten behouden met custom CSS overrides

### MADE-138: Registreren en inloggen met Google
- Laravel Socialite geïnstalleerd
- Google OAuth redirect/callback flow
- Account aanmaken bij onbekend e-mailadres
- Account samenvoegen bij bestaand e-mailadres
- `google_id` kolom op users tabel, `password` nullable gemaakt
- "Doorgaan met Google" knop op login en register pagina

### MADE-139: Registreren en inloggen met Apple
- `socialiteproviders/apple` community provider geïnstalleerd
- Apple OAuth redirect/callback flow (POST callback)
- CSRF-uitzondering voor Apple callback
- Event listener registratie voor community provider
- `apple_id` kolom op users tabel
- "Doorgaan met Apple" knop op login en register pagina
- Fallback naam "Apple User" wanneer Apple geen naam retourneert

### MADE-140: Poll dashboard
- Poll model met UUID, slug, status enum (open/closed)
- Polls migratie, factory en User relatie (`hasMany`)
- Livewire Dashboard component (full-page)
- Empty state met orbital graphic en "Maak je eerste poll" CTA
- Poll lijst met cards (titel, status badge, aanmaakdatum)
- Gestaggerde slide-up animaties op poll cards
- Placeholder routes voor `/poll/create` en `/poll/{slug}/manage`

### MADE-151: Account instellingen
- Livewire AccountSettings component op `/account`
- Wachtwoord instellen (SSO-only users) en wijzigen (met huidig wachtwoord)
- Google en Apple loskoppelen met veiligheidscheck
- Google en Apple koppelen vanuit account instellingen
- OAuth controllers uitgebreid: ingelogde users kunnen providers koppelen
- Bescherming tegen loskoppelen zonder alternatieve inlogmethode
- Account link in dashboard header

### Vertalingen
- `lang/nl.json` — Nederlandse vertalingen voor validatie, auth, dashboard, account
- `lang/nl/passwords.php` — Wachtwoord reset berichten
- `lang/nl/auth.php` — Login foutmeldingen

## Statistieken

- **81 tests**, 207 assertions
- **100% code coverage**
- PHP-bestanden geformateerd met Laravel Pint
