<!doctype html>
<html lang="hr">
<head>
    <meta charset="utf-8">
    <title>Finasport - prijava</title>
</head>
<body>
    <h1>Prijava</h1>
    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form method="post" action="/login">
        <label>Email <input type="email" name="email" required></label><br>
        <label>Lozinka <input type="password" name="pass" required></label><br>
        <button type="submit">Prijava</button>
    </form>

    <p><a href="/register">Registracija</a></p>
</body>
</html>
