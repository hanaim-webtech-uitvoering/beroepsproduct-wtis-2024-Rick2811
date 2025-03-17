<?php
function connectToDatabase() {
    $DB_HOST = "database_server";
    $DB_NAME = "pizzeria";
    $DB_USER = "sa";
    $DB_PASS = "abc123!@#";

    try {
        $pdo = new PDO("sqlsrv:server=$DB_HOST;Database=$DB_NAME;Encrypt=no;TrustServerCertificate=yes", $DB_USER, $DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Databaseverbinding mislukt: " . $e->getMessage());
    }
}
function toonNavbar() {
    ?>
    <div class="navbar">
        <button onclick="window.location.href='pizzeriaDiRick.php'">Home</button>
        <button onclick="window.location.href='Menu.php'">Menu</button>

        <?php if (!isset($_SESSION['ingelogd']) || $_SESSION['rol'] !== 'Personnel'): ?>
            <button onclick="window.location.href='ordercustomer.php'">Bestelling Plaatsen</button>
        <?php endif; ?>

        <?php if (isset($_SESSION['ingelogd']) && $_SESSION['ingelogd'] === true): ?>
            <button onclick="window.location.href='uitloggen.php'">Uitloggen</button>
        <?php else: ?>
            <button onclick="window.location.href='inloggen.php'">Inloggen</button>
        <?php endif; ?>
    </div>
    <?php
}


function toonFooter() {
    ?>
    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> Pizzeria di Rick | <a href="privacystatement.php">Privacyverklaring</a></p>
    </footer>
    <?php
}

function toonHeader($title) {
    ?>
    <!DOCTYPE html>
    <html lang="nl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($title); ?> | Pizzeria di Rick</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
    <?php toonNavbar(); ?>
    <?php
}
?>
