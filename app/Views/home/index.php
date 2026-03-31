<!doctype html>
<html lang="hr">
<head>
    <meta charset="utf-8">
    <title>Finasport - home</title>
</head>
<body>
    <h1>Finasport</h1>
    <?php if (!empty($context['user'])): ?>
        <p>Pozdrav, <?= htmlspecialchars($context['user']->ime . ' ' . $context['user']->prezime, ENT_QUOTES, 'UTF-8') ?></p>
        <p>Selekcija: <?= htmlspecialchars((string) ($context['selekcija'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
        <p>Godina: <?= htmlspecialchars((string) ($context['godina'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
        <p>
            <a href="/profile">Profil</a>
            <?php if (($context['user']->tipKorisnika ?? 3) === 1): ?>
                | <a href="/admin">Administracija</a>
            <?php endif; ?>
            | <a href="/selection">Rezultati</a>
            <?php if (($context['user']->tipKorisnika ?? 3) === 1): ?>
                | <a href="/selection/manage">Selekcija</a>
            <?php endif; ?>
            | <a href="/logout">Logout</a>
        </p>
    <?php else: ?>
        <p>Nema korisničkog konteksta.</p>
    <?php endif; ?>
</body>
</html>
