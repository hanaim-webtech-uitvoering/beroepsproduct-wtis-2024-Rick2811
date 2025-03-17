<?php
session_start();

include 'functions.php';

$pdo = connectToDatabase();

$foutmelding = "";
$succesmelding = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gebruikersnaam = trim($_POST['gebruikersnaam'] ?? '');
    $wachtwoord = password_hash(trim($_POST['wachtwoord'] ?? ''), PASSWORD_DEFAULT);
    $voornaam = trim($_POST['voornaam'] ?? '');
    $achternaam = trim($_POST['achternaam'] ?? '');
    $adres = trim($_POST['adres'] ?? '');
    $datetime = date('Y-m-d H:i:s'); 
    $status = 0; 

    if (!empty($gebruikersnaam) && !empty($wachtwoord) && !empty($voornaam) && !empty($achternaam) && !empty($adres)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM [User] WHERE username = :username");
        $stmt->execute(['username' => $gebruikersnaam]);
        $gebruiker_bestaat = $stmt->fetchColumn();

        if ($gebruiker_bestaat > 0) {
            $foutmelding = "❌ Deze gebruikersnaam is al in gebruik. Kies een andere.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO [User] (username, [password], first_name, last_name, [role]) 
                                   VALUES (:username, :password, :first_name, :last_name, 'Client')");
            $stmt->execute([
                'username' => $gebruikersnaam,
                'password' => $wachtwoord, 
                'first_name' => $voornaam,
                'last_name' => $achternaam
            ]);

            $stmt = $pdo->prepare("SELECT username FROM [User] WHERE role = 'Personnel' ORDER BY NEWID()");
            $stmt->execute();
            $medewerker = $stmt->fetchColumn();

            if (!$medewerker) {
                $foutmelding = "❌ Er zijn geen medewerkers beschikbaar om een bestelling op te nemen.";
            } else {
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

toonHeader('Account Aanmaken');
?>

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

<?php toonFooter(); ?>
</body>
</html>
