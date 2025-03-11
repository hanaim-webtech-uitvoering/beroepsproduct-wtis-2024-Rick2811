<?php
session_start();

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

$foutmelding = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gebruikersnaam = trim($_POST['gebruikersnaam'] ?? '');
    $wachtwoord = trim($_POST['wachtwoord'] ?? '');

    if (!empty($gebruikersnaam) && !empty($wachtwoord)) {
        // ✅ Haal het adres op uit de `Pizza_Order` tabel
        $stmt = $pdo->prepare("
            SELECT u.username, u.password, u.role, po.address 
            FROM [User] u
            LEFT JOIN [Pizza_Order] po ON u.username = po.client_username
            WHERE u.username = :username
            ORDER BY po.datetime DESC
        ");
        $stmt->execute(['username' => $gebruikersnaam]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $wachtwoord === $user['password']) { // Tijdelijke wachtwoordcontrole
            $_SESSION['ingelogd'] = true;
            $_SESSION['gebruiker'] = $user['username'];
            $_SESSION['rol'] = $user['role'];

            // ✅ Haal het adres uit de bestelling als het bestaat
            if (!empty($user['address'])) {
                $_SESSION['adres'] = $user['address'];
            } else {
                $_SESSION['adres'] = null; // Adres niet gevonden
            }

            // Redirect naar de juiste pagina
            header('Location: pizzeriaDiRick.php');
            exit();
        } else {
            $foutmelding = "❌ Ongeldige gebruikersnaam of wachtwoord!";
        }
    } else {
        $foutmelding = "⚠ Vul alle velden in.";
    }
}
?>


<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <button onclick="window.location.href='pizzeriaDiRick.php'">Home</button>
        <button onclick="window.location.href='Menu.php'">Menu</button>
        <button onclick="window.location.href='order.php'">Bestelling Plaatsen</button>

        <?php if (isset($_SESSION['ingelogd']) && $_SESSION['ingelogd'] === true): ?>
            <button onclick="window.location.href='profiel.php'">👤 <?= htmlspecialchars($_SESSION['gebruiker']) ?></button>
            <button onclick="window.location.href='uitloggen.php'">Uitloggen</button>
        <?php else: ?>
            <button onclick="window.location.href='inloggen.php'">Inloggen</button>
        <?php endif; ?>
    </div>
    
    <div class="container">
        <h2>Inloggen</h2>
        <?php if (!empty($foutmelding)): ?>
            <p class="error"><?= htmlspecialchars($foutmelding); ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <label for="gebruikersnaam">Gebruikersnaam:</label>
            <input type="text" id="gebruikersnaam" name="gebruikersnaam" required>
            
            <label for="wachtwoord">Wachtwoord:</label>
            <input type="password" id="wachtwoord" name="wachtwoord" required>
            
            <button type="submit">Inloggen</button>
        </form>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2025 Pizzeria di Rick | <a href="privacystatement.php">Privacyverklaring</a></p>
    </footer>

</body>
</html>
