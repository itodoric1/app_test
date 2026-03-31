<!doctype html>
<html lang="hr">
<head>
    <meta charset="utf-8">
    <title>Finasport - upravljanje selekcijom</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; color: #111; }
        .flash-success { color: #0a5a1f; }
        .flash-error { color: #8b0000; }
        .cards { display: flex; gap: 12px; flex-wrap: wrap; margin: 20px 0; }
        .card { border: 1px solid #ddd; border-radius: 8px; padding: 12px; min-width: 170px; }
        table { border-collapse: collapse; width: 100%; margin: 18px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: top; }
        th { background: #f7f7f7; }
        form { margin: 0; }
        .actions { white-space: nowrap; }
    </style>
</head>
<body>
    <h1>Upravljanje selekcijom</h1>
    <p>Administrator: <?= htmlspecialchars($user->ime . ' ' . $user->prezime, ENT_QUOTES, 'UTF-8') ?></p>
    <p>Status selekcije: <strong><?= htmlspecialchars((string) $status, ENT_QUOTES, 'UTF-8') ?></strong></p>
    <p><a href="/admin">Administracija</a> | <a href="/selection">Javni prikaz rezultata</a> | <a href="/home">Home</a></p>

    <?php if (!empty($flash['success'])): ?>
        <p class="flash-success"><?= htmlspecialchars((string) $flash['success'], ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <?php if (!empty($flash['error'])): ?>
        <p class="flash-error"><?= htmlspecialchars((string) $flash['error'], ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <div class="cards">
        <div class="card"><strong>Prijavljene žene</strong><br><?= (int) $totals['registered_women'] ?></div>
        <div class="card"><strong>Prijavljeni muškarci</strong><br><?= (int) $totals['registered_men'] ?></div>
        <div class="card"><strong>Ukupno prijavljeni</strong><br><?= (int) $totals['registered_total'] ?></div>
        <div class="card"><strong>Odabrane žene</strong><br><?= (int) $totals['selected_women'] ?></div>
        <div class="card"><strong>Odabrani muškarci</strong><br><?= (int) $totals['selected_men'] ?></div>
        <div class="card"><strong>Ukupno odabrani</strong><br><?= (int) $totals['selected_total'] ?></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Korisnik</th>
                <th>Org. jedinica</th>
                <th>Sport</th>
                <th>Redni sport</th>
                <th>Spol</th>
                <th>Konf. broj</th>
                <th>Status</th>
                <th>Opis</th>
                <th>Akcija</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($candidates as $row): ?>
            <tr>
                <td><?= (int) $row['id'] ?></td>
                <td><?= htmlspecialchars(trim($row['ime'] . ' ' . $row['prezime']), ENT_QUOTES, 'UTF-8') ?><br><small><?= htmlspecialchars((string) $row['email'], ENT_QUOTES, 'UTF-8') ?></small></td>
                <td><?= htmlspecialchars((string) $row['org_jed'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) $row['sport_naziv'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= (int) $row['broj_sporta'] ?></td>
                <td><?= htmlspecialchars(mb_strtoupper((string) $row['spol']), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) $row['konf_br'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) $row['odabran'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string) ($row['opis'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="actions">
                    <form method="post" action="/selection/manage">
                        <input type="hidden" name="entry_id" value="<?= (int) $row['id'] ?>">
                        <input type="hidden" name="selected" value="<?= ($row['odabran'] ?? 'ne') === 'da' ? 0 : 1 ?>">
                        <button type="submit"><?= ($row['odabran'] ?? 'ne') === 'da' ? 'Makni' : 'Odaberi' ?></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
