<?php
session_start();

// Controleer of de gebruiker is ingelogd
$ingelogd = isset($_SESSION['ingelogd']) && $_SESSION['ingelogd'] === true;
$gebruikersnaam = $ingelogd ? $_SESSION['gebruiker'] : "Gast";

// ✅ Controleer of een adres is opgeslagen vanuit de `Pizza_Order` tabel
$heeftAdres = $ingelogd && isset($_SESSION['adres']) && !empty($_SESSION['adres']);

// ✅ Debugging: Toon de volledige sessie om te controleren wat er gebeurt
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Controleer of een bestelling in de winkelwagen zit
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<script>alert('Je winkelwagen is leeg!'); window.location.href='menu.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestelling Overzicht | Pizzeria di Rick</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <button onclick="window.location.href='pizzeriaDiRick.php'">Home</button>
        <button onclick="window.location.href='Menu.php'">Menu</button>
        <button onclick="window.location.href='order.php'">Bestelling Plaatsen</button>

        <?php if ($ingelogd): ?>
            <button onclick="window.location.href='profiel.php'">👤 <?= htmlspecialchars($gebruikersnaam) ?></button>
            <button onclick="window.location.href='uitloggen.php'">Uitloggen</button>
        <?php else: ?>
            <button onclick="window.location.href='inloggen.php'">Inloggen</button>
        <?php endif; ?>
    </div>

    <div class="container">
        <h2>Jouw Bestelling</h2>

        <p><strong>Totaalprijs:</strong> €<?= number_format(array_sum(array_column($_SESSION['cart'], 'price')), 2, ',', '.') ?></p>

        <?php if ($heeftAdres): ?>
            <p>📍 <strong>Bezorgadres:</strong> <?= htmlspecialchars($_SESSION['adres']) ?></p>
            <form action="bestelling_bevestigd.php" method="post">
                <button type="submit">✅ Bestelling Plaatsen</button>
            </form>
        <?php else: ?>
            <h3>⚠ Vul je bezorgadres in</h3>
            <form action="bestelling_bevestigd.php" method="post">
                <label for="naam">Naam:</label>
                <input type="text" name="naam" required>
                
                <label for="adres">Adres:</label>
                <input type="text" name="adres" required>

                <button type="submit">✅ Bestelling Plaatsen</button>
            </form>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Pizzeria di Rick | <a href="privacystatement.php">Privacyverklaring</a></p>
    </footer>
</body>
</html>
            