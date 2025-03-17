<?php
session_start();
include 'functions.php';

if (!isset($_SESSION['ingelogd']) || !$_SESSION['ingelogd']) {
    header('Location: inloggen.php');
    exit();
}

$pdo = connectToDatabase();

if (!isset($_GET['order_id'])) {
    die("Ongeldige bestelling.");
}
$order_id = $_GET['order_id'];

$stmt = $pdo->prepare("SELECT client_name, address, datetime, status FROM Pizza_Order WHERE order_id = :order_id");
$stmt->execute(['order_id' => $order_id]);
$bestelling = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bestelling) {
    die("Bestelling niet gevonden.");
}

$stmt = $pdo->prepare("SELECT p.name, pop.quantity FROM Pizza_Order_Product pop 
                       JOIN Product p ON pop.product_name = p.name
                       WHERE pop.order_id = :order_id");
$stmt->execute(['order_id' => $order_id]);
$producten = $stmt->fetchAll(PDO::FETCH_ASSOC);

$statusOpties = [
    1 => "Aan begonnen",
    2 => "Ready to go",
    3 => "Onderweg"
];

$statusBericht = ""; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $nieuweStatus = $_POST['status'];
    $updateStmt = $pdo->prepare("UPDATE Pizza_Order SET status = :status WHERE order_id = :order_id");
    $updateStmt->execute(['status' => $nieuweStatus, 'order_id' => $order_id]);

    $statusBericht = "<p id='status-melding' style='color: green; font-weight: bold;'>✅ Status succesvol bijgewerkt!</p>";

    $bestelling['status'] = $nieuweStatus;
}

toonHeader('Bestelling Details');
?>

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

    <?= $statusBericht ?> 

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

<?php toonFooter(); ?>
</body>
</html>
