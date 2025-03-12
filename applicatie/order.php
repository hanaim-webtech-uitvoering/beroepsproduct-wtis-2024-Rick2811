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

// Haal order_id op uit GET-parameter
if (!isset($_GET['order_id'])) {
    die("Ongeldige bestelling.");
}
$order_id = $_GET['order_id'];

// Haal bestellingsinformatie op
$stmt = $pdo->prepare("SELECT client_name, address, datetime, status FROM Pizza_Order WHERE order_id = :order_id");
$stmt->execute(['order_id' => $order_id]);
$bestelling = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bestelling) {
    die("Bestelling niet gevonden.");
}

// Haal producten op die bij deze bestelling horen
$stmt = $pdo->prepare("SELECT p.name, pop.quantity FROM Pizza_Order_Product pop 
                       JOIN Product p ON pop.product_name = p.name
                       WHERE pop.order_id = :order_id");
$stmt->execute(['order_id' => $order_id]);
$producten = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Status opties
$statusOpties = [
    1 => "Aan begonnen",
    2 => "Ready to go",
    3 => "Onderweg"
];

// Als de status wordt gewijzigd
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $nieuweStatus = $_POST['status'];
    $updateStmt = $pdo->prepare("UPDATE Pizza_Order SET status = :status WHERE order_id = :order_id");
    $updateStmt->execute(['status' => $nieuweStatus, 'order_id' => $order_id]);
    header("Location: orderDetails.php?order_id=" . $order_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestelling Details | Pizzeria di Rick</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="navbar">
        <button onclick="window.location.href='workerspage.php'">⬅ Terug naar overzicht</button>
    </div>

    <div class="container">
        <h2>Bestelling #<?= htmlspecialchars($order_id) ?></h2>
        <p><strong>Klant:</strong> <?= htmlspecialchars($bestelling['client_name']) ?></p>
        <p><strong>Adres:</strong> <?= htmlspecialchars($bestelling['address']) ?></p>
        <p><strong>Datum & Tijd:</strong> <?= htmlspecialchars($bestelling['datetime']) ?></p>

        <h3>Bestelde Producten</h3>
        <ul>
            <?php foreach ($producten as $product): ?>
                <li><?= htmlspecialchars($product['quantity']) ?>x <?= htmlspecialchars($product['name']) ?></li>
            <?php endforeach; ?>
        </ul>

        <h3>Status Wijzigen</h3>
        <form method="post">
            <select name="status">
                <?php foreach ($statusOpties as $waarde => $label): ?>
                    <option value="<?= $waarde ?>" <?= $bestelling['status'] == $waarde ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Bijwerken</button>
        </form>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Pizzeria di Rick | <a href="privacystatement.php">Privacyverklaring</a></p>
    </footer>
</body>
</html>
