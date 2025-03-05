<?php
    session_start();

    // Database configuratie
    $DB_HOST = "database_server"; // Pas dit aan
    $DB_NAME = "pizzeria";        // Jouw database naam
    $DB_USER = "sa";              // SQL Server SA-gebruiker
    $DB_PASS = "abc123!@#";       // Jouw SQL Server SA-wachtwoord

    try {
        // PDO verbinding met SQL Server en SSL-verificatie uitschakelen
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
            // Controleer of de gebruiker bestaat in de database
            $stmt = $pdo->prepare("SELECT username, password, role FROM [User] WHERE username = :username");
            $stmt->execute(['username' => $gebruikersnaam]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Tijdelijke oplossing: Vergelijk platte tekst wachtwoorden
                if ($wachtwoord === $user['password']) {
                    $_SESSION['ingelogd'] = true;
                    $_SESSION['gebruiker'] = $gebruikersnaam;
                    $_SESSION['rol'] = $user['role'];

                    // Redirect op basis van de rol
                    if ($user['role'] === 'Personnel') {
                        header('Location: workerspage.php');
                    } else {
                        header('Location: customerspage.php');
                    }
                    exit();
                } else {
                    $foutmelding = "❌ Ongeldige gebruikersnaam of wachtwoord!";
                }
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
    <div class="navbar">
        <button onclick="window.location.href='index.php'">Home</button>
        <button onclick="alert('Menu geopend!')">Menu</button>
        <button onclick="alert('Bestelling plaatsen...')">Bestelling Plaatsen</button>
        <button onclick="window.location.href='inloggen.php'">Inloggen</button>
    </div>
    
    <div class="login-container">
        <h2>Inloggen</h2>
        <?php if (!empty($foutmelding)): ?>
            <p class="error"> <?php echo htmlspecialchars($foutmelding); ?> </p>
        <?php endif; ?>
        <form action="" method="POST">
            <label for="gebruikersnaam">Gebruikersnaam:</label>
            <input type="text" id="gebruikersnaam" name="gebruikersnaam" required>
            
            <label for="wachtwoord">Wachtwoord:</label>
            <input type="password" id="wachtwoord" name="wachtwoord" required>
            
            <button type="submit">Inloggen</button>
        </form>
    </div>
</body>
</html>
