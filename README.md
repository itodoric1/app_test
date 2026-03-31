# Finasport OOP starter

Ovo je početni paket za refaktor legacy aplikacije u OOP bez frameworka.

## Pokretanje
1. Kopiraj `.env.example` u `.env`
2. Upiši nove DB podatke
3. U rootu pokreni `composer dump-autoload` ili ostavi ugrađeni autoload iz `bootstrap/app.php`
4. Neka web server gađa `public/`

## Napomena o lozinkama
Stara aplikacija koristi SHA-512 hash bez salta. Ovaj starter podržava:
- prijavu postojećih korisnika
- automatsku zamjenu starog hasha u `password_hash()` pri prvoj uspješnoj prijavi

## Što još nije migrirano
- `postavke.php`
- `prijava.php`
- `administracija.php`
- `selekcija.php`
- mailovi
- exporti
- svi admin listing fileovi

## Preporučeni rollout
- prvo digni ovo lokalno na kopiji baze
- potvrdi login/registraciju
- onda modul po modul seli stare funkcije u service/repository sloj


## Phase 2

- `/profile` route added
- `postavke.php` broken into controller/service/repository/view layers
- profile, password, phone, organization, shirt size and sports 1-3 updates included
