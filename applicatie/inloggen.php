<?php
    session_start();
    
    // Simpele gebruikersauthenticatie (voor demonstratie)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $gebruikersnaam = $_POST['gebruikersnaam'] ?? '';
        $wachtwoord = $_POST['wachtwoord'] ?? '';
        
        // Dummy gegevens (in een echte app haal je dit uit een database)
        $correcte_gebruiker = 'admin';
        $correcte_wachtwoord = 'pizza123';
        
        if ($gebruikersnaam === $correcte_gebruiker && $wachtwoord === $correcte_wachtwoord) {
            $_SESSION['ingelogd'] = true;
            header('Location: dashboard.php'); // Verwijzen naar een dashboard of homepage
            exit();
        } else {
            $foutmelding = 'Ongeldige gebruikersnaam of wachtwoord';
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
        <?php if (isset($foutmelding)): ?>
            <p class="error"> <?php echo $foutmelding; ?> </p>
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