<?php
session_start();

include 'functions.php';

if (!isset($_SESSION['ingelogd']) || !$_SESSION['ingelogd']) {
    header('Location: inloggen.php');
    exit();
}

$pdo = connectToDatabase();

$gebruikersnaam = $_SESSION['gebruiker'];

$stmt = $pdo->query("SELECT order_id, client_name, datetime, status FROM Pizza_Order ORDER BY datetime DESC");
$bestellingen = $stmt->fetchAll(PDO::FETCH_ASSOC);

$statusTekst = [
    1 => "Aan begonnen",
    2 => "Ready to go",
    3 => "Onderweg"
];

toonHeader('Dashboard');
?>

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

<?php toonFooter(); ?>
</body>
</html>
