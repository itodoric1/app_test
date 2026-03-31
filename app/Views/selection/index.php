<!doctype html>
<html lang="hr">
<head>
    <meta charset="utf-8">
    <title>Finasport - rezultati selekcije</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; color: #111; }
        .muted { color: #555; }
        .flash-success { color: #0a5a1f; }
        .flash-error { color: #8b0000; }
        .cards { display: flex; gap: 12px; flex-wrap: wrap; margin: 20px 0; }
        .card { border: 1px solid #ddd; border-radius: 8px; padding: 12px; min-width: 170px; }
        table { border-collapse: collapse; width: 100%; margin: 18px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: top; }
        th { background: #f7f7f7; }
        .center { text-align: center; }
        .small { font-size: 13px; }
    </style>
</head>
<body>
    <h1>Rezultati selekcije</h1>
    <p class="muted">Status selekcije: <strong><?= htmlspecialchars((string) $status, ENT_QUOTES, 'UTF-8') ?></strong></p>

    <?php if (!empty($flash['success'])): ?>
        <p class="flash-success"><?= htmlspecialchars((string) $flash['success'], ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <?php if (!empty($flash['error'])): ?>
        <p class="flash-error"><?= htmlspecialchars((string) $flash['error'], ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <?php if ($status === 'zatvoreno'): ?>
        <p>Selekcijski postupak je u tijeku. Pristup rezultatima je privremeno zatvoren.</p>
    <?php elseif ($status === 'otvoreno'): ?>
        <p>Prijave su otvorene. Konačni rezultati još nisu objavljeni.</p>
    <?php else: ?>
        <div class="cards">
            <div class="card"><strong>Prijavljene žene</strong><br><?= (int) $totals['registered_women'] ?></div>
            <div class="card"><strong>Prijavljeni muškarci</strong><br><?= (int) $totals['registered_men'] ?></div>
            <div class="card"><strong>Ukupno prijavljeni</strong><br><?= (int) $totals['registered_total'] ?></div>
            <div class="card"><strong>Odabrane žene</strong><br><?= (int) $totals['selected_women'] ?></div>
            <div class="card"><strong>Odabrani muškarci</strong><br><?= (int) $totals['selected_men'] ?></div>
            <div class="card"><strong>Ukupno odabrani</strong><br><?= (int) $totals['selected_total'] ?></div>
        </div>

        <h2>Prijavljeni / odabrani po organizacijskim jedinicama</h2>
        <table>
            <thead>
                <tr>
                    <th>Rb</th>
                    <th>Organizacijska jedinica</th>
                    <th class="center">Prijavljene Ž</th>
                    <th class="center">Prijavljeni M</th>
                    <th class="center">Prijavljeni ukupno</th>
                    <th class="center">Odabrane Ž</th>
                    <th class="center">Odabrani M</th>
                    <th class="center">Odabrani ukupno</th>
                </tr>
            </thead>
            <tbody>
            <?php $i = 1; foreach ($byOrganization as $row):
                $regTotal = (int) $row['registered_women'] + (int) $row['registered_men'];
                $selTotal = (int) $row['selected_women'] + (int) $row['selected_men'];
                if ($regTotal === 0 && $selTotal === 0) { continue; }
            ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars((string) $row['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="center"><?= (int) $row['registered_women'] ?></td>
                    <td class="center"><?= (int) $row['registered_men'] ?></td>
                    <td class="center"><?= $regTotal ?></td>
                    <td class="center"><?= (int) $row['selected_women'] ?></td>
                    <td class="center"><?= (int) $row['selected_men'] ?></td>
                    <td class="center"><?= $selTotal ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Prijavljeni / odabrani po sportovima</h2>
        <table>
            <thead>
                <tr>
                    <th>Rb</th>
                    <th>Sport</th>
                    <th class="center">Prijavljene Ž</th>
                    <th class="center">Prijavljeni M</th>
                    <th class="center">Prijavljeni ukupno</th>
                    <th class="center">Odabrane Ž</th>
                    <th class="center">Odabrani M</th>
                    <th class="center">Odabrani ukupno</th>
                </tr>
            </thead>
            <tbody>
            <?php $i = 1; foreach ($bySport as $row):
                $regTotal = (int) $row['registered_women'] + (int) $row['registered_men'];
                $selTotal = (int) $row['selected_women'] + (int) $row['selected_men'];
                if ($regTotal === 0 && $selTotal === 0) { continue; }
            ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars((string) $row['naziv'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="center"><?= (int) $row['registered_women'] ?></td>
                    <td class="center"><?= (int) $row['registered_men'] ?></td>
                    <td class="center"><?= $regTotal ?></td>
                    <td class="center"><?= (int) $row['selected_women'] ?></td>
                    <td class="center"><?= (int) $row['selected_men'] ?></td>
                    <td class="center"><?= $selTotal ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Odabrani sudionici</h2>
        <table>
            <thead>
                <tr>
                    <th>Ime i prezime</th>
                    <th>Organizacijska jedinica</th>
                    <th>Konf. broj</th>
                    <th>Sport 1</th>
                    <th>Sport 2</th>
                    <th>Sport 3</th>
                    <th>Sport 4</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($participants as $row): ?>
                <tr>
                    <td><?= htmlspecialchars(trim(($row['ime'] ?? '') . ' ' . ($row['prezime'] ?? '')), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) ($row['org_jed'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) ($row['konf_br'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) ($row['sport_1'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) ($row['sport_2'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) ($row['sport_3'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) ($row['sport_4'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (!empty($vipParticipants)): ?>
            <h2>VIP / organizacija</h2>
            <table class="small">
                <thead>
                    <tr>
                        <th>Ime i prezime</th>
                        <th>Organizacijska jedinica</th>
                        <th>E-mail</th>
                        <th>Telefon</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($vipParticipants as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars(trim(($row['ime'] ?? '') . ' ' . ($row['prezime'] ?? '')), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) ($row['org_jed'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) ($row['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(trim(($row['pozivni'] ?? '') . ' ' . ($row['telefon'] ?? '')), ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
