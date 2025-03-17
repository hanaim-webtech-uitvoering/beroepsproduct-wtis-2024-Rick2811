<?php
session_start();

include 'functions.php';

$pdo = connectToDatabase();

$ingelogd = isset($_SESSION['ingelogd']) && $_SESSION['ingelogd'] === true;
$gebruikersnaam = $ingelogd ? $_SESSION['gebruiker'] : NULL;

$statusTekst = [
    1 => "Aan begonnen",
    2 => "Ready to go",
    3 => "Onderweg"
];

$bestelling_gelukt = false;
$bestelling_status = 1; 
$bestelling_id = null;
$bestelde_producten = [];

if (!empty($_SESSION['cart'])) {
    $naam = isset($_POST['naam']) ? $_POST['naam'] : "Gast";
    $adres = isset($_POST['adres']) ? $_POST['adres'] : "";
    $gebruiker = isset($_SESSION['gebruiker']) ? $_SESSION['gebruiker'] : NULL;

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO Pizza_Order (client_username, client_name, personnel_username, datetime, status, address) 
                               VALUES (:client_username, :client_name, 'rdeboer', GETDATE(), 1, :address)");
        $stmt->execute([
            ':client_username' => $gebruiker,
            ':client_name' => $naam,
            ':address' => $adres
        ]);

        $bestelling_id = $pdo->lastInsertId();
        $_SESSION['last_order_id'] = $bestelling_id; 

        $stmt = $pdo->prepare("INSERT INTO Pizza_Order_Product (order_id, product_name, quantity) 
                               VALUES (:order_id, :product_name, :quantity)");

        foreach ($_SESSION['cart'] as $item) {
            $stmt->execute([
                ':order_id' => $bestelling_id,
                ':product_name' => $item['name'],
                ':quantity' => $item['quantity']
            ]);
        }

        $pdo->commit();
        $bestelling_gelukt = true;

        $stmt = $pdo->prepare("SELECT status FROM Pizza_Order WHERE order_id = ?");
        $stmt->execute([$bestelling_id]);
        $bestelling_status = $stmt->fetchColumn();
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Bestelling mislukt: " . $e->getMessage());
    }
} else {
    $bestelling_id = $_SESSION['last_order_id'] ?? null;

    if ($bestelling_id) {
        $stmt = $pdo->prepare("SELECT status FROM Pizza_Order WHERE order_id = ?");
        $stmt->execute([$bestelling_id]);
        $bestelling_status = $stmt->fetchColumn();
    }
}

toonHeader('Bestelling Bevestiging');
?>

<div class="container">
    <h2>Bestelling Bevestiging</h2>
    <?php if ($bestelling_gelukt): ?>
        <p>Bedankt voor je bestelling! Je bestelnummer is <?= htmlspecialchars($bestelling_id) ?>.</p>
        <p>De status van je bestelling is: <?= htmlspecialchars($statusTekst[$bestelling_status]) ?>.</p>
    <?php else: ?>
        <p>Er is iets misgegaan met je bestelling. Probeer het opnieuw.</p>
    <?php endif; ?>
</div>

<?php toonFooter(); ?>
</body>
</html>
