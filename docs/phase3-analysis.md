# Phase 3 - administracija.php refaktor

U ovoj fazi je uveden prvi OOP admin modul kao zamjena za veliki legacy file `prijava/administracija.php`.

## Dodano
- `AdminController`
- `AdminService`
- `AdminRepository`
- `app/Views/admin/index.php`
- routeovi `GET /admin` i `POST /admin`

## Pokriveni use-caseovi
- pregled osnovnih statistika sustava
- ažuriranje postavki:
  - godina
  - verzija
  - redni broj bankarskih
  - termin održavanja
  - datum prijave
- promjena statusa aplikacije:
  - `otvoreno`
  - `zatvoreno`
  - `rezultat`
- reset prijava
- brisanje svih neadministratorskih korisnika i njihovih prijava
- brisanje pojedinog korisnika po ID-u
- generiranje finalne tablice `odabrani`

## Što je poboljšano
- SQL je izdvojen iz view sloja
- svi upiti koriste PDO prepared statements gdje se unose parametri
- destruktivne akcije rade unutar transakcija
- admin pristup je ograničen na korisnike s `tip_korisnika = 1`
- `SettingsRepository` sada ima `all()` i `set()` metode

## Namjerno još nije riješeno 1:1
- puni legacy HTML iz stare administracije nije prenešen jedan-na-jedan
- list viewovi i export stranice još nisu migrirani
- koordinatorske podstranice i detaljni izvještaji ostaju za sljedeću fazu

## Predloženi Phase 4
Najbolji sljedeći korak je refaktor `selekcija.php` i `selekcija_1.php` u zaseban modul:
- `SelectionController`
- `SelectionService`
- `SelectionRepository`
- pregled kandidata po disciplini
- odabir / poništavanje odabira kandidata
