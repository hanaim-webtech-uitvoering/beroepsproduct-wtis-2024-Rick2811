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
$succesmelding = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verzamel en trim de invoer
    $gebruikersnaam = trim($_POST['gebruikersnaam'] ?? '');
    $wachtwoord = trim($_POST['wachtwoord'] ?? '');
    $voornaam = trim($_POST['voornaam'] ?? '');
    $achternaam = trim($_POST['achternaam'] ?? '');
    $adres = trim($_POST['adres'] ?? '');
    $datetime = date('Y-m-d H:i:s'); // Huidige tijd
    $status = 0; // Standaard status (0 = In behandeling)

    // Controleer of alle velden ingevuld zijn
    if (!empty($gebruikersnaam) && !empty($wachtwoord) && !empty($voornaam) && !empty($achternaam) && !empty($adres)) {

        // Controleer of de gebruikersnaam al bestaat
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM [User] WHERE username = :username");
        $stmt->execute(['username' => $gebruikersnaam]);
        $gebruiker_bestaat = $stmt->fetchColumn();

        if ($gebruiker_bestaat > 0) {
            $foutmelding = "❌ Deze gebruikersnaam is al in gebruik. Kies een andere.";
        } else {
            // Voeg gebruiker toe aan de [User] tabel
            $stmt = $pdo->prepare("INSERT INTO [User] (username, [password], first_name, last_name, [role]) 
                                   VALUES (:username, :password, :first_name, :last_name, 'Client')");
            $stmt->execute([
                'username' => $gebruikersnaam,
                'password' => $wachtwoord, // Let op: hier hoort eigenlijk hashing (bijv. password_hash())
                'first_name' => $voornaam,
                'last_name' => $achternaam
            ]);

            // ✅ Haal een willekeurige medewerker op
            $stmt = $pdo->prepare("SELECT username FROM [User] WHERE role = 'Personnel' ORDER BY NEWID()");
            $stmt->execute();
            $medewerker = $stmt->fetchColumn();

            if (!$medewerker) {
                $foutmelding = "❌ Er zijn geen medewerkers beschikbaar om een bestelling op te nemen.";
            } else {
                // Voeg bestelling toe aan de [Pizza_Order] tabel
                $stmt = $pdo->prepare("INSERT INTO [Pizza_Order] (client_username, client_name, personnel_username, datetime, status, address) 
                                       VALUES (:client_username, :client_name, :personnel_username, :datetime, :status, :address)");
                $stmt->execute([
                    'client_username' => $gebruikersnaam,
                    'client_name' => "$voornaam $achternaam",
                    'personnel_username' => $medewerker,
                    'datetime' => $datetime,
                    'status' => $status,
                    'address' => $adres
                ]);

                $succesmelding = "✅ Account succesvol aangemaakt!";
            }
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
    <title>Account Aanmaken</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <button onclick="window.location.href='pizzeriaDiRick.php'">Home</button>
        <button onclick="window.location.href='Menu.php'">Menu</button>
        <button onclick="window.location.href='customerorder.php'">Bestelling Plaatsen</button>

        <?php if (isset($_SESSION['ingelogd']) && $_SESSION['ingelogd'] === true): ?>
            <button onclick="window.location.href='profiel.php'">👤 <?= htmlspecialchars($_SESSION['gebruiker']) ?></button>
            <button onclick="window.location.href='uitloggen.php'">Uitloggen</button>
        <?php else: ?>
            <button onclick="window.location.href='inloggen.php'">Inloggen</button>
        <?php endif; ?>
    </div>

    <!-- Formulier Container -->
    <div class="pizzeria-form">
        <h2>Account Aanmaken</h2>

        <?php if (!empty($foutmelding)): ?>
            <p class="error"><?= htmlspecialchars($foutmelding); ?></p>
        <?php endif; ?>

        <?php if (!empty($succesmelding)): ?>
            <p class="success"><?= htmlspecialchars($succesmelding); ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="gebruikersnaam">Gebruikersnaam:</label>
            <input type="text" id="gebruikersnaam" name="gebruikersnaam" required>

            <label for="wachtwoord">Wachtwoord:</label>
            <input type="password" id="wachtwoord" name="wachtwoord" required>

            <label for="voornaam">Voornaam:</label>
            <input type="text" id="voornaam" name="voornaam" required>

            <label for="achternaam">Achternaam:</label>
            <input type="text" id="achternaam" name="achternaam" required>

            <label for="adres">Adres:</label>
            <input type="text" id="adres" name="adres" required>

            <button type="submit">Account Aanmaken</button>
        </form>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2025 Pizzeria di Rick | <a href="privacystatement.php">Privacyverklaring</a></p>
    </footer>

</body>
</html>
