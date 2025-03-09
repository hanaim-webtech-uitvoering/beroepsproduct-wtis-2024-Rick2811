<?php
    // Start PHP-script (optioneel voor toekomstige uitbreiding)
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacyverklaring - Pizzeria di Rick</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navigatiebalk -->
    <div class="navbar">
        <button onclick="window.location.href='pizzeriaDiRick.php'">Home</button>
        <button onclick="alert('Menu geopend!')">Menu</button>
        <button onclick="alert('Bestelling plaatsen...')">Bestelling Plaatsen</button>
        <button onclick="window.location.href='inloggen.php'">Inloggen</button>
    </div>
    
    <!-- Titel -->
    <h1 class="welcome-text">Privacyverklaring</h1>
    
    <!-- Privacyverklaring Tekst -->
    <div class="privacy-container">
        <h2>Privacyverklaring - Pizzeria di Rick</h2>

        <p><strong>Laatst bijgewerkt: 09/03/2025</strong></p>

        <p>Welkom bij **Pizzeria di Rick**! Wij hechten veel waarde aan de privacy van onze klanten en bezoekers. In deze privacyverklaring leggen wij uit welke persoonsgegevens wij verzamelen, hoe wij deze gebruiken en welke rechten jij hebt met betrekking tot jouw gegevens.</p>

        <h3>1. Welke gegevens verzamelen wij?</h3>
        <ul>
            <li><strong>Persoonlijke gegevens:</strong> Naam, e-mailadres, telefoonnummer en afleveradres (bij online bestellingen).</li>
            <li><strong>Bestelgegevens:</strong> Producten die je hebt besteld en betaalinformatie (zoals betaalmethode, geen bankgegevens).</li>
            <li><strong>Websitegebruik:</strong> IP-adres, browserinformatie en hoe je onze website gebruikt.</li>
        </ul>

        <h3>2. Hoe gebruiken wij deze gegevens?</h3>
        <p>Wij gebruiken jouw gegevens voor de volgende doeleinden:</p>
        <ul>
            <li><strong>Bestellingen verwerken:</strong> Om je pizza's te leveren en je op de hoogte te houden van je bestelling.</li>
            <li><strong>Klantenservice:</strong> Voor vragen, klachten of ondersteuning.</li>
            <li><strong>Marketing & aanbiedingen:</strong> Alleen indien je toestemming hebt gegeven, bijvoorbeeld voor nieuwsbrieven of speciale acties.</li>
            <li><strong>Websiteoptimalisatie:</strong> Om onze website te verbeteren en jouw gebruikerservaring te optimaliseren.</li>
        </ul>

        <h3>3. Hoe lang bewaren wij jouw gegevens?</h3>
        <p>Wij bewaren jouw gegevens niet langer dan noodzakelijk. Bestelinformatie wordt bewaard zolang als wettelijk verplicht (bijvoorbeeld voor belastingdoeleinden). Marketinggegevens bewaren we totdat jij je afmeldt.</p>

        <h3>4. Delen van gegevens met derden</h3>
        <p>Wij delen jouw gegevens <strong>niet</strong> met derden, behalve wanneer dit noodzakelijk is, zoals:</p>
        <ul>
            <li><strong>Bezorgpartners:</strong> Voor het leveren van jouw bestelling.</li>
            <li><strong>Betaaldiensten:</strong> Om betalingen veilig te verwerken.</li>
            <li><strong>Overheidsinstanties:</strong> Alleen indien wettelijk verplicht.</li>
        </ul>
        <p>Wij verkopen of verhuren jouw gegevens nooit aan derden.</p>

        <h3>5. Jouw rechten</h3>
        <p>Jij hebt het recht om:</p>
        <ul>
            <li>Jouw <strong>gegevens in te zien</strong> en een kopie op te vragen.</li>
            <li>Je gegevens te laten <strong>wijzigen of verwijderen</strong>.</li>
            <li>Je <strong>af te melden</strong> voor marketingcommunicatie.</li>
            <li>Bezwaar te maken tegen het gebruik van jouw gegevens.</li>
        </ul>
        <p>Wil je gebruik maken van deze rechten? Neem contact met ons op via <strong>[e-mailadres invoegen]</strong>.</p>

        <h3>6. Beveiliging van jouw gegevens</h3>
        <p>Wij nemen de beveiliging van jouw gegevens serieus en maken gebruik van <strong>versleuteling, beveiligde verbindingen (SSL) en toegangscontrole</strong> om misbruik of verlies te voorkomen.</p>

       
        <h3>7. Contactgegevens</h3>
        <p>Heb je vragen over deze privacyverklaring? Neem dan contact met ons op:</p>
        <p>📍 <strong>Pizzeria di Rick</strong><br>
        📧 <strong>R.schoenmaker2@student.han.nl</strong><br>
        📞 <strong>06-12345678</strong><br>
        🌐 <strong>http://localhost:8080/pizzeriaDiRick.php</strong></p>

        <p><strong>📌 Deze privacyverklaring kan worden gewijzigd. Controleer deze pagina regelmatig voor updates.</strong></p>
    

    <!-- Akkoord/Niet akkoord sectie -->
    <div class="agreement-container">
        <p>Door op "Ik ga akkoord" te klikken, accepteer je onze privacyvoorwaarden.</p>
        <button onclick="acceptPrivacy()" class="agree-button">Ik ga akkoord</button>
        <button onclick="declinePrivacy()" class="decline-button">Ik ga niet akkoord</button>
    </div>
    </div>


    <script>
        function acceptPrivacy() {
            window.location.href = "pizzeriaDiRick.php"; // Stuur door naar de homepagina
        }

        function declinePrivacy() {
            alert("Je hebt de voorwaarden niet geaccepteerd. Je wordt nu van de pagina verwijderd.");
            window.location.href = "https://www.google.com"; // Verwijder gebruiker van de pagina
        }
    </script>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2025 Pizzeria di Rick | <a href="privacystatement.php">Privacyverklaring</a></p>
    </footer>
</body>
</html>
