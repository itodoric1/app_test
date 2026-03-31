<?php
/** @var \App\Models\User $user */
/** @var array $settings */
/** @var array $stats */
/** @var array $flash */

$selectionStatus = $settings['selekcija'] ?? 'otvoreno';
?>
<!doctype html>
<html lang="hr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administracija</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Administracija</h1>
            <div class="text-muted"><?= htmlspecialchars($user->ime . ' ' . $user->prezime, ENT_QUOTES, 'UTF-8') ?></div>
        </div>
        <div class="d-flex gap-2">
            <a href="/home" class="btn btn-outline-secondary">Home</a>
            <a href="/logout" class="btn btn-outline-danger">Logout</a>
        </div>
    </div>

    <?php if (!empty($flash['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars((string) $flash['success'], ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if (!empty($flash['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars((string) $flash['error'], ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Korisnici</div><div class="h3 mb-0"><?= (int) ($stats['korisnici'] ?? 0) ?></div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Aktivni upisi</div><div class="h3 mb-0"><?= (int) ($stats['aktivni_upisi'] ?? 0) ?></div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Prijave zbirno</div><div class="h3 mb-0"><?= (int) ($stats['prijave_zbirno'] ?? 0) ?></div></div></div></div>
        <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Odabrani</div><div class="h3 mb-0"><?= (int) ($stats['odabrani'] ?? 0) ?></div></div></div></div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm mb-4"><div class="card-body">
                <h2 class="h5">Postavke aplikacije</h2>

                <form method="post" action="/admin" class="row g-3 mt-1">
                    <input type="hidden" name="action" value="update_year">
                    <div class="col-8"><input class="form-control" name="value" value="<?= htmlspecialchars((string) ($settings['godina'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>
                    <div class="col-4"><button class="btn btn-primary w-100">Spremi godinu</button></div>
                </form>

                <form method="post" action="/admin" class="row g-3 mt-1">
                    <input type="hidden" name="action" value="update_version">
                    <div class="col-8"><input class="form-control" name="value" value="<?= htmlspecialchars((string) ($settings['verzija'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>
                    <div class="col-4"><button class="btn btn-primary w-100">Spremi verziju</button></div>
                </form>

                <form method="post" action="/admin" class="row g-3 mt-1">
                    <input type="hidden" name="action" value="update_bank_serial">
                    <div class="col-8"><input class="form-control" name="value" value="<?= htmlspecialchars((string) ($settings['redni_broj_bankarskih'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>
                    <div class="col-4"><button class="btn btn-primary w-100">Spremi redni broj</button></div>
                </form>

                <form method="post" action="/admin" class="row g-3 mt-1">
                    <input type="hidden" name="action" value="update_term">
                    <div class="col-8"><input class="form-control" name="value" value="<?= htmlspecialchars((string) ($settings['termin_odrzavanja'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>
                    <div class="col-4"><button class="btn btn-primary w-100">Spremi termin</button></div>
                </form>

                <form method="post" action="/admin" class="row g-3 mt-1">
                    <input type="hidden" name="action" value="update_signup_date">
                    <div class="col-8"><input class="form-control" name="value" value="<?= htmlspecialchars((string) ($settings['datum_prijave'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>
                    <div class="col-4"><button class="btn btn-primary w-100">Spremi datum</button></div>
                </form>
            </div></div>

            <div class="card shadow-sm"><div class="card-body">
                <h2 class="h5">Stanje aplikacije</h2>
                <p class="mb-3">Trenutni status: <span class="badge text-bg-secondary"><?= htmlspecialchars($selectionStatus, ENT_QUOTES, 'UTF-8') ?></span></p>
                <div class="d-grid gap-2">
                    <form method="post" action="/admin">
                        <input type="hidden" name="action" value="open_app">
                        <button class="btn btn-success w-100">Otključaj aplikaciju</button>
                    </form>
                    <form method="post" action="/admin">
                        <input type="hidden" name="action" value="lock_app">
                        <button class="btn btn-warning w-100">Zaključaj za selekciju</button>
                    </form>
                    <form method="post" action="/admin">
                        <input type="hidden" name="action" value="publish_results">
                        <button class="btn btn-dark w-100">Generiraj konačnu listu</button>
                    </form>
                </div>
            </div></div>
        </div>

        <div class="col-lg-6">
            <div class="card border-warning shadow-sm mb-4"><div class="card-body">
                <h2 class="h5">Servisne akcije</h2>
                <p class="text-muted small">Reset čisti sportske prijave i vraća korisnike u stanje ponovnog popunjavanja. Brisanje svih ostavlja samo administratore.</p>
                <div class="d-grid gap-2">
                    <form method="post" action="/admin">
                        <input type="hidden" name="action" value="reset_data">
                        <button class="btn btn-outline-warning w-100">Resetiraj prijave</button>
                    </form>
                    <form method="post" action="/admin">
                        <input type="hidden" name="action" value="delete_all">
                        <button class="btn btn-outline-danger w-100">Obriši sve korisnike i prijave</button>
                    </form>
                </div>
            </div></div>

            <div class="card border-danger shadow-sm"><div class="card-body">
                <h2 class="h5 text-danger">Brisanje pojedinog korisnika</h2>
                <form method="post" action="/admin" class="row g-3">
                    <input type="hidden" name="action" value="delete_user">
                    <div class="col-8"><input class="form-control" name="korisnik_id" type="number" min="1" placeholder="ID korisnika"></div>
                    <div class="col-4"><button class="btn btn-danger w-100">Obriši korisnika</button></div>
                </form>
                <p class="small text-muted mt-3 mb-0">Akcija briše korisnika iz tablica korisnici, prijava i prijave_zbirno. Administratore ne briše.</p>
            </div></div>
        </div>
    </div>
</div>
</body>
</html>
