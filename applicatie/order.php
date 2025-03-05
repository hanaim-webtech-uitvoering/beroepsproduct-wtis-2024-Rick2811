<?php
    session_start();
    
    // Databaseverbinding
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

    // Haal order_id op uit de URL
    $order_id = $_GET['order_id'] ?? null;

    if ($order_id) {
        // Haal bestelling informatie op
        $stmt = $pdo->prepare("SELECT * FROM Pizza_Order WHERE order_id = :order_id");
        $stmt->execute(['order_id' => $order_id]);
        $bestelling = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        die("Geen bestelling gevonden.");
    }

    // Status omzetten naar tekst
    $statusTekst = [
        1 => "Aan begonnen",
        2 => "Ready to go",
        3 => "Onderweg"
    ];

    // Verwerk statusupdate
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_status'])) {
        $new_status = $_POST['new_status'];
        $stmt = $pdo->prepare("UPDATE Pizza_Order SET status = :status WHERE order_id = :order_id");
        $stmt->execute(['status' => $new_status, 'order_id' => $order_id]);
        header("Location: order.php?order_id=$order_id");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestelling Details</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="navbar">
        <button onclick="window.location.href='index.php'">Home</button>
        <button onclick="alert('Menu geopend!')">Menu</button>
        <button onclick="alert('Bestelling plaatsen...')">Bestelling Plaatsen</button>
        <button onclick="window.location.href='dashboard.php'">Dashboard</button>
    </div>

    <div class="order-details">
        <h2>Bestelling #<?php echo htmlspecialchars($bestelling['order_id']); ?></h2>
        <p><strong>Klantnaam:</strong> <?php echo htmlspecialchars($bestelling['client_name']); ?></p>
        <p><strong>Adres:</strong> <?php echo htmlspecialchars($bestelling['address']); ?></p>
        <p><strong>Datum & Tijd:</strong> <?php echo htmlspecialchars($bestelling['datetime']); ?></p>
        
        <form method="POST">
            <label for="new_status"><strong>Status:</strong></label>
            <select name="new_status" id="new_status">
                <option value="1" <?php echo ($bestelling['status'] == 1) ? 'selected' : ''; ?>>Aan begonnen</option>
                <option value="2" <?php echo ($bestelling['status'] == 2) ? 'selected' : ''; ?>>Ready to go</option>
                <option value="3" <?php echo ($bestelling['status'] == 3) ? 'selected' : ''; ?>>Onderweg</option>
            </select>
            <button type="submit">Wijzig Status</button>
        </form>
    </div>
</body>
</html>
