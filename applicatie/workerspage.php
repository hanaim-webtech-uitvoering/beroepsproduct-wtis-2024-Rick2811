<?php
session_start();

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['ingelogd']) || !$_SESSION['ingelogd']) {
    header('Location: inloggen.php');
    exit();
}

// Database configuratie
$DB_HOST = "database_server";
$DB_NAME = "pizzeria";
$DB_USER = "sa";
$DB_PASS = "abc123!@#";

try {
    $pdo = new PDO("sqlsrv:server=$DB_HOST;Database=$DB_NAME;Encrypt=no", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Databaseverbinding mislukt: " . $e->getMessage());
}

// Haal gebruikersnaam op uit de sessie
$gebruikersnaam = $_SESSION['gebruiker'];

// Haal actieve bestellingen op
$stmt = $pdo->query("SELECT order_id, client_name, datetime, status FROM Pizza_Order ORDER BY datetime DESC");
$bestellingen = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Status opties
$statusTekst = [
    1 => "Aan begonnen",
    2 => "Ready to go",
    3 => "Onderweg"
];
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Pizzeria di Rick</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="navbar">
        <button onclick="window.location.href='index.php'">Home</button>
        <button onclick="window.location.href='Menu.php'">Menu</button>
        <button onclick="window.location.href='orderCustomer.php'">Bestelling</button>
        <button onclick="window.location.href='inloggen.php'">inloggen</button>
    </div>
    
    <div class="container">
        <h2>Actieve Bestellingen</h2>
        <div class="bestelling-container">
            <?php foreach ($bestellingen as $bestelling): ?>
                <div class="bestelling-box" onclick="window.location.href='order.php?order_id=<?= $bestelling['order_id']; ?>'">
                    <p><strong>Bestelnummer:</strong> <?= htmlspecialchars($bestelling['order_id']); ?></p>
                    <p><strong>Klantnaam:</strong> <?= htmlspecialchars($bestelling['client_name']); ?></p>
                    <p><strong>Datum & Tijd:</strong> <?= htmlspecialchars($bestelling['datetime']); ?></p>
                    <p><strong>Status:</strong> <?= $statusTekst[$bestelling['status']] ?? "Onbekend"; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Pizzeria di Rick | <a href="privacystatement.php">Privacyverklaring</a></p>
    </footer>
</body>
</html>
