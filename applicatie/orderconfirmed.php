<?php
session_start();

// Database configuratie
$DB_HOST = "database_server";
$DB_NAME = "pizzeria";
$DB_USER = "sa";
$DB_PASS = "abc123!@#";

try {
    // Verbinding maken met de database via PDO
    $pdo = new PDO("sqlsrv:server=$DB_HOST;Database=$DB_NAME;Encrypt=no;TrustServerCertificate=yes", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Databaseverbinding mislukt: " . $e->getMessage());
}

// Controleer of de gebruiker is ingelogd
$ingelogd = isset($_SESSION['ingelogd']) && $_SESSION['ingelogd'] === true;
$gebruikersnaam = $ingelogd ? $_SESSION['gebruiker'] : NULL;

// Controleer of er een bestelling is geplaatst en sla deze op
if (!empty($_SESSION['cart'])) {
    $naam = isset($_POST['naam']) ? $_POST['naam'] : "Gast";
    $adres = isset($_POST['adres']) ? $_POST['adres'] : "";
    $gebruiker = isset($_SESSION['gebruiker']) ? $_SESSION['gebruiker'] : NULL;
    $bestelling_gelukt = false;
    $bestelling_status = null;
    $bestelling_id = null;
    $bestelde_producten = [];

    try {
        // Start een transactie
        $pdo->beginTransaction();

        // Voeg bestelling toe aan `Pizza_Order`
        $stmt = $pdo->prepare("INSERT INTO Pizza_Order (client_username, client_name, personnel_username, datetime, status, address) 
                            VALUES (:client_username, :client_name, 'rdeboer', GETDATE(), 1, :address)");
        $stmt->execute([
            ':client_username' => $gebruiker,
            ':client_name' => $naam,
            ':address' => $adres
        ]);

        // Haal het ID van de nieuwe bestelling op
        $bestelling_id = $pdo->lastInsertId();
        $_SESSION['last_order_id'] = $bestelling_id; // Sla op voor toekomstige weergave

        // Voeg producten toe aan `Pizza_Order_Product`
        $stmt = $pdo->prepare("INSERT INTO Pizza_Order_Product (order_id, product_name, quantity) 
                            VALUES (:order_id, :product_name, :quantity)");

        foreach ($_SESSION['cart'] as $item) {
            $stmt->execute([
                ':order_id' => $bestelling_id,
                ':product_name' => $item['name'],
                ':quantity' => $item['quantity']
            ]);
            // Sla de bestelde producten op voor weergave
            $bestelde_producten[] = [
                'name' => $item['name'],
                'quantity' => $item['quantity']
            ];
        }

        // Commit transactie
        $pdo->commit();

        // Bestelling is gelukt
        $bestelling_gelukt = true;

        // Haal de status op van de bestelling
        $stmt = $pdo->prepare("SELECT status FROM Pizza_Order WHERE order_id = ?");
        $stmt->execute([$bestelling_id]);
        $bestelling_status = $stmt->fetchColumn();

        // Leeg de winkelwagen na succesvolle bestelling
        unset($_SESSION['cart']);

    } catch (Exception $e) {
        // Rol de transactie terug bij een fout
        $pdo->rollBack();
        die("<p style='color:red;'>Fout bij het plaatsen van de bestelling: " . $e->getMessage() . "</p>");
    }
} else {
    // Haal de laatst geplaatste bestelling op
    $bestelling_id = isset($_SESSION['last_order_id']) ? $_SESSION['last_order_id'] : null;

    if ($bestelling_id) {
        // Haal bestelling informatie op uit de database
        $stmt = $pdo->prepare("SELECT client_name, address, status FROM Pizza_Order WHERE order_id = ?");
        $stmt->execute([$bestelling_id]);
        $bestelling = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($bestelling) {
            $naam = $bestelling['client_name'];
            $adres = $bestelling['address'];
            $bestelling_status = $bestelling['status'];
            $bestelling_gelukt = true;

            // Haal de bestelde producten op
            $stmt = $pdo->prepare("SELECT product_name, quantity FROM Pizza_Order_Product WHERE order_id = ?");
            $stmt->execute([$bestelling_id]);
            $bestelde_producten = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestelling Bevestiging | Pizzeria di Rick</title>
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
        <h2>Bestelling Bevestiging</h2>

        <?php if ($bestelling_gelukt): ?>
            <p style="color:green;"><strong>✅ Je bestelling is succesvol geplaatst!</strong></p>
            <p><strong>Bestelling ID:</strong> <?= htmlspecialchars($bestelling_id) ?></p>
            <p><strong>Naam:</strong> <?= htmlspecialchars($naam) ?></p>
            <p><strong>Adres:</strong> <?= htmlspecialchars($adres) ?></p>
            <p><strong>Status:</strong> <?= ($bestelling_status == 1) ? 'In behandeling' : 'Onbekend' ?></p>
            <p style="color:blue;">ℹ️ Je kunt de bestelstatus later bekijken door op de bestelstatus-knop op de homepage te klikken.</p>
            <h3>Bestelde Producten</h3>
            <table border="1">
                <tr>
                    <th>Product</th>
                    <th>Aantal</th>
                </tr>
                <?php foreach ($bestelde_producten as $product): ?>
                    <tr>
                    <td><?= isset($product['product_name']) ? htmlspecialchars($product['product_name']) : 'Onbekend product' ?></td>

                        <td><?= $product['quantity'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p style="color:red;">⚠️ Geen bestelling gevonden. Plaats een bestelling en probeer opnieuw.</p>
        <?php endif; ?>

        <a href="pizzeriaDiRick.php">Terug naar Home</a>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Pizzeria di Rick | <a href="privacystatement.php">Privacyverklaring</a></p>
    </footer>

</body>
</html>
