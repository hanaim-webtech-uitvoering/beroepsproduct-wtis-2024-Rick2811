<?php
session_start();

include 'functions.php';

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
    <?php toonNavbar(); ?>


    <div class="container">
        <h2>Je bent succesvol uitgelogd</h2>
        <p>Bedankt voor je bezoek aan Pizzeria di Rick. We hopen je snel weer te zien!</p>
        <button onclick="window.location.href='pizzeriaDiRick.php'">Terug naar Home</button>
    </div>

    <?php toonFooter(); ?>
</body>
</html>