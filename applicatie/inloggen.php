<?php
session_start();

include 'functions.php';

$pdo = connectToDatabase();

$foutmelding = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gebruikersnaam = trim($_POST['gebruikersnaam'] ?? '');
    $wachtwoord = trim($_POST['wachtwoord'] ?? '');

    if (!empty($gebruikersnaam) && !empty($wachtwoord)) {
        $stmt = $pdo->prepare("
            SELECT u.username, u.password, u.role, po.address 
            FROM [User] u
            LEFT JOIN [Pizza_Order] po ON u.username = po.client_username
            WHERE u.username = :username
            ORDER BY po.datetime DESC
        ");
        $stmt->execute(['username' => $gebruikersnaam]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $wachtwoord === $user['password']) { 
            $_SESSION['ingelogd'] = true;
            $_SESSION['gebruiker'] = $user['username'];
            $_SESSION['rol'] = $user['role'];

            if (!empty($user['address'])) {
                $_SESSION['adres'] = $user['address'];
            } else {
                $_SESSION['adres'] = null; 
            }

            if ($user['role'] === 'Personnel') {
                header('Location: workerspage.php'); 
            } else {
                header('Location: pizzeriaDiRick.php'); 
            }
            exit();
        } else {
            $foutmelding = "❌ Ongeldige gebruikersnaam of wachtwoord!";
        }
    } else {
        $foutmelding = "⚠ Vul alle velden in.";
    }
}

toonHeader('Inloggen');
?>

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
    <p>Heb je nog geen account? Klik hieronder om je te registreren.</p>
    <button onclick="window.location.href='addaccount.php'">Account aanmaken</button>
</div>

<?php toonFooter(); ?>
</body>
</html>
