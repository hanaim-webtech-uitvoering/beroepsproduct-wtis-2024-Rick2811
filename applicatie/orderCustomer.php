<?php
session_start();

// Controleer of de gebruiker is ingelogd
$ingelogd = isset($_SESSION['ingelogd']) && $_SESSION['ingelogd'] === true;
$gebruikersnaam = $ingelogd ? $_SESSION['gebruiker'] : "Gast";
$adres = $ingelogd && isset($_SESSION['adres']) ? $_SESSION['adres'] : "";

// Zorg ervoor dat de winkelwagen is ingesteld
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Debugging: Toon de volledige sessie om te controleren wat er gebeurt
// echo "<pre>"; print_r($_SESSION); echo "</pre>";

// Controleer of het formulier is ingediend en voeg bestelling toe aan de sessie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['producten'])) {
    $_SESSION['cart'] = json_decode($_POST['cart_data'], true);
}

// Controleer of er producten in de winkelwagen zitten
// if (empty($_SESSION['cart'])) {
//     echo "<script>alert('Je winkelwagen is leeg!'); window.location.href='menu.php';</script>";
//     exit;
// }
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

        <p><strong>Totaalprijs:</strong> €<?= number_format(array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
        }, $_SESSION['cart'])), 2, ',', '.') ?></p>

        <form action="bestelling_bevestigd.php" method="post">
            <label for="naam">Naam:</label>
            <input type="text" name="naam" required value="<?= $ingelogd ? htmlspecialchars($gebruikersnaam) : '' ?>">
            
            <label for="adres">Adres:</label>
            <input type="text" name="adres" required value="<?= $adres ?>" <?= $ingelogd ? 'readonly' : '' ?> >
            
            <?php foreach ($_SESSION['cart'] as $product => $item): ?>
                <input type="hidden" name="producten[<?= htmlspecialchars($product) ?>][name]" value="<?= htmlspecialchars($item['name']) ?>">
                <input type="hidden" name="producten[<?= htmlspecialchars($product) ?>][quantity]" value="<?= $item['quantity'] ?>">
                <input type="hidden" name="producten[<?= htmlspecialchars($product) ?>][price]" value="<?= $item['price'] ?>">
            <?php endforeach; ?>
            
            <input type="hidden" name="cart_data" value='<?= json_encode($_SESSION['cart']) ?>'>
            
            <button type="submit">✅ Bestelling Plaatsen</button>
        </form>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Pizzeria di Rick | <a href="privacystatement.php">Privacyverklaring</a></p>
    </footer>
</body>
</html>
