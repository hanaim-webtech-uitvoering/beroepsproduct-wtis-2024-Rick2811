<?php
session_start();

// Vernietig de sessie en stuur de gebruiker terug naar de homepagina
session_unset();
session_destroy();

header("Location: pizzeriaDiRick.php");
exit;
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uitloggen | Pizzeria di Rick</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <button onclick="window.location.href='pizzeriaDiRick.php'">Home</button>
        <button onclick="window.location.href='Menu.php'">Menu</button>
        <button onclick="window.location.href='order.php'">Bestelling Plaatsen</button>
        <button onclick="window.location.href='inloggen.php'">Inloggen</button>
    </div>

    <div class="container">
        <h2>Je bent succesvol uitgelogd</h2>
        <p>Bedankt voor je bezoek aan Pizzeria di Rick. We hopen je snel weer te zien!</p>
        <button onclick="window.location.href='pizzeriaDiRick.php'">Terug naar Home</button>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Pizzeria di Rick | <a href="privacystatement.php">Privacyverklaring</a></p>
    </footer>
</body>
</html>