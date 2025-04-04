<?php
session_start();
include 'functions.php';

if (!isset($_SESSION['ingelogd']) || $_SESSION['rol'] !== 'Client') {
    header('Location: inloggen.php');
    exit();
}

$pdo = connectToDatabase();
$gebruikersnaam = $_SESSION['gebruiker'];

$stmt = $pdo->prepare("SELECT order_id, datetime, status FROM Pizza_Order WHERE client_username = :username ORDER BY datetime DESC");
$stmt->execute(['username' => $gebruikersnaam]);
$bestellingen = $stmt->fetchAll(PDO::FETCH_ASSOC);

toonHeader('Mijn Bestellingen');
?>

<div class="container">
    <h2>Mijn Bestelgeschiedenis</h2>

    <?php if (empty($bestellingen)): ?>
        <p>Je hebt nog geen bestellingen geplaatst. <a href="menu.php">Bekijk ons menu</a> om te starten!</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Bestelnummer</th>
                    <th>Datum & Tijd</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bestellingen as $bestelling): ?>
                    <tr>
                        <td><?= htmlspecialchars($bestelling['order_id']) ?></td>
                        <td><?= htmlspecialchars($bestelling['datetime']) ?></td>
                        <td><?= htmlspecialchars(getStatusText($bestelling['status'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php toonFooter(); ?>
</body>
</html>
