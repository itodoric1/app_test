<?php
/** @var array $profile */
/** @var array $application */
/** @var array $organizations */
/** @var array $disciplines */
/** @var array $allowedPhonePrefixes */
/** @var array $allowedShirtSizes */
/** @var array $flash */

$disciplineOptions = [];
foreach ($disciplines as $discipline) {
    $disciplineOptions[(int) $discipline['id']] = $discipline['naziv'];
}
?>
<!doctype html>
<html lang="hr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil korisnika</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Profil korisnika</h1>
            <div class="text-muted"><?= htmlspecialchars(($profile['ime'] ?? '') . ' ' . ($profile['prezime'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
        </div>
        <a href="/home" class="btn btn-outline-secondary">Home</a>
    </div>

    <?php if (!empty($flash['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars((string) $flash['success'], ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if (!empty($flash['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars((string) $flash['error'], ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm mb-4"><div class="card-body">
                <h2 class="h5">Osobni podaci</h2>

                <form method="post" action="/profile" class="row g-3 mt-1">
                    <input type="hidden" name="action" value="update_name">
                    <div class="col-8"><input class="form-control" name="ime" value="<?= htmlspecialchars((string) ($profile['ime'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>
                    <div class="col-4"><button class="btn btn-primary w-100">Spremi ime</button></div>
                </form>

                <form method="post" action="/profile" class="row g-3 mt-1">
                    <input type="hidden" name="action" value="update_surname">
                    <div class="col-8"><input class="form-control" name="prezime" value="<?= htmlspecialchars((string) ($profile['prezime'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>
                    <div class="col-4"><button class="btn btn-primary w-100">Spremi prezime</button></div>
                </form>

                <form method="post" action="/profile" class="row g-3 mt-1">
                    <input type="hidden" name="action" value="update_birth_year">
                    <div class="col-8"><input class="form-control" name="god_rodenja" value="<?= htmlspecialchars((string) ($profile['god_rodenja'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>
                    <div class="col-4"><button class="btn btn-primary w-100">Spremi godinu</button></div>
                </form>

                <form method="post" action="/profile" class="row g-3 mt-1">
                    <input type="hidden" name="action" value="update_email">
                    <div class="col-8"><input type="email" class="form-control" name="email" value="<?= htmlspecialchars((string) ($profile['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>
                    <div class="col-4"><button class="btn btn-warning w-100">Spremi e-mail</button></div>
                </form>

                <form method="post" action="/profile" class="row g-3 mt-1">
                    <input type="hidden" name="action" value="update_phone">
                    <div class="col-4">
                        <select class="form-select" name="pozivni">
                            <option value="">Pozivni</option>
                            <?php foreach ($allowedPhonePrefixes as $prefix): ?>
                                <option value="<?= $prefix ?>" <?= ($profile['pozivni'] ?? '') === $prefix ? 'selected' : '' ?>><?= $prefix ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-4"><input class="form-control" name="telefon" value="<?= htmlspecialchars((string) ($profile['telefon'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></div>
                    <div class="col-4"><button class="btn btn-primary w-100">Spremi telefon</button></div>
                </form>

                <form method="post" action="/profile" class="row g-3 mt-1">
                    <input type="hidden" name="action" value="update_organization">
                    <div class="col-8">
                        <select class="form-select" name="org_jed">
                            <option value="">Odaberi organizacijsku jedinicu</option>
                            <?php foreach ($organizations as $organization): ?>
                                <option value="<?= (int) $organization['id'] ?>" <?= (int) ($profile['org_jed'] ?? 0) === (int) $organization['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($organization['name'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-4"><button class="btn btn-primary w-100">Spremi org. jed.</button></div>
                </form>

                <form method="post" action="/profile" class="row g-3 mt-1">
                    <input type="hidden" name="action" value="update_shirt_size">
                    <div class="col-8">
                        <select class="form-select" name="konf_br">
                            <?php foreach ($allowedShirtSizes as $size): ?>
                                <option value="<?= $size ?>" <?= ($profile['konf_br'] ?? '') === $size ? 'selected' : '' ?>><?= strtoupper($size) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-4"><button class="btn btn-primary w-100">Spremi veličinu</button></div>
                </form>
            </div></div>

            <div class="card shadow-sm"><div class="card-body">
                <h2 class="h5">Promjena lozinke</h2>
                <form method="post" action="/profile" class="row g-3 mt-1">
                    <input type="hidden" name="action" value="update_password">
                    <div class="col-12"><input type="password" class="form-control" name="lozinka_1" placeholder="Nova lozinka"></div>
                    <div class="col-12"><input type="password" class="form-control" name="lozinka_2" placeholder="Ponovi lozinku"></div>
                    <div class="col-12"><button class="btn btn-primary">Spremi lozinku</button></div>
                </form>
            </div></div>
        </div>

        <div class="col-lg-6">
            <?php for ($slot = 1; $slot <= 3; $slot++): ?>
                <div class="card shadow-sm mb-4"><div class="card-body">
                    <h2 class="h5">Sport <?= $slot ?></h2>
                    <form method="post" action="/profile" class="row g-3">
                        <input type="hidden" name="action" value="update_sport_<?= $slot ?>">
                        <div class="col-12">
                            <select class="form-select" name="sport_<?= $slot ?>">
                                <option value="">Odaberi disciplinu</option>
                                <?php foreach ($disciplineOptions as $disciplineId => $disciplineName): ?>
                                    <option value="<?= $disciplineId ?>" <?= (int) ($application['id_sport_' . $slot] ?? 0) === $disciplineId ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($disciplineName, ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <textarea class="form-control" name="opis_<?= $slot ?>" rows="4" placeholder="Iskustvo / napomena"><?= htmlspecialchars((string) ($application['opis_' . $slot] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                        <div class="col-6"><button class="btn btn-success w-100">Spremi sport</button></div>
                    </form>
                    <form method="post" action="/profile" class="mt-2">
                        <input type="hidden" name="action" value="delete_sport_<?= $slot ?>">
                        <button class="btn btn-outline-danger w-100">Obriši prijavu sporta <?= $slot ?></button>
                    </form>
                </div></div>
            <?php endfor; ?>

            <div class="card border-danger shadow-sm"><div class="card-body">
                <h2 class="h5 text-danger">Opasna zona</h2>
                <p class="text-muted small mb-3">Brisanje korisnika briše zapis iz tablica korisnici, prijava i prijave_zbirno.</p>
                <form method="post" action="/profile">
                    <input type="hidden" name="action" value="delete_user">
                    <button class="btn btn-danger">Obriši korisnički račun</button>
                </form>
            </div></div>
        </div>
    </div>
</div>
</body>
</html>
