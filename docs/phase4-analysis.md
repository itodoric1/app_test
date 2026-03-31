# Faza 4 - Selekcija u OOP-u

U ovoj fazi razbijen je legacy modul `selekcija.php` / `selekcija_1.php` u zasebne slojeve:

- `SelectionController`
- `SelectionService`
- `SelectionRepository`
- view-ovi:
  - `app/Views/selection/index.php`
  - `app/Views/selection/manage.php`

## Što sada postoji

### Javni prikaz rezultata
Route: `GET /selection`

Prikazuje:
- trenutni status selekcije iz tablice `postavke`
- zbirne brojeve prijavljenih i odabranih kandidata
- analizu po organizacijskim jedinicama
- analizu po sportovima
- finalni popis odabranih iz tablice `odabrani`
- VIP / organizacijski dio iz tablice `odabrani`

### Admin upravljanje selekcijom
Routeovi:
- `GET /selection/manage`
- `POST /selection/manage`

Prikazuje pregled svih prijava iz `prijave_zbirno` i omogućuje toggle polja `odabran` na razini pojedine sportske prijave.

## Bitne promjene

- SQL je izbačen iz view datoteka.
- Status selekcije ide kroz repository.
- Upravljanje odabirom kandidata ide kroz service layer.
- Access control za admin dio ide preko `tip_korisnika = 1`.
- Svi upiti koriste PDO prepared statements.

## Što još NIJE 1:1 migrirano

Ovo je čisti i održivi OOP temelj modula, ali nije još potpuna replika svih legacy detalja:
- napredno sortiranje/tab plugin iz stare verzije
- svi specifični exporti/tablice iz helper list_* datoteka
- kompletna vizualna kopija starog HTML/CSS layouta
- sva rubna pravila koja su bila razbacana po legacy include fileovima

## Preporučeni idući korak

Faza 5:
- `prijava.php`
- povezivanje prijave sportova sa selekcijom end-to-end
- čišćenje `list_*` legacy ispisa u odvojene report/repository klase
