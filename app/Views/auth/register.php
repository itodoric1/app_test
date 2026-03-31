<!doctype html>
<html lang="hr">
<head>
    <meta charset="utf-8">
    <title>Finasport - registracija</title>
</head>
<body>
    <h1>Registracija</h1>
    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <p style="color:green;"><?= htmlspecialchars((string) $success, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form method="post" action="/register">
        <label>Ime <input type="text" name="ime" required></label><br>
        <label>Prezime <input type="text" name="prezime" required></label><br>
        <label>Spol <input type="text" name="spol" maxlength="1" value="m"></label><br>
        <label>Email <input type="email" name="email" required></label><br>
        <label>Lozinka <input type="password" name="lozinka1" required></label><br>
        <label>Ponovi lozinku <input type="password" name="lozinka2" required></label><br>
        <button type="submit">Spremi</button>
    </form>
</body>
</html>
