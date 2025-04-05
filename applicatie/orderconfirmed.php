<?php
session_start();
include 'functions.php';

$pdo = connectToDatabase();

$ingelogd = isset($_SESSION['ingelogd']) && $_SESSION['ingelogd'] === true;
$gebruikersnaam = $ingelogd ? $_SESSION['gebruiker'] : null;

$bestelling_gelukt = false;
$bestelling_status = 1;
$bestelling_id = null;

// Alleen bestelling verwerken als het een formulier POST is
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['cart'])) {
    $naam = $_POST['naam'] ?? 'Gast';
    $adres = '';

    if ($ingelogd) {
        $adres = $_POST['adres'] ?? '';
    } else {
        // Gasten: adres samenstellen uit losse velden
        $straat = trim($_POST['straat'] ?? '');
        $huisnummer = trim($_POST['huisnummer'] ?? '');
        $postcode = trim($_POST['postcode'] ?? '');
        $plaats = trim($_POST['plaats'] ?? '');

        if (!empty($straat) && !empty($huisnummer) && !empty($postcode) && !empty($plaats)) {
            $adres = "$straat $huisnummer, $postcode, $plaats";
        } else {
            $bestelling_gelukt = false;
        }
    }

    // Alleen verdergaan als adres goed gevuld is
    if (!empty($adres)) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO Pizza_Order (client_username, client_name, personnel_username, datetime, status, address) 
                                   VALUES (:client_username, :client_name, 'rdeboer', GETDATE(), :status, :address)");
            $stmt->execute([
                ':client_username' => $gebruikersnaam,
                ':client_name' => $naam,
                ':status' => $bestelling_status,
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

            // 🧼 Winkelwagen legen na succesvolle bestelling
            unset($_SESSION['cart']);

            $stmt = $pdo->prepare("SELECT status FROM Pizza_Order WHERE order_id = ?");
            $stmt->execute([$bestelling_id]);
            $bestelling_status = $stmt->fetchColumn();
        } catch (Exception $e) {
            $pdo->rollBack();
            die("❌ Bestelling mislukt: " . $e->getMessage());
        }
    }
}

// Als er geen nieuwe bestelling is geplaatst, toon laatste
if (!$bestelling_gelukt) {
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
        <p>✅ Bedankt voor je bestelling! Je bestelnummer is <strong><?= htmlspecialchars($bestelling_id) ?></strong>.</p>
        <p>Status van je bestelling: <strong><?= htmlspecialchars(getStatusText($bestelling_status)) ?></strong>.</p>
    <?php elseif ($bestelling_id): ?>
        <p>📦 Laatste bestelling: <strong>#<?= htmlspecialchars($bestelling_id) ?></strong></p>
        <p>Status: <strong><?= htmlspecialchars(getStatusText($bestelling_status)) ?></strong>.</p>
    <?php else: ?>
        <p>Je hebt nog geen bestelling geplaatst.</p>
    <?php endif; ?>
</div>

<?php toonFooter(); ?>
</body>
</html>
