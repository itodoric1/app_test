# Faza 2 - refaktor `prijava/postavke.php`

Ova faza razbija legacy `postavke.php` na manje OOP dijelove.

## Što je izdvojeno

- `ProfileController`
  - GET `/profile` za prikaz profila
  - POST `/profile` za obradu akcija
- `ProfileService`
  - validacija i business pravila za profil
  - promjena lozinke, imena, prezimena, godine rođenja, e-maila, telefona, org. jedinice, konfekcijskog broja
  - prijava/odjava sportova 1-3
  - brisanje korisnika
- `ProfileRepository`
  - dohvat i update podataka tablice `korisnici`
- `SportApplicationRepository`
  - dohvat i update tablice `prijava`
  - sinkronizacija tablice `prijave_zbirno`
- `OrganizationRepository`
  - dohvat `organizacija`
- `DisciplineRepository`
  - dohvat `discipline`
- `app/Views/profile/index.php`
  - novi čisti view za profil bez SQL-a i bez poslovne logike

## Što je riješeno u odnosu na stari file

- nema `global $dbc`
- nema SQL-a u view-u
- nema desetaka malih update funkcija u istom page fileu
- uveden je jedan ulazni endpoint i jedan service sloj
- upiti su preko PDO prepared statements
- brisanje korisnika radi u transakciji
- validacija je centralizirana u service klasi

## Što još ostaje za sljedeću fazu

- preseliti postojeći Bootstrap/HTML iz starog `postavke.php` u partiale/layoute
- dodati CSRF zaštitu
- uvesti middleware za auth i role-based access
- odvojiti mail/logging
- refaktor `administracija.php`
- refaktor `selekcija.php`

## Napomena

Ovo nije 1:1 kopija cijelog starog `postavke.php`, nego čista OOP osnova i radni smjer za migraciju. Ideja je da dalje selimo dio po dio bez pucanja cijele aplikacije.
