<?php
session_start();

// Controleer of de gebruiker is ingelogd
$ingelogd = isset($_SESSION['ingelogd']) && $_SESSION['ingelogd'] === true;
$gebruikersnaam = $ingelogd ? $_SESSION['gebruiker'] : "Gast";
$adres = $ingelogd && isset($_SESSION['adres']) ? $_SESSION['adres'] : "";

// Controleer of het formulier is ingediend en voeg bestelling toe aan de sessie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_data'])) {
    $_SESSION['cart'] = json_decode($_POST['cart_data'], true);
}

// Zorg ervoor dat de winkelwagen correct is ingesteld
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
        <button onclick="window.location.href='orderCustomer.php'">Bestelling Plaatsen</button>

        <?php if ($ingelogd): ?>
            <button onclick="window.location.href='profiel.php'">👤 <?= htmlspecialchars($gebruikersnaam) ?></button>
            <button onclick="window.location.href='uitloggen.php'">Uitloggen</button>
        <?php else: ?>
            <button onclick="window.location.href='inloggen.php'">Inloggen</button>
        <?php endif; ?>
    </div>

    <div class="container">
        <h2>Jouw Bestelling</h2>

        <!-- Toon winkelwagen producten -->
        <table>
            <tr>
                <th>Product</th>
                <th>Aantal</th>
                <th>Prijs per stuk (€)</th>
                <th>Totaal (€)</th>
                <th>Extra Ingrediënten</th>
            </tr>
            <?php 
            $totaalprijs = 0;
            foreach ($_SESSION['cart'] as $item) : 
                $totaalprijs += $item['price'] * $item['quantity'];
            ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= number_format($item['price'], 2, ',', '.') ?> €</td>
                    <td><?= number_format($item['price'] * $item['quantity'], 2, ',', '.') ?> €</td>
                    <td><?= !empty($item['extra']) ? htmlspecialchars($item['extra']) : 'Geen extra' ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <p><strong>Totaalprijs:</strong> €<?= number_format($totaalprijs, 2, ',', '.') ?></p>

        <!-- Bestelformulier -->
        <form action="bestelling_bevestigd.php" method="post">
            <label for="naam">Naam:</label>
            <input type="text" name="naam" required value="<?= $ingelogd ? htmlspecialchars($gebruikersnaam) : '' ?>">
            
            <label for="adres">Adres:</label>
            <input type="text" name="adres" required value="<?= htmlspecialchars($adres) ?>" <?= $ingelogd ? 'readonly' : '' ?>>
            
            <!-- Winkelwagen naar verborgen velden voor doorgifte -->
            <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                <input type="hidden" name="producten[<?= $index ?>][name]" value="<?= htmlspecialchars($item['name']) ?>">
                <input type="hidden" name="producten[<?= $index ?>][quantity]" value="<?= $item['quantity'] ?>">
                <input type="hidden" name="producten[<?= $index ?>][price]" value="<?= $item['price'] ?>">
                <input type="hidden" name="producten[<?= $index ?>][extra]" value="<?= htmlspecialchars($item['extra']) ?>">
            <?php endforeach; ?>

            <button type="submit">✅ Bestelling Plaatsen</button>
        </form>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Pizzeria di Rick | <a href="privacystatement.php">Privacyverklaring</a></p>
    </footer>
</body>
</html>
