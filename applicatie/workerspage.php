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

    // Haal de voornaam van de gebruiker op uit de database
    $stmt = $pdo->prepare("SELECT first_name FROM [User] WHERE username = :username");
    $stmt->execute(['username' => $gebruikersnaam]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $voornaam = $user ? $user['first_name'] : 'Gebruiker';

    // Haal actieve bestellingen op
    $stmt = $pdo->query("SELECT order_id, client_name, datetime, status FROM Pizza_Order");
    $bestellingen = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Status omzetten naar tekst
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
    <title>Dashboard</title>
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
    
    <div class="dashboard-container">
        <h2>Hallo, <?php echo htmlspecialchars($voornaam); ?>!</h2>
        <h3>Actieve Bestellingen</h3>
        <div class="bestelling-container">
            <?php foreach ($bestellingen as $bestelling): ?>
                <div class="bestelling-balk">
                    <div class="bestelling-box" onclick="window.location.href='order.php?order_id=<?php echo $bestelling['order_id']; ?>'">
                        <p><strong>Bestelnummer:</strong> <?php echo htmlspecialchars($bestelling['order_id']); ?></p>
                        <p><strong>Klantnaam:</strong> <?php echo htmlspecialchars($bestelling['client_name']); ?></p>
                        <p><strong>Datum & Tijd:</strong> <?php echo htmlspecialchars($bestelling['datetime']); ?></p>
                        <p><strong>Status:</strong> <?php echo $statusTekst[$bestelling['status']] ?? "Onbekend"; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
<footer class="footer">
        <p>&copy; 2025 Pizzeria di Rick | <a href="privacystatement.php">Privacyverklaring</a></p>
    </footer>

</html>
