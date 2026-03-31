# Finasport OOP refactor – faza 1

## Što je pokriveno
- novi `public/index.php` front controller
- mini router
- `Database` wrapper preko PDO + prepared statements
- `Session` sloj
- `AuthService` za login/logout
- `RegistrationService` za registraciju
- `UserRepository`, `RegistrationRepository`, `SettingsRepository`
- početni `HomeController`
- fallback podrška za stari SHA-512 hash uz automatski rehash na `password_hash()` kod prve uspješne prijave

## Legacy → OOP mapiranje
- `prijava/configBP.php` → `app/Core/Database.php` + `.env`
- `prijava/session.php` → `app/Core/Session.php`
- `prijava/index.php` login dio → `AuthController` + `AuthService`
- `prijava/registracija.php` / `registracija_korisnika.php` → `RegistrationController` + `RegistrationService`
- dijelovi `prijava/header.php` → `UserContextService`

## Zašto je ovo prvi dobar rez
Prijava i registracija su ulazne točke aplikacije. Kad se to odvoji iz page-fileova:
1. uklanja se globalni `$dbc`
2. uvodi se centralno upravljanje sessionom
3. uvodi se testabilan auth sloj
4. može se postupno migrirati `postavke.php`, `prijava.php`, `administracija.php`, `selekcija.php`

## Kritični rizici koje treba riješiti odmah
- maknuti stvarne podatke i DB lozinke iz deploy paketa
- promijeniti lozinku iz starog `configBP.php`
- SQL dump držati izvan web roota
- dodati CSRF zaštitu na sve write forme
- postepeno ugasiti direktne include pozive između page fileova

## Sljedeća faza
1. `UserProfileController` + `UserProfileService` za `postavke.php`
2. `SelectionController` + repozitoriji za `prijava` i `discipline`
3. `AdminController` + `SettingsService` za `administracija.php`
4. layout/template sloj da se makne HTML iz poslovne logike
